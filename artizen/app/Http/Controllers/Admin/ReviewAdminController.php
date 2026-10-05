<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Services\CelebrationCatalogService;
use App\Services\JsonStorageService;
use Illuminate\Http\Request;

class ReviewAdminController extends Controller
{
    /**
     * Display the dedicated admin review management page.
     */
    public function index(Request $request)
    {
        $search = trim((string)$request->query('search', ''));
        $status = $request->query('status', 'all');
        $visibility = $request->query('visibility', 'all');
        $packageFilter = $request->query('package', 'all');
        $ratingFilter = $request->query('rating', 'all');

        $query = Testimonial::query();

        // Search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('author', 'like', "%{$search}%")
                  ->orWhere('review', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('event_type', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // Visibility filter
        if ($visibility === 'home') {
            $query->where('show_on_home', true);
        } elseif ($visibility === 'reviews_page') {
            $query->where('show_on_reviews_page', true);
        } elseif ($visibility === 'event_details') {
            $query->where('show_on_event_details', true);
        }

        // Package slug filter
        if ($packageFilter !== 'all') {
            $query->where('package_slug', $packageFilter);
        }

        // Star rating filter
        if ($ratingFilter !== 'all' && is_numeric($ratingFilter)) {
            $query->where('rating', (int)$ratingFilter);
        }

        $reviews = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(16)->withQueryString();

        // Database statistics
        $allReviews = Testimonial::all();
        $totalCount = $allReviews->count();
        $activeCount = $allReviews->where('is_active', true)->count();
        $inactiveCount = $allReviews->where('is_active', false)->count();
        $homeCount = $allReviews->where('is_active', true)->where('show_on_home', true)->count();
        $reviewsPageCount = $allReviews->where('is_active', true)->where('show_on_reviews_page', true)->count();
        $eventDetailsCount = $allReviews->where('is_active', true)->where('show_on_event_details', true)->count();
        $avgRating = $totalCount > 0 ? round($allReviews->avg('rating'), 1) : 5.0;

        // Retrieve celebration packages for selector dropdown
        $allPackages = CelebrationCatalogService::getAllPackages();
        $packageOptions = [];
        foreach ($allPackages as $p) {
            $packageOptions[$p['slug']] = [
                'slug'     => $p['slug'],
                'title'    => $p['title'],
                'category' => $p['category'] ?? 'Celebration',
            ];
        }

        return view('admin.pages.reviews', compact(
            'reviews',
            'totalCount',
            'activeCount',
            'inactiveCount',
            'homeCount',
            'reviewsPageCount',
            'eventDetailsCount',
            'avgRating',
            'packageOptions',
            'search',
            'status',
            'visibility',
            'packageFilter',
            'ratingFilter'
        ), ['adminActivePage' => 'reviews']);
    }

    /**
     * Display the separate create review form page.
     */
    public function create()
    {
        $allPackages = CelebrationCatalogService::getAllPackages();
        $packageOptions = [];
        foreach ($allPackages as $p) {
            $packageOptions[$p['slug']] = [
                'slug'     => $p['slug'],
                'title'    => $p['title'],
                'category' => $p['category'] ?? 'Celebration',
            ];
        }

        $review = new Testimonial([
            'rating'                => 5,
            'location'              => 'Indore, MP',
            'is_active'             => true,
            'show_on_home'          => true,
            'show_on_reviews_page'  => true,
            'show_on_event_details' => true,
            'sort_order'            => ((Testimonial::max('sort_order') ?? 0) + 1),
        ]);

        return view('admin.pages.review-form', [
            'isEdit'          => false,
            'review'          => $review,
            'packageOptions'  => $packageOptions,
            'adminActivePage' => 'reviews',
        ]);
    }

    /**
     * Display the separate edit review form page.
     */
    public function edit($id)
    {
        $review = Testimonial::findOrFail($id);

        $allPackages = CelebrationCatalogService::getAllPackages();
        $packageOptions = [];
        foreach ($allPackages as $p) {
            $packageOptions[$p['slug']] = [
                'slug'     => $p['slug'],
                'title'    => $p['title'],
                'category' => $p['category'] ?? 'Celebration',
            ];
        }

        return view('admin.pages.review-form', [
            'isEdit'          => true,
            'review'          => $review,
            'packageOptions'  => $packageOptions,
            'adminActivePage' => 'reviews',
        ]);
    }

    /**
     * Store or update a review.
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'id'                    => 'nullable|string|max:50',
            'author'                => 'required|string|max:255',
            'email'                 => 'required|email|max:255',
            'location'              => 'nullable|string|max:255',
            'event_type'            => 'nullable|string|max:255',
            'package_slug'          => 'nullable|string|max:150',
            'review'                => 'required|string|max:3000',
            'rating'                => 'required|integer|min:1|max:5',
            'avatar'                => 'nullable|string|max:1000',
            'sort_order'            => 'nullable|integer',
            'is_active'             => 'nullable',
            'show_on_home'          => 'nullable',
            'show_on_reviews_page'  => 'nullable',
            'show_on_event_details' => 'nullable',
        ]);

        $id = $request->input('id');
        $author = trim($validated['author']);
        $email = trim($validated['email']);
        $location = trim($request->input('location', 'Indore, MP')) ?: 'Indore, MP';
        $packageSlug = trim((string)$request->input('package_slug', '')) ?: null;
        $reviewBody = trim($validated['review']);
        $rating = max(1, min(5, (int)$validated['rating']));
        $sortOrder = (int)$request->input('sort_order', 0);

        // Derive event_type from package_slug if not explicitly provided
        $eventType = trim((string)$request->input('event_type', ''));
        if (empty($eventType) && !empty($packageSlug)) {
            $eventType = ucwords(str_replace('-', ' ', $packageSlug));
        }
        if (empty($eventType)) {
            $eventType = 'Celebration Setup';
        }

        // Determine visibility booleans safely
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;
        $showOnHome = $request->has('show_on_home') ? $request->boolean('show_on_home') : false;
        $showOnReviewsPage = $request->has('show_on_reviews_page') ? $request->boolean('show_on_reviews_page') : true;
        $showOnEventDetails = $request->has('show_on_event_details') ? $request->boolean('show_on_event_details') : false;

        $data = [
            'author'                => $author,
            'email'                 => $email,
            'location'              => $location,
            'event_type'            => $eventType,
            'package_slug'          => $packageSlug,
            'review'                => $reviewBody,
            'rating'                => $rating,
            'avatar'                => $request->input('avatar'),
            'is_verified'           => true,
            'is_active'             => $isActive,
            'show_on_home'          => $showOnHome,
            'show_on_reviews_page'  => $showOnReviewsPage,
            'show_on_event_details' => $showOnEventDetails,
            'sort_order'            => $sortOrder,
        ];

        if (!empty($id)) {
            $review = Testimonial::find($id);
            if ($review) {
                $review->update($data);
            } else {
                $data['id'] = $id;
                $review = Testimonial::create($data);
            }
        } else {
            $data['id'] = 'TST-' . time() . rand(10, 99);
            if ($sortOrder === 0) {
                $data['sort_order'] = ((Testimonial::max('sort_order') ?? 0) + 1);
            }
            $review = Testimonial::create($data);
        }

        // Synchronize storage
        $this->syncStorage();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review saved successfully!',
                'review'  => $review
            ]);
        }

        return redirect()->route('admin.reviews')->with('success', 'Review saved successfully!');
    }

    /**
     * Delete a review.
     */
    public function delete(Request $request, $id)
    {
        Testimonial::where('id', $id)->delete();
        $this->syncStorage();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully!'
            ]);
        }

        return redirect()->route('admin.reviews')->with('success', 'Review deleted successfully!');
    }

    /**
     * Quick AJAX toggle for independent visibility controls.
     */
    public function toggleVisibility(Request $request)
    {
        $validated = $request->validate([
            'id'    => 'required|string',
            'field' => 'required|string|in:is_active,show_on_home,show_on_reviews_page,show_on_event_details'
        ]);

        $review = Testimonial::find($validated['id']);
        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Review not found.'], 404);
        }

        $field = $validated['field'];
        $review->$field = !$review->$field;
        $review->save();

        $this->syncStorage();

        $labelMap = [
            'is_active'             => 'Publication Status',
            'show_on_home'          => 'Homepage Visibility',
            'show_on_reviews_page'  => 'Reviews Page Visibility',
            'show_on_event_details' => 'Event Details Visibility',
        ];

        $statusText = $review->$field ? 'Enabled' : 'Disabled';
        return response()->json([
            'success'   => true,
            'field'     => $field,
            'new_value' => (bool)$review->$field,
            'message'   => "{$labelMap[$field]} {$statusText} successfully!"
        ]);
    }

    /**
     * Helper to synchronize testimonials.json with database state.
     */
    protected function syncStorage(): void
    {
        $allFromDb = Testimonial::ordered()->get()->map(function($t) {
            return [
                'id'                    => $t->id,
                'author'                => $t->author,
                'name'                  => $t->author,
                'email'                 => $t->email,
                'location'              => $t->location,
                'event_type'            => $t->event_type,
                'package_slug'          => $t->package_slug,
                'review'                => $t->review,
                'content'               => $t->review,
                'rating'                => (int)$t->rating,
                'avatar'                => $t->avatar,
                'is_verified'           => (bool)$t->is_verified,
                'is_active'             => (bool)$t->is_active,
                'active'                => (bool)$t->is_active,
                'show_on_home'          => (bool)$t->show_on_home,
                'show_on_reviews_page'  => (bool)$t->show_on_reviews_page,
                'show_on_event_details' => (bool)$t->show_on_event_details,
                'sort_order'            => (int)$t->sort_order,
                'created_at'            => $t->created_at ? $t->created_at->format('d M Y') : date('d M Y'),
            ];
        })->toArray();

        JsonStorageService::write('testimonials.json', $allFromDb);
    }
}
