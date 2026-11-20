<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\JsonStorageService;

class CustomerOrderController extends Controller
{
    /**
     * Display the authenticated customer's personal bookings & order history.
     * Categorizes into Pending, Confirmed, Completed, Cancelled/Rejected.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isLoggedIn = $user || session('user_logged_in');

        // Enforce Authentication
        if (!$isLoggedIn) {
            return redirect()->route('login', ['redirect' => '/orders'])
                ->with('info', 'Please log in to your account to view your bookings and order history.');
        }

        $userId = $user ? $user->id : session('user_id');
        $userEmail = strtolower(trim((string)($user ? $user->email : session('user_email', ''))));
        $userName = $user ? $user->name : session('user_name', 'Customer');

        // Load customer bookings using targeted query
        $userBookings = [];
        $allBookings = [];

        if (config('artizen.storage_driver') === 'database') {
            try {
                $query = \App\Models\Booking::query();
                $query->where(function($q) use ($userId, $userEmail) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    }
                    if (!empty($userEmail)) {
                        $q->orWhere('customer_email', $userEmail);
                    }
                });
                $dbUserBookings = $query->orderBy('created_at', 'desc')->get();
                if ($dbUserBookings->isNotEmpty()) {
                    $userBookings = $dbUserBookings->map(function($b) {
                        return [
                            'id' => $b->id,
                            'user_id' => $b->user_id,
                            'customer_id' => $b->customer_id,
                            'name' => $b->customer_name ?: 'Valued Customer',
                            'customer_name' => $b->customer_name ?: 'Valued Customer',
                            'mobile' => $b->customer_mobile ?: '',
                            'customer_mobile' => $b->customer_mobile ?: '',
                            'whatsapp' => $b->customer_whatsapp ?: ($b->customer_mobile ?: ''),
                            'customer_whatsapp' => $b->customer_whatsapp ?: ($b->customer_mobile ?: ''),
                            'email' => $b->customer_email ?: '',
                            'customer_email' => $b->customer_email ?: '',
                            'package' => $b->package_name ?: 'Celebration Package',
                            'package_name' => $b->package_name ?: 'Celebration Package',
                            'category' => $b->category_name ?: 'Event Setup',
                            'category_name' => $b->category_name ?: 'Event Setup',
                            'tier' => $b->tier_name ?: 'Standard Setup',
                            'tier_name' => $b->tier_name ?: 'Standard Setup',
                            'date' => $b->event_date ?: date('Y-m-d'),
                            'event_date' => $b->event_date ?: date('Y-m-d'),
                            'time' => $b->event_time ?: '18:00',
                            'event_time' => $b->event_time ?: '18:00',
                            'guest_count' => (int)($b->guest_count ?: 25),
                            'address' => $b->address ?: 'Indore, Madhya Pradesh',
                            'landmark' => $b->landmark ?: '',
                            'area' => $b->area ?: '',
                            'city' => $b->city ?: 'Indore',
                            'state' => $b->state ?: 'Madhya Pradesh',
                            'pincode' => $b->pincode ?: '452010',
                            'package_price' => (float)($b->package_price ?: $b->total_amount),
                            'surcharge' => (float)($b->surcharge_amount ?: 0),
                            'surcharge_amount' => (float)($b->surcharge_amount ?: 0),
                            'total' => (float)$b->total_amount,
                            'total_amount' => (float)$b->total_amount,
                            'addons' => is_array($b->addons) ? $b->addons : [],
                            'status' => $b->status ?: 'Pending Review',
                            'notes' => $b->notes ?: '',
                            'created_at' => $b->created_at ? $b->created_at->format('d M Y, h:i A') : date('d M Y'),
                        ];
                    })->toArray();
                }
            } catch (\Throwable $e) {
                // Fallback below
            }
        }

        // Fallback to JSON storage if database query returned no results or driver is JSON
        if (empty($userBookings)) {
            $allBookings = JsonStorageService::read('bookings.json', []);
            $userBookings = array_values(array_filter($allBookings, function ($b) use ($userId, $userEmail) {
                $bUserId = $b['user_id'] ?? null;
                $bEmail = strtolower(trim((string)($b['email'] ?? ($b['customer_email'] ?? ''))));
                
                if ($userId && $bUserId && (string)$bUserId === (string)$userId) {
                    return true;
                }
                if (!empty($userEmail) && !empty($bEmail) && $bEmail === $userEmail) {
                    return true;
                }
                return false;
            }));
        }

        // Fallback: If user created a booking in current session
        if (session()->has('last_booking_id')) {
            $lastId = (string)session('last_booking_id');
            $hasLast = false;
            foreach ($userBookings as $ub) {
                if (($ub['id'] ?? '') === $lastId) {
                    $hasLast = true;
                    break;
                }
            }
            if (!$hasLast) {
                foreach ($allBookings as $b) {
                    if (($b['id'] ?? '') === $lastId) {
                        $bEmail = strtolower(trim((string)($b['email'] ?? ($b['customer_email'] ?? ''))));
                        $bUserId = $b['user_id'] ?? null;
                        if ((!empty($userEmail) && $bEmail === $userEmail) || (!empty($userId) && (string)$bUserId === (string)$userId)) {
                            array_unshift($userBookings, $b);
                        }
                        break;
                    }
                }
            }
        }

        // Calculate counts for each status tab
        $counts = [
            'all' => count($userBookings),
            'pending' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        foreach ($userBookings as $b) {
            $status = strtolower(trim($b['status'] ?? 'pending'));
            if ($status === 'pending' || $status === 'under review') {
                $counts['pending']++;
            } elseif ($status === 'confirmed' || $status === 'contacted' || $status === 'approved') {
                $counts['confirmed']++;
            } elseif ($status === 'completed') {
                $counts['completed']++;
            } elseif ($status === 'cancelled' || $status === 'rejected') {
                $counts['cancelled']++;
            } else {
                $counts['pending']++;
            }
        }

        // Active tab filtering
        $activeTab = strtolower(trim($request->query('status', 'all')));
        if (!in_array($activeTab, ['all', 'pending', 'confirmed', 'completed', 'cancelled'])) {
            $activeTab = 'all';
        }

        $filteredBookings = $userBookings;
        if ($activeTab !== 'all') {
            $filteredBookings = array_values(array_filter($userBookings, function ($b) use ($activeTab) {
                $status = strtolower(trim($b['status'] ?? 'pending'));
                if ($activeTab === 'pending') {
                    return $status === 'pending' || $status === 'under review';
                }
                if ($activeTab === 'confirmed') {
                    return $status === 'confirmed' || $status === 'contacted' || $status === 'approved';
                }
                if ($activeTab === 'completed') {
                    return $status === 'completed';
                }
                if ($activeTab === 'cancelled') {
                    return $status === 'cancelled' || $status === 'rejected';
                }
                return true;
            }));
        }

        return view('booking.orders', compact(
            'userBookings',
            'filteredBookings',
            'counts',
            'activeTab',
            'userName',
            'userEmail'
        ));
    }
}
