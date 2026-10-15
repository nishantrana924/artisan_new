<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CelebrationCatalogService;
use App\Services\JsonStorageService;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    /**
     * Display the public event celebration gallery.
     */
    public function index()
    {
        // 1. Fetch custom admin gallery items (if any uploaded by admin)
        $customGallery = JsonStorageService::read('gallery.json', []);
        
        // 2. Fetch all real celebration packages from catalog service
        $allPackages = CelebrationCatalogService::getAllPackages();

        $galleryItems = [];

        // Add custom gallery items from admin
        foreach ($customGallery as $cg) {
            if (!empty($cg['image']) && ($cg['active'] ?? true)) {
                $galleryItems[] = [
                    'id' => 'custom-' . ($cg['id'] ?? rand(100, 999)),
                    'title' => $cg['title'] ?? 'Artizen Setup',
                    'category' => $cg['category'] ?? 'Celebration Setup',
                    'image' => $cg['image'],
                    'package_slug' => Str::slug($cg['package_slug'] ?? ($cg['category'] ?? ''))
                ];
            }
        }

        // Add all high-res photos from real packages
        foreach ($allPackages as $p) {
            $pkgSlug = $p['slug'] ?? Str::slug($p['title']);
            $catName = $p['category_name'] ?? 'Celebration Setup';

            // Primary Cover Photo
            if (!empty($p['image'])) {
                $galleryItems[] = [
                    'id' => 'pkg-main-' . $p['id'],
                    'title' => $p['title'],
                    'category' => $catName,
                    'image' => $p['image'],
                    'package_slug' => $pkgSlug
                ];
            }

            // Additional Gallery Photos for this package
            foreach ($p['gallery'] ?? [] as $gIdx => $gImg) {
                if (empty($gImg) || $gImg === $p['image']) continue;
                $galleryItems[] = [
                    'id' => 'pkg-gal-' . $p['id'] . '-' . $gIdx,
                    'title' => $p['title'],
                    'category' => $catName,
                    'image' => $gImg,
                    'package_slug' => $pkgSlug
                ];
            }
        }

        // Deduplicate images by image URL
        $uniqueGallery = [];
        $seenImages = [];
        foreach ($galleryItems as $item) {
            if (!in_array($item['image'], $seenImages)) {
                $seenImages[] = $item['image'];
                $uniqueGallery[] = $item;
            }
        }

        return view('gallery.index', [
            'galleryItems' => $uniqueGallery
        ]);
    }
}
