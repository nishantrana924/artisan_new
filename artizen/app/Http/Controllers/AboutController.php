<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class AboutController extends Controller
{
    /**
     * Display the About Us page.
     */
    public function index()
    {
        $cms = \App\Services\JsonStorageService::read('cms.json');

        // Setup default fallbacks if JSON settings are missing
        $about = $cms['about'] ?? [
            'badge' => 'INSTANT BOOKING GUARANTEE',
            'title' => 'BOOKING CONFIRMED IN SECONDS',
            'desc' => 'We engineered the fastest event booking flow in the industry. Forget calling multiple decorators, negotiating sound rentals, or coordinating with DJs separately. With Artizen, choose your setup, book instantly, and relax. No lag, no hassles.',
            'image' => '/assets/images/hero/1.jpg',
            'cards' => [
                [
                    'icon' => 'fa-solid fa-clock',
                    'title' => '24/7 SUPPORT',
                    'desc' => 'Our support team is live 24/7 to resolve booking queries instantly.'
                ],
                [
                    'icon' => 'fa-solid fa-bolt',
                    'title' => 'INSTANT SETUP',
                    'desc' => 'Seamless delivery and basic setups installed on-site within hours.'
                ],
                [
                    'icon' => 'fa-solid fa-shield-halved',
                    'title' => 'SECURE GATEWAY',
                    'desc' => 'Cryptographically signed QR codes and secure SSL payment channels.'
                ]
            ]
        ];

        return view('about.index', compact('about'));
    }
}
