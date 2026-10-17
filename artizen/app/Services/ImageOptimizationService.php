<?php

namespace App\Services;

use App\Exceptions\ImageOptimizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Default WebP output compression quality (1-100).
     */
    public const DEFAULT_QUALITY = 82;

    /**
     * Maximum allowed upload size in bytes (12 MB).
     */
    public const MAX_FILE_SIZE_BYTES = 12 * 1024 * 1024;

    /**
     * Supported MIME types for raster WebP optimization.
     */
    public const SUPPORTED_MIMES = [
        'image/jpeg',
        'image/jpg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'image/webp',
    ];

    /**
     * Standard dimensional constraints by usage profile (maximum width and height).
     * Aspect ratios are always strictly preserved; images are never upscaled.
     */
    public const PROFILES = [
        'avatar'             => ['max_width' => 500,  'max_height' => 500],
        'category'           => ['max_width' => 800,  'max_height' => 800],
        'category_thumbnail' => ['max_width' => 800,  'max_height' => 800],
        'package'            => ['max_width' => 1000, 'max_height' => 1000],
        'package_card'       => ['max_width' => 1000, 'max_height' => 1000],
        'gallery'            => ['max_width' => 1600, 'max_height' => 1600],
        'hero_slide'         => ['max_width' => 1920, 'max_height' => 1200],
        'hero'               => ['max_width' => 1920, 'max_height' => 1200],
        'review'             => ['max_width' => 600,  'max_height' => 600],
        'artist'             => ['max_width' => 800,  'max_height' => 800],
        'recipient'          => ['max_width' => 800,  'max_height' => 800],
        'default'            => ['max_width' => 1200, 'max_height' => 1200],
    ];

    /**
     * Optimize an uploaded image or source file and save it as WebP.
     *
     * @param UploadedFile|string $file Uploaded file instance or absolute source path
     * @param string $targetSubdir Directory relative to public_path() (e.g. 'uploads/packages') or absolute path
     * @param string $profile Image profile name from PROFILES (e.g. 'package', 'avatar', 'gallery')
     * @param string|null $prefix Optional filename prefix (e.g. 'pkg', 'slider', 'avatar_1')
     * @param int $quality WebP quality factor (default: 82)
     * @return string Web-accessible relative URL path (e.g. '/uploads/packages/pkg_1786478375_a1b2c3d4.webp')
     *
     * @throws ImageOptimizationException
     */
    public function optimize(
        UploadedFile|string $file,
        string $targetSubdir = 'uploads',
        string $profile = 'default',
        ?string $prefix = null,
        int $quality = self::DEFAULT_QUALITY
    ): string {
        $meta = $this->optimizeWithMeta($file, $targetSubdir, $profile, $prefix, $quality);
        return $meta['path'];
    }

    /**
     * Optimize an image and return comprehensive metadata (dimensions, size, paths).
     *
     * @param UploadedFile|string $file
     * @param string $targetSubdir
     * @param string $profile
     * @param string|null $prefix
     * @param int $quality
     * @return array
     *
     * @throws ImageOptimizationException
     */
    public function optimizeWithMeta(
        UploadedFile|string $file,
        string $targetSubdir = 'uploads',
        string $profile = 'default',
        ?string $prefix = null,
        int $quality = self::DEFAULT_QUALITY
    ): array {
        $this->ensureGdAvailable();

        // 1. Resolve source file path and validate upload
        $sourcePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $originalSize = $file instanceof UploadedFile ? $file->getSize() : (@filesize($sourcePath) ?: 0);

        $this->validateSourceFile($file, $sourcePath, $originalSize);

        // 2. Resolve target directories and unique filename
        $destInfo = $this->resolveDestination($targetSubdir, $prefix);
        $absoluteDestDir = $destInfo['dir'];
        $filename = $destInfo['filename'];
        $absoluteDestPath = $destInfo['path'];
        $webRelativePath = $destInfo['relative_path'];

        // 3. Load image resource with GD
        $sourceImage = $this->createImageResource($sourcePath);

        try {
            // 4. Auto-orient based on EXIF data (for JPEGs captured on mobile devices)
            $sourceImage = $this->applyExifOrientation($sourceImage, $sourcePath);

            $origWidth = imagesx($sourceImage);
            $origHeight = imagesy($sourceImage);

            if ($origWidth <= 0 || $origHeight <= 0) {
                throw new ImageOptimizationException('Invalid image dimensions detected.');
            }

            // 5. Calculate proportional target dimensions (avoid upscaling)
            $dimensions = $this->calculateTargetDimensions($origWidth, $origHeight, $profile);
            $targetWidth = $dimensions['width'];
            $targetHeight = $dimensions['height'];

            // 6. Create truecolor canvas with full alpha transparency support
            $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);

            // 7. Resample high-quality output
            imagecopyresampled(
                $targetImage,
                $sourceImage,
                0, 0, 0, 0,
                $targetWidth,
                $targetHeight,
                $origWidth,
                $origHeight
            );

            // 8. Output as WebP
            $clampedQuality = max(10, min(100, $quality));
            $saved = @imagewebp($targetImage, $absoluteDestPath, $clampedQuality);

            if (!$saved || !file_exists($absoluteDestPath) || filesize($absoluteDestPath) === 0) {
                if (file_exists($absoluteDestPath)) {
                    @unlink($absoluteDestPath);
                }
                throw new ImageOptimizationException('Failed to write WebP image to destination: ' . $absoluteDestPath);
            }

            $optimizedSize = filesize($absoluteDestPath);

            return [
                'path'            => $webRelativePath,
                'absolute_path'   => $absoluteDestPath,
                'filename'        => $filename,
                'width'           => $targetWidth,
                'height'          => $targetHeight,
                'size'            => $optimizedSize,
                'mime'            => 'image/webp',
                'original_width'  => $origWidth,
                'original_height' => $origHeight,
                'original_size'   => $originalSize,
            ];
        } finally {
            if (is_resource($sourceImage) || $sourceImage instanceof \GdImage) {
                imagedestroy($sourceImage);
            }
            if (isset($targetImage) && (is_resource($targetImage) || $targetImage instanceof \GdImage)) {
                imagedestroy($targetImage);
            }
        }
    }

    /**
     * Convert an existing local asset to WebP.
     *
     * @param string $sourcePath Absolute source path
     * @param string $destPath Absolute destination path
     * @param string $profile Image profile name
     * @param int $quality WebP quality
     * @return array Metadata
     */
    public function convertExistingFile(
        string $sourcePath,
        string $destPath,
        string $profile = 'default',
        int $quality = self::DEFAULT_QUALITY
    ): array {
        $this->ensureGdAvailable();

        if (!file_exists($sourcePath) || !is_readable($sourcePath)) {
            throw new ImageOptimizationException("Source file does not exist or is not readable: {$sourcePath}");
        }

        $destDir = dirname($destPath);
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $sourceImage = $this->createImageResource($sourcePath);

        try {
            $sourceImage = $this->applyExifOrientation($sourceImage, $sourcePath);
            $origWidth = imagesx($sourceImage);
            $origHeight = imagesy($sourceImage);

            $dimensions = $this->calculateTargetDimensions($origWidth, $origHeight, $profile);
            $targetWidth = $dimensions['width'];
            $targetHeight = $dimensions['height'];

            $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);

            imagecopyresampled(
                $targetImage,
                $sourceImage,
                0, 0, 0, 0,
                $targetWidth,
                $targetHeight,
                $origWidth,
                $origHeight
            );

            $clampedQuality = max(10, min(100, $quality));
            $saved = @imagewebp($targetImage, $destPath, $clampedQuality);

            if (!$saved || !file_exists($destPath) || filesize($destPath) === 0) {
                if (file_exists($destPath)) {
                    @unlink($destPath);
                }
                throw new ImageOptimizationException("Failed to write WebP file to {$destPath}");
            }

            return [
                'source_path'     => $sourcePath,
                'dest_path'       => $destPath,
                'width'           => $targetWidth,
                'height'          => $targetHeight,
                'original_size'   => filesize($sourcePath),
                'optimized_size'  => filesize($destPath),
                'savings_bytes'   => max(0, filesize($sourcePath) - filesize($destPath)),
            ];
        } finally {
            if (is_resource($sourceImage) || $sourceImage instanceof \GdImage) {
                imagedestroy($sourceImage);
            }
            if (isset($targetImage) && (is_resource($targetImage) || $targetImage instanceof \GdImage)) {
                imagedestroy($targetImage);
            }
        }
    }

    /**
     * Ensure the PHP GD extension is installed and supports WebP.
     */
    protected function ensureGdAvailable(): void
    {
        if (!extension_loaded('gd')) {
            throw new ImageOptimizationException('The GD PHP extension is required for image optimization but is not installed.');
        }

        if (!function_exists('imagewebp')) {
            throw new ImageOptimizationException('The current PHP GD installation does not support WebP image encoding.');
        }
    }

    /**
     * Validate the source file for existence, size, MIME type, and raster format.
     */
    protected function validateSourceFile(UploadedFile|string $file, string $sourcePath, int $size): void
    {
        if (empty($sourcePath) || !file_exists($sourcePath) || !is_readable($sourcePath)) {
            throw new ImageOptimizationException('The uploaded file could not be read or was not found on the server.');
        }

        if ($size > self::MAX_FILE_SIZE_BYTES) {
            $maxMb = round(self::MAX_FILE_SIZE_BYTES / 1024 / 1024);
            throw new ImageOptimizationException("The uploaded image exceeds the maximum allowed size of {$maxMb}MB.");
        }

        // Determine actual MIME type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo ? finfo_file($finfo, $sourcePath) : null;
        if ($finfo) {
            finfo_close($finfo);
        }

        if ($file instanceof UploadedFile) {
            $clientMime = $file->getMimeType();
            $mime = $detectedMime ?: $clientMime;
        } else {
            $mime = $detectedMime;
        }

        $mime = strtolower((string)$mime);

        // Check for vector SVG or XML disguised as raster
        if (str_contains($mime, 'svg') || str_contains($mime, 'xml')) {
            throw new ImageOptimizationException('SVG vector images cannot be converted to raster WebP. Preserve original vector artwork.');
        }

        // Check for animated GIF
        if ($mime === 'image/gif') {
            if ($this->isAnimatedGif($sourcePath)) {
                throw new ImageOptimizationException('Animated GIF images cannot be converted to static WebP without losing animation frames.');
            }
            // Static GIFs can be converted
            return;
        }

        if (!in_array($mime, self::SUPPORTED_MIMES, true)) {
            throw new ImageOptimizationException("Unsupported image format ({$mime}). Only JPEG, PNG, and WebP are supported.");
        }
    }

    /**
     * Detect whether a GIF image file contains multiple animation frames.
     */
    public function isAnimatedGif(string $filePath): bool
    {
        $content = @file_get_contents($filePath);
        if (!$content) {
            return false;
        }
        $count = preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $content);
        return $count > 1;
    }

    /**
     * Load an image file into a GD resource safely.
     */
    protected function createImageResource(string $sourcePath)
    {
        $raw = @file_get_contents($sourcePath);
        if ($raw === false || strlen($raw) < 12) {
            throw new ImageOptimizationException('The uploaded image content is empty or unreadable.');
        }

        $image = @imagecreatefromstring($raw);
        if (!$image) {
            throw new ImageOptimizationException('The uploaded file is corrupted or not a valid image format.');
        }

        if (function_exists('imageistruecolor') && !imageistruecolor($image) && function_exists('imagepalettetotruecolor')) {
            imagepalettetotruecolor($image);
        }

        return $image;
    }

    /**
     * Auto-orient JPEG image resource using EXIF orientation metadata.
     */
    protected function applyExifOrientation($image, string $sourcePath)
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }

        try {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                switch ((int)$exif['Orientation']) {
                    case 3:
                        $rotated = imagerotate($image, 180, 0);
                        if ($rotated) {
                            imagedestroy($image);
                            return $rotated;
                        }
                        break;
                    case 6:
                        $rotated = imagerotate($image, -90, 0);
                        if ($rotated) {
                            imagedestroy($image);
                            return $rotated;
                        }
                        break;
                    case 8:
                        $rotated = imagerotate($image, 90, 0);
                        if ($rotated) {
                            imagedestroy($image);
                            return $rotated;
                        }
                        break;
                }
            }
        } catch (\Throwable $e) {
            // Ignore EXIF read failures and proceed with original orientation
        }

        return $image;
    }

    /**
     * Calculate proportional target dimensions preserving aspect ratio and preventing upscaling.
     */
    protected function calculateTargetDimensions(int $origWidth, int $origHeight, string $profile): array
    {
        $profileConfig = self::PROFILES[$profile] ?? self::PROFILES['default'];
        $maxWidth = $profileConfig['max_width'];
        $maxHeight = $profileConfig['max_height'];

        // If the original image is already within bounds, preserve original dimensions (no upscaling)
        if ($origWidth <= $maxWidth && $origHeight <= $maxHeight) {
            return [
                'width'  => $origWidth,
                'height' => $origHeight,
            ];
        }

        // Calculate aspect-ratio scale factor
        $scale = min($maxWidth / $origWidth, $maxHeight / $origHeight);
        $targetWidth = (int) max(1, round($origWidth * $scale));
        $targetHeight = (int) max(1, round($origHeight * $scale));

        return [
            'width'  => $targetWidth,
            'height' => $targetHeight,
        ];
    }

    /**
     * Resolve target directory, filename, and relative web path with path traversal protection.
     */
    protected function resolveDestination(string $targetSubdir, ?string $prefix): array
    {
        // Guard against path traversal attempts
        if (str_contains($targetSubdir, '..')) {
            throw new ImageOptimizationException('Invalid upload directory specified.');
        }

        // Determine absolute destination directory
        if (str_starts_with($targetSubdir, '/')) {
            // Absolute destination path (e.g. test directory or custom path)
            $absoluteDir = rtrim($targetSubdir, '/');
            $relativePrefix = '/' . ltrim(str_replace(public_path(), '', $absoluteDir), '/');
        } else {
            $cleanSubdir = trim($targetSubdir, '/');
            $absoluteDir = public_path($cleanSubdir);
            $relativePrefix = '/' . $cleanSubdir;
        }

        if (!File::exists($absoluteDir)) {
            File::makeDirectory($absoluteDir, 0755, true);
        }

        // Generate unique, collision-resistant filename
        $sanitizedPrefix = $prefix ? Str::slug($prefix, '_') : 'img';
        $filename = $sanitizedPrefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
        $absolutePath = $absoluteDir . DIRECTORY_SEPARATOR . $filename;
        $relativePath = rtrim($relativePrefix, '/') . '/' . $filename;

        return [
            'dir'           => $absoluteDir,
            'filename'      => $filename,
            'path'          => $absolutePath,
            'relative_path' => $relativePath,
        ];
    }
}
