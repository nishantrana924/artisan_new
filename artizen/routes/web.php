<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;

// Public Front-end Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [AboutController::class, 'index'])->name('about-us');
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('throttle:10,1');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
Route::redirect('/faqs', '/faq');

// Public Legal & Policy Routes
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('legal.privacy');
Route::get('/terms-and-conditions', [LegalController::class, 'termsConditions'])->name('legal.terms');
Route::get('/cancellation-policy', [LegalController::class, 'cancellationPolicy'])->name('legal.cancellation');
Route::get('/booking-policy', [LegalController::class, 'bookingPolicy'])->name('legal.booking-policy');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::redirect('/event', '/events');
Route::get('/category/{slug}', [EventController::class, 'categoryShow'])->name('category.show');
Route::get('/event/{slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{slug}', [EventController::class, 'show']);

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
    $id = (string)$request->query('id', 1);
    $tier = $request->query('tier', 0);
    $legacyIdMap = [
        '1' => '1st-birthday-wonderland',
        '2' => 'rooftop-neon-dj-rig',
        '3' => 'marry-me-proposal',
        '4' => 'sangeet-dance-stage',
        '5' => 'live-acoustic-guitarist',
        '6' => 'sweet-16-club-bash',
        '7' => 'fairy-light-cabana',
        '8' => 'cozy-teepee-tent-village',
        '9' => 'corporate-gala-stage',
        '10' => 'haldi-brass-urli',
        '11' => 'punjabi-dhol-players',
        '12' => 'high-bass-speakers-mics',
    ];
    $slug = $legacyIdMap[$id] ?? $id;
    return redirect()->route('events.show', ['slug' => $slug, 'tier' => $tier], 301);
});

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

// User Authentication Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit')->middleware('throttle:5,1');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Authentication Public Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit')->middleware('throttle:5,1');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

use App\Http\Controllers\Admin\AdminPageController;

// Protected Admin Panel Routes (Enforced by AdminMiddleware)
Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Separate Admin Section Pages
    Route::get('/bookings',     [AdminPageController::class, 'bookings'])->name('admin.bookings');
    Route::get('/packages',     [AdminPageController::class, 'packages'])->name('admin.packages');
    Route::get('/categories',          [AdminPageController::class, 'categories'])->name('admin.categories');
    Route::get('/categories/create',   [AdminPageController::class, 'categoryCreate'])->name('admin.categories.create');
    Route::get('/categories/{id}/edit', [AdminPageController::class, 'categoryEdit'])->name('admin.categories.edit');
    Route::get('/customers',    [AdminPageController::class, 'customers'])->name('admin.customers');
    Route::get('/faq-reviews',  [AdminPageController::class, 'testimonials'])->name('admin.testimonials');
    Route::get('/faqs',         [AdminPageController::class, 'testimonials'])->name('admin.faqs');
    Route::get('/hero-slider',  [AdminPageController::class, 'hero'])->name('admin.hero');
    Route::get('/about',        [AdminPageController::class, 'about'])->name('admin.about');
    Route::get('/legal-pages',  [AdminPageController::class, 'legal'])->name('admin.legal');
    Route::get('/settings',     [AdminPageController::class, 'settings'])->name('admin.settings');
    
    // CMS Settings & Assets
    Route::post('/cms/save', [DashboardController::class, 'saveCms'])->name('admin.cms.save');
    Route::post('/settings/save', [DashboardController::class, 'saveSettings'])->name('admin.settings.save');
    
    // Categories & Package CRUD
    Route::post('/categories/save', [DashboardController::class, 'saveCategory'])->name('admin.categories.save');
    Route::post('/categories/delete/{id}', [DashboardController::class, 'deleteCategory'])->name('admin.categories.delete');
    Route::get('/packages/create', [AdminPageController::class, 'packageCreate'])->name('admin.packages.create');
    Route::get('/packages/{id}/edit', [AdminPageController::class, 'packageEdit'])->name('admin.packages.edit');
    Route::get('/packages/edit/{catId}/{tierIdx}', [AdminPageController::class, 'packageEditLegacy'])->name('admin.packages.edit.legacy');
    Route::post('/packages/save', [DashboardController::class, 'savePackage'])->name('admin.packages.save');
    Route::post('/packages/delete/{catId}/{tierIdx}', [DashboardController::class, 'deletePackage'])->name('admin.packages.delete');
    Route::post('/packages/toggle-status/{id}', [DashboardController::class, 'togglePackageStatus'])->name('admin.packages.toggle-status');
    Route::post('/packages/destroy/{id}', [DashboardController::class, 'destroyPackage'])->name('admin.packages.destroy');
    
    // Bookings Status Management
    Route::post('/bookings/update-status', [DashboardController::class, 'updateBookingStatus'])->name('admin.bookings.update-status');
    
    // Artists Management
    Route::post('/artists/save', [DashboardController::class, 'saveArtist'])->name('admin.artists.save');
    Route::post('/artists/delete/{id}', [DashboardController::class, 'deleteArtist'])->name('admin.artists.delete');
    
    // FAQ Management
    Route::post('/faqs/save', [DashboardController::class, 'saveFaq'])->name('admin.faqs.save');
    Route::post('/faqs/delete/{id}', [DashboardController::class, 'deleteFaq'])->name('admin.faqs.delete');

    // Dedicated Review Management
    Route::get('/reviews',                    [\App\Http\Controllers\Admin\ReviewAdminController::class, 'index'])->name('admin.reviews');
    Route::get('/reviews/create',             [\App\Http\Controllers\Admin\ReviewAdminController::class, 'create'])->name('admin.reviews.create');
    Route::get('/reviews/{id}/edit',          [\App\Http\Controllers\Admin\ReviewAdminController::class, 'edit'])->name('admin.reviews.edit');
    Route::post('/reviews/save',              [\App\Http\Controllers\Admin\ReviewAdminController::class, 'save'])->name('admin.reviews.save');
    Route::post('/reviews/delete/{id}',       [\App\Http\Controllers\Admin\ReviewAdminController::class, 'delete'])->name('admin.reviews.delete');
    Route::post('/reviews/toggle-visibility', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'toggleVisibility'])->name('admin.reviews.toggle-visibility');

    // Dedicated Celebrations for Everyone (Recipients) Management
    Route::get('/recipients',                     [\App\Http\Controllers\Admin\RecipientAdminController::class, 'index'])->name('admin.recipients');
    Route::get('/recipients/create',              [\App\Http\Controllers\Admin\RecipientAdminController::class, 'create'])->name('admin.recipients.create');
    Route::get('/recipients/{id}/edit',           [\App\Http\Controllers\Admin\RecipientAdminController::class, 'edit'])->name('admin.recipients.edit');
    Route::post('/recipients/save',               [\App\Http\Controllers\Admin\RecipientAdminController::class, 'save'])->name('admin.recipients.save');
    Route::match(['post', 'delete'], '/recipients/delete/{id}', [\App\Http\Controllers\Admin\RecipientAdminController::class, 'destroy'])->name('admin.recipients.delete');
    Route::post('/recipients/toggle-status/{id}', [\App\Http\Controllers\Admin\RecipientAdminController::class, 'toggleStatus'])->name('admin.recipients.toggle-status');

    // Testimonials & Gallery CRUD
    Route::post('/testimonials/save', [DashboardController::class, 'saveTestimonial'])->name('admin.testimonials.save');
    Route::post('/testimonials/delete/{id}', [DashboardController::class, 'deleteTestimonial'])->name('admin.testimonials.delete');
    Route::post('/gallery/save', [DashboardController::class, 'saveGalleryItem'])->name('admin.gallery.save');
    Route::post('/gallery/delete/{id}', [DashboardController::class, 'deleteGalleryItem'])->name('admin.gallery.delete');

    // Legal Pages Manager
    Route::post('/legal/save', [DashboardController::class, 'saveLegalPages'])->name('admin.legal.save');
});
