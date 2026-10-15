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

        // Load all bookings
        $allBookings = JsonStorageService::read('bookings.json', []);

        // Filter bookings belonging to this customer
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
                        array_unshift($userBookings, $b);
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
