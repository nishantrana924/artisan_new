<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySubcategorySeeder extends Seeder
{
    /**
     * Seeds categories (with new image/icon fields) and their subcategories.
     * Safe to run multiple times — uses updateOrCreate.
     */
    public function run(): void
    {
        $categories = [
            [
                'id'             => 1,
                'title'          => 'Birthdays',
                'slug'           => 'birthdays-1',
                'nav_slug'       => 'cat-birthdays',
                'icon'           => 'fa-solid fa-cake-candles',
                'bg_color'       => '#F6CFB2',
                'slider_image'   => '/images/occasions/birthday.webp',
                'dropdown_image' => '/images/dropdowns/birthdays.webp',
                'dropdown_badge' => 'Popular Choice',
                'display_order'  => 1,
                'subs' => [
                    ['group' => 'Setup Themes',  'name' => 'Balloon Arch Backdrops',  'badge' => 'Popular'],
                    ['group' => 'Setup Themes',  'name' => 'Neon Sign Ring Arch',     'badge' => 'Trending'],
                    ['group' => 'Setup Themes',  'name' => 'Pastel Floral Theme',     'badge' => null],
                    ['group' => 'Setup Themes',  'name' => 'Club & Stage Lighting',   'badge' => null],
                    ['group' => 'Setup Themes',  'name' => 'Cake Table Styling',      'badge' => null],
                    ['group' => 'Setup Themes',  'name' => 'Custom Photobooths',      'badge' => null],
                    ['group' => 'By Milestones', 'name' => '1st Birthday Specials',  'badge' => 'Top'],
                    ['group' => 'By Milestones', 'name' => 'Sweet 16 & 18th Years',  'badge' => null],
                    ['group' => 'By Milestones', 'name' => '21st & 25th Club Party', 'badge' => null],
                    ['group' => 'By Milestones', 'name' => '30th to 50th Jubilee',   'badge' => null],
                    ['group' => 'By Milestones', 'name' => 'Birthday For Her',        'badge' => null],
                    ['group' => 'By Milestones', 'name' => 'Birthday For Him',        'badge' => null],
                    ['group' => 'Experiences',   'name' => 'Live DJ Sound Columns',   'badge' => null],
                    ['group' => 'Experiences',   'name' => 'DSLR Photography',        'badge' => null],
                    ['group' => 'Experiences',   'name' => 'Heavy Low Fog Effects',   'badge' => null],
                    ['group' => 'Experiences',   'name' => 'Party Anchor & Emcee',    'badge' => null],
                    ['group' => 'Experiences',   'name' => 'LED Disco Lights',        'badge' => null],
                ],
            ],
            [
                'id'             => 2,
                'title'          => 'House Party & DJ',
                'slug'           => 'house-party-dj-2',
                'nav_slug'       => 'cat-house-party',
                'icon'           => 'fa-solid fa-volume-high',
                'bg_color'       => '#F4D2B3',
                'slider_image'   => '/images/occasions/house-party.webp',
                'dropdown_image' => '/images/dropdowns/house-party.webp',
                'dropdown_badge' => 'Party Setup',
                'display_order'  => 2,
                'subs' => [
                    ['group' => 'Sound & DJ Rigs', 'name' => 'High-Bass Column Speakers', 'badge' => 'Popular'],
                    ['group' => 'Sound & DJ Rigs', 'name' => 'Live Mixing DJ Console',    'badge' => 'New'],
                    ['group' => 'Sound & DJ Rigs', 'name' => 'Active Dual Speakers',      'badge' => null],
                    ['group' => 'Sound & DJ Rigs', 'name' => 'Wireless Karaoke Mics',     'badge' => null],
                    ['group' => 'Sound & DJ Rigs', 'name' => 'Bluetooth Plug & Play',     'badge' => null],
                    ['group' => 'Sound & DJ Rigs', 'name' => 'Power Amplifiers',          'badge' => null],
                    ['group' => 'Party Lighting',  'name' => 'Sound-Active Strobes',      'badge' => null],
                    ['group' => 'Party Lighting',  'name' => 'Heavy Smoke Machines',      'badge' => null],
                    ['group' => 'Party Lighting',  'name' => 'Laser & Disco Pars',        'badge' => null],
                    ['group' => 'Party Lighting',  'name' => 'RGB Ambient Washes',        'badge' => null],
                    ['group' => 'Party Lighting',  'name' => 'Club Lighting Stands',      'badge' => null],
                    ['group' => 'Party Lighting',  'name' => 'Moving Beam Spots',         'badge' => null],
                    ['group' => 'Venues',          'name' => 'Rooftop & Terrace Bash',    'badge' => null],
                    ['group' => 'Venues',          'name' => 'Living Room & Flat',        'badge' => null],
                    ['group' => 'Venues',          'name' => 'Farmhouse & Poolside',      'badge' => null],
                    ['group' => 'Venues',          'name' => 'Basement Club Setup',       'badge' => null],
                    ['group' => 'Venues',          'name' => 'Lawn & Garden Rig',         'badge' => null],
                ],
            ],
            [
                'id'             => 3,
                'title'          => 'Proposals',
                'slug'           => 'proposals-3',
                'nav_slug'       => 'cat-proposal-anniversary',
                'icon'           => 'fa-solid fa-heart',
                'bg_color'       => '#F8C6CF',
                'slider_image'   => '/images/occasions/proposals.webp',
                'dropdown_image' => '/images/dropdowns/proposals.webp',
                'dropdown_badge' => 'Romantic Special',
                'display_order'  => 3,
                'subs' => [
                    ['group' => 'Proposal Setups', 'name' => '\'Marry Me\' Neon Arch',   'badge' => 'Top'],
                    ['group' => 'Proposal Setups', 'name' => 'Candlelight Pathway Trail', 'badge' => null],
                    ['group' => 'Proposal Setups', 'name' => 'Rose Petal Heart Carpet',  'badge' => null],
                    ['group' => 'Proposal Setups', 'name' => 'Fairy Light Cabana',        'badge' => null],
                    ['group' => 'Proposal Setups', 'name' => 'Floral Ring Cylinders',     'badge' => null],
                    ['group' => 'Proposal Setups', 'name' => 'Romantic Sunset Terrace',   'badge' => null],
                    ['group' => 'Proposal Venues', 'name' => 'Private Rooftop Terrace',   'badge' => null],
                    ['group' => 'Proposal Venues', 'name' => 'Hotel Balcony Decor',       'badge' => null],
                    ['group' => 'Proposal Venues', 'name' => 'Garden & Lawn Cabana',      'badge' => null],
                    ['group' => 'Proposal Venues', 'name' => 'Farmhouse Romantic Trail',  'badge' => null],
                    ['group' => 'Proposal Venues', 'name' => 'Living Room Surprise',      'badge' => null],
                    ['group' => 'Proposal Venues', 'name' => 'Secret Outdoor Sunset Spot','badge' => null],
                    ['group' => 'Surprise Add-ons','name' => 'Live Violinist Entry',      'badge' => 'New'],
                    ['group' => 'Surprise Add-ons','name' => 'Cold Pyro Fireworks',       'badge' => null],
                    ['group' => 'Surprise Add-ons','name' => 'Surprise DSLR Shoot',       'badge' => null],
                    ['group' => 'Surprise Add-ons','name' => 'Champagne & Cake Table',    'badge' => null],
                    ['group' => 'Surprise Add-ons','name' => 'Giant Lighted Letters',     'badge' => null],
                ],
            ],
            [
                'id'             => 4,
                'title'          => 'Anniversaries',
                'slug'           => 'anniversaries-4',
                'nav_slug'       => 'cat-proposal-anniversary',
                'icon'           => 'fa-regular fa-gem',
                'bg_color'       => '#F6B6C1',
                'slider_image'   => '/images/occasions/anniversary.webp',
                'dropdown_image' => '/images/dropdowns/anniversaries.webp',
                'dropdown_badge' => 'Celebrate Love',
                'display_order'  => 4,
                'subs' => [
                    ['group' => 'Milestones',    'name' => '1st Paper Anniversary', 'badge' => 'Top'],
                    ['group' => 'Milestones',    'name' => '5th & 10th Milestones', 'badge' => null],
                    ['group' => 'Milestones',    'name' => '25th Silver Jubilee',   'badge' => 'Special'],
                    ['group' => 'Milestones',    'name' => '50th Golden Jubilee',   'badge' => null],
                    ['group' => 'Milestones',    'name' => 'Parents Anniversary',   'badge' => null],
                    ['group' => 'Milestones',    'name' => 'Grand Floral Backdrop', 'badge' => null],
                    ['group' => 'Romantic Decor','name' => 'Warm Fairy Lights',     'badge' => null],
                    ['group' => 'Romantic Decor','name' => 'Candlelight Dinner Table', 'badge' => null],
                    ['group' => 'Romantic Decor','name' => 'Floral Ring Backdrop',  'badge' => null],
                    ['group' => 'Romantic Decor','name' => 'Memory Photo Wall',     'badge' => null],
                    ['group' => 'Romantic Decor','name' => 'Neon Love Signs',       'badge' => null],
                    ['group' => 'Romantic Decor','name' => 'Red Carpet Walkway',    'badge' => null],
                    ['group' => 'Music & Sound', 'name' => 'Live Acoustic Guitarist','badge' => null],
                    ['group' => 'Music & Sound', 'name' => 'Bollywood Unplugged',   'badge' => null],
                    ['group' => 'Music & Sound', 'name' => 'Dual Column Speakers',  'badge' => null],
                    ['group' => 'Music & Sound', 'name' => 'Couple Photography',    'badge' => null],
                    ['group' => 'Music & Sound', 'name' => 'Montage Projector',     'badge' => null],
                ],
            ],
            [
                'id'             => 5,
                'title'          => 'Weddings & Sangeet',
                'slug'           => 'weddings-sangeet-5',
                'nav_slug'       => 'cat-weddings-sangeet',
                'icon'           => 'fa-solid fa-ring',
                'bg_color'       => '#FEE08B',
                'slider_image'   => '/images/occasions/weddings.webp',
                'dropdown_image' => '/images/dropdowns/weddings-sangeet.webp',
                'dropdown_badge' => 'Royal Setup',
                'display_order'  => 5,
                'subs' => [
                    ['group' => 'Pre-Wedding Events','name' => 'Haldi Brass Urli Setup',   'badge' => 'Top'],
                    ['group' => 'Pre-Wedding Events','name' => 'Mehendi Colorful Drapes',  'badge' => null],
                    ['group' => 'Pre-Wedding Events','name' => 'Sangeet DJ & Dance Stage', 'badge' => null],
                    ['group' => 'Pre-Wedding Events','name' => 'Cocktail Club Lighting',   'badge' => null],
                    ['group' => 'Pre-Wedding Events','name' => 'Ring Ceremony Stage',      'badge' => null],
                    ['group' => 'Pre-Wedding Events','name' => 'Grand Entrance Arch',      'badge' => null],
                    ['group' => 'Stage & Decor',     'name' => 'Marigold & Genda Decor',   'badge' => null],
                    ['group' => 'Stage & Decor',     'name' => 'Pastel Mandap Setups',     'badge' => null],
                    ['group' => 'Stage & Decor',     'name' => 'Bolsters & Diwan Seating', 'badge' => null],
                    ['group' => 'Stage & Decor',     'name' => 'LED Par Light Ambient Washes','badge' => null],
                    ['group' => 'Stage & Decor',     'name' => 'Brass Props & Hanging Bells','badge' => null],
                    ['group' => 'Stage & Decor',     'name' => 'Royal Photobooths',        'badge' => null],
                    ['group' => 'Music & FX',        'name' => 'High-Power DJ Rigs',       'badge' => 'Top'],
                    ['group' => 'Music & FX',        'name' => 'Punjabi Dhol Players',     'badge' => null],
                    ['group' => 'Music & FX',        'name' => 'LED Truss Stage Lighting', 'badge' => null],
                    ['group' => 'Music & FX',        'name' => 'Cold Pyro & Low-Fog Entry','badge' => null],
                    ['group' => 'Music & FX',        'name' => 'Audio Mixing Console Board','badge' => null],
                ],
            ],
            [
                'id'             => 6,
                'title'          => 'Baby Shower & Kids',
                'slug'           => 'baby-shower-kids-6',
                'nav_slug'       => 'cat-kids-cozy',
                'icon'           => 'fa-solid fa-child',
                'bg_color'       => '#C4E7D7',
                'slider_image'   => '/images/occasions/baby-shower.webp',
                'dropdown_image' => '/images/dropdowns/baby-shower-kids.webp',
                'dropdown_badge' => 'Cute Specials',
                'display_order'  => 6,
                'subs' => [
                    ['group' => 'Baby Shower',   'name' => 'Baby Shower Pastel Decor',    'badge' => 'Top'],
                    ['group' => 'Baby Shower',   'name' => 'Gender Reveal Balloon Box',   'badge' => 'New'],
                    ['group' => 'Baby Shower',   'name' => 'Blessings & Reveal Setups',   'badge' => null],
                    ['group' => 'Baby Shower',   'name' => 'Naming Ceremony Decor',       'badge' => null],
                    ['group' => 'Baby Shower',   'name' => 'Baby Welcome Fairy Lights',   'badge' => null],
                    ['group' => 'Baby Shower',   'name' => 'Crib & Cradle Setup',         'badge' => null],
                    ['group' => 'Kids Parties',  'name' => '1st Birthday Specials',       'badge' => 'Popular'],
                    ['group' => 'Kids Parties',  'name' => 'Cartoon Theme Backdrops',     'badge' => null],
                    ['group' => 'Kids Parties',  'name' => 'Cozy Teepee Tent Village',    'badge' => null],
                    ['group' => 'Kids Parties',  'name' => 'Princess & Superhero Themes', 'badge' => null],
                    ['group' => 'Kids Parties',  'name' => 'Candy Table Setup',           'badge' => null],
                    ['group' => 'Kids Parties',  'name' => 'Outdoor Play Decor',          'badge' => null],
                ],
            ],
            [
                'id'             => 7,
                'title'          => 'Corporate Events',
                'slug'           => 'corporate-events-7',
                'nav_slug'       => 'cat-baby-corporate',
                'icon'           => 'fa-solid fa-briefcase',
                'bg_color'       => '#A5C8E4',
                'slider_image'   => '/images/occasions/corporate.webp',
                'dropdown_image' => '/images/dropdowns/corporate-events.webp',
                'dropdown_badge' => 'Professional Setup',
                'display_order'  => 7,
                'subs' => [
                    ['group' => 'Corporate Setups',  'name' => 'Corporate Gala Stage',        'badge' => 'Top'],
                    ['group' => 'Corporate Setups',  'name' => 'Award Ceremony Lighting',     'badge' => null],
                    ['group' => 'Corporate Setups',  'name' => 'Brand Activation Backdrop',   'badge' => null],
                    ['group' => 'Corporate Setups',  'name' => 'Office Party Decor',          'badge' => 'New'],
                    ['group' => 'Corporate Setups',  'name' => 'Team Building Event Setup',   'badge' => null],
                    ['group' => 'Corporate Setups',  'name' => 'Conference & Summit Stage',   'badge' => null],
                    ['group' => 'AV & Sound',        'name' => 'Pro PA Speaker Systems',      'badge' => 'Popular'],
                    ['group' => 'AV & Sound',        'name' => 'Wireless Lavalier Mics',      'badge' => null],
                    ['group' => 'AV & Sound',        'name' => 'LED Video Wall Panels',       'badge' => null],
                    ['group' => 'AV & Sound',        'name' => 'Projector & Screen Setup',    'badge' => null],
                    ['group' => 'AV & Sound',        'name' => 'Stage Monitor Systems',       'badge' => null],
                    ['group' => 'Ambience',          'name' => 'Truss & Ambient Lighting',    'badge' => null],
                    ['group' => 'Ambience',          'name' => 'Corporate Photo Wall',        'badge' => null],
                    ['group' => 'Ambience',          'name' => 'Table & Chair Linen Setup',   'badge' => null],
                ],
            ],
            [
                'id'             => null, // New category — will be inserted
                'title'          => 'Live DJ & Acoustic',
                'slug'           => 'live-dj-acoustic',
                'nav_slug'       => 'cat-dj-acoustic',
                'icon'           => 'fa-solid fa-music',
                'bg_color'       => '#D8C9F3',
                'slider_image'   => '/images/occasions/live-music.webp',
                'dropdown_image' => '/images/dropdowns/live-dj-acoustic.webp',
                'dropdown_badge' => 'Live Entertainment',
                'display_order'  => 8,
                'subs' => [
                    ['group' => 'DJ Services',       'name' => 'High-Power DJ Rigs',         'badge' => 'Top'],
                    ['group' => 'DJ Services',       'name' => 'Rooftop Neon DJ Setup',      'badge' => 'New'],
                    ['group' => 'DJ Services',       'name' => 'Live Mixing DJ Console',     'badge' => null],
                    ['group' => 'DJ Services',       'name' => 'Sound-Active Strobes',       'badge' => null],
                    ['group' => 'DJ Services',       'name' => 'Heavy Smoke Machines',       'badge' => null],
                    ['group' => 'DJ Services',       'name' => 'LED Truss Stage Lighting',   'badge' => null],
                    ['group' => 'Live Artists',      'name' => 'Live Acoustic Guitarist',    'badge' => 'Popular'],
                    ['group' => 'Live Artists',      'name' => 'Bollywood Unplugged Singer', 'badge' => null],
                    ['group' => 'Live Artists',      'name' => 'Punjabi Dhol Players',       'badge' => null],
                    ['group' => 'Live Artists',      'name' => 'Live Violinist Entry',       'badge' => null],
                    ['group' => 'Sound Systems',     'name' => 'High-Bass Column Speakers',  'badge' => null],
                    ['group' => 'Sound Systems',     'name' => 'Dual Active Speaker Pair',   'badge' => null],
                    ['group' => 'Sound Systems',     'name' => 'Wireless Karaoke Mics',      'badge' => null],
                    ['group' => 'Sound Systems',     'name' => 'Audio Mixing Console Board', 'badge' => null],
                ],
            ],
        ];

        $order = 1;
        foreach ($categories as $catData) {
            $subs = $catData['subs'] ?? [];
            unset($catData['subs']);

            // Determine if this is an existing category by ID or title
            $category = null;
            if ($catData['id']) {
                $category = Category::find($catData['id']);
            }
            if (!$category) {
                $category = Category::where('title', $catData['title'])->first();
            }

            $payload = [
                'title'          => $catData['title'],
                'slug'           => $catData['slug'],
                'nav_slug'       => $catData['nav_slug'],
                'icon'           => $catData['icon'],
                'bg_color'       => $catData['bg_color'],
                'slider_image'   => $catData['slider_image'],
                'dropdown_image' => $catData['dropdown_image'],
                'dropdown_badge' => $catData['dropdown_badge'],
                'display_order'  => $catData['display_order'],
                'active'         => true,
            ];

            if ($category) {
                $category->update($payload);
            } else {
                $category = Category::create($payload);
            }

            // Seed subcategories
            $subOrder = 1;
            foreach ($subs as $sub) {
                Subcategory::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name'        => $sub['name'],
                    ],
                    [
                        'slug'          => Str::slug($sub['name']),
                        'group_name'    => $sub['group'],
                        'badge'         => $sub['badge'],
                        'display_order' => $subOrder++,
                        'is_active'     => true,
                    ]
                );
            }
        }

        $this->command->info('Categories & Subcategories seeded successfully!');
    }
}
