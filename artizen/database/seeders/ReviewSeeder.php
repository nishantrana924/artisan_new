<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Services\JsonStorageService;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $curatedReviews = [
            [
                'id'                    => 'TST-1001',
                'author'                => 'Priya & Kunal Sharma',
                'email'                 => 'priya.sharma@example.com',
                'location'              => 'Saket Nagar, Indore',
                'event_type'            => '1st Birthday Setup',
                'package_slug'          => '1st-birthday-wonderland',
                'review'                => 'Booked the 1st birthday wonderland theme for my son. The setup team arrived exactly 2 hours prior, assembled everything cleanly, and the photos turned out fabulous. Zero hassle with advance payments too — paid offline after everything was verified!',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 1,
            ],
            [
                'id'                    => 'TST-1002',
                'author'                => 'Rohit Verma',
                'email'                 => 'rohit.verma@example.com',
                'location'              => 'Vijay Nagar, Indore',
                'event_type'            => 'High-Bass Sound Rig',
                'package_slug'          => 'high-bass-speakers-mics',
                'review'                => 'Rented the active speaker column and strobe lasers for our terrace club party. The bass was punchy, mics worked flawlessly for karaoke, and the technician was courteous and helpful throughout the night. All guests loved it.',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 2,
            ],
            [
                'id'                    => 'TST-1003',
                'author'                => 'Ananya Kapoor',
                'email'                 => 'ananya.k@example.com',
                'location'              => 'New Palasia, Indore',
                'event_type'            => 'Proposal & Anniversary',
                'package_slug'          => 'marry-me-proposal',
                'review'                => 'The fairy lights, red roses, and "Marry Me" LED marquee letters created the most romantic proposal ambience. The coordinator was discreet, communicated on WhatsApp, and executed the surprise flawlessly on our private rooftop.',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 3,
            ],
            [
                'id'                    => 'TST-1004',
                'author'                => 'Deepak & Neha Jain',
                'email'                 => 'deepak.jain@example.com',
                'location'              => 'Bypass Road, Indore',
                'event_type'            => 'Haldi & Mehendi Urli',
                'package_slug'          => 'haldi-brass-urli',
                'review'                => 'We needed a traditional haldi ceremony setup for 80 guests. Fresh yellow & orange marigolds, decorated brass Urli with flower petals, and traditional floral seating were arranged in our lawn right on time. Highly recommended!',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 4,
            ],
            [
                'id'                    => 'TST-1005',
                'author'                => 'Megha Chawla',
                'email'                 => 'megha.c@example.com',
                'location'              => 'Nipania, Indore',
                'event_type'            => 'Baby Shower',
                'package_slug'          => 'fairy-light-cabana',
                'review'                => 'Super clean and aesthetically pleasing baby shower setup. The "Oh Baby" golden neon sign and pastel balloon garland looked straight out of Pinterest. Seamless experience with zero advance fee stress.',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 5,
            ],
            [
                'id'                    => 'TST-1006',
                'author'                => 'Vikramaditya Solanki',
                'email'                 => 'vikramaditya@example.com',
                'location'              => 'Super Corridor, Indore',
                'event_type'            => 'Corporate Gala',
                'package_slug'          => 'corporate-gala-stage',
                'review'                => 'Organized our IT company annual gala on Super Corridor. Artizen delivered stage lighting, PA system, and P3 LED backdrop with seamless technical support. Very punctual, professional, and transparent pricing.',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 6,
            ],
            [
                'id'                    => 'TST-1007',
                'author'                => 'Siddharth Agrawal',
                'email'                 => 'siddharth.a@example.com',
                'location'              => 'Scheme 54, Indore',
                'event_type'            => 'Neon Ring Arch Theme',
                'package_slug'          => 'neon-sign-ring-arch',
                'review'                => 'Amazing birthday surprise for my sister. The ring arch with personalized neon name lettering was executed to perfection. Punctual team and clean disassembly afterward.',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 7,
            ],
            [
                'id'                    => 'TST-1008',
                'author'                => 'Ritu & Harshwardhan',
                'email'                 => 'ritu.h@example.com',
                'location'              => 'Annapurna Road, Indore',
                'event_type'            => 'Rooftop Candlelight Dinner',
                'package_slug'          => 'rooftop-starlight-dinner',
                'review'                => 'Booked an anniversary surprise on our terrace. The cabana, warm fairy lights, and customized playlist setup made our evening magical. True 5-star service in Indore.',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 8,
            ],
            [
                'id'                    => 'TST-1009',
                'author'                => 'Amitabh Sen',
                'email'                 => 'amitabh.s@example.com',
                'location'              => 'Bhawarkua, Indore',
                'event_type'            => 'Sound & Karaoke Setup',
                'package_slug'          => 'high-bass-speakers-mics',
                'review'                => 'House party setup with twin sound towers and wireless mics. Flawless audio clarity, high bass, and hassle-free offline payment after the event.',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 9,
            ],
            [
                'id'                    => 'TST-179101026467',
                'author'                => 'Rohan Khandelwal',
                'email'                 => 'rohan.k@example.com',
                'location'              => 'Old Palasia, Indore',
                'event_type'            => '1st Birthday Wonderland',
                'package_slug'          => '1st-birthday-wonderland',
                'review'                => 'Booked the 1st birthday theme decoration. The team arrived on time, was polite, and set up everything smoothly. Paid offline after verifying everything.',
                'rating'                => 5,
                'avatar'                => null,
                'is_verified'           => true,
                'is_active'             => true,
                'show_on_home'          => true,
                'show_on_reviews_page'  => true,
                'show_on_event_details' => true,
                'sort_order'            => 10,
            ],
        ];

        // Seed/Update curated reviews
        foreach ($curatedReviews as $rev) {
            Testimonial::updateOrCreate(
                ['id' => $rev['id']],
                $rev
            );
        }

        // Also preserve any customer-submitted reviews in JSON file
        $stored = JsonStorageService::read('testimonials.json', []);
        $curatedIds = collect($curatedReviews)->pluck('id')->all();
        $order = 11;

        foreach ($stored as $s) {
            $id = $s['id'] ?? null;
            if (!$id || in_array($id, $curatedIds)) continue;

            $author = trim($s['author'] ?? ($s['name'] ?? ''));
            if (empty($author)) continue;

            Testimonial::updateOrCreate(
                ['id' => $id],
                [
                    'author'                => $author,
                    'email'                 => $s['email'] ?? null,
                    'location'              => $s['location'] ?? 'Indore, MP',
                    'event_type'            => $s['event_type'] ?? 'Celebration Setup',
                    'package_slug'          => $s['package_slug'] ?? null,
                    'review'                => trim($s['review'] ?? ($s['content'] ?? ($s['text'] ?? ''))),
                    'rating'                => (int)($s['rating'] ?? 5),
                    'avatar'                => $s['avatar'] ?? null,
                    'is_verified'           => true,
                    'is_active'             => true,
                    'show_on_home'          => (bool)($s['show_on_home'] ?? true),
                    'show_on_reviews_page'  => (bool)($s['show_on_reviews_page'] ?? true),
                    'show_on_event_details' => (bool)($s['show_on_event_details'] ?? true),
                    'sort_order'            => $order++,
                ]
            );
        }

        // Synchronize testimonials.json with database state
        $allFromDb = Testimonial::ordered()->get()->map(function($t) {
            return [
                'id'                    => $t->id,
                'author'                => $t->author,
                'email'                 => $t->email,
                'location'              => $t->location,
                'event_type'            => $t->event_type,
                'package_slug'          => $t->package_slug,
                'review'                => $t->review,
                'rating'                => (int)$t->rating,
                'avatar'                => $t->avatar,
                'is_verified'           => (bool)$t->is_verified,
                'is_active'             => (bool)$t->is_active,
                'show_on_home'          => (bool)$t->show_on_home,
                'show_on_reviews_page'  => (bool)$t->show_on_reviews_page,
                'show_on_event_details' => (bool)$t->show_on_event_details,
                'sort_order'            => (int)$t->sort_order,
                'created_at'            => $t->created_at ? $t->created_at->format('d M Y') : date('d M Y'),
            ];
        })->toArray();

        JsonStorageService::write('testimonials.json', $allFromDb);
    }
}
