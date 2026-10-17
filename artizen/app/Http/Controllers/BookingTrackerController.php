<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\JsonStorageService;
use App\Models\Booking;

class BookingTrackerController extends Controller
{
    /**
     * Display the public booking tracker form.
     */
    public function showForm(Request $request)
    {
        $bookingId = strtoupper(trim($request->query('id', '')));
        $mobile = trim($request->query('mobile', ''));
        $booking = null;
        $searched = false;

        // 1. If both ID and mobile provided in query
        if (!empty($bookingId) && !empty($mobile)) {
            $booking = $this->findBooking($bookingId, $mobile);
            $searched = true;
        } elseif (!empty($bookingId)) {
            // Check session ownership (user just completed checkout in current session)
            $sessionBookingId = strtoupper(trim((string)session('last_booking_id', '')));
            $isSessionOwner = (!empty($sessionBookingId) && $sessionBookingId === $bookingId);

            // Check authenticated user ownership or admin privilege
            $isAuthOwner = false;
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->isAdmin()) {
                    $isAuthOwner = true;
                } else {
                    $candidate = $this->findBookingById($bookingId);
                    if ($candidate && (
                        (!empty($candidate['email']) && strcasecmp($candidate['email'], $user->email) === 0) ||
                        (!empty($candidate['user_id']) && (string)$candidate['user_id'] === (string)$user->id)
                    )) {
                        $isAuthOwner = true;
                    }
                }
            }

            if ($isSessionOwner || $isAuthOwner) {
                $booking = $this->findBookingById($bookingId);
                if ($booking) {
                    $searched = true;
                }
            } else {
                // Do NOT reveal private booking details to unauthenticated third parties!
                // Leave $booking = null and $searched = false so the lookup form is shown with $bookingId prefilled
                session()->flash('info', 'Please verify your registered mobile number to view this booking.');
            }
        } elseif (session()->has('last_booking_id')) {
            // If user previously booked in current session
            $sessionBookingId = strtoupper(trim((string)session('last_booking_id')));
            $booking = $this->findBookingById($sessionBookingId);
            if ($booking) {
                $bookingId = $sessionBookingId;
                $searched = true;
            }
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
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No booking request found matching the provided Booking ID and Mobile Number.'
                ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'No booking request found matching the provided Booking ID and Mobile Number. Please double check your booking details.');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'booking' => $booking
            ]);
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
        $cleanBookingId = strtoupper(trim($bookingId));
        $cleanInputPhone = preg_replace('/[^0-9]/', '', $mobile);
        if (strlen($cleanInputPhone) > 10) {
            $cleanInputPhone = substr($cleanInputPhone, -10);
        }

        if (empty($cleanInputPhone) || empty($cleanBookingId)) {
            return null;
        }

        // 1. Direct indexed database query
        $dbBooking = Booking::where('id', $cleanBookingId)->first();
        if ($dbBooking) {
            $storedMobile = preg_replace('/[^0-9]/', '', (string)$dbBooking->customer_mobile);
            $storedWhatsapp = preg_replace('/[^0-9]/', '', (string)$dbBooking->customer_whatsapp);

            if (strlen($storedMobile) > 10) $storedMobile = substr($storedMobile, -10);
            if (strlen($storedWhatsapp) > 10) $storedWhatsapp = substr($storedWhatsapp, -10);

            if ($storedMobile === $cleanInputPhone || $storedWhatsapp === $cleanInputPhone) {
                return [
                    'id' => $dbBooking->id,
                    'user_id' => $dbBooking->user_id,
                    'name' => $dbBooking->customer_name,
                    'mobile' => $dbBooking->customer_mobile,
                    'whatsapp' => $dbBooking->customer_whatsapp,
                    'email' => $dbBooking->customer_email,
                    'package' => $dbBooking->package_name,
                    'category' => $dbBooking->category_name,
                    'tier' => $dbBooking->tier_name,
                    'date' => $dbBooking->event_date,
                    'time' => $dbBooking->event_time,
                    'guest_count' => $dbBooking->guest_count,
                    'address' => $dbBooking->address,
                    'landmark' => $dbBooking->landmark,
                    'area' => $dbBooking->area,
                    'city' => $dbBooking->city,
                    'state' => $dbBooking->state,
                    'pincode' => $dbBooking->pincode,
                    'notes' => $dbBooking->notes,
                    'addons' => $dbBooking->addons ?? [],
                    'package_price' => (int)$dbBooking->package_price,
                    'surcharge' => (int)$dbBooking->surcharge_amount,
                    'total' => (int)$dbBooking->total_amount,
                    'status' => $dbBooking->status,
                    'notifications_sent' => $dbBooking->notifications_sent ?? [],
                    'created_at' => $dbBooking->created_at ? $dbBooking->created_at->format('d M Y, h:i A') : date('d M Y')
                ];
            }
        }

        // 2. Fallback to JSON for testing or legacy records
        $bookings = JsonStorageService::read('bookings.json', []);
        foreach ($bookings as $b) {
            $storedId = strtoupper(trim($b['id'] ?? ''));
            if ($storedId === $cleanBookingId) {
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

    /**
     * Lookup booking by Booking ID alone.
     */
    private function findBookingById(string $bookingId): ?array
    {
        $cleanId = strtoupper(trim($bookingId));
        if (empty($cleanId)) return null;

        // 1. Direct indexed database query
        $dbBooking = Booking::where('id', $cleanId)->first();
        if ($dbBooking) {
            return [
                'id' => $dbBooking->id,
                'user_id' => $dbBooking->user_id,
                'name' => $dbBooking->customer_name,
                'mobile' => $dbBooking->customer_mobile,
                'whatsapp' => $dbBooking->customer_whatsapp,
                'email' => $dbBooking->customer_email,
                'package' => $dbBooking->package_name,
                'category' => $dbBooking->category_name,
                'tier' => $dbBooking->tier_name,
                'date' => $dbBooking->event_date,
                'time' => $dbBooking->event_time,
                'guest_count' => $dbBooking->guest_count,
                'address' => $dbBooking->address,
                'landmark' => $dbBooking->landmark,
                'area' => $dbBooking->area,
                'city' => $dbBooking->city,
                'state' => $dbBooking->state,
                'pincode' => $dbBooking->pincode,
                'notes' => $dbBooking->notes,
                'addons' => $dbBooking->addons ?? [],
                'package_price' => (int)$dbBooking->package_price,
                'surcharge' => (int)$dbBooking->surcharge_amount,
                'total' => (int)$dbBooking->total_amount,
                'status' => $dbBooking->status,
                'notifications_sent' => $dbBooking->notifications_sent ?? [],
                'created_at' => $dbBooking->created_at ? $dbBooking->created_at->format('d M Y, h:i A') : date('d M Y')
            ];
        }

        // 2. Fallback to JSON
        $bookings = JsonStorageService::read('bookings.json', []);
        foreach ($bookings as $b) {
            if (strtoupper(trim($b['id'] ?? '')) === $cleanId) {
                return $b;
            }
        }

        return null;
    }
}
