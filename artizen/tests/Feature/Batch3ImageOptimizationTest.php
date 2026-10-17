<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Batch3ImageOptimizationTest extends TestCase
{
    /**
     * 1. Test all 8 occasion card images have valid .opt.webp siblings.
     */
    public function test_occasion_images_have_valid_opt_webp_siblings(): void
    {
        $occasionImages = [
            'birthday.webp',
            'house-party.webp',
            'proposals.webp',
            'anniversary.webp',
            'weddings.webp',
            'baby-shower.webp',
            'corporate.webp',
            'live-music.webp',
        ];

        foreach ($occasionImages as $name) {
            $origPath = public_path("images/occasions/{$name}");
            $optPath  = public_path("images/occasions/" . preg_replace('/\.webp$/i', '.opt.webp', $name));

            $this->assertFileExists($origPath, "Original occasion image {$name} must exist");
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
     * 2. Test all 4 About page spaces/banner images have valid .opt.webp siblings.
     */
    public function test_about_page_space_images_have_valid_opt_webp_siblings(): void
    {
        $spaceImages = [
            'images/spaces/rooftop.webp',
            'images/spaces/farmhouse.webp',
            'images/spaces/living-room.webp',
            'images/banners/custom_warm_reception.webp',
        ];

        foreach ($spaceImages as $relPath) {
            $origPath = public_path($relPath);
            $optPath  = public_path(preg_replace('/\.webp$/i', '.opt.webp', $relPath));

            $this->assertFileExists($origPath, "Original image {$relPath} must exist");
            $this->assertFileExists($optPath, "Sibling {$optPath} must exist");

            $im = @imagecreatefromwebp($optPath);
            $this->assertNotFalse($im, "{$optPath} must be a valid WebP image");

            $w = imagesx($im);
            $h = imagesy($im);
            imagedestroy($im);

            $this->assertLessThanOrEqual(1000, $w, "Width must be <= 1000 (package profile)");
            $this->assertLessThanOrEqual(1000, $h, "Height must be <= 1000 (package profile)");
            $this->assertLessThan(filesize($origPath), filesize($optPath), "Optimized {$relPath} must be smaller than original");
        }
    }

    /**
     * 3. Test backup manifest integrity matches all 12 original files bit-for-bit.
     */
    public function test_backup_manifest_integrity_verified(): void
    {
        $manifestPath = storage_path('app/backups/pre_step4d_images/manifest.json');
        $this->assertFileExists($manifestPath, 'Manifest file must exist');

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $this->assertCount(12, $manifest, 'Manifest must contain exactly 12 verified files');

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
     * 4. Test homepage renders occasion cards with .opt.webp picture sources.
     */
    public function test_homepage_renders_picture_tags_for_occasion_cards(): void
    {
        $response = Http::get('http://127.0.0.1:8000/');
        $this->assertTrue($response->successful(), 'Homepage must respond with HTTP 200');

        $html = $response->body();
        $this->assertStringContainsString('images/occasions/birthday.opt.webp', $html);
        $this->assertStringContainsString('images/occasions/weddings.opt.webp', $html);
        $this->assertStringContainsString('images/occasions/proposals.opt.webp', $html);
        $this->assertStringContainsString('images/occasions/house-party.opt.webp', $html);
        $this->assertStringContainsString('images/occasions/anniversary.opt.webp', $html);
        $this->assertStringContainsString('images/occasions/baby-shower.opt.webp', $html);
        $this->assertStringContainsString('images/occasions/corporate.opt.webp', $html);
        $this->assertStringContainsString('images/occasions/live-music.opt.webp', $html);
    }

    /**
     * 5. Test About page renders Transformed Spaces with .opt.webp picture sources.
     */
    public function test_about_page_renders_spaces_with_opt_webp(): void
    {
        $response = Http::get('http://127.0.0.1:8000/about');
        $this->assertTrue($response->successful(), 'About page must respond with HTTP 200');

        $html = $response->body();
        $this->assertStringContainsString('images/spaces/rooftop.opt.webp', $html);
        $this->assertStringContainsString('images/spaces/farmhouse.opt.webp', $html);
        $this->assertStringContainsString('images/spaces/living-room.opt.webp', $html);
        $this->assertStringContainsString('images/banners/custom_warm_reception.opt.webp', $html);
    }

    /**
     * 6. Test all 12 original and 12 optimized assets respond with HTTP 200.
     */
    public function test_all_original_and_optimized_urls_return_http_200(): void
    {
        $urls = [
            'images/occasions/birthday.webp',
            'images/occasions/birthday.opt.webp',
            'images/occasions/house-party.webp',
            'images/occasions/house-party.opt.webp',
            'images/occasions/proposals.webp',
            'images/occasions/proposals.opt.webp',
            'images/occasions/anniversary.webp',
            'images/occasions/anniversary.opt.webp',
            'images/occasions/weddings.webp',
            'images/occasions/weddings.opt.webp',
            'images/occasions/baby-shower.webp',
            'images/occasions/baby-shower.opt.webp',
            'images/occasions/corporate.webp',
            'images/occasions/corporate.opt.webp',
            'images/occasions/live-music.webp',
            'images/occasions/live-music.opt.webp',
            'images/spaces/rooftop.webp',
            'images/spaces/rooftop.opt.webp',
            'images/spaces/farmhouse.webp',
            'images/spaces/farmhouse.opt.webp',
            'images/spaces/living-room.webp',
            'images/spaces/living-room.opt.webp',
            'images/banners/custom_warm_reception.webp',
            'images/banners/custom_warm_reception.opt.webp',
        ];

        foreach ($urls as $relUrl) {
            $url = "http://127.0.0.1:8000/{$relUrl}";
            $res = Http::head($url);
            $this->assertEquals(200, $res->status(), "URL must return HTTP 200: {$url}");
        }
    }
}
