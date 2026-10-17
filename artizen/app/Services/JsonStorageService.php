<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Category;
use App\Models\CmsSlide;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Package;
use App\Models\PackageTier;
use App\Models\Reel;
use App\Models\Testimonial;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class JsonStorageService
{
    /**
     * Safely read data from active storage driver (database or json).
     */
    public static function read(string $filename, array $default = []): array
    {
        $driver = config('artizen.storage_driver', 'database');

        if ($driver === 'database') {
            try {
                $basename = basename($filename);
                return match($basename) {
                    'categories.json' => self::readCategoriesFromDb($default),
                    'packages.json' => self::readPackagesFromDb($default),
                    'bookings.json' => self::readBookingsFromDb($default),
                    'faqs.json' => self::readFaqsFromDb($default),
                    'testimonials.json' => self::readTestimonialsFromDb($default),
                    'gallery.json' => self::readGalleryFromDb($default),
                    'enquiries.json' => self::readEnquiriesFromDb($default),
                    'cms.json' => self::readCmsFromDb($default),
                    'settings.json' => self::readSettingsFromDb($default),
                    'reels.json' => self::readReelsFromDb($default),
                    default => self::readJsonFile($filename, $default)
                };
            } catch (\Throwable $e) {
                // Safe fallback to JSON file if DB is unavailable
                return self::readJsonFile($filename, $default);
            }
        }

        return self::readJsonFile($filename, $default);
    }

    /**
     * Safely write data to active storage driver (database or json).
     */
    public static function write(string $filename, array $data): bool
    {
        // Always write to JSON file to keep fallback synchronized
        $jsonWritten = self::writeJsonFile($filename, $data);

        $driver = config('artizen.storage_driver', 'database');
        if ($driver === 'database') {
            try {
                $basename = basename($filename);
                match($basename) {
                    'categories.json' => self::writeCategoriesToDb($data),
                    'packages.json' => self::writePackagesToDb($data),
                    'bookings.json' => self::writeBookingsToDb($data),
                    'faqs.json' => self::writeFaqsToDb($data),
                    'testimonials.json' => self::writeTestimonialsToDb($data),
                    'gallery.json' => self::writeGalleryToDb($data),
                    'enquiries.json' => self::writeEnquiriesToDb($data),
                    'cms.json' => self::writeCmsToDb($data),
                    'settings.json' => self::writeSettingsToDb($data),
                    'reels.json' => self::writeReelsToDb($data),
                    default => null
                };
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Database sync failed for {$filename}: " . $e->getMessage(), [
                    'filename' => $filename,
                    'error_code' => $e->getCode(),
                    'exception' => get_class($e),
                ]);
                return false;
            }
        }

        return $jsonWritten;
    }

    /* =========================================================================
     * DATABASE READ IMPLEMENTATIONS
     * ========================================================================= */

    protected static function readCategoriesFromDb(array $default): array
    {
        $cats = Category::orderBy('id')->get();
        if ($cats->isEmpty()) return self::readJsonFile('categories.json', $default);

        return $cats->map(function($c) {
            return [
                'id' => $c->id,
                'title' => $c->title,
                'count' => $c->count,
                'image' => $c->image,
                'active' => (bool)$c->active
            ];
        })->toArray();
    }

    protected static function readPackagesFromDb(array $default): array
    {
        $packages = Package::with(['tiers', 'category'])->get();
        if ($packages->isEmpty()) return self::readJsonFile('packages.json', $default);

        $result = [];
        foreach ($packages as $p) {
            $rawId = (string)($p->id);
            $tiers = $p->tiers->map(function($t) {
                return [
                    'name' => $t->name,
                    'price' => (int)$t->price,
                    'desc' => $t->description,
                    'image' => $t->image,
                    'inclusions' => $t->inclusions ?? []
                ];
            })->toArray();

            $result[$rawId] = [
                'id' => $p->id,
                'title' => $p->title,
                'desc' => $p->description,
                'image' => $p->image,
                'tag' => $p->tag ?? 'decor',
                'active' => (bool)$p->active,
                'gallery' => $p->gallery ?? [],
                'tiers' => $tiers
            ];
        }

        return $result;
    }

    protected static function readBookingsFromDb(array $default): array
    {
        $bookings = Booking::with('customer')->orderBy('created_at', 'desc')->get();
        if ($bookings->isEmpty()) return self::readJsonFile('bookings.json', $default);

        return $bookings->map(function($b) {
            $customer = $b->customer;
            $name = $b->customer_name ?: ($customer?->name ?? 'Guest Customer');
            $mobile = $b->customer_mobile ?: ($customer?->mobile ?? '');
            $whatsapp = $b->customer_whatsapp ?: ($customer?->whatsapp ?? $mobile);
            $email = $b->customer_email ?: ($customer?->email ?? '');

            return [
                'id' => $b->id,
                'user_id' => $b->user_id,
                'customer_id' => $b->customer_id,
                'package_id' => $b->package_id,
                'name' => $name,
                'mobile' => $mobile,
                'whatsapp' => $whatsapp,
                'email' => $email,
                'customer_name' => $name,
                'customer_mobile' => $mobile,
                'customer_whatsapp' => $whatsapp,
                'customer_email' => $email,
                'customer_profile' => $customer ? [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'mobile' => $customer->mobile,
                    'whatsapp' => $customer->whatsapp,
                    'email' => $customer->email,
                    'address' => $customer->address,
                    'area' => $customer->area,
                    'city' => $customer->city,
                ] : null,
                'package' => $b->package_name,
                'category' => $b->category_name,
                'tier' => $b->tier_name,
                'date' => $b->event_date,
                'time' => $b->event_time,
                'guest_count' => $b->guest_count,
                'address' => $b->address,
                'landmark' => $b->landmark,
                'area' => $b->area,
                'city' => $b->city,
                'state' => $b->state,
                'pincode' => $b->pincode,
                'notes' => $b->notes,
                'addons' => $b->addons ?? [],
                'package_price' => (int)$b->package_price,
                'surcharge' => (int)$b->surcharge_amount,
                'total' => (int)$b->total_amount,
                'status' => $b->status,
                'notifications_sent' => $b->notifications_sent ?? [],
                'created_at' => $b->created_at ? $b->created_at->format('d M Y, h:i A') : date('d M Y')
            ];
        })->toArray();
    }

    protected static function readFaqsFromDb(array $default): array
    {
        $faqs = Faq::ordered()->get();
        if ($faqs->isEmpty()) return self::readJsonFile('faqs.json', $default);

        return $faqs->map(function($f) {
            return [
                'id'               => $f->id,
                'q'                => $f->question,
                'question'         => $f->question,
                'a'                => $f->answer,
                'answer'           => $f->answer,
                'category'         => $f->category,
                'active'           => (bool)$f->is_active,
                'is_active'        => (bool)$f->is_active,
                'show_on_home'     => (bool)$f->show_on_homepage,
                'show_on_homepage' => (bool)$f->show_on_homepage,
                'sort_order'       => (int)$f->sort_order,
                'display_order'    => (int)$f->sort_order,
            ];
        })->toArray();
    }

    protected static function readTestimonialsFromDb(array $default): array
    {
        $items = Testimonial::ordered()->get();
        if ($items->isEmpty()) return self::readJsonFile('testimonials.json', $default);

        return $items->map(function($t) {
            return [
                'id'                    => $t->id,
                'author'                => $t->author,
                'name'                  => $t->author,
                'email'                 => $t->email,
                'location'              => $t->location,
                'event_type'            => $t->event_type,
                'package_slug'          => $t->package_slug,
                'title'                 => $t->title,
                'review'                => $t->review,
                'content'               => $t->review,
                'rating'                => (int)$t->rating,
                'avatar'                => $t->avatar,
                'is_verified'           => (bool)$t->is_verified,
                'active'                => (bool)$t->is_active,
                'is_active'             => (bool)$t->is_active,
                'show_on_home'          => (bool)$t->show_on_home,
                'show_on_reviews_page'  => (bool)$t->show_on_reviews_page,
                'show_on_event_details' => (bool)$t->show_on_event_details,
                'sort_order'            => (int)$t->sort_order,
                'created_at'            => $t->created_at ? $t->created_at->format('d M Y') : date('d M Y'),
            ];
        })->toArray();
    }

    protected static function readGalleryFromDb(array $default): array
    {
        $items = GalleryItem::get();
        if ($items->isEmpty()) return self::readJsonFile('gallery.json', $default);

        return $items->map(function($g) {
            return [
                'id' => $g->id,
                'title' => $g->title,
                'category' => $g->category,
                'image' => $g->image,
                'active' => (bool)$g->is_active
            ];
        })->toArray();
    }

    protected static function readEnquiriesFromDb(array $default): array
    {
        $items = Enquiry::orderBy('created_at', 'desc')->get();
        if ($items->isEmpty()) return self::readJsonFile('enquiries.json', $default);

        return $items->map(function($e) {
            return [
                'id' => $e->id,
                'name' => $e->name,
                'phone' => $e->phone,
                'email' => $e->email,
                'subject' => $e->subject,
                'message' => $e->message,
                'status' => $e->status,
                'created_at' => $e->created_at ? $e->created_at->format('d M Y, h:i A') : date('d M Y')
            ];
        })->toArray();
    }

    protected static function readCmsFromDb(array $default): array
    {
        $cmsData = self::readJsonFile('cms.json', $default);

        // 1. Hero Slides from MySQL
        try {
            $slides = CmsSlide::orderBy('display_order')->get();
            if ($slides->isNotEmpty()) {
                $heroSlides = $slides->map(function($s) {
                    return [
                        'id' => $s->id,
                        'image' => $s->image,
                        'title' => $s->title,
                        'desc' => $s->description,
                        'link1' => $s->button1_link,
                        'btn1Text' => $s->button1_text,
                        'link2' => $s->button2_link,
                        'btn2Text' => $s->button2_text,
                        'display_order' => $s->display_order,
                        'is_active' => (bool)$s->is_active,
                    ];
                })->toArray();

                $cmsData['hero_slides'] = $heroSlides;
            } elseif (!isset($cmsData['hero_slides'])) {
                $cmsData['hero_slides'] = $default['hero_slides'] ?? [];
            }
        } catch (\Throwable $e) {
            // Safe fallback to JSON
        }

        // 2. Offers Section from MySQL
        try {
            $offerSetting = \App\Models\CmsOfferSetting::first();
            $offers = \App\Models\CmsOffer::orderBy('display_order')->get();
            if ($offers->isNotEmpty() || $offerSetting) {
                $cmsData['offers'] = [
                    'section_title' => $offerSetting?->section_title ?? ($cmsData['offers']['section_title'] ?? 'Save More with Offers'),
                    'is_active'     => $offerSetting ? (bool)$offerSetting->is_active : ($cmsData['offers']['is_active'] ?? true),
                    'items'         => $offers->map(function($o) {
                        return [
                            'id'             => $o->id,
                            'category_name'  => $o->category_name,
                            'icon'           => $o->icon,
                            'icon_color'     => $o->icon_color,
                            'subheading'     => $o->subheading,
                            'discount_title' => $o->discount_title,
                            'terms'          => $o->terms,
                            'link'           => $o->link,
                            'display_order'  => (int)$o->display_order,
                            'is_active'      => (bool)$o->is_active,
                        ];
                    })->toArray(),
                ];
            }
        } catch (\Throwable $e) {
            // Safe fallback to JSON
        }

        // 3. Promise Banner from MySQL
        try {
            $pb = \App\Models\CmsPromiseBanner::first();
            if ($pb) {
                $cmsData['promise_banner'] = [
                    'image'     => $pb->image,
                    'title'     => $pb->title,
                    'link'      => $pb->link,
                    'is_active' => (bool)$pb->is_active,
                ];
            }
        } catch (\Throwable $e) {
            // Safe fallback to JSON
        }

        return $cmsData;
    }

    protected static function readSettingsFromDb(array $default): array
    {
        return self::readJsonFile('settings.json', $default);
    }

    protected static function readReelsFromDb(array $default): array
    {
        $items = Reel::orderBy('display_order', 'asc')->orderBy('id', 'asc')->get();
        if ($items->isEmpty()) return self::readJsonFile('reels.json', $default);

        return $items->map(function($r) {
            return [
                'id' => $r->id,
                'category' => $r->category,
                'badge' => $r->badge,
                'badgeIcon' => $r->badge_icon ?? 'fa-fire',
                'views' => $r->views ?? '1.0K',
                'title' => $r->title,
                'location' => $r->location ?? 'Indore',
                'duration' => $r->duration ?? '45s',
                'desc' => $r->description ?? '',
                'image' => $r->image,
                'video' => $r->video,
                'is_active' => (bool)$r->is_active,
                'display_order' => (int)$r->display_order,
            ];
        })->toArray();
    }

    /* =========================================================================
     * DATABASE WRITE IMPLEMENTATIONS
     * ========================================================================= */

    protected static function writeCategoriesToDb(array $data): void
    {
        foreach ($data as $c) {
            Category::updateOrCreate(
                ['title' => trim($c['title'] ?? 'Category')],
                [
                    'slug' => Str::slug($c['title'] ?? 'Category') . '-' . ($c['id'] ?? rand(1, 99)),
                    'count' => (int)($c['count'] ?? 0),
                    'image' => $c['image'] ?? null,
                    'active' => (bool)($c['active'] ?? true),
                ]
            );
        }
    }

    protected static function writePackagesToDb(array $data): void
    {
        foreach ($data as $rawId => $pData) {
            $title = $pData['title'] ?? 'Package ' . $rawId;
            $package = Package::updateOrCreate(
                ['title' => $title],
                [
                    'category_id' => is_numeric($rawId) ? (int)$rawId : null,
                    'slug' => Str::slug($title) . '-' . $rawId,
                    'description' => $pData['desc'] ?? null,
                    'image' => $pData['image'] ?? null,
                    'tag' => $pData['tag'] ?? 'decor',
                    'gallery' => $pData['gallery'] ?? [],
                    'active' => (bool)($pData['active'] ?? true),
                ]
            );

            foreach ($pData['tiers'] ?? [] as $idx => $t) {
                PackageTier::updateOrCreate(
                    [
                        'package_id' => $package->id,
                        'name' => $t['name'] ?? ($title . ' Tier ' . ($idx + 1)),
                    ],
                    [
                        'slug' => Str::slug($t['name'] ?? 'Tier') . '-' . ($idx + 1),
                        'price' => (float)($t['price'] ?? 0),
                        'description' => $t['desc'] ?? null,
                        'image' => $t['image'] ?? null,
                        'inclusions' => $t['inclusions'] ?? [],
                        'active' => true,
                        'display_order' => $idx,
                    ]
                );
            }
        }
    }

    protected static function writeBookingsToDb(array $data): void
    {
        foreach ($data as $b) {
            if (empty($b['id'])) continue;

            $phone = preg_replace('/[^0-9]/', '', $b['mobile'] ?? $b['whatsapp'] ?? '');
            if (empty($phone)) $phone = '9131668156';

            $customer = Customer::firstOrCreate(
                ['mobile' => $phone],
                [
                    'name' => trim($b['name'] ?? 'Guest Customer'),
                    'whatsapp' => $b['whatsapp'] ?? $phone,
                    'email' => $b['email'] ?? null,
                    'address' => $b['address'] ?? null,
                    'area' => $b['area'] ?? 'Vijay Nagar',
                    'city' => $b['city'] ?? 'Indore',
                    'state' => $b['state'] ?? 'Madhya Pradesh',
                    'pincode' => $b['pincode'] ?? '452010',
                ]
            );

            Booking::updateOrCreate(
                ['id' => $b['id']],
                [
                    'user_id' => $b['user_id'] ?? null,
                    'customer_id' => $customer->id,
                    'package_id' => $b['package_id'] ?? null,
                    'customer_name' => trim($b['name'] ?? 'Guest Customer'),
                    'customer_mobile' => $phone,
                    'customer_whatsapp' => $b['whatsapp'] ?? $phone,
                    'customer_email' => $b['email'] ?? null,
                    'package_name' => $b['package'] ?? 'Event Package',
                    'category_name' => $b['category'] ?? 'Event Setup',
                    'tier_name' => $b['tier'] ?? 'Standard',
                    'event_date' => $b['date'] ?? date('Y-m-d'),
                    'event_time' => $b['time'] ?? '18:00',
                    'guest_count' => (int)($b['guest_count'] ?? 25),
                    'address' => $b['address'] ?? 'Indore',
                    'landmark' => $b['landmark'] ?? null,
                    'area' => $b['area'] ?? 'Vijay Nagar',
                    'city' => $b['city'] ?? 'Indore',
                    'state' => $b['state'] ?? 'Madhya Pradesh',
                    'pincode' => $b['pincode'] ?? '452010',
                    'notes' => $b['notes'] ?? null,
                    'addons' => $b['addons'] ?? [],
                    'package_price' => (float)($b['package_price'] ?? 0),
                    'surcharge_amount' => (float)($b['surcharge'] ?? 0),
                    'total_amount' => (float)($b['total'] ?? $b['package_price'] ?? 0),
                    'status' => $b['status'] ?? 'Pending',
                    'notifications_sent' => $b['notifications_sent'] ?? [],
                ]
            );
        }
    }

    protected static function writeFaqsToDb(array $data): void
    {
        $existingIds = [];
        foreach ($data as $idx => $f) {
            $cat = !empty($f['category']) ? strtolower(trim($f['category'])) : 'general';
            $showOnHome = isset($f['show_on_homepage']) ? (bool)$f['show_on_homepage'] : (bool)($f['show_on_home'] ?? false);
            $isActive = isset($f['is_active']) ? (bool)$f['is_active'] : (bool)($f['active'] ?? true);
            $sortOrder = (int)($f['sort_order'] ?? $f['display_order'] ?? ($idx + 1));

            $payload = [
                'question'         => trim($f['q'] ?? $f['question'] ?? 'Question'),
                'answer'           => trim($f['a'] ?? $f['answer'] ?? 'Answer'),
                'category'         => $cat,
                'is_active'        => $isActive,
                'show_on_homepage' => $showOnHome,
                'sort_order'       => $sortOrder,
            ];

            if (!empty($f['id'])) {
                $faq = Faq::find($f['id']);
                if ($faq) {
                    $faq->update($payload);
                    $existingIds[] = $faq->id;
                } else {
                    $payload['id'] = $f['id'];
                    $newFaq = Faq::create($payload);
                    $existingIds[] = $newFaq->id;
                }
            } else {
                $newFaq = Faq::create($payload);
                $existingIds[] = $newFaq->id;
            }
        }
        if (!empty($existingIds)) {
            Faq::whereNotIn('id', $existingIds)->delete();
        }
    }

    protected static function writeTestimonialsToDb(array $data): void
    {
        foreach ($data as $t) {
            if (empty($t['id'])) continue;
            Testimonial::updateOrCreate(
                ['id' => $t['id']],
                [
                    'author'                => trim($t['author'] ?? ($t['name'] ?? 'Customer')),
                    'email'                 => $t['email'] ?? null,
                    'location'              => $t['location'] ?? 'Indore, MP',
                    'event_type'            => $t['event_type'] ?? 'Celebration',
                    'package_slug'          => $t['package_slug'] ?? null,
                    'title'                 => $t['title'] ?? null,
                    'review'                => trim($t['review'] ?? ($t['content'] ?? '')),
                    'rating'                => (int)($t['rating'] ?? 5),
                    'avatar'                => $t['avatar'] ?? null,
                    'is_verified'           => isset($t['is_verified']) ? (bool)$t['is_verified'] : true,
                    'is_active'             => isset($t['is_active']) ? (bool)$t['is_active'] : (isset($t['active']) ? (bool)$t['active'] : true),
                    'show_on_home'          => isset($t['show_on_home']) ? (bool)$t['show_on_home'] : true,
                    'show_on_reviews_page'  => isset($t['show_on_reviews_page']) ? (bool)$t['show_on_reviews_page'] : true,
                    'show_on_event_details' => isset($t['show_on_event_details']) ? (bool)$t['show_on_event_details'] : true,
                    'sort_order'            => (int)($t['sort_order'] ?? 0),
                ]
            );
        }
    }

    protected static function writeGalleryToDb(array $data): void
    {
        foreach ($data as $g) {
            if (empty($g['id'])) continue;
            GalleryItem::updateOrCreate(
                ['id' => $g['id']],
                [
                    'title' => trim($g['title'] ?? 'Gallery Item'),
                    'category' => $g['category'] ?? 'Decor',
                    'image' => $g['image'] ?? '/assets/images/hero/1.jpg',
                    'is_active' => (bool)($g['active'] ?? true),
                ]
            );
        }
    }

    protected static function writeEnquiriesToDb(array $data): void
    {
        foreach ($data as $e) {
            if (empty($e['id'])) continue;
            Enquiry::updateOrCreate(
                ['id' => $e['id']],
                [
                    'name' => trim($e['name'] ?? 'Customer'),
                    'phone' => $e['phone'] ?? '9131668156',
                    'email' => $e['email'] ?? null,
                    'subject' => $e['subject'] ?? 'Inquiry',
                    'message' => $e['message'] ?? '',
                    'status' => $e['status'] ?? 'Unread',
                ]
            );
        }
    }

    protected static function writeCmsToDb(array $data): void
    {
        // 1. Hero Slides
        if (isset($data['hero_slides']) && is_array($data['hero_slides'])) {
            $processedIds = [];
            foreach ($data['hero_slides'] as $idx => $s) {
                $slideId = !empty($s['id']) ? (int)$s['id'] : null;
                $slideAttributes = [
                    'title' => trim($s['title'] ?? 'Slide'),
                    'description' => $s['desc'] ?? null,
                    'image' => $s['image'] ?? '/assets/images/hero/1.jpg',
                    'button1_text' => $s['btn1Text'] ?? null,
                    'button1_link' => $s['link1'] ?? null,
                    'button2_text' => $s['btn2Text'] ?? null,
                    'button2_link' => $s['link2'] ?? null,
                    'display_order' => (int)($s['display_order'] ?? $idx),
                    'is_active' => isset($s['is_active']) ? (bool)$s['is_active'] : true,
                ];

                if (!empty($slideId) && CmsSlide::where('id', $slideId)->exists()) {
                    $slide = CmsSlide::find($slideId);
                    $slide->update($slideAttributes);
                    $processedIds[] = $slide->id;
                } else {
                    $slide = CmsSlide::create($slideAttributes);
                    $processedIds[] = $slide->id;
                }
            }

            if (!empty($processedIds)) {
                CmsSlide::whereNotIn('id', $processedIds)->delete();
            }
        }

        // 2. Offers Section
        if (isset($data['offers']) && is_array($data['offers'])) {
            $offersData = $data['offers'];
            if (\Illuminate\Support\Facades\Schema::hasTable('cms_offer_settings')) {
                \App\Models\CmsOfferSetting::updateOrCreate(
                    ['id' => 1],
                    [
                        'section_title' => trim($offersData['section_title'] ?? 'Save More with Offers'),
                        'is_active'     => isset($offersData['is_active']) ? (bool)$offersData['is_active'] : true,
                    ]
                );
            }

            if (isset($offersData['items']) && is_array($offersData['items']) && \Illuminate\Support\Facades\Schema::hasTable('cms_offers')) {
                $processedOfferIds = [];
                foreach ($offersData['items'] as $idx => $item) {
                    $offerId = !empty($item['id']) ? (int)$item['id'] : null;
                    $offerAttributes = [
                        'category_name'  => trim($item['category_name'] ?? 'Celebration Offer'),
                        'icon'           => trim($item['icon'] ?? 'fa-solid fa-cake-candles'),
                        'icon_color'     => trim($item['icon_color'] ?? 'text-amber-600'),
                        'subheading'     => trim($item['subheading'] ?? ''),
                        'discount_title' => trim($item['discount_title'] ?? 'Special Offer'),
                        'terms'          => trim($item['terms'] ?? ''),
                        'link'           => trim($item['link'] ?? '/events'),
                        'display_order'  => (int)($item['display_order'] ?? $idx),
                        'is_active'      => isset($item['is_active']) ? (bool)$item['is_active'] : true,
                    ];

                    if (!empty($offerId) && \App\Models\CmsOffer::where('id', $offerId)->exists()) {
                        $offer = \App\Models\CmsOffer::find($offerId);
                        $offer->update($offerAttributes);
                        $processedOfferIds[] = $offer->id;
                    } else {
                        $offer = \App\Models\CmsOffer::create($offerAttributes);
                        $processedOfferIds[] = $offer->id;
                    }
                }

                if (!empty($processedOfferIds)) {
                    \App\Models\CmsOffer::whereNotIn('id', $processedOfferIds)->delete();
                }
            }
        }

        // 3. Promise Banner
        if (isset($data['promise_banner']) && is_array($data['promise_banner'])) {
            $pbData = $data['promise_banner'];
            if (\Illuminate\Support\Facades\Schema::hasTable('cms_promise_banners')) {
                \App\Models\CmsPromiseBanner::updateOrCreate(
                    ['id' => 1],
                    [
                        'image'     => $pbData['image'] ?? '/images/banners/artizen-promise-banner.webp',
                        'title'     => trim($pbData['title'] ?? 'Artizen Promise'),
                        'link'      => trim($pbData['link'] ?? '/events'),
                        'is_active' => isset($pbData['is_active']) ? (bool)$pbData['is_active'] : true,
                    ]
                );
            }
        }
    }

    protected static function writeSettingsToDb(array $data): void
    {
        self::writeJsonFile('settings.json', $data);
    }

    protected static function writeReelsToDb(array $data): void
    {
        foreach ($data as $r) {
            if (empty($r['id'])) continue;
            Reel::updateOrCreate(
                ['id' => $r['id']],
                [
                    'title' => trim($r['title'] ?? 'Reel'),
                    'category' => $r['category'] ?? 'birthday',
                    'badge' => $r['badge'] ?? null,
                    'badge_icon' => $r['badgeIcon'] ?? 'fa-fire',
                    'views' => $r['views'] ?? '1.0K',
                    'location' => $r['location'] ?? 'Indore',
                    'duration' => $r['duration'] ?? '45s',
                    'description' => $r['desc'] ?? null,
                    'image' => $r['image'] ?? '',
                    'video' => $r['video'] ?? '',
                    'is_active' => (bool)($r['is_active'] ?? true),
                    'display_order' => (int)($r['display_order'] ?? 0),
                ]
            );
        }
    }

    /* =========================================================================
     * JSON FILE READ/WRITE HELPERS
     * ========================================================================= */

    protected static function readJsonFile(string $filename, array $default = []): array
    {
        $path = storage_path('app/' . ltrim($filename, '/'));
        if (!File::exists($path)) {
            return $default;
        }

        $content = File::get($path);
        if (empty($content)) {
            return $default;
        }

        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : $default;
    }

    protected static function writeJsonFile(string $filename, array $data): bool
    {
        $path = storage_path('app/' . ltrim($filename, '/'));
        $dir = dirname($path);

        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($jsonContent === false) {
            return false;
        }

        $tempPath = $path . '.' . uniqid('tmp_', true);
        if (File::put($tempPath, $jsonContent) === false) {
            return false;
        }

        $success = @rename($tempPath, $path);
        if (!$success) {
            $success = File::put($path, $jsonContent) !== false;
            @unlink($tempPath);
        }

        return $success;
    }

    /**
     * Atomically mutate a JSON file under an exclusive file lock (flock).
     */
    public static function updateJsonFileAtomic(string $filename, callable $modifier): array
    {
        $path = storage_path('app/' . ltrim($filename, '/'));
        $lockFile = storage_path('app/.' . basename($filename) . '.lock');

        $lockDir = dirname($lockFile);
        if (!File::isDirectory($lockDir)) {
            File::makeDirectory($lockDir, 0755, true, true);
        }

        $fp = fopen($lockFile, 'c+');
        if (!$fp) {
            $currentData = self::readJsonFile($filename, []);
            $updatedData = $modifier($currentData);
            self::writeJsonFile($filename, $updatedData);
            return $updatedData;
        }

        try {
            flock($fp, LOCK_EX);

            $currentData = self::readJsonFile($filename, []);
            $updatedData = $modifier($currentData);
            self::writeJsonFile($filename, $updatedData);

            flock($fp, LOCK_UN);
            return $updatedData;
        } finally {
            fclose($fp);
        }
    }

    /**
     * Atomically append a single booking record to bookings.json under flock without DB full rewrite.
     */
    public static function appendBookingRecord(array $newBooking): bool
    {
        self::updateJsonFileAtomic('bookings.json', function(array $bookings) use ($newBooking) {
            $filtered = array_filter($bookings, fn($b) => ($b['id'] ?? '') !== ($newBooking['id'] ?? ''));
            $filtered[] = $newBooking;
            return array_values($filtered);
        });

        return true;
    }

    /**
     * Atomically update a single booking record in bookings.json under flock without touching unrelated bookings.
     */
    public static function updateBookingRecord(string $id, array $updates): ?array
    {
        $updatedTarget = null;
        self::updateJsonFileAtomic('bookings.json', function(array $bookings) use ($id, $updates, &$updatedTarget) {
            foreach ($bookings as &$b) {
                if (($b['id'] ?? '') === $id) {
                    $b = array_merge($b, $updates);
                    $updatedTarget = $b;
                    break;
                }
            }
            return $bookings;
        });

        return $updatedTarget;
    }

    /**
     * Atomically append a single enquiry record to enquiries.json under flock.
     */
    public static function appendEnquiryRecord(array $newEnquiry): bool
    {
        self::updateJsonFileAtomic('enquiries.json', function(array $enquiries) use ($newEnquiry) {
            $filtered = array_filter($enquiries, fn($e) => ($e['id'] ?? '') !== ($newEnquiry['id'] ?? ''));
            $filtered[] = $newEnquiry;
            return array_values($filtered);
        });

        return true;
    }

    /**
     * Atomically prepend a single review to testimonials.json under flock.
     */
    public static function prependTestimonialRecord(array $newReview): bool
    {
        self::updateJsonFileAtomic('testimonials.json', function(array $reviews) use ($newReview) {
            $filtered = array_filter($reviews, fn($r) => ($r['id'] ?? '') !== ($newReview['id'] ?? ''));
            array_unshift($filtered, $newReview);
            return array_values($filtered);
        });

        return true;
    }
}
