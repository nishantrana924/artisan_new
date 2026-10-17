<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Batch2ImageOptimizationTest extends TestCase
{
    /**
     * 1. Test all 8 category dropdown images have valid .opt.webp siblings.
     */
    public function test_category_dropdown_images_have_valid_opt_webp_siblings(): void
    {
        $dropdownImages = [
            'anniversaries.webp',
            'baby-shower-kids.webp',
            'birthdays.webp',
            'corporate-events.webp',
            'house-party.webp',
            'live-dj-acoustic.webp',
            'proposals.webp',
            'weddings-sangeet.webp',
        ];

        foreach ($dropdownImages as $name) {
            $origPath = public_path("images/dropdowns/{$name}");
            $optPath  = public_path("images/dropdowns/" . preg_replace('/\.webp$/i', '.opt.webp', $name));

            $this->assertFileExists($origPath, "Original dropdown image {$name} must exist");
            $this->assertFileExists($optPath, "Sibling {$name} opt.webp must exist");

            $im = @imagecreatefromwebp($optPath);
            $this->assertNotFalse($im, "{$optPath} must be a valid WebP image");

            $w = imagesx($im);
            $h = imagesy($im);
            imagedestroy($im);

            $this->assertLessThanOrEqual(800, $w, "Width must be <= 800 (category profile)");
            $this->assertLessThanOrEqual(800, $h, "Height must be <= 800 (category profile)");
            $this->assertLessThan(filesize($origPath), filesize($optPath), "Optimized file must be smaller than original");
        }
    }

    /**
     * 2. Test all 34 celebration images have valid .opt.webp siblings.
     */
    public function test_celebration_images_have_valid_opt_webp_siblings(): void
    {
        $celebrationFiles = glob(public_path('images/celebrations/*.webp'));
        $origFiles = array_filter($celebrationFiles, fn($p) => !str_contains($p, '.opt.webp'));

        $this->assertCount(34, $origFiles, 'There must be exactly 34 celebration original images');

        foreach ($origFiles as $origPath) {
            $filename = basename($origPath);
            $optPath = preg_replace('/\.webp$/i', '.opt.webp', $origPath);

            $this->assertFileExists($origPath, "Original {$filename} must exist");
            $this->assertFileExists($optPath, "Sibling for {$filename} must exist");

            $im = @imagecreatefromwebp($optPath);
            $this->assertNotFalse($im, "{$optPath} must be a valid WebP image");

            $w = imagesx($im);
            $h = imagesy($im);
            imagedestroy($im);

            $this->assertLessThanOrEqual(1000, $w, "Width must be <= 1000 (package profile)");
            $this->assertLessThanOrEqual(1000, $h, "Height must be <= 1000 (package profile)");
            $this->assertLessThan(filesize($origPath), filesize($optPath), "Optimized {$filename} must be smaller than original");
        }
    }

    /**
     * 3. Test backup manifest integrity matches all original files bit-for-bit.
     */
    public function test_backup_manifest_integrity_verified(): void
    {
        $manifestPath = storage_path('app/backups/pre_step4b_images/manifest.json');
        $this->assertFileExists($manifestPath, 'Manifest file must exist');

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $this->assertCount(42, $manifest, 'Manifest must contain exactly 42 verified files');

        foreach ($manifest as $rel => $info) {
            $currentPath = public_path($rel);
            $this->assertFileExists($currentPath, "Current original must exist: {$rel}");

            $this->assertEquals(
                $info['sha256'],
                hash_file('sha256', $currentPath),
                "Source file {$rel} hash must remain unchanged"
            );
            $this->assertEquals(
                $info['size'],
                filesize($currentPath),
                "Source file {$rel} size must remain unchanged"
            );
        }
    }

    /**
     * 4. Test homepage renders category dropdown and celebration pictures.
     */
    public function test_homepage_renders_picture_tags_for_categories_and_celebrations(): void
    {
        $response = Http::get('http://127.0.0.1:8000/');
        $this->assertTrue($response->successful(), 'Homepage must respond with HTTP 200');

        $html = $response->body();
        $this->assertStringContainsString('images/dropdowns/', $html);
        $this->assertStringContainsString('.opt.webp', $html);
        $this->assertStringContainsString('<picture>', $html);
        $this->assertGreaterThan(50, substr_count($html, '<picture>'));
    }

    /**
     * 5. Test events catalog and details pages render optimized pictures with fallbacks.
     */
    public function test_events_catalog_and_details_render_optimized_pictures(): void
    {
        $catResponse = Http::get('http://127.0.0.1:8000/events');
        $this->assertTrue($catResponse->successful(), 'Catalog page must respond with HTTP 200');
        $this->assertStringContainsString('.opt.webp', $catResponse->body());

        $detailResponse = Http::get('http://127.0.0.1:8000/events/1st-birthday-wonderland');
        $this->assertEquals(200, $detailResponse->status(), 'Detail page must respond with HTTP 200');
        $this->assertStringContainsString('id="detail-image"', $detailResponse->body());
        $this->assertStringContainsString('.opt.webp', $detailResponse->body());

        $aboutResponse = Http::get('http://127.0.0.1:8000/about');
        $this->assertTrue($aboutResponse->successful(), 'About page must respond with HTTP 200');
        $this->assertStringContainsString('artizen-grand-celebration.opt.webp', $aboutResponse->body());
    }
}
