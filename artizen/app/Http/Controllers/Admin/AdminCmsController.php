<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LegalController;
use App\Services\ImageOptimizationService;
use App\Services\JsonStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminCmsController extends Controller
{
    /**
     * Save Hero Banners.
     */
    public function saveHeroSlides(Request $request)
    {
        $request->validate([
            'hero_slides'                 => ['nullable', 'array'],
            'hero_slides.*.id'            => ['nullable'],
            'hero_slides.*.image_file'    => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'hero_slides.*.image'         => ['nullable', 'string', 'max:255'],
            'hero_slides.*.badge'         => ['nullable', 'string', 'max:100'],
            'hero_slides.*.title'         => ['nullable', 'string', 'max:255'],
            'hero_slides.*.desc'          => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $cleaned = trim(strip_tags((string)$value));
                    $wordCount = $cleaned === '' ? 0 : count(preg_split('/\s+/', $cleaned));
                    if ($wordCount > 50) {
                        $fail("The description must not exceed 50 words (currently {$wordCount} words).");
                    }
                },
            ],
            'hero_slides.*.btn1Text'      => ['nullable', 'string', 'max:100'],
            'hero_slides.*.link1'         => ['nullable', 'string', 'max:255'],
            'hero_slides.*.btn2Text'      => ['nullable', 'string', 'max:100'],
            'hero_slides.*.link2'         => ['nullable', 'string', 'max:255'],
            'hero_slides.*.display_order' => ['nullable', 'integer'],
        ]);

        $currentCms = JsonStorageService::read('cms.json', []);
        $heroSlidesInput = $request->input('hero_slides', []);
        $heroSlides = [];

        foreach ($heroSlidesInput as $idx => $slideData) {
            if ($request->hasFile("hero_slides.{$idx}.image_file")) {
                try {
                    $slideData['image'] = app(ImageOptimizationService::class)->optimize(
                        file: $request->file("hero_slides.{$idx}.image_file"),
                        targetSubdir: 'uploads',
                        profile: 'hero_slide',
                        prefix: 'hero_' . $idx,
                        quality: ImageOptimizationService::DEFAULT_QUALITY
                    );
                } catch (\Throwable $e) {
                    Log::warning("Hero slide {$idx} optimization failed: " . $e->getMessage());
                    $slideData['image'] = $slideData['image'] ?? ($currentCms['hero_slides'][$idx]['image'] ?? '/assets/images/hero/1.jpg');
                }
            } else {
                $slideData['image'] = $slideData['image'] ?? ($currentCms['hero_slides'][$idx]['image'] ?? '/assets/images/hero/1.jpg');
            }
            unset($slideData['image_file']);

            $slideData['id'] = !empty($slideData['id']) ? (int)$slideData['id'] : null;
            $slideData['title'] = trim((string)($slideData['title'] ?? ''));
            $slideData['desc'] = trim(strip_tags((string)($slideData['desc'] ?? '')));
            $slideData['btn1Text'] = trim((string)($slideData['btn1Text'] ?? 'Explore Packages'));
            $slideData['link1'] = trim((string)($slideData['link1'] ?? '/events'));

            if (isset($slideData['is_active'])) {
                $slideData['is_active'] = in_array($slideData['is_active'], [true, 1, '1', 'on', 'true'], true);
            } else {
                $slideData['is_active'] = true;
            }

            $slideData['display_order'] = (int)($slideData['display_order'] ?? $idx);
            $heroSlides[] = $slideData;
        }

        $currentCms['hero_slides'] = $heroSlides;
        JsonStorageService::write('cms.json', $currentCms);

        return redirect()->route('admin.hero')->with('success', 'Hero banners updated successfully!');
    }

    /**
     * Save Offers Section.
     */
    public function saveOffers(Request $request)
    {
        $request->validate([
            'offers'                        => ['nullable', 'array'],
            'offers.section_title'          => ['nullable', 'string', 'max:150'],
            'offers.items'                  => ['nullable', 'array'],
            'offers.items.*.category_name'  => ['nullable', 'string', 'max:150'],
            'offers.items.*.discount_title' => ['nullable', 'string', 'max:100'],
            'offers.items.*.subheading'     => ['nullable', 'string', 'max:150'],
            'offers.items.*.terms'          => ['nullable', 'string', 'max:255'],
            'offers.items.*.link'           => ['nullable', 'string', 'max:255'],
            'offers.items.*.icon'           => ['nullable', 'string', 'max:100'],
            'offers.items.*.icon_color'     => ['nullable', 'string', 'max:50'],
        ]);

        $currentCms = JsonStorageService::read('cms.json', []);
        $offersInput = $request->input('offers', []);
        $items = [];

        foreach ($offersInput['items'] ?? [] as $oIdx => $item) {
            $items[] = [
                'id'             => !empty($item['id']) ? (int)$item['id'] : ($oIdx + 1),
                'category_name'  => trim($item['category_name'] ?? 'Celebration Offer'),
                'icon'           => trim($item['icon'] ?? 'fa-solid fa-cake-candles'),
                'icon_color'     => trim($item['icon_color'] ?? 'text-amber-600'),
                'subheading'     => trim($item['subheading'] ?? ''),
                'discount_title' => trim($item['discount_title'] ?? 'Special Offer'),
                'terms'          => trim($item['terms'] ?? 'T&C Apply'),
                'link'           => trim($item['link'] ?? '/events'),
                'is_active'      => isset($item['is_active']) ? (bool)$item['is_active'] : true,
                'display_order'  => (int)($item['display_order'] ?? ($oIdx + 1)),
            ];
        }

        $currentCms['offers'] = [
            'section_title' => trim($offersInput['section_title'] ?? 'Save More with Offers'),
            'is_active'     => isset($offersInput['is_active']) ? (bool)$offersInput['is_active'] : true,
            'items'         => $items,
        ];

        JsonStorageService::write('cms.json', $currentCms);

        return redirect()->route('admin.offers')->with('success', 'Offers section updated successfully!');
    }

    /**
     * Save Promise Banner.
     */
    public function savePromiseBanner(Request $request)
    {
        $request->validate([
            'promise_banner'            => ['nullable', 'array'],
            'promise_banner.image_file' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'promise_banner.title'      => ['nullable', 'string', 'max:255'],
            'promise_banner.link'       => ['nullable', 'string', 'max:255'],
        ]);

        $currentCms = JsonStorageService::read('cms.json', []);
        $pbInput = $request->input('promise_banner', []);
        $pbImage = $pbInput['image'] ?? ($currentCms['promise_banner']['image'] ?? '/images/banners/artizen-promise-banner.webp');

        if ($request->hasFile('promise_banner.image_file')) {
            try {
                $pbImage = app(ImageOptimizationService::class)->optimize(
                    file: $request->file('promise_banner.image_file'),
                    targetSubdir: 'uploads',
                    profile: 'hero_slide',
                    prefix: 'promise_banner',
                    quality: ImageOptimizationService::DEFAULT_QUALITY
                );
            } catch (\Throwable $e) {
                Log::warning("Promise banner optimization failed: " . $e->getMessage());
            }
        }

        $currentCms['promise_banner'] = [
            'image'     => $pbImage,
            'title'     => trim($pbInput['title'] ?? 'Artizen Promise'),
            'link'      => trim($pbInput['link'] ?? '/events'),
            'is_active' => isset($pbInput['is_active']) ? (bool)$pbInput['is_active'] : true,
        ];

        JsonStorageService::write('cms.json', $currentCms);

        return redirect()->route('admin.promise-banner')->with('success', 'Promise banner updated successfully!');
    }

    /**
     * Save dynamic CMS settings (backward-compatible master handler).
     */
    public function saveCms(Request $request)
    {
        $request->validate([
            'hero_slides'                      => ['nullable', 'array'],
            'hero_slides.*.id'                 => ['nullable'],
            'hero_slides.*.image_file'         => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'hero_slides.*.badge'              => ['nullable', 'string', 'max:100'],
            'hero_slides.*.title'              => ['nullable', 'string', 'max:255'],
            'hero_slides.*.desc'               => ['nullable', 'string'],
            'hero_slides.*.btn1Text'           => ['nullable', 'string', 'max:100'],
            'hero_slides.*.link1'              => ['nullable', 'string', 'max:255'],
            'hero_slides.*.btn2Text'           => ['nullable', 'string', 'max:100'],
            'hero_slides.*.link2'              => ['nullable', 'string', 'max:255'],
            'offers'                           => ['nullable', 'array'],
            'offers.section_title'             => ['nullable', 'string', 'max:150'],
            'offers.items'                     => ['nullable', 'array'],
            'offers.items.*.category_name'     => ['nullable', 'string', 'max:150'],
            'offers.items.*.discount_title'    => ['nullable', 'string', 'max:100'],
            'offers.items.*.subheading'        => ['nullable', 'string', 'max:150'],
            'offers.items.*.terms'             => ['nullable', 'string', 'max:255'],
            'offers.items.*.link'              => ['nullable', 'string', 'max:255'],
            'promise_banner'                   => ['nullable', 'array'],
            'promise_banner.image_file'        => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'promise_banner.title'             => ['nullable', 'string', 'max:255'],
            'promise_banner.link'              => ['nullable', 'string', 'max:255'],
        ]);

        $currentCms = JsonStorageService::read('cms.json', []);

        // 1. Process Hero Slides if provided in request
        if ($request->has('hero_slides')) {
            $heroSlidesInput = $request->input('hero_slides', []);
            $heroSlides = [];

            foreach ($heroSlidesInput as $idx => $slideData) {
                if ($request->hasFile("hero_slides.{$idx}.image_file")) {
                    try {
                        $slideData['image'] = app(ImageOptimizationService::class)->optimize(
                            file: $request->file("hero_slides.{$idx}.image_file"),
                            targetSubdir: 'uploads',
                            profile: 'hero_slide',
                            prefix: 'hero_' . $idx,
                            quality: ImageOptimizationService::DEFAULT_QUALITY
                        );
                    } catch (\Throwable $e) {
                        Log::warning("Hero slide {$idx} optimization failed: " . $e->getMessage());
                        $slideData['image'] = $slideData['image'] ?? ($currentCms['hero_slides'][$idx]['image'] ?? '/assets/images/hero/1.jpg');
                    }
                } else {
                    $slideData['image'] = $slideData['image'] ?? ($currentCms['hero_slides'][$idx]['image'] ?? '/assets/images/hero/1.jpg');
                }
                unset($slideData['image_file']);
                $slideData['id'] = !empty($slideData['id']) ? (int)$slideData['id'] : null;
                $slideData['is_active'] = isset($slideData['is_active']) ? (bool)$slideData['is_active'] : true;
                $slideData['display_order'] = (int)($slideData['display_order'] ?? $idx);
                $heroSlides[] = $slideData;
            }
            $currentCms['hero_slides'] = $heroSlides;
        }

        // 2. Process Offers if provided in request
        if ($request->has('offers')) {
            $offersInput = $request->input('offers', []);
            $items = [];
            foreach ($offersInput['items'] ?? [] as $oIdx => $item) {
                $items[] = [
                    'id'             => (int)($item['id'] ?? ($oIdx + 1)),
                    'category_name'  => trim($item['category_name'] ?? 'Celebration Offer'),
                    'icon'           => trim($item['icon'] ?? 'fa-solid fa-tag'),
                    'icon_color'     => trim($item['icon_color'] ?? 'text-amber-600'),
                    'subheading'     => trim($item['subheading'] ?? ''),
                    'discount_title' => trim($item['discount_title'] ?? 'Special Offer'),
                    'terms'          => trim($item['terms'] ?? 'T&C Apply'),
                    'link'           => trim($item['link'] ?? '/events'),
                    'is_active'      => isset($item['is_active']) ? (bool)$item['is_active'] : true,
                    'display_order'  => (int)($item['display_order'] ?? ($oIdx + 1)),
                ];
            }

            $currentCms['offers'] = [
                'section_title' => trim($offersInput['section_title'] ?? 'Save More with Offers'),
                'is_active'     => isset($offersInput['is_active']) ? (bool)$offersInput['is_active'] : true,
                'items'         => $items,
            ];
        }

        // 3. Process Promise Banner if provided in request
        if ($request->has('promise_banner')) {
            $pbInput = $request->input('promise_banner', []);
            $pbImage = $currentCms['promise_banner']['image'] ?? '/images/banners/artizen-promise-banner.webp';

            if ($request->hasFile('promise_banner.image_file')) {
                try {
                    $pbImage = app(ImageOptimizationService::class)->optimize(
                        file: $request->file('promise_banner.image_file'),
                        targetSubdir: 'uploads',
                        profile: 'hero_slide',
                        prefix: 'promise_banner',
                        quality: ImageOptimizationService::DEFAULT_QUALITY
                    );
                } catch (\Throwable $e) {
                    Log::warning("Promise banner optimization failed: " . $e->getMessage());
                }
            } elseif (!empty($pbInput['image'])) {
                $pbImage = $pbInput['image'];
            }

            $currentCms['promise_banner'] = [
                'image'     => $pbImage,
                'title'     => trim($pbInput['title'] ?? 'Artizen Promise'),
                'link'      => trim($pbInput['link'] ?? '/events'),
                'is_active' => isset($pbInput['is_active']) ? (bool)$pbInput['is_active'] : true,
            ];
        }

        JsonStorageService::write('cms.json', $currentCms);

        return redirect()->route('admin.hero')->with('success', 'CMS updated successfully!');
    }

    /**
     * Save dynamic Legal Policy Pages.
     */
    public function saveLegalPages(Request $request)
    {
        $defaultLegalPages = app(LegalController::class)->getDefaultLegalPages();

        $legalPages = [
            'privacy' => [
                'title' => $request->input('privacy_title', $defaultLegalPages['privacy']['title']),
                'subtitle' => $request->input('privacy_subtitle', $defaultLegalPages['privacy']['subtitle']),
                'content' => $request->input('privacy_content', $defaultLegalPages['privacy']['content']),
            ],
            'terms' => [
                'title' => $request->input('terms_title', $defaultLegalPages['terms']['title']),
                'subtitle' => $request->input('terms_subtitle', $defaultLegalPages['terms']['subtitle']),
                'content' => $request->input('terms_content', $defaultLegalPages['terms']['content']),
            ],
            'cancellation' => [
                'title' => $request->input('cancellation_title', $defaultLegalPages['cancellation']['title']),
                'subtitle' => $request->input('cancellation_subtitle', $defaultLegalPages['cancellation']['subtitle']),
                'content' => $request->input('cancellation_content', $defaultLegalPages['cancellation']['content']),
            ],
            'booking_policy' => [
                'title' => $request->input('booking_policy_title', $defaultLegalPages['booking_policy']['title']),
                'subtitle' => $request->input('booking_policy_subtitle', $defaultLegalPages['booking_policy']['subtitle']),
                'content' => $request->input('booking_policy_content', $defaultLegalPages['booking_policy']['content']),
            ],
        ];

        JsonStorageService::write('legal_pages.json', $legalPages);

        return redirect()->route('admin.legal')->with('success', 'Legal Policy pages updated successfully!');
    }
}
