<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonStorageService;
use App\Models\Enquiry;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Display the public contact & inquiry page.
     */
    public function index()
    {
        $contactInfo = [
            'phone' => '+91 9131668156',
            'whatsapp' => '919131668156',
            'email' => 'info@artizenevents.com',
            'address' => 'Artizen Event Portal, Vijay Nagar, Indore, Madhya Pradesh - 452010',
            'hours' => 'Monday - Sunday: 09:00 AM - 10:00 PM'
        ];

        return view('contact.index', compact('contactInfo'));
    }

    /**
     * Save customer contact inquiry to storage/app/enquiries.json with atomic writes.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        // Duplicate submission debounce (prevents exact duplicate submissions within 60 seconds)
        $debounceKey = 'contact_debounce_' . md5($request->ip() . '|' . $request->input('email') . '|' . $request->input('message'));
        if (\Illuminate\Support\Facades\Cache::has($debounceKey)) {
            $msg = 'Your inquiry has already been received. Our team will reach out to you shortly.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->route('contact.index')->with('success', $msg);
        }
        \Illuminate\Support\Facades\Cache::put($debounceKey, true, 60);

        // Generate unique, collision-resistant Enquiry ID (ENQ-XXXXXX)
        do {
            $newId = 'ENQ-' . strtoupper(Str::random(6));
        } while (Enquiry::where('id', $newId)->exists());

        $enquiry = [
            'id' => $newId,
            'name' => trim($request->input('name')),
            'phone' => trim($request->input('phone')),
            'email' => trim($request->input('email', '')),
            'subject' => trim($request->input('subject')),
            'message' => trim($request->input('message')),
            'status' => 'Unread',
            'created_at' => date('d M Y, h:i A')
        ];

        // 1. Direct Eloquent insertion
        Enquiry::create([
            'id' => $newId,
            'name' => $enquiry['name'],
            'phone' => $enquiry['phone'],
            'email' => $enquiry['email'] ?: null,
            'subject' => $enquiry['subject'],
            'message' => $enquiry['message'],
            'status' => 'Unread',
        ]);

        // 2. Synchronize single record to JSON fallback under exclusive flock
        JsonStorageService::appendEnquiryRecord($enquiry);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your inquiry (ID: ' . $newId . ') has been sent successfully. Our team will contact you shortly.'
            ]);
        }

        return redirect()->route('contact.index')->with('success', 'Thank you! Your inquiry (Ref ID: ' . $newId . ') has been received. Our Artizen event manager will reach out to you within 15 minutes.');
    }
}
