<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonStorageService;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    /**
     * Display the public event celebration gallery.
     */
    public function index()
    {
        // 1. Fetch categories for filter pills
        $categoriesList = JsonStorageService::read('categories.json');

        // 2. Fetch custom admin gallery items & packages to compile gallery
        $customGallery = JsonStorageService::read('gallery.json', []);
        $packagesDb = JsonStorageService::read('packages.json', []);

        $galleryItems = [];

        foreach ($customGallery as $cg) {
            if (!empty($cg['image']) && ($cg['active'] ?? true)) {
                $galleryItems[] = [
                    'id' => 'custom-' . ($cg['id'] ?? rand(100, 999)),
                    'title' => $cg['title'] ?? 'Artizen Setup',
                    'category' => $cg['category'] ?? 'Event Setup',
                    'category_slug' => Str::slug($cg['category'] ?? 'decor'),
                    'image' => $cg['image'],
                    'package_slug' => Str::slug($cg['category'] ?? 'decor')
                ];
            }
        }

        foreach ($packagesDb as $catId => $catData) {
            if (!($catData['active'] ?? true)) continue;

            $catTitle = $catData['title'] ?? 'Event Setup';
            $catSlug = Str::slug($catTitle);
            if ($catSlug === 'adult-birthdays') $catSlug = 'birthdays';

            // Add main category cover image
            if (!empty($catData['image'])) {
                $galleryItems[] = [
                    'id' => 'cat-' . $catId,
                    'title' => $catTitle . ' Setup',
                    'category' => $catTitle,
                    'category_slug' => $catSlug,
                    'image' => $catData['image'],
                    'package_slug' => $catSlug
                ];
            }

            // Add images from gallery array
            foreach ($catData['gallery'] ?? [] as $gIdx => $gImg) {
                if (empty($gImg)) continue;
                $galleryItems[] = [
                    'id' => 'cat-g-' . $catId . '-' . $gIdx,
                    'title' => $catTitle . ' Celebration Highlight ' . ($gIdx + 1),
                    'category' => $catTitle,
                    'category_slug' => $catSlug,
                    'image' => $gImg,
                    'package_slug' => $catSlug
                ];
            }

            // Add images from package tiers
            foreach ($catData['tiers'] ?? [] as $tIdx => $tier) {
                if (!empty($tier['image'])) {
                    $galleryItems[] = [
                        'id' => 'tier-' . $catId . '-' . $tIdx,
                        'title' => $tier['name'] ?? ($catTitle . ' Setup'),
                        'category' => $catTitle,
                        'category_slug' => $catSlug,
                        'image' => $tier['image'],
                        'price' => $tier['price'] ?? 0,
                        'package_slug' => $catSlug
                    ];
                }

                // Tier gallery array
                foreach ($tier['gallery'] ?? [] as $tgIdx => $tgImg) {
                    if (empty($tgImg)) continue;
                    $galleryItems[] = [
                        'id' => 'tier-g-' . $catId . '-' . $tIdx . '-' . $tgIdx,
                        'title' => ($tier['name'] ?? $catTitle) . ' Photo ' . ($tgIdx + 1),
                        'category' => $catTitle,
                        'category_slug' => $catSlug,
                        'image' => $tgImg,
                        'price' => $tier['price'] ?? 0,
                        'package_slug' => $catSlug
                    ];
                }
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
            'galleryItems' => $uniqueGallery,
            'categoriesList' => $categoriesList
        ]);
    }
}
