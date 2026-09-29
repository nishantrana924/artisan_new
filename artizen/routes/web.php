<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;

// Public Front-end Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [AboutController::class, 'index'])->name('about-us');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::redirect('/event', '/events');
Route::get('/category/{slug}', [EventController::class, 'categoryShow'])->name('category.show');
Route::get('/event/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
Route::get('/booking/success', [BookingController::class, 'success'])->name('booking.success');
Route::post('/booking/save', [BookingController::class, 'store'])->name('booking.save')->middleware('throttle:10,1');

use App\Http\Controllers\BookingTrackerController;

// Public Booking Status Tracker Routes
Route::get('/track-booking', [BookingTrackerController::class, 'showForm'])->name('booking.track');
Route::post('/track-booking/search', [BookingTrackerController::class, 'search'])->name('booking.track.search')->middleware('throttle:15,1');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact/send', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:10,1');

Route::get('/event-details.html', function (\Illuminate\Http\Request $request) {
    $id = $request->query('id', 1);
    $tier = $request->query('tier', 0);
    return redirect()->route('events.show', ['slug' => $id, 'tier' => $tier]);
});

use App\Http\Controllers\Auth\RegisterController;

// User Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit')->middleware('throttle:5,1');

// Admin Authentication Public Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit')->middleware('throttle:5,1');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Protected Admin Panel Routes (Enforced by AdminMiddleware)
Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // CMS Settings & Assets
    Route::post('/cms/save', [DashboardController::class, 'saveCms'])->name('admin.cms.save');
    Route::post('/settings/save', [DashboardController::class, 'saveSettings'])->name('admin.settings.save');
    
    // Categories & Package CRUD
    Route::post('/categories/save', [DashboardController::class, 'saveCategory'])->name('admin.categories.save');
    Route::post('/categories/delete/{id}', [DashboardController::class, 'deleteCategory'])->name('admin.categories.delete');
    Route::get('/packages/create', [DashboardController::class, 'createPackagePage'])->name('admin.packages.create');
    Route::get('/packages/edit/{catId}/{tierIdx}', [DashboardController::class, 'editPackagePage'])->name('admin.packages.edit');
    Route::post('/packages/save', [DashboardController::class, 'savePackage'])->name('admin.packages.save');
    Route::post('/packages/delete/{catId}/{tierIdx}', [DashboardController::class, 'deletePackage'])->name('admin.packages.delete');
    
    // Bookings Status Management
    Route::post('/bookings/update-status', [DashboardController::class, 'updateBookingStatus'])->name('admin.bookings.update-status');
    
    // Artists Management
    Route::post('/artists/save', [DashboardController::class, 'saveArtist'])->name('admin.artists.save');
    Route::post('/artists/delete/{id}', [DashboardController::class, 'deleteArtist'])->name('admin.artists.delete');
    
    // FAQ Management
    Route::post('/faqs/save', [DashboardController::class, 'saveFaq'])->name('admin.faqs.save');
    Route::post('/faqs/delete/{id}', [DashboardController::class, 'deleteFaq'])->name('admin.faqs.delete');

    // Testimonials & Gallery CRUD
    Route::post('/testimonials/save', [DashboardController::class, 'saveTestimonial'])->name('admin.testimonials.save');
    Route::post('/testimonials/delete/{id}', [DashboardController::class, 'deleteTestimonial'])->name('admin.testimonials.delete');
    Route::post('/gallery/save', [DashboardController::class, 'saveGalleryItem'])->name('admin.gallery.save');
    Route::post('/gallery/delete/{id}', [DashboardController::class, 'deleteGalleryItem'])->name('admin.gallery.delete');

    // Enquiries Management
    Route::post('/enquiries/delete/{id}', [DashboardController::class, 'deleteEnquiry'])->name('admin.enquiries.delete');
});
