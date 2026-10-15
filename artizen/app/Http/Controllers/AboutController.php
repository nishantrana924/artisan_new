<?php

namespace App\Http\Controllers;

class AboutController extends Controller
{
    /**
     * Display the Clean, Modern Public About Us page.
     * Matches the Event page UI theme (beige/warm neutral palette, clean typography, zero icon clutter).
     */
    public function index()
    {
        $hero = [
            'title' => 'Curated Celebrations Across Indore',
            'subtitle' => 'All-in-one celebration packages uniting designer decor, theme lighting, acoustic sound, and verified on-site crew execution with transparent offline pricing.',
            'main_image' => asset('images/celebrations/artizen-grand-celebration.webp'),
            'gallery' => [
                asset('images/celebrations/sangeet-dance-stage.webp'),
                asset('images/celebrations/rooftop-starlight-dinner.webp'),
                asset('images/celebrations/marry-me-proposal.webp'),
                asset('images/celebrations/haldi-brass-urli.webp'),
            ]
        ];

        // Key trust stats (pure typography, zero icons)
        $stats = [
            [
                'value' => '1,200+',
                'label' => 'Celebrations Executed',
                'sub' => 'Across Indore venues & residences'
            ],
            [
                'value' => '4.9 ★',
                'label' => 'Customer Rating',
                'sub' => 'From verified celebration reviews'
            ],
            [
                'value' => '2–4 Hrs',
                'label' => 'Early Arrival',
                'sub' => 'Full sound and lighting checks'
            ],
            [
                'value' => '₹0',
                'label' => 'Online Payment Risk',
                'sub' => '100% offline payment settlement'
            ]
        ];

        // The Clean Comparison: Traditional Hassle vs The Artizen Standard
        $comparison = [
            'traditional' => [
                'Coordinating 3 to 4 separate vendors for decor, sound, and lighting',
                'Unpredictable pricing with last-minute surcharges and negotiation stress',
                'Late arrivals and rushed installations on celebration day',
                'Advance payment gateway demands with unknown operators'
            ],
            'artizen' => [
                'One complete curated blueprint with decor, lighting, and sound unified',
                'Fixed transparent package rates with zero hidden charges',
                'Guaranteed 2 to 4 hour early arrival for sound-checked readiness',
                '100% offline payment settled securely after booking confirmation'
            ]
        ];

        // 3-Step Effortless Experience (Clean minimal cards)
        $steps = [
            [
                'number' => '01',
                'title' => 'Choose Setup',
                'desc' => 'Explore 40+ curated celebration packages for birthdays, house parties, proposals, anniversaries, and weddings.'
            ],
            [
                'number' => '02',
                'title' => 'Instant Request',
                'desc' => 'Pick your date, time slot, venue address in Indore, and expected guests. Submit your booking in 60 seconds with ₹0 upfront.'
            ],
            [
                'number' => '03',
                'title' => 'Setup & Offline Pay',
                'desc' => 'Our verified field crew arrives hours early to set up and test everything. Settle payment securely offline.'
            ]
        ];

        // Transformed Spaces across Indore
        $spaces = [
            [
                'title' => 'Rooftops & Terraces',
                'desc' => 'Fairy light canopies, neon backdrops, cabana tents, and acoustic sundowners under the open Indore sky.',
                'image' => asset('images/spaces/rooftop.webp')
            ],
            [
                'title' => 'Living Rooms & Halls',
                'desc' => 'Balloon ring arches, cake table styling, pastel themes, and anniversary flower walls customized for home interiors.',
                'image' => asset('images/spaces/living-room.webp')
            ],
            [
                'title' => 'Farmhouses & Lawns',
                'desc' => 'High-bass active speaker columns, concert fog machines, cold pyro sparklers, and poolside club lighting rigs.',
                'image' => asset('images/spaces/farmhouse.webp')
            ],
            [
                'title' => 'Banquets & Resorts',
                'desc' => 'Royal wedding mandaps, haldi brass urli arrangements, sangeet dance stages, and floral cascades.',
                'image' => asset('images/banners/custom_warm_reception.webp')
            ]
        ];

        // Indore coverage neighborhoods
        $indoreHubs = [
            'Vijay Nagar',
            'Palasia',
            'AB Road',
            'Super Corridor',
            'Bypass & Nipania',
            'Scheme 54',
            'Rau & Silicon City',
            'Saket & Bengali Square',
            'Annapurna & Rajendra Nagar',
        ];

        return view('about.index', compact('hero', 'stats', 'comparison', 'steps', 'spaces', 'indoreHubs'));
    }
}
