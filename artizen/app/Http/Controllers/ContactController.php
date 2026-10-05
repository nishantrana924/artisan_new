<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonStorageService;

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

        $enquiries = JsonStorageService::read('enquiries.json');

        // Generate unique Enquiry ID (ENQ-XXXX)
        do {
            $newId = 'ENQ-' . rand(1000, 9999);
            $idExists = false;
            foreach ($enquiries as $e) {
                if (($e['id'] ?? '') === $newId) {
                    $idExists = true;
                    break;
                }
            }
        } while ($idExists);

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

        $enquiries[] = $enquiry;
        JsonStorageService::write('enquiries.json', $enquiries);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your inquiry (ID: ' . $newId . ') has been sent successfully. Our team will contact you shortly.'
            ]);
        }

        return redirect()->route('contact.index')->with('success', 'Thank you! Your inquiry (Ref ID: ' . $newId . ') has been received. Our Artizen event manager will reach out to you within 15 minutes.');
    }
}
