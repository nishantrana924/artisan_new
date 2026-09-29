<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Services\JsonStorageService;

class HomeController extends Controller
{
    public function index()
    {
        $defaultCms = [
            'hero_slides' => [
                [
                    'image' => '/assets/images/hero/1.jpg',
                    'badge' => 'PLAN, BOOK & RELAX',
                    'title' => 'ELEVATE YOUR CELEBRATIONS',
                    'desc' => 'Book complete premium decor, sound, and lighting setups instantly for birthdays, house parties, and sangeet.',
                    'link1' => '/events',
                    'btn1Text' => 'Explore Packages',
                    'link2' => 'https://wa.me/919131668156',
                    'btn2Text' => 'WhatsApp Inquiry'
                ],
                [
                    'image' => '/assets/images/hero/2.jpg',
                    'badge' => 'SOUNDS OF ARTIZEN',
                    'title' => 'HIGH-BASS PARTY RIGS',
                    'desc' => 'Professional active speaker columns, live DJs, strobes, and concert fog delivered directly to your venue.',
                    'link1' => '/events',
                    'btn1Text' => 'View DJ Setups',
                    'link2' => 'https://wa.me/919131668156',
                    'btn2Text' => 'Book Instant'
                ],
                [
                    'image' => '/assets/images/hero/3.jpg',
                    'badge' => 'ROMANTIC MEMORIES',
                    'title' => 'ROYAL STAGES & DECORS',
                    'desc' => 'Exquisite wedding mandaps, traditional haldi Urli setups, and premium anniversary flower panels.',
                    'link1' => '/events',
                    'btn1Text' => 'View Weddings',
                    'link2' => 'https://wa.me/919131668156',
                    'btn2Text' => 'Contact Planner'
                ]
            ],
            'about' => [
                'badge' => 'INSTANT BOOKING GUARANTEE',
                'title' => 'BOOKING CONFIRMED IN SECONDS',
                'desc' => 'We engineered the fastest event booking flow in the industry. Forget calling multiple decorators, negotiating sound rentals, or coordinating with DJs separately. With Artizen, choose your setup, book instantly, and relax. No lag, no hassles.',
                'cards' => [
                    [
                        'icon' => 'clock',
                        'title' => '24/7 SUPPORT',
                        'desc' => 'Our support team is live 24/7 to resolve booking queries instantly.'
                    ],
                    [
                        'icon' => 'lightning',
                        'title' => 'INSTANT SETUP',
                        'desc' => 'Seamless delivery and basic setups installed on-site within hours.'
                    ],
                    [
                        'icon' => 'shield',
                        'title' => 'SECURE GATEWAY',
                        'desc' => 'Cryptographically signed QR codes and secure SSL payment channels.'
                    ]
                ]
            ]
        ];

        $cms = JsonStorageService::read('cms.json', $defaultCms);
        $categories = JsonStorageService::read('categories.json');
        $packages = JsonStorageService::read('packages.json');

        return view('home.index', compact('cms', 'categories', 'packages'));
    }
}
