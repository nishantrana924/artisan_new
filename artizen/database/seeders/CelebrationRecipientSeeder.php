<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CelebrationRecipient;

class CelebrationRecipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipients = [
            [
                'name'          => 'Him',
                'slug'          => 'him',
                'image'         => '/images/for-everyone/him.webp',
                'category_slug' => 'cat-birthdays',
                'display_order' => 1,
                'is_active'     => true,
            ],
            [
                'name'          => 'Her',
                'slug'          => 'her',
                'image'         => '/images/for-everyone/her.webp',
                'category_slug' => 'cat-proposal-anniversary',
                'display_order' => 2,
                'is_active'     => true,
            ],
            [
                'name'          => 'Kids',
                'slug'          => 'kids',
                'image'         => '/images/for-everyone/kids.webp',
                'category_slug' => 'cat-kids-cozy',
                'display_order' => 3,
                'is_active'     => true,
            ],
            [
                'name'          => 'Friend',
                'slug'          => 'friend',
                'image'         => '/images/for-everyone/friend.webp',
                'category_slug' => 'cat-house-party',
                'display_order' => 4,
                'is_active'     => true,
            ],
            [
                'name'          => 'Wife',
                'slug'          => 'wife',
                'image'         => '/images/for-everyone/wife.webp',
                'category_slug' => 'cat-proposal-anniversary',
                'display_order' => 5,
                'is_active'     => true,
            ],
            [
                'name'          => 'Husband',
                'slug'          => 'husband',
                'image'         => '/images/for-everyone/husband.webp',
                'category_slug' => 'cat-weddings-sangeet',
                'display_order' => 6,
                'is_active'     => true,
            ],
            [
                'name'          => 'Parents',
                'slug'          => 'parents',
                'image'         => '/images/for-everyone/parents.webp',
                'category_slug' => 'cat-weddings-sangeet',
                'display_order' => 7,
                'is_active'     => true,
            ],
        ];

        foreach ($recipients as $data) {
            CelebrationRecipient::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
