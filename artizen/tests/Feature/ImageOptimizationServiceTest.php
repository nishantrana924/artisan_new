<?php

namespace Tests\Feature;

use App\Exceptions\ImageOptimizationException;
use App\Models\Category;
use App\Models\CelebrationRecipient;
use App\Models\Package;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\ImageOptimizationService;
use App\Services\JsonStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ImageOptimizationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ImageOptimizationService $service;
    protected string $testUploadDir;
    protected array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ImageOptimizationService::class);
        $this->testUploadDir = public_path('uploads/test_phase5b');
        if (!File::exists($this->testUploadDir)) {
            File::makeDirectory($this->testUploadDir, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        // Clean up any test artifacts created in public/uploads or test dirs
        foreach ($this->createdFiles as $path) {
            if (File::exists($path)) {
                @unlink($path);
            }
        }

        if (File::isDirectory($this->testUploadDir)) {
            File::deleteDirectory($this->testUploadDir);
        }

        // Clean up test files in standard upload paths
        $testUploads = File::glob(public_path('uploads/*_test_*.*'));
        foreach ($testUploads as $file) {
            @unlink($file);
        }

        $testCategoryUploads = File::glob(public_path('uploads/categories/*_test_*.*'));
        foreach ($testCategoryUploads as $file) {
            @unlink($file);
        }

        $testPackageUploads = File::glob(public_path('uploads/packages/*_test_*.*'));
        foreach ($testPackageUploads as $file) {
            @unlink($file);
        }

        $testRecipientUploads = File::glob(public_path('images/for-everyone/recipient_test*.*'));
        foreach ($testRecipientUploads as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    protected function trackFile(string $absoluteOrRelativePath): string
    {
        $absPath = str_starts_with($actual = $absoluteOrRelativePath, '/') && !str_starts_with($actual, public_path())
            ? public_path(ltrim($actual, '/'))
            : $actual;
        $this->createdFiles[] = $absPath;
        return $absoluteOrRelativePath;
    }

    /**
     * 1. Test JPEG to WebP conversion.
     */
    public function test_jpeg_to_webp_conversion(): void
    {
        $jpegFile = UploadedFile::fake()->image('photo.jpg', 640, 480);

        $result = $this->service->optimizeWithMeta(
            file: $jpegFile,
            targetSubdir: 'uploads/test_phase5b',
            profile: 'default',
            prefix: 'unit_jpeg_test'
        );

        $this->trackFile($result['absolute_path']);

        $this->assertFileExists($result['absolute_path']);
        $this->assertStringEndsWith('.webp', $result['filename']);
        $this->assertEquals('image/webp', $result['mime']);
        $this->assertGreaterThan(0, $result['size']);
        $this->assertEquals(640, $result['width']);
        $this->assertEquals(480, $result['height']);

        // Verify valid WebP header using getimagesize
        $info = @getimagesize($result['absolute_path']);
        $this->assertNotEmpty($info);
        $this->assertEquals('image/webp', $info['mime']);
    }

    /**
     * 2. Test PNG alpha transparency preservation.
     */
    public function test_png_transparency_preservation(): void
    {
        // Create genuine PNG with transparent corners and solid center
        $tmpPng = tempnam(sys_get_temp_dir(), 'test_png_') . '.png';
        $im = imagecreatetruecolor(100, 100);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefilledrectangle($im, 0, 0, 100, 100, $transparent);

        // Solid red box in center
        $red = imagecolorallocatealpha($im, 255, 0, 0, 0);
        imagefilledrectangle($im, 30, 30, 70, 70, $red);
        imagepng($im, $tmpPng);
        imagedestroy($im);

        $pngFile = new UploadedFile($tmpPng, 'transparent.png', 'image/png', null, true);

        $result = $this->service->optimizeWithMeta(
            file: $pngFile,
            targetSubdir: 'uploads/test_phase5b',
            profile: 'default',
            prefix: 'unit_trans_test'
        );

        $this->trackFile($result['absolute_path']);
        @unlink($tmpPng);

        $this->assertFileExists($result['absolute_path']);

        // Inspect pixel at (5, 5) which was transparent
        $optimizedIm = imagecreatefromwebp($result['absolute_path']);
        $this->assertNotFalse($optimizedIm);

        $cornerColor = imagecolorat($optimizedIm, 5, 5);
        $cornerAlpha = ($cornerColor >> 24) & 0x7F;
        // GD alpha is 0 (opaque) to 127 (fully transparent)
        $this->assertGreaterThan(100, $cornerAlpha, 'Corner pixel should preserve high alpha transparency');

        // Inspect pixel at center (50, 50) which was solid red
        $centerColor = imagecolorat($optimizedIm, 50, 50);
        $centerAlpha = ($centerColor >> 24) & 0x7F;
        $this->assertEquals(0, $centerAlpha, 'Center pixel should be fully opaque');

        imagedestroy($optimizedIm);
    }

    /**
     * 3. Test WebP input handling and optimization.
     */
    public function test_webp_input_handling(): void
    {
        $tmpWebp = tempnam(sys_get_temp_dir(), 'test_webp_') . '.webp';
        $im = imagecreatetruecolor(200, 200);
        $blue = imagecolorallocate($im, 20, 80, 200);
        imagefilledrectangle($im, 0, 0, 200, 200, $blue);
        imagewebp($im, $tmpWebp, 90);
        imagedestroy($im);

        $webpFile = new UploadedFile($tmpWebp, 'input.webp', 'image/webp', null, true);

        $result = $this->service->optimizeWithMeta(
            file: $webpFile,
            targetSubdir: 'uploads/test_phase5b',
            profile: 'avatar', // 500x500 max (should preserve 200x200 without upscaling)
            prefix: 'unit_webp_input'
        );

        $this->trackFile($result['absolute_path']);
        @unlink($tmpWebp);

        $this->assertFileExists($result['absolute_path']);
        $this->assertEquals(200, $result['width']);
        $this->assertEquals(200, $result['height']);
        $this->assertEquals('image/webp', $result['mime']);
    }

    /**
     * 4. Test image dimension limits and aspect ratio preservation without upscaling.
     */
    public function test_dimension_limits_and_aspect_ratio_preservation(): void
    {
        // 1. Oversized image: 2000 x 1000 (2:1 aspect ratio) with profile 'package' (max 1000x1000)
        $largeFile = UploadedFile::fake()->image('big_package.jpg', 2000, 1000);

        $result = $this->service->optimizeWithMeta(
            file: $largeFile,
            targetSubdir: 'uploads/test_phase5b',
            profile: 'package',
            prefix: 'unit_downscale'
        );

        $this->trackFile($result['absolute_path']);

        $this->assertEquals(1000, $result['width']);
        $this->assertEquals(500, $result['height']); // 2:1 ratio preserved
        $this->assertFileExists($result['absolute_path']);

        // 2. Small image: 300 x 200 with profile 'package' (max 1000x1000) - must NOT be upscaled
        $smallFile = UploadedFile::fake()->image('small_package.jpg', 300, 200);

        $smallResult = $this->service->optimizeWithMeta(
            file: $smallFile,
            targetSubdir: 'uploads/test_phase5b',
            profile: 'package',
            prefix: 'unit_no_upscale'
        );

        $this->trackFile($smallResult['absolute_path']);

        $this->assertEquals(300, $smallResult['width'], 'Image must not be upscaled');
        $this->assertEquals(200, $smallResult['height'], 'Image must not be upscaled');
    }

    /**
     * 5. Test invalid and corrupted uploads.
     */
    public function test_invalid_and_corrupted_uploads_rejected(): void
    {
        $corruptFile = UploadedFile::fake()->createWithContent('corrupted.jpg', 'THIS_IS_CORRUPTED_BINARY_NOT_AN_IMAGE');

        $this->expectException(ImageOptimizationException::class);
        $this->service->optimize(
            file: $corruptFile,
            targetSubdir: 'uploads/test_phase5b'
        );
    }

    /**
     * 6. Test unsupported formats: SVG vector rejection.
     */
    public function test_svg_vector_rejection(): void
    {
        $svgContent = '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="50" cy="50" r="40"/></svg>';
        $svgFile = UploadedFile::fake()->createWithContent('logo.svg', $svgContent);

        $this->expectException(ImageOptimizationException::class);
        $this->expectExceptionMessage('SVG vector images cannot be converted to raster WebP');
        $this->service->optimize(
            file: $svgFile,
            targetSubdir: 'uploads/test_phase5b'
        );
    }

    /**
     * 7. Test unsupported formats: Animated GIF rejection.
     */
    public function test_animated_gif_rejection(): void
    {
        // Minimal simulated multi-frame GIF binary pattern
        $multiFrameGif = "GIF89a" . str_repeat("\x00", 10) .
            "\x00\x21\xF9\x04\x00\x00\x00\x00\x00\x2C" . str_repeat("\x00", 10) .
            "\x00\x21\xF9\x04\x00\x00\x00\x00\x00\x2C" . str_repeat("\x00", 10);

        $gifFile = UploadedFile::fake()->createWithContent('animation.gif', $multiFrameGif);

        $this->expectException(ImageOptimizationException::class);
        $this->expectExceptionMessage('Animated GIF images cannot be converted');
        $this->service->optimize(
            file: $gifFile,
            targetSubdir: 'uploads/test_phase5b'
        );
    }

    /**
     * 8. Test path traversal protection.
     */
    public function test_path_traversal_protection(): void
    {
        $file = UploadedFile::fake()->image('test.jpg', 100, 100);

        $this->expectException(ImageOptimizationException::class);
        $this->expectExceptionMessage('Invalid upload directory specified');
        $this->service->optimize(
            file: $file,
            targetSubdir: '../secret_path'
        );
    }

    /**
     * 9. Test unique filenames generation.
     */
    public function test_unique_filenames_generated(): void
    {
        $file1 = UploadedFile::fake()->image('same_name.jpg', 100, 100);
        $file2 = UploadedFile::fake()->image('same_name.jpg', 100, 100);

        $path1 = $this->service->optimize($file1, 'uploads/test_phase5b', 'avatar', 'avatar_test');
        $path2 = $this->service->optimize($file2, 'uploads/test_phase5b', 'avatar', 'avatar_test');

        $this->trackFile($path1);
        $this->trackFile($path2);

        $this->assertNotEquals($path1, $path2);
        $this->assertFileExists(public_path(ltrim($path1, '/')));
        $this->assertFileExists(public_path(ltrim($path2, '/')));
    }

    protected function makeAdmin(): User
    {
        $user = new User();
        $user->name = 'Artizen Admin';
        $user->email = 'testadmin_' . uniqid() . '@artizen.com';
        $user->password = bcrypt('Secret123!');
        $user->is_admin = true;
        $user->save();
        return $user;
    }

    /**
     * 10. Test end-to-end integration: Admin category upload with slider and dropdown images.
     */
    public function test_admin_category_upload_workflow_integration(): void
    {
        $sliderImg = UploadedFile::fake()->image('slider_test.jpg', 1200, 600);
        $dropdownImg = UploadedFile::fake()->image('dropdown_test.png', 400, 400);

        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post(route('admin.categories.save'), [
                'title'               => 'Test Optimization Category',
                'bg_color'            => '#EAEAEA',
                'slider_image_file'   => $sliderImg,
                'dropdown_image_file' => $dropdownImg,
                'active'              => '1',
            ]);

        $response->assertRedirect(route('admin.categories'));

        $category = Category::where('title', 'Test Optimization Category')->first();
        $this->assertNotNull($category);
        $this->assertNotNull($category->slider_image);
        $this->assertStringEndsWith('.webp', $category->slider_image);
        $this->assertStringStartsWith('/uploads/categories/', $category->slider_image);
        $this->assertFileExists(public_path(ltrim($category->slider_image, '/')));
        $this->trackFile($category->slider_image);

        $this->assertNotNull($category->dropdown_image);
        $this->assertStringEndsWith('.webp', $category->dropdown_image);
        $this->assertStringStartsWith('/uploads/categories/', $category->dropdown_image);
        $this->assertFileExists(public_path(ltrim($category->dropdown_image, '/')));
        $this->trackFile($category->dropdown_image);
    }

    /**
     * 11. Test end-to-end integration: Admin package upload with cover image and gallery images.
     */
    public function test_admin_package_upload_workflow_integration(): void
    {
        $category = Category::create([
            'title' => 'Test Cat ' . uniqid(),
            'slug'  => 'test-cat-' . uniqid(),
        ]);

        $coverImg = UploadedFile::fake()->image('pkg_cover_test.jpg', 1600, 1200);
        $gal0 = UploadedFile::fake()->image('pkg_gal_test_0.jpg', 1600, 1200);
        $gal1 = UploadedFile::fake()->image('pkg_gal_test_1.png', 800, 800);

        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post(route('admin.packages.save'), [
                'title'          => 'Test Optimization Package',
                'category_id'    => $category->id,
                'price'          => 4999,
                'description'    => 'Complete package with optimized images.',
                'image_file'     => $coverImg,
                'gallery_files'  => [$gal0, $gal1],
            ]);

        $response->assertRedirect(route('admin.packages'));

        $pkg = Package::where('title', 'Test Optimization Package')->first();
        $this->assertNotNull($pkg);
        $this->assertNotNull($pkg->image);
        $this->assertStringEndsWith('.webp', $pkg->image);
        $this->assertStringStartsWith('/uploads/packages/', $pkg->image);
        $this->assertFileExists(public_path(ltrim($pkg->image, '/')));
        $this->trackFile($pkg->image);

        // Verify gallery images array
        $this->assertIsArray($pkg->gallery);
        $this->assertGreaterThanOrEqual(2, count($pkg->gallery));
        foreach ($pkg->gallery as $galPath) {
            $this->assertStringEndsWith('.webp', $galPath);
            $this->assertStringStartsWith('/uploads/packages/', $galPath);
            $this->assertFileExists(public_path(ltrim($galPath, '/')));
            $this->trackFile($galPath);
        }
    }

    /**
     * 12. Test end-to-end integration: Celebration recipient upload.
     */
    public function test_celebration_recipient_upload_workflow_integration(): void
    {
        $recipientImg = UploadedFile::fake()->image('recipient_test.jpg', 1000, 1000);

        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post(route('admin.recipients.save'), [
                'name'          => 'Test Persona Optimization',
                'image_file'    => $recipientImg,
                'display_order' => 1,
                'is_active'     => '1',
            ]);

        $response->assertRedirect(route('admin.recipients'));

        $recipient = CelebrationRecipient::where('name', 'Test Persona Optimization')->first();
        $this->assertNotNull($recipient);
        $this->assertNotNull($recipient->image);
        $this->assertStringEndsWith('.webp', $recipient->image);
        $this->assertStringStartsWith('/images/for-everyone/', $recipient->image);
        $this->assertFileExists(public_path(ltrim($recipient->image, '/')));
        $this->trackFile($recipient->image);
    }

    /**
     * 13. Test convertExistingFile method directly on a local disk image.
     */
    public function test_convert_existing_file_on_disk(): void
    {
        $tmpJpeg = tempnam(sys_get_temp_dir(), 'test_orig_') . '.jpg';
        $im = imagecreatetruecolor(800, 600);
        $green = imagecolorallocate($im, 34, 139, 34);
        imagefilledrectangle($im, 0, 0, 800, 600, $green);
        imagejpeg($im, $tmpJpeg, 95);
        imagedestroy($im);

        $destWebp = $this->testUploadDir . '/converted_existing.webp';

        $meta = $this->service->convertExistingFile(
            sourcePath: $tmpJpeg,
            destPath: $destWebp,
            profile: 'category',
            quality: 82
        );

        $this->trackFile($destWebp);
        @unlink($tmpJpeg);

        $this->assertFileExists($destWebp);
        $this->assertEquals(800, $meta['width']);
        $this->assertEquals(600, $meta['height']);
        $this->assertGreaterThan(0, $meta['optimized_size']);
    }

    /**
     * 14. Test end-to-end integration: Admin artist upload workflow.
     */
    public function test_admin_artist_upload_workflow_integration(): void
    {
        $artistImg = UploadedFile::fake()->image('artist_test.jpg', 900, 900);
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post(route('admin.artists.save'), [
                'name'       => 'Test Live Artist',
                'category'   => 'Live Acoustic',
                'price'      => '₹15,000 onwards',
                'image_file' => $artistImg,
            ]);

        $response->assertRedirect(route('admin.testimonials'));

        $artists = JsonStorageService::read('artists.json', []);
        $found = null;
        foreach ($artists as $art) {
            if (($art['name'] ?? '') === 'Test Live Artist') {
                $found = $art;
                break;
            }
        }

        $this->assertNotNull($found);
        $this->assertStringEndsWith('.webp', $found['image']);
        $this->assertStringStartsWith('/uploads/', $found['image']);
        $this->assertFileExists(public_path(ltrim($found['image'], '/')));
        $this->trackFile($found['image']);

        // Clean up created artist from artists.json
        $cleaned = array_values(array_filter($artists, fn($a) => ($a['name'] ?? '') !== 'Test Live Artist'));
        JsonStorageService::write('artists.json', $cleaned);
    }

    /**
     * 15. Test end-to-end integration: Admin gallery showcase item upload workflow.
     */
    public function test_admin_gallery_item_upload_workflow_integration(): void
    {
        $galleryImg = UploadedFile::fake()->image('gallery_test.jpg', 1800, 1200);
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post(route('admin.gallery.save'), [
                'title'      => 'Test Wedding Stage Showcase',
                'category'   => 'Weddings & Sangeet',
                'image_file' => $galleryImg,
            ]);

        $response->assertRedirect(route('admin.testimonials'));

        $gallery = JsonStorageService::read('gallery.json', []);
        $found = null;
        foreach ($gallery as $item) {
            if (($item['title'] ?? '') === 'Test Wedding Stage Showcase') {
                $found = $item;
                break;
            }
        }

        $this->assertNotNull($found);
        $this->assertStringEndsWith('.webp', $found['image']);
        $this->assertStringStartsWith('/uploads/', $found['image']);
        $this->assertFileExists(public_path(ltrim($found['image'], '/')));
        $this->trackFile($found['image']);

        // Clean up created gallery item from gallery.json
        $cleaned = array_values(array_filter($gallery, fn($g) => ($g['title'] ?? '') !== 'Test Wedding Stage Showcase'));
        JsonStorageService::write('gallery.json', $cleaned);
    }

    /**
     * 16. Test end-to-end integration: Admin CMS hero slide upload workflow.
     */
    public function test_admin_cms_hero_slide_upload_workflow_integration(): void
    {
        $originalCms = JsonStorageService::read('cms.json', []);
        try {
            $slideImg = UploadedFile::fake()->image('slide_test.jpg', 1920, 1080);
            $admin = $this->makeAdmin();

            $response = $this->actingAs($admin)
                ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
                ->post(route('admin.cms.save'), [
                    'hero_slides' => [
                        0 => [
                            'title'      => 'Test Hero Slide',
                            'subtitle'   => 'Indore Premier Celebrations',
                            'image_file' => $slideImg,
                        ]
                    ]
                ]);

            $response->assertRedirect(route('admin.hero'));

            $cms = JsonStorageService::read('cms.json', []);
            $this->assertNotEmpty($cms['hero_slides'] ?? []);
            $firstSlide = $cms['hero_slides'][0];
            $this->assertStringEndsWith('.webp', $firstSlide['image']);
            $this->assertStringStartsWith('/uploads/', $firstSlide['image']);
            $this->assertFileExists(public_path(ltrim($firstSlide['image'], '/')));
            $this->trackFile($firstSlide['image']);
        } finally {
            JsonStorageService::write('cms.json', $originalCms);
        }
    }

    /**
     * 17. Test end-to-end integration: Admin review avatar upload workflow.
     */
    public function test_admin_review_avatar_upload_workflow_integration(): void
    {
        $avatarImg = UploadedFile::fake()->image('reviewer_test.jpg', 600, 600);
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_logged_in' => true, 'admin_email' => $admin->email])
            ->post(route('admin.reviews.save'), [
                'author'      => 'Test Reviewer',
                'email'       => 'reviewer@example.com',
                'location'    => 'Indore, MP',
                'event_type'  => 'Birthday Party',
                'review'      => 'Outstanding setup and prompt execution!',
                'rating'      => 5,
                'avatar_file' => $avatarImg,
            ]);

        $response->assertRedirect(route('admin.reviews'));

        $review = Testimonial::where('author', 'Test Reviewer')->first();
        $this->assertNotNull($review);
        $this->assertNotNull($review->avatar);
        $this->assertStringEndsWith('.webp', $review->avatar);
        $this->assertStringStartsWith('/uploads/', $review->avatar);
        $this->assertFileExists(public_path(ltrim($review->avatar, '/')));
        $this->trackFile($review->avatar);
    }
}
