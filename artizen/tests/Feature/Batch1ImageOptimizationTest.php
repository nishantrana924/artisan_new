<?php

namespace Tests\Feature;

use Tests\TestCase;

class Batch1ImageOptimizationTest extends TestCase
{
    /**
     * 1. Test main logo WebP sibling and alpha transparency.
     */
    public function test_main_logo_webp_sibling_exists_and_preserves_transparency(): void
    {
        $pngPath = public_path('assets/images/logo/artizen.png');
        $webpPath = public_path('assets/images/logo/artizen.webp');

        $this->assertFileExists($pngPath, 'Original artizen.png must exist');
        $this->assertFileExists($webpPath, 'Optimized sibling artizen.webp must exist');

        $im = @imagecreatefromwebp($webpPath);
        $this->assertNotFalse($im, 'Sibling artizen.webp must be a valid WebP image');

        $w = imagesx($im);
        $h = imagesy($im);
        $this->assertLessThanOrEqual(500, $w, 'Width must be <= 500 (avatar profile)');
        $this->assertLessThanOrEqual(500, $h, 'Height must be <= 500 (avatar profile)');

        // Test alpha transparency
        $hasAlpha = false;
        for ($y = 0; $y < $h; $y += 5) {
            for ($x = 0; $x < $w; $x += 5) {
                $rgba = imagecolorat($im, $x, $y);
                $alpha = ($rgba >> 24) & 0x7F;
                if ($alpha > 0) {
                    $hasAlpha = true;
                    break 2;
                }
            }
        }
        imagedestroy($im);

        $this->assertTrue($hasAlpha, 'artizen.webp must retain alpha channel transparency');
        $this->assertLessThan(filesize($pngPath), filesize($webpPath), 'artizen.webp must be smaller than artizen.png');
    }

    /**
     * 2. Test seven homepage persona images have valid .opt.webp siblings.
     */
    public function test_seven_persona_images_have_valid_opt_webp_siblings(): void
    {
        $personas = ['husband', 'parents', 'wife', 'her', 'friend', 'him', 'kids'];

        foreach ($personas as $name) {
            $origPath = public_path("images/for-everyone/{$name}.webp");
            $optPath  = public_path("images/for-everyone/{$name}.opt.webp");

            $this->assertFileExists($origPath, "Original {$name}.webp must remain intact");
            $this->assertFileExists($optPath, "Sibling {$name}.opt.webp must exist");

            $im = @imagecreatefromwebp($optPath);
            $this->assertNotFalse($im, "{$name}.opt.webp must be decodable");

            $w = imagesx($im);
            $h = imagesy($im);
            $this->assertLessThanOrEqual(800, $w, "{$name}.opt.webp width must be <= 800");
            $this->assertLessThanOrEqual(800, $h, "{$name}.opt.webp height must be <= 800");

            imagedestroy($im);

            $this->assertLessThan(
                filesize($origPath),
                filesize($optPath),
                "{$name}.opt.webp must be significantly smaller than original"
            );
        }
    }

    /**
     * 3. Test hero banners have valid .opt.webp siblings.
     */
    public function test_hero_banners_have_valid_opt_webp_siblings(): void
    {
        $banners = [
            'banner-navy-floral',
            'banner-emerald-wedding',
            'banner-gold-dj',
            'artizen-banner-birthday',
        ];

        foreach ($banners as $name) {
            $origPath = public_path("images/banners/{$name}.webp");
            $optPath  = public_path("images/banners/{$name}.opt.webp");

            $this->assertFileExists($origPath, "Original {$name}.webp must remain intact");
            $this->assertFileExists($optPath, "Sibling {$name}.opt.webp must exist");

            $im = @imagecreatefromwebp($optPath);
            $this->assertNotFalse($im, "{$name}.opt.webp must be decodable");
            imagedestroy($im);

            $this->assertLessThan(
                filesize($origPath),
                filesize($optPath),
                "{$name}.opt.webp must be smaller than original"
            );
        }
    }

    /**
     * 4. Test hero slide 1.webp sibling exists alongside 1.jpg.
     */
    public function test_hero_slide_1_webp_sibling_exists(): void
    {
        $origPath = public_path('assets/images/hero/1.jpg');
        $webpPath = public_path('assets/images/hero/1.webp');

        $this->assertFileExists($origPath, 'Original 1.jpg must remain intact');
        $this->assertFileExists($webpPath, 'Sibling 1.webp must exist');

        $im = @imagecreatefromwebp($webpPath);
        $this->assertNotFalse($im, '1.webp must be decodable');
        imagedestroy($im);

        $this->assertLessThan(filesize($origPath), filesize($webpPath), '1.webp must be smaller than 1.jpg');
    }

    /**
     * 5. Test homepage renders picture tags with WebP siblings and original fallbacks.
     */
    public function test_homepage_renders_picture_tags_with_webp_and_fallbacks(): void
    {
        $response = \Illuminate\Support\Facades\Http::get('http://127.0.0.1:8000/');
        $this->assertTrue($response->successful(), 'Homepage must respond with HTTP 200');

        $content = $response->body();

        // 1. Logo picture tag in navbar
        $this->assertStringContainsString('assets/images/logo/artizen.webp', $content);
        $this->assertStringContainsString('assets/images/logo/artizen.png', $content);

        // 2. Persona images opt.webp in carousel
        $this->assertStringContainsString('images/for-everyone/husband.opt.webp', $content);
        $this->assertStringContainsString('images/for-everyone/wife.opt.webp', $content);

        // 3. Hero banners in carousel
        $this->assertStringContainsString('images/banners/banner-navy-floral.opt.webp', $content);
        $this->assertStringContainsString('images/banners/banner-navy-floral.webp', $content);
    }
}
