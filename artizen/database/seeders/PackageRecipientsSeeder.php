<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageRecipientsSeeder extends Seeder
{
    /**
     * Seed initial sensible recipient personas on packages.
     */
    public function run(): void
    {
        $mappings = [
            '1st Birthday Wonderland'              => ['kids'],
            'Kids Jungle Safari Theme'             => ['kids'],
            'Pastel Balloon Arch & Teddy Bear'     => ['kids', 'her'],
            "'Oh Baby' Neon & Mom Throne"          => ['her', 'wife'],
            'Neon Sign Ring Arch Setup'            => ['him', 'her', 'friend'],
            'Sweet 16 & 18th Club Bash'            => ['friend', 'her', 'him'],
            '21st & 25th High-Energy Party'        => ['him', 'friend'],
            'Cozy Teepee Tent Village'             => ['kids', 'friend'],
            '30th to 50th Jubilee Celebration'     => ['him', 'husband', 'parents'],
            'Princess Castle & Fairy Tale'         => ['kids', 'her'],
            'Cake Table & Photobooth Styling'      => ['her', 'him', 'friend', 'kids'],
            "The 'Marry Me' Proposal"              => ['her', 'wife'],
            'Silver Jubilee Elegance Arch'         => ['parents', 'husband', 'wife'],
            'Fairy Light Romantic Cabana'          => ['her', 'wife'],
            '100+ Candlelight Pathway Trail'       => ['her', 'wife'],
            'Golden Jubilee Grand Stage'           => ['parents'],
            'Rose Petal Heart & Champagne Table'   => ['her', 'wife'],
            'Private Rooftop Starlight Dinner'     => ['her', 'wife'],
            'Live Violinist Romantic Entry'        => ['her', 'wife'],
            'Floral Ring & Memory Photo Wall'      => ['wife', 'husband', 'parents'],
            'Secret Sunset Terrace Proposal'       => ['her', 'wife'],
            'Parents Anniversary Special Arch'     => ['parents'],
            'Haldi Brass Urli & Marigold Setup'    => ['her', 'him', 'wife', 'husband', 'parents'],
            'Mehendi Colorful Drapes & Diwan'      => ['her', 'wife'],
            'Rooftop Neon House Party DJ Rig'      => ['friend', 'him'],
            'Sangeet DJ Dance Floor & Club Pars'   => ['friend', 'him', 'her'],
            'Live Acoustic Singer & Guitarist'     => ['friend', 'her', 'him'],
            'Punjabi Dhol Players & Festive Entry' => ['friend', 'him'],
            'High-Bass Column Speakers & Mics'     => ['friend', 'him'],
            'Cold Pyro Sparklers & Low-Fog Clouds' => ['friend', 'her', 'him'],
            'Sufi & Bollywood Unplugged Sundowner' => ['friend', 'him', 'her'],
            'Corporate Gala & Milestone Stage'     => ['him', 'friend'],
            'Farmhouse Poolside All-Night DJ Bash' => ['friend', 'him'],
        ];

        foreach ($mappings as $title => $recipients) {
            Package::where('title', 'like', "%{$title}%")->update([
                'recipients' => $recipients,
            ]);
        }
    }
}
