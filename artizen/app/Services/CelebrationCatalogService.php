<?php

namespace App\Services;

class CelebrationCatalogService
{
    /**
     * Master Category Taxonomy - loaded dynamically from database categories
     */
    public static function getCategories(): array
    {
        try {
            $dbCats = \App\Models\Category::where('active', true)->orderBy('display_order')->get();
            if ($dbCats->isNotEmpty()) {
                $categories = [
                    'all' => [
                        'slug' => 'all',
                        'name' => 'All Celebrations',
                        'short' => 'All',
                        'icon' => 'fa-solid fa-sparkles',
                    ],
                ];
                foreach ($dbCats as $cat) {
                    $navSlug = $cat->nav_slug;
                    if (!isset($categories[$navSlug])) {
                        $categories[$navSlug] = [
                            'slug' => $navSlug,
                            'name' => $cat->title,
                            'short' => $cat->title,
                            'icon' => $cat->icon ?: 'fa-solid fa-cake-candles',
                        ];
                    }
                }
                return $categories;
            }
        } catch (\Throwable $e) {
            // Fallback below
        }

        return [
            'all' => [
                'slug' => 'all',
                'name' => 'All Celebrations',
                'short' => 'All',
                'icon' => 'fa-solid fa-sparkles',
            ],
            'cat-birthdays' => [
                'slug' => 'cat-birthdays',
                'name' => 'Birthdays',
                'short' => 'Birthdays',
                'icon' => 'fa-solid fa-cake-candles',
            ],
            'cat-kids-cozy' => [
                'slug' => 'cat-kids-cozy',
                'name' => 'Baby Shower & Kids',
                'short' => 'Kids & Baby',
                'icon' => 'fa-solid fa-baby-carriage',
            ],
            'cat-proposal-anniversary' => [
                'slug' => 'cat-proposal-anniversary',
                'name' => 'Proposals & Anniversaries',
                'short' => 'Proposals',
                'icon' => 'fa-solid fa-heart',
            ],
            'cat-weddings-sangeet' => [
                'slug' => 'cat-weddings-sangeet',
                'name' => 'Weddings & Sangeet',
                'short' => 'Weddings',
                'icon' => 'fa-solid fa-champagne-glasses',
            ],
            'cat-house-party' => [
                'slug' => 'cat-house-party',
                'name' => 'House Party',
                'short' => 'House Party',
                'icon' => 'fa-solid fa-compact-disc',
            ],
            'cat-dj-acoustic' => [
                'slug' => 'cat-dj-acoustic',
                'name' => 'DJ & Live Artists',
                'short' => 'Live Artists',
                'icon' => 'fa-solid fa-guitar',
            ],
            'cat-baby-corporate' => [
                'slug' => 'cat-baby-corporate',
                'name' => 'Corporate Events',
                'short' => 'Corporate',
                'icon' => 'fa-solid fa-briefcase',
            ],
        ];
    }

    /**
     * Complete Master Catalog - dynamically queries database packages table with relationships,
     * ensuring newly created admin packages immediately appear everywhere across the site.
     */
    public static function getAllPackages(): array
    {
        try {
            $dbPackages = \App\Models\Package::with([
                'category',
                'subcategories',
                'tiers' => function ($q) {
                    $q->where('active', true)->orderBy('display_order');
                }
            ])
            ->where('active', true)
            ->orderBy('id', 'asc')
            ->get();

            if ($dbPackages->isNotEmpty()) {
                $packages = [];
                foreach ($dbPackages as $p) {
                    $catNavSlug = $p->category?->nav_slug ?? ('cat-' . \Illuminate\Support\Str::slug($p->category?->title ?? 'celebrations'));
                    $catName = $p->category?->title ?? 'Celebrations';
                    $subcat = $p->subcategories->first()?->name ?? ($p->tag ?: 'Curated Setup');

                    $inclusions = [];
                    $tiersList = [];
                    foreach ($p->tiers as $t) {
                        $tInclusions = is_array($t->inclusions) ? $t->inclusions : [];
                        if (empty($inclusions) && !empty($tInclusions)) {
                            $inclusions = $tInclusions;
                        }
                        $tiersList[] = [
                            'id' => $t->id,
                            'name' => $t->name,
                            'badge' => $t->badge ?? null,
                            'price' => (float)$t->price,
                            'original_price' => (float)($p->original_price ?: round($t->price * 1.15)),
                            'desc' => $t->description ?: $p->description,
                            'image' => $t->image ?: $p->image,
                            'inclusions' => $tInclusions,
                        ];
                    }

                    if (empty($inclusions)) {
                        $inclusions = [
                            'Full theme backdrop & decoration setup',
                            'Ambient LED lighting & focus spots',
                            'Indore on-site delivery and coordination team'
                        ];
                    }

                    $gallery = is_array($p->gallery) ? $p->gallery : [];
                    if (empty($gallery) && $p->image) {
                        $gallery = [$p->image];
                    }

                    $tags = array_values(array_filter(array_unique(array_merge(
                        [$p->tag ?: 'decor'],
                        $p->subcategories->pluck('name')->toArray(),
                        explode(' ', strtolower($p->title))
                    ))));

                    $price = (float)$p->price;
                    $origPrice = (float)($p->original_price ?: round($price * 1.15));

                    $packages[] = [
                        'id' => 'pkg-' . $p->id,
                        'db_id' => $p->id,
                        'slug' => $p->slug,
                        'title' => $p->title,
                        'subcategory' => $subcat,
                        'category_id' => $catNavSlug,
                        'category_name' => $catName,
                        'price' => $price,
                        'original_price' => $origPrice,
                        'rating' => 4.9,
                        'reviews_count' => 65,
                        'image' => $p->image ?: asset('assets/images/hero/1.jpg'),
                        'gallery' => $gallery,
                        'desc' => $p->description ?? '',
                        'tags' => $tags,
                        'inclusions' => $inclusions,
                        'badge' => $p->badge ?: '15% OFF',
                        'recipients' => is_array($p->recipients) ? $p->recipients : (json_decode($p->recipients ?? '[]', true) ?? []),
                        'tiers' => $tiersList,
                    ];
                }
                return $packages;
            }
        } catch (\Throwable $e) {
            // Fallback to static catalog if DB is offline or migrating
        }

        return self::getStaticPackagesFallback();
    }

    /**
     * Fallback static catalog of 33 packages in case database is empty or unavailable
     */
    public static function getStaticPackagesFallback(): array
    {
        return [
            // ==========================================
            // BIRTHDAYS & KIDS CELEBRATIONS
            // ==========================================
            [
                'id' => 'pkg-101',
                'slug' => '1st-birthday-wonderland',
                'title' => '1st Birthday Wonderland',
                'subcategory' => '1st Birthday Specials',
                'category_id' => 'cat-birthdays',
                'category_name' => 'Birthdays',
                'price' => 4999,
                'original_price' => 5749,
                'rating' => 4.9,
                'reviews_count' => 84,
                'image' => asset('images/celebrations/1st-birthday-wonderland.webp'),
                'desc' => 'Magical pastel wonderland theme backdrop with themed props, balloon arch, and cake pedestal in Indore.',
                'tags' => ['birthday', '1st birthday', 'kids', 'balloons', 'pastel'],
                'inclusions' => [
                    'Pastel Wonderland Themed Backdrop',
                    'Organic Dual-Shade Balloon Garland',
                    'Themed Cake Cutting Pedestal',
                    'Indore On-Site Delivery & Setup'
                ]
            ],
            [
                'id' => 'pkg-102',
                'slug' => 'kids-jungle-safari',
                'title' => 'Kids Jungle Safari Theme',
                'subcategory' => 'Kids Theme Parties',
                'category_id' => 'cat-kids-cozy',
                'category_name' => 'Baby Shower & Kids',
                'price' => 5499,
                'original_price' => 6324,
                'rating' => 4.9,
                'reviews_count' => 62,
                'image' => asset('images/celebrations/kids-jungle-safari.webp'),
                'desc' => 'Vibrant safari animal cutouts, earthy balloon arches, leafy backdrops, and interactive party props.',
                'tags' => ['kids', 'safari', 'jungle', 'birthday', 'animals'],
                'inclusions' => [
                    '3D Jungle Safari Animal Cutouts',
                    'Tropical Leaf & Balloon Arch Rig',
                    'Custom Birthday Name Banner',
                    'Indore Setup & Pack-up Support'
                ]
            ],
            [
                'id' => 'pkg-103',
                'slug' => 'pastel-balloon-arch',
                'title' => 'Pastel Balloon Arch & Teddy Bear',
                'subcategory' => 'Baby Shower Decor',
                'category_id' => 'cat-kids-cozy',
                'category_name' => 'Baby Shower & Kids',
                'price' => 5499,
                'original_price' => 6324,
                'rating' => 4.9,
                'reviews_count' => 57,
                'image' => asset('images/celebrations/pastel-balloon-arch.webp'),
                'desc' => 'Gentle pastel tones balloon arch with life-size plush teddy bear, clouds, and golden stars.',
                'tags' => ['baby shower', 'teddy bear', 'pastel', 'newborn', 'mom to be'],
                'inclusions' => [
                    'Pastel Cloud Balloon Arch Wall',
                    'Life-Size Plush Teddy Bear Prop',
                    'Wooden Baby Block Letters Display',
                    'Warm Fairy Backlight Illumination'
                ]
            ],
            [
                'id' => 'pkg-104',
                'slug' => 'oh-baby-neon-throne',
                'title' => "'Oh Baby' Neon & Mom Throne",
                'subcategory' => 'Blessings & Reveals',
                'category_id' => 'cat-kids-cozy',
                'category_name' => 'Baby Shower & Kids',
                'price' => 6499,
                'original_price' => 7474,
                'rating' => 4.9,
                'reviews_count' => 71,
                'image' => asset('images/celebrations/oh-baby-neon-throne.webp'),
                'desc' => "Royal peacock chair or velvet mom throne with 'Oh Baby' warm neon signage and lush floral garland.",
                'tags' => ['baby shower', 'mom throne', 'neon', 'gender reveal', 'blessing'],
                'inclusions' => [
                    'Velvet Mom Throne / Peacock Chair',
                    "'Oh Baby' Warm Neon Sign",
                    'Lush Floral Ring Backdrop',
                    'Cake Table Stand & Pedestal'
                ]
            ],
            [
                'id' => 'pkg-105',
                'slug' => 'neon-sign-ring-arch',
                'title' => 'Neon Sign Ring Arch Setup',
                'subcategory' => 'Setup Themes',
                'category_id' => 'cat-birthdays',
                'category_name' => 'Birthdays',
                'price' => 4499,
                'original_price' => 5174,
                'rating' => 4.9,
                'reviews_count' => 93,
                'image' => asset('images/celebrations/neon-sign-ring-arch.webp'),
                'desc' => 'Chic metallic circular ring arch with custom neon lighting, organic dual-shade balloons and floral accents.',
                'tags' => ['neon', 'ring arch', 'birthday', 'adult birthday', 'modern'],
                'inclusions' => [
                    'Metallic Circular Ring Arch Structure',
                    'Custom Happy Birthday Neon Sign',
                    'Dual-Shade Organic Balloon Ring',
                    'Warm Spotlights Setup'
                ]
            ],
            [
                'id' => 'pkg-106',
                'slug' => 'sweet-16-club-bash',
                'title' => 'Sweet 16 & 18th Club Bash',
                'subcategory' => 'Youth Milestones',
                'category_id' => 'cat-birthdays',
                'category_name' => 'Birthdays',
                'price' => 6499,
                'original_price' => 7474,
                'rating' => 4.9,
                'reviews_count' => 49,
                'image' => asset('images/celebrations/sweet-16-club-bash.webp'),
                'desc' => 'Youth club party stage with vibrant neon lighting, disco strobes, and photo backdrop.',
                'tags' => ['sweet 16', '18th birthday', 'youth', 'party', 'disco'],
                'inclusions' => [
                    'Youth Club Neon Stage Backdrop',
                    'LED Moving Disco Strobe Lights',
                    'High-Bass Bluetooth Party Audio',
                    'Indore Venue Logistics'
                ]
            ],
            [
                'id' => 'pkg-107',
                'slug' => '21st-high-energy-party',
                'title' => '21st & 25th High-Energy Party',
                'subcategory' => 'Club Party',
                'category_id' => 'cat-birthdays',
                'category_name' => 'Birthdays',
                'price' => 6999,
                'original_price' => 8049,
                'rating' => 4.9,
                'reviews_count' => 68,
                'image' => asset('images/celebrations/21st-high-energy-party.webp'),
                'desc' => 'High-voltage neon stage setup with strobe effects, custom birthday sign and party props.',
                'tags' => ['21st birthday', '25th birthday', 'club party', 'night bash', 'neon'],
                'inclusions' => [
                    'High-Voltage Neon Stage Grid',
                    'Strobe & UV Glow Party Par Lights',
                    'VIP Cake Table Pedestal',
                    'Complete Indore Venue Installation'
                ]
            ],
            [
                'id' => 'pkg-108',
                'slug' => 'cozy-teepee-tent-village',
                'title' => 'Cozy Teepee Tent Village',
                'subcategory' => 'Kids Experiences',
                'category_id' => 'cat-kids-cozy',
                'category_name' => 'Baby Shower & Kids',
                'price' => 5999,
                'original_price' => 6899,
                'rating' => 4.9,
                'reviews_count' => 42,
                'image' => asset('images/celebrations/cozy-teepee-tent-village.webp'),
                'desc' => 'Miniature glamping teepee tents with fairy lights, floor cushions, and dreamcatcher styling.',
                'tags' => ['teepee', 'glamping', 'kids sleepover', 'picnic', 'cozy'],
                'inclusions' => [
                    '3 Glamping Canvas Teepee Tents',
                    'Cozy Floor Cushions & Boho Rugs',
                    'Warm Micro-Fairy Light Strings',
                    'Picnic Dining Table Styling'
                ]
            ],
            [
                'id' => 'pkg-109',
                'slug' => 'jubilee-celebration',
                'title' => '30th to 50th Jubilee Celebration',
                'subcategory' => 'Adult Milestones',
                'category_id' => 'cat-birthdays',
                'category_name' => 'Birthdays',
                'price' => 7499,
                'original_price' => 8624,
                'rating' => 4.9,
                'reviews_count' => 53,
                'image' => asset('images/celebrations/jubilee-celebration.webp'),
                'desc' => 'Sophisticated black & gold milestone backdrop, shimmer paneling, and warm amber spotlighting.',
                'tags' => ['30th birthday', '40th birthday', '50th birthday', 'gold', 'luxury'],
                'inclusions' => [
                    'Gold & Black Shimmer Sequin Wall',
                    'Custom Age Milestone Neon Monogram',
                    'Warm Amber Stage Up-lighting',
                    'Luxury Cylindrical Cake Plinth'
                ]
            ],
            [
                'id' => 'pkg-110',
                'slug' => 'princess-castle-fairy-tale',
                'title' => 'Princess Castle & Fairy Tale',
                'subcategory' => 'Fairy Tale & Fantasy',
                'category_id' => 'cat-kids-cozy',
                'category_name' => 'Baby Shower & Kids',
                'price' => 5299,
                'original_price' => 6094,
                'rating' => 4.9,
                'reviews_count' => 38,
                'image' => asset('images/celebrations/princess-castle-fairy-tale.webp'),
                'desc' => 'Royal fairytale castle backdrop with blush pink and lavender balloon styling, tiara props, and lights.',
                'tags' => ['princess', 'fairy tale', 'castle', 'girls birthday', 'pink'],
                'inclusions' => [
                    'Fairytale Castle Arch Backdrop',
                    'Blush Pink & Lilac Balloon Arches',
                    'Tiara Cake Cutting Table',
                    'Floor Warm Glow Spotlights'
                ]
            ],
            [
                'id' => 'pkg-111',
                'slug' => 'cake-table-styling',
                'title' => 'Cake Table & Photobooth Styling',
                'subcategory' => 'Photo Experiences',
                'category_id' => 'cat-birthdays',
                'category_name' => 'Birthdays',
                'price' => 3999,
                'original_price' => 4599,
                'rating' => 4.9,
                'reviews_count' => 89,
                'image' => asset('images/celebrations/cake-table-styling.webp'),
                'desc' => 'Designer cake cutting table arrangement with luxury pedestals, florals, and photo-ready backdrop.',
                'tags' => ['photobooth', 'cake table', 'budget', 'simple', 'aesthetic'],
                'inclusions' => [
                    'Tiered Fluted Cake Plinths',
                    'Artificial Floral Garland Runners',
                    'Fairy Ambient String Lights',
                    'Indore Setup & Removal'
                ]
            ],

            // ==========================================
            // PROPOSALS & ROMANTIC CELEBRATIONS
            // ==========================================
            [
                'id' => 'pkg-201',
                'slug' => 'marry-me-proposal',
                'title' => "The 'Marry Me' Proposal",
                'subcategory' => 'Romantic Proposals',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 8999,
                'original_price' => 10349,
                'rating' => 4.9,
                'reviews_count' => 112,
                'image' => asset('images/celebrations/marry-me-proposal.webp'),
                'desc' => "Grand marquee 'MARRY ME' giant LED letters, red carpet runner, fresh rose petals, and hurricane glass candles.",
                'tags' => ['proposal', 'marry me', 'romance', 'led letters', 'candlelight'],
                'inclusions' => [
                    "Giant 4ft Marquee 'MARRY ME' Letters",
                    'Fresh Red Rose Petal Pathway Trail',
                    '30+ Glass Candle Cylinders & Votives',
                    'Indore Private Coordination Support'
                ]
            ],
            [
                'id' => 'pkg-202',
                'slug' => 'silver-jubilee-arch',
                'title' => 'Silver Jubilee Elegance Arch',
                'subcategory' => 'Silver Jubilee (25th)',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 7499,
                'original_price' => 8624,
                'rating' => 4.9,
                'reviews_count' => 45,
                'image' => asset('images/celebrations/silver-jubilee-arch.webp'),
                'desc' => 'Silver chrome and white floral archway commemorating 25 golden years of togetherness.',
                'tags' => ['silver jubilee', '25th anniversary', 'parents', 'arch', 'silver'],
                'inclusions' => [
                    'Silver Chrome Double Ring Arch',
                    'White Orchid & Rose Floral Accents',
                    "'25 Golden Years' Neon Emblem",
                    'Ambient Floor Up-lighting'
                ]
            ],
            [
                'id' => 'pkg-203',
                'slug' => 'fairy-light-cabana',
                'title' => 'Fairy Light Romantic Cabana',
                'subcategory' => 'Date Nights & Dinners',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 5999,
                'original_price' => 6899,
                'rating' => 4.9,
                'reviews_count' => 78,
                'image' => asset('images/celebrations/fairy-light-cabana.webp'),
                'desc' => 'Private sheer canopy cabana adorned with 500+ micro fairy lights, bohemian low seating, and candlelight table.',
                'tags' => ['cabana', 'date night', 'candlelight dinner', 'fairy lights', 'terrace'],
                'inclusions' => [
                    'Sheer Draped 4-Pillar Bamboo Cabana',
                    '500+ Micro Warm Fairy Lights',
                    'Bohemian Low Seating Rugs & Cushions',
                    'Candlelit Dining Centerpiece Setup'
                ]
            ],
            [
                'id' => 'pkg-204',
                'slug' => 'candlelight-pathway',
                'title' => '100+ Candlelight Pathway Trail',
                'subcategory' => 'Candlelight Trails',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 4999,
                'original_price' => 5749,
                'rating' => 4.9,
                'reviews_count' => 64,
                'image' => asset('images/celebrations/candlelight-pathway.webp'),
                'desc' => 'Romantic candlelit entrance path leading to celebration area with crystal candle vases and rose petals.',
                'tags' => ['candlelight', 'pathway', 'surprise', 'rose petals', 'entrance'],
                'inclusions' => [
                    '100+ Glass Candle Cylinders & Vases',
                    'Fresh Red Rose Petal Trails',
                    'LED Flameless Tea Lights for Safety',
                    'Terrace or Garden Area Deployment'
                ]
            ],
            [
                'id' => 'pkg-205',
                'slug' => 'golden-jubilee-stage',
                'title' => 'Golden Jubilee Grand Stage',
                'subcategory' => 'Golden Jubilee (50th)',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 9499,
                'original_price' => 10924,
                'rating' => 4.9,
                'reviews_count' => 36,
                'image' => asset('images/celebrations/golden-jubilee-stage.webp'),
                'desc' => 'Grand celebratory stage with 24k gold leaf accents, imperial velvet backdrops, and floral urns.',
                'tags' => ['golden jubilee', '50th anniversary', 'grand stage', 'gold', 'parents'],
                'inclusions' => [
                    'Imperial Gold Leaf Stage Wall',
                    '50th Anniversary Neon Monogram',
                    'Twin Grand Brass Urns with Flowers',
                    'Stage Carpet & Warm Amber Lights'
                ]
            ],
            [
                'id' => 'pkg-206',
                'slug' => 'rose-petal-heart',
                'title' => 'Rose Petal Heart & Champagne Table',
                'subcategory' => 'Surprise Add-ons',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 4499,
                'original_price' => 5174,
                'rating' => 4.9,
                'reviews_count' => 88,
                'image' => asset('images/celebrations/rose-petal-heart.webp'),
                'desc' => 'Giant floor rose-petal heart with illuminated LED letters and styled champagne bucket table.',
                'tags' => ['heart', 'rose petals', 'champagne', 'anniversary', 'surprise'],
                'inclusions' => [
                    'Giant 6ft Floor Rose Petal Heart',
                    'Crystal Champagne Table Setting',
                    'Warm Fairy Curtain Backdrop',
                    'Bluetooth Music Audio Connection'
                ]
            ],
            [
                'id' => 'pkg-207',
                'slug' => 'rooftop-starlight-dinner',
                'title' => 'Private Rooftop Starlight Dinner',
                'subcategory' => 'Rooftop Venues',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 6999,
                'original_price' => 8049,
                'rating' => 4.9,
                'reviews_count' => 95,
                'image' => asset('images/celebrations/rooftop-starlight-dinner.webp'),
                'desc' => 'Indore rooftop transformation with overhead fairy canopy, elegant table setting, and city-view ambience.',
                'tags' => ['rooftop', 'dinner', 'starlight', 'date night', 'indore views'],
                'inclusions' => [
                    'Overhead Fairy Light Starlight Canopy',
                    'Candlelit 2-Seater Dining Table Setup',
                    'Rose Petals & Votive Jars Decor',
                    'Portable Background Sound Speaker'
                ]
            ],
            [
                'id' => 'pkg-208',
                'slug' => 'live-violinist-entry',
                'title' => 'Live Violinist Romantic Entry',
                'subcategory' => 'Live Artists',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 6499,
                'original_price' => 7474,
                'rating' => 4.9,
                'reviews_count' => 52,
                'image' => asset('images/celebrations/live-violinist-entry.webp'),
                'desc' => 'Trained professional solo violinist playing romantic Bollywood and western classical melodies live.',
                'tags' => ['violinist', 'live music', 'entry', 'romantic', 'artist'],
                'inclusions' => [
                    '1 Hour Live Violin Performance',
                    'Wireless Bodypack Audio Mic & Amp',
                    'Curated Romantic Bollywood Playlist',
                    'Formal Tuxedo / Dress Attire'
                ]
            ],
            [
                'id' => 'pkg-209',
                'slug' => 'floral-ring-memory-wall',
                'title' => 'Floral Ring & Memory Photo Wall',
                'subcategory' => 'Floral Ring & Memory Photo Wall',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 5499,
                'original_price' => 6324,
                'rating' => 4.9,
                'reviews_count' => 60,
                'image' => asset('images/celebrations/floral-ring-memory-wall.webp'),
                'desc' => 'Lush circular greenery and rose floral ring accompanied by 20+ hanging Polaroid photo memories.',
                'tags' => ['floral ring', 'polaroid', 'memory wall', 'anniversary', 'photos'],
                'inclusions' => [
                    'Circular Floral Ring Archway',
                    '20 High-Gloss Polaroids Printing & Clips',
                    'Fairy Light String Accents',
                    "Neon Sign 'Together Forever'"
                ]
            ],
            [
                'id' => 'pkg-210',
                'slug' => 'sunset-terrace-proposal',
                'title' => 'Secret Sunset Terrace Proposal',
                'subcategory' => 'Outdoor Sunset',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 7999,
                'original_price' => 9199,
                'rating' => 4.9,
                'reviews_count' => 41,
                'image' => asset('images/celebrations/sunset-terrace-proposal.webp'),
                'desc' => 'Golden hour sunset setup with rustic wooden arch, pampas grass, warm lantern jars, and couple sign.',
                'tags' => ['sunset', 'terrace', 'boho', 'pampas', 'golden hour'],
                'inclusions' => [
                    'Rustic Wooden Hexagon Arch',
                    'Pampas Grass & Bohemian Florals',
                    'Vintage Moroccan Glass Lanterns',
                    'Golden Hour Timing Coordination'
                ]
            ],
            [
                'id' => 'pkg-211',
                'slug' => 'parents-anniversary-arch',
                'title' => 'Parents Anniversary Special Arch',
                'subcategory' => 'Parents Anniversary',
                'category_id' => 'cat-proposal-anniversary',
                'category_name' => 'Proposals & Anniversaries',
                'price' => 6999,
                'original_price' => 8049,
                'rating' => 4.9,
                'reviews_count' => 73,
                'image' => asset('images/celebrations/parents-anniversary-arch.webp'),
                'desc' => 'Elegant floral arch with commemorative signage, family photo frame display, and gentle acoustic backdrop.',
                'tags' => ['parents', 'anniversary', 'white rose', 'family', 'traditional'],
                'inclusions' => [
                    'Luxury White Rose & Peony Floral Arch',
                    'Customized Family Name Board',
                    'Cake Cutting Pedestal Arrangement',
                    'Indore Free Delivery & Dismantling'
                ]
            ],

            // ==========================================
            // WEDDINGS, SANGEET & LIVE DJ PARTIES
            // ==========================================
            [
                'id' => 'pkg-301',
                'slug' => 'haldi-brass-urli',
                'title' => 'Haldi Brass Urli & Marigold Setup',
                'subcategory' => 'Pre-Wedding Events',
                'category_id' => 'cat-weddings-sangeet',
                'category_name' => 'Weddings & Sangeet',
                'price' => 8499,
                'original_price' => 9774,
                'rating' => 4.9,
                'reviews_count' => 104,
                'image' => asset('images/celebrations/haldi-brass-urli.webp'),
                'desc' => 'Traditional hammered brass Urli tub, cascading marigold garlands, yellow drape backdrops, and floor bolsters.',
                'tags' => ['haldi', 'brass urli', 'marigold', 'wedding', 'yellow'],
                'inclusions' => [
                    'Royal Hammered Brass Urli Tub & Stand',
                    'Fresh Genda / Marigold Cascading Curtains',
                    'Traditional Sunshine Yellow Drapes',
                    'Floor Sitting Carpets & Satin Bolsters'
                ]
            ],
            [
                'id' => 'pkg-302',
                'slug' => 'mehendi-colorful-drapes',
                'title' => 'Mehendi Colorful Drapes & Diwan',
                'subcategory' => 'Wedding Decor',
                'category_id' => 'cat-weddings-sangeet',
                'category_name' => 'Weddings & Sangeet',
                'price' => 7999,
                'original_price' => 9199,
                'rating' => 4.9,
                'reviews_count' => 66,
                'image' => asset('images/celebrations/mehendi-colorful-drapes.webp'),
                'desc' => 'Vibrant Rajasthani & Punjabi multi-color drape canopy with decorated bride Diwan, tassels, and umbrellas.',
                'tags' => ['mehendi', 'drapes', 'diwan', 'colorful', 'rajasthani'],
                'inclusions' => [
                    'Multi-Color Flowing Georgette Drapes',
                    'Carved Bride Mehendi Diwan Throne',
                    'Traditional Tassels, Windchimes & Umbrellas',
                    'Ambient Stage Fairy Lights Sync'
                ]
            ],
            [
                'id' => 'pkg-303',
                'slug' => 'rooftop-neon-dj-rig',
                'title' => 'Rooftop Neon House Party DJ Rig',
                'subcategory' => 'House Party Sound',
                'category_id' => 'cat-house-party',
                'category_name' => 'House Party',
                'price' => 6999,
                'original_price' => 8049,
                'rating' => 4.9,
                'reviews_count' => 87,
                'image' => asset('images/celebrations/rooftop-neon-dj-rig.webp'),
                'desc' => 'Complete rooftop party setup with high-decibel Bluetooth columns, neon party signs, laser lights, and disco par.',
                'tags' => ['dj', 'house party', 'rooftop', 'sound system', 'lasers'],
                'inclusions' => [
                    '2000W Active PA Sound Column Rig',
                    'Sound-Active Laser & Party Strobe Lights',
                    "Neon 'House Party' Sign",
                    '2 UHF Wireless Cordless Microphones'
                ]
            ],
            [
                'id' => 'pkg-304',
                'slug' => 'sangeet-dance-stage',
                'title' => 'Sangeet DJ Dance Floor & Club Pars',
                'subcategory' => 'Sangeet & Dance Stage',
                'category_id' => 'cat-weddings-sangeet',
                'category_name' => 'Weddings & Sangeet',
                'price' => 9999,
                'original_price' => 11499,
                'rating' => 4.9,
                'reviews_count' => 91,
                'image' => asset('images/celebrations/sangeet-dance-stage.webp'),
                'desc' => 'High-wattage sound system with DJ console, LED color par cans, dance floor lighting, and fog effects.',
                'tags' => ['sangeet', 'dance floor', 'dj sound', 'party', 'wedding'],
                'inclusions' => [
                    'Dual High-Bass PA Sound Columns',
                    'Sound-Active LED Par Stage Lights',
                    'Pro DJ Console Mixer Integration',
                    'Heavy Dense Fog Entrance Machine'
                ]
            ],
            [
                'id' => 'pkg-305',
                'slug' => 'live-acoustic-guitarist',
                'title' => 'Live Acoustic Singer & Guitarist',
                'subcategory' => 'Live Artists',
                'category_id' => 'cat-dj-acoustic',
                'category_name' => 'DJ & Live Artists',
                'price' => 7999,
                'original_price' => 9199,
                'rating' => 4.9,
                'reviews_count' => 58,
                'image' => asset('images/celebrations/live-acoustic-guitarist.webp'),
                'desc' => 'Versatile acoustic vocalist & guitarist performing Bollywood classics, English pop, and soulful melodies.',
                'tags' => ['guitarist', 'live singer', 'acoustic', 'sundowner', 'bollywood'],
                'inclusions' => [
                    '2 Hours Live Singing & Guitar Performance',
                    'Dedicated Acoustic Amp & Vocal Mic Rig',
                    'Personal Stage Audio Monitor',
                    'Custom Song Dedications & Requests'
                ]
            ],
            [
                'id' => 'pkg-306',
                'slug' => 'punjabi-dhol-players',
                'title' => 'Punjabi Dhol Players & Festive Entry',
                'subcategory' => 'Live Performances',
                'category_id' => 'cat-weddings-sangeet',
                'category_name' => 'Weddings & Sangeet',
                'price' => 5499,
                'original_price' => 6324,
                'rating' => 4.9,
                'reviews_count' => 115,
                'image' => asset('images/celebrations/punjabi-dhol-players.webp'),
                'desc' => 'Authentic Punjabi dhol masters in traditional attire bringing infectious high-energy beats for Baraat & Sangeet.',
                'tags' => ['dhol', 'punjabi dhol', 'baraat', 'entry', 'festive'],
                'inclusions' => [
                    '2 Professional Traditional Dhol Masters',
                    'Authentic Kurta & Turban Attire',
                    '1.5 Hours High-Energy Continuous Beats',
                    'Indore City Travel & Logistics Included'
                ]
            ],
            [
                'id' => 'pkg-307',
                'slug' => 'high-bass-speakers-mics',
                'title' => 'High-Bass Column Speakers & Mics',
                'subcategory' => 'Pro Audio Rigs',
                'category_id' => 'cat-house-party',
                'category_name' => 'House Party',
                'price' => 5999,
                'original_price' => 6899,
                'rating' => 4.9,
                'reviews_count' => 79,
                'image' => asset('images/celebrations/high-bass-speakers-mics.webp'),
                'desc' => 'Crisp concert-grade sound column system with dual wireless cordless mics and Bluetooth / Aux input.',
                'tags' => ['sound system', 'column speaker', 'bluetooth', 'mics', 'karaoke'],
                'inclusions' => [
                    'Dual Array Concert Sound Columns',
                    '2 UHF Wireless Cordless Microphones',
                    'Multi-Channel Audio Mixer Console',
                    'Indore Sound Engineer On-Site Setup'
                ]
            ],
            [
                'id' => 'pkg-308',
                'slug' => 'cold-pyro-sparklers',
                'title' => 'Cold Pyro Sparklers & Low-Fog Clouds',
                'subcategory' => 'Concert FX & Entry',
                'category_id' => 'cat-weddings-sangeet',
                'category_name' => 'Weddings & Sangeet',
                'price' => 4999,
                'original_price' => 5749,
                'rating' => 4.9,
                'reviews_count' => 97,
                'image' => asset('images/celebrations/cold-pyro-sparklers.webp'),
                'desc' => 'Indoor-safe zero-heat cold firework sparkler fountains and dense low-lying dry ice fog for couple entry.',
                'tags' => ['cold pyro', 'fog', 'dry ice', 'entry effect', 'sparklers'],
                'inclusions' => [
                    '4 Cold Pyro Electronic Fountain Guns (Indoor Safe)',
                    'Dry Ice Low-Lying Dense Fog Machine',
                    'Certified Artizen FX Safety Technician',
                    'Synchronized Grand Entry Trigger'
                ]
            ],
            [
                'id' => 'pkg-309',
                'slug' => 'sufi-bollywood-unplugged',
                'title' => 'Sufi & Bollywood Unplugged Sundowner',
                'subcategory' => 'Acoustic Sessions',
                'category_id' => 'cat-dj-acoustic',
                'category_name' => 'DJ & Live Artists',
                'price' => 8499,
                'original_price' => 9774,
                'rating' => 4.9,
                'reviews_count' => 63,
                'image' => asset('images/celebrations/sufi-bollywood-unplugged.webp'),
                'desc' => 'Atmospheric unplugged Sufi and indie-Bollywood musical evening with live percussion and strings.',
                'tags' => ['sufi', 'unplugged', 'sundowner', 'live band', 'bollywood'],
                'inclusions' => [
                    'Vocalist + Percussionist / Cajon Duo',
                    'Warm Vintage Lamp Stage Decor',
                    'Complete Stage Audio System & Mixer',
                    '2.5 Hours Immersive Performance'
                ]
            ],
            [
                'id' => 'pkg-310',
                'slug' => 'corporate-gala-stage',
                'title' => 'Corporate Gala & Milestone Stage',
                'subcategory' => 'Corporate & Summits',
                'category_id' => 'cat-baby-corporate',
                'category_name' => 'Corporate Events',
                'price' => 9999,
                'original_price' => 11499,
                'rating' => 4.9,
                'reviews_count' => 46,
                'image' => asset('images/celebrations/corporate-gala-stage.webp'),
                'desc' => 'Executive corporate stage backdrop, podium with gooseneck mic, dual PA columns, and branded lighting.',
                'tags' => ['corporate', 'annual gala', 'stage backdrop', 'podium', 'conference'],
                'inclusions' => [
                    'Branded Corporate Stage Backdrop Wall',
                    'Executive Lectern / Podium with Gooseneck Mic',
                    'Dual High-Clarity Audio Columns & Console',
                    'Stage Spotlight Illumination Rig'
                ]
            ],
            [
                'id' => 'pkg-311',
                'slug' => 'farmhouse-poolside-bash',
                'title' => 'Farmhouse Poolside All-Night DJ Bash',
                'subcategory' => 'Poolside & Farmhouse',
                'category_id' => 'cat-house-party',
                'category_name' => 'House Party',
                'price' => 8999,
                'original_price' => 10349,
                'rating' => 4.9,
                'reviews_count' => 74,
                'image' => asset('images/celebrations/farmhouse-poolside-bash.webp'),
                'desc' => 'Outdoor waterproof sound rigs with UV blacklights, floating pool lights, and DJ station for farmhouse parties.',
                'tags' => ['farmhouse', 'pool party', 'all night', 'dj', 'glow'],
                'inclusions' => [
                    'High-Power Outdoor PA Sound Rig',
                    'UV Glow Blacklights & Floating Pool Spheres',
                    'Illuminated DJ Booth Stand',
                    'Indore Bypass & Farmhouse Delivery'
                ]
            ],
        ];
    }
}
