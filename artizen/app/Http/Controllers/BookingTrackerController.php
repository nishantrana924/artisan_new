<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonStorageService;

class BookingTrackerController extends Controller
{
    /**
     * Display the public booking tracker form.
     */
    public function showForm(Request $request)
    {
        $bookingId = trim($request->query('id', ''));
        $booking = null;
        $searched = false;

        // If ID passed via query string from booking success page, attempt lookup if mobile provided
        if (!empty($bookingId) && $request->has('mobile')) {
            $booking = $this->findBooking($bookingId, $request->query('mobile'));
            $searched = true;
        }

        return view('booking.track', compact('booking', 'searched', 'bookingId'));
    }

    /**
     * Search and verify customer booking status.
     */
    public function search(Request $request)
    {
        $request->validate([
            'booking_id' => [
                'required',
                'string',
                'regex:/^BK-[A-Z0-9]{4,8}$/i'
            ],
            'mobile' => [
                'required',
                'string',
                'min:8',
                'max:20'
            ],
        ], [
            'booking_id.required' => 'Booking Reference ID is required.',
            'booking_id.regex' => 'Booking ID must be in the format BK-XXXX (e.g., BK-8844).',
            'mobile.required' => 'Registered Mobile / WhatsApp number is required.',
            'mobile.min' => 'Please enter a valid mobile number.',
        ]);

        $bookingId = strtoupper(trim($request->input('booking_id')));
        $mobile = trim($request->input('mobile'));

        $booking = $this->findBooking($bookingId, $mobile);

        if (!$booking) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No booking request found matching the provided Booking ID and Mobile Number. Please double check your booking details.');
        }

        return view('booking.track', [
            'booking' => $booking,
            'searched' => true,
            'bookingId' => $bookingId
        ]);
    }

    /**
     * Securely lookup booking by matching BOTH Booking ID and Customer Phone.
     */
    private function findBooking(string $bookingId, string $mobile): ?array
    {
        $bookings = JsonStorageService::read('bookings.json', []);
        
        $cleanInputPhone = preg_replace('/[^0-9]/', '', $mobile);
        if (strlen($cleanInputPhone) > 10) {
            $cleanInputPhone = substr($cleanInputPhone, -10);
        }

        if (empty($cleanInputPhone)) {
            return null;
        }

        foreach ($bookings as $b) {
            $storedId = strtoupper(trim($b['id'] ?? ''));
            
            // Check Booking ID exact match first
            if ($storedId === $bookingId) {
                // Now check Phone match (mobile or whatsapp)
                $storedMobile = preg_replace('/[^0-9]/', '', $b['mobile'] ?? '');
                $storedWhatsapp = preg_replace('/[^0-9]/', '', $b['whatsapp'] ?? '');

                if (strlen($storedMobile) > 10) $storedMobile = substr($storedMobile, -10);
                if (strlen($storedWhatsapp) > 10) $storedWhatsapp = substr($storedWhatsapp, -10);

                if ($storedMobile === $cleanInputPhone || $storedWhatsapp === $cleanInputPhone) {
                    return $b;
                }
            }
        }

        return null;
    }
}
