<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Services\JsonStorageService;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display the public FAQ and Help Center page.
     */
    public function index(Request $request)
    {
        $categoryLabels = [
            'booking'       => 'Booking & Reservations',
            'payments'      => 'Payments & Billing',
            'setup'         => 'On-Site Setup & Timings',
            'customization' => 'Themes & Customization',
            'policy'        => 'Rescheduling & Policies',
            'general'       => 'General Inquiries',
        ];

        try {
            $faqs = Faq::active()->ordered()->get();
        } catch (\Throwable $e) {
            $rawFaqs = JsonStorageService::read('faqs.json', []);
            $faqs = collect($rawFaqs)->filter(fn($f) => ($f['active'] ?? $f['is_active'] ?? true));
        }

        $faqList = [];
        foreach ($faqs as $f) {
            $cat = is_object($f) ? strtolower(trim($f->category ?? 'general')) : strtolower(trim($f['category'] ?? 'general'));
            $q = is_object($f) ? $f->question : ($f['q'] ?? $f['question'] ?? '');
            $a = is_object($f) ? $f->answer : ($f['a'] ?? $f['answer'] ?? '');

            if (!empty($q)) {
                $faqList[] = [
                    'id'             => is_object($f) ? $f->id : ($f['id'] ?? null),
                    'category'       => $cat,
                    'category_label' => $categoryLabels[$cat] ?? ucfirst($cat),
                    'q'              => $q,
                    'a'              => $a,
                ];
            }
        }

        return view('faq.index', compact('faqList'));
    }
}

