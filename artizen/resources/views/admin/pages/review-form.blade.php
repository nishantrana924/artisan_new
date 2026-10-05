@extends('layouts.admin')

@section('page_title', $isEdit ? 'Edit Review #' . $review->id : 'Add New Review')
@section('page_heading', $isEdit ? 'Edit Review' : 'Add New Review')
@section('page_subheading', $isEdit ? 'Update verified celebration story, ratings, and placement controls' : 'Create a verified customer review with independent page visibility')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Navigation / Breadcrumbs -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.reviews') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-2xs">
            <i class="fa-solid fa-arrow-left"></i> Back to Reviews
        </a>

        @if($isEdit)
            <div class="flex items-center gap-2">
                <span class="text-xs font-mono font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded">ID: {{ $review->id }}</span>
                <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="button" 
                            onclick="confirmDelete(this, 'Review #{{ $review->id }}')"
                            class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-trash-can"></i> Delete
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Main Form Card -->
    <form action="{{ route('admin.reviews.save') }}" method="POST" id="review-editor-page-form" class="space-y-6">
        @csrf
        @if($isEdit)
            <input type="hidden" name="id" value="{{ $review->id }}">
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT 2 COLUMNS: Story Details -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. Customer & Event Information -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-2xs space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-amber-500"></i> Customer & Celebration Details
                        </h2>
                        <p class="text-xs text-gray-500">Provide the client details and their celebration occasion.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Customer Name -->
                        <div>
                            <label for="form-author" class="block text-xs font-bold text-gray-700 mb-1">
                                Customer Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="form-author" name="author" value="{{ old('author', $review->author) }}" required
                                   placeholder="e.g. Priya Sharma"
                                   class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold text-gray-900 focus:bg-white focus:border-gray-900 focus:outline-none transition-colors">
                            @error('author')
                                <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="form-email" class="block text-xs font-bold text-gray-700 mb-1">
                                Customer Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="form-email" name="email" value="{{ old('email', $review->email) }}" required
                                   placeholder="e.g. priya@example.com"
                                   class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold text-gray-900 focus:bg-white focus:border-gray-900 focus:outline-none transition-colors">
                            @error('email')
                                <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City / Location -->
                        <div>
                            <label for="form-location" class="block text-xs font-bold text-gray-700 mb-1">
                                City / Area
                            </label>
                            <input type="text" id="form-location" name="location" value="{{ old('location', $review->location ?: 'Indore, MP') }}"
                                   placeholder="Indore, MP"
                                   class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold text-gray-900 focus:bg-white focus:border-gray-900 focus:outline-none transition-colors">
                        </div>

                        <!-- Associated Celebration Package -->
                        <div>
                            <label for="form-package" class="block text-xs font-bold text-gray-700 mb-1">
                                Celebration Package
                            </label>
                            <select id="form-package" name="package_slug"
                                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold text-gray-900 focus:bg-white focus:border-gray-900 focus:outline-none transition-colors">
                                <option value="">-- General / Not Package-Specific --</option>
                                @foreach($packageOptions as $pkg)
                                    <option value="{{ $pkg['slug'] }}" {{ old('package_slug', $review->package_slug) === $pkg['slug'] ? 'selected' : '' }}>
                                        {{ $pkg['title'] }} ({{ $pkg['category'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 2. Review Content & Rating -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-2xs space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-star text-amber-500"></i> Star Rating & Testimonial Story
                        </h2>
                        <p class="text-xs text-gray-500">The verified rating and full customer commentary.</p>
                    </div>

                    <!-- Star Rating Interactive Selector -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">
                            Customer Rating <span class="text-rose-500">*</span>
                        </label>
                        <input type="hidden" name="rating" id="form-rating-val" value="{{ old('rating', $review->rating ?: 5) }}">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2" id="star-rating-container">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setFormRating({{ $i }})" class="star-btn cursor-pointer transition-transform hover:scale-125 focus:outline-none" data-val="{{ $i }}">
                                        <i class="fa-solid fa-star text-lg {{ ($review->rating ?: 5) >= $i ? 'text-amber-400' : 'text-gray-300' }}"></i>
                                    </button>
                                @endfor
                            </div>
                            <span id="star-rating-label" class="text-xs font-bold text-gray-700 bg-amber-50 text-amber-800 border border-amber-200 px-2.5 py-1.5 rounded-lg">
                                {{ old('rating', $review->rating ?: 5) }}.0 / 5.0 Stars
                            </span>
                        </div>
                    </div>


                    <!-- Review Body -->
                    <div>
                        <label for="form-review" class="block text-xs font-bold text-gray-700 mb-1">
                            Customer Feedback / Review Body <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="form-review" name="review" rows="5" required
                                  placeholder="Write the customer's full story or feedback..."
                                  class="w-full bg-gray-50 border border-gray-300 rounded-lg p-3 text-xs font-medium text-gray-900 focus:bg-white focus:border-gray-900 focus:outline-none transition-colors leading-relaxed">{{ old('review', $review->review) }}</textarea>
                        @error('review')
                            <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Avatar / Initial -->
                    <div>
                        <label for="form-avatar" class="block text-xs font-bold text-gray-700 mb-1">
                            Avatar Image URL <span class="text-gray-400 font-normal">(Optional — defaults to elegant neutral initial avatar)</span>
                        </label>
                        <input type="text" id="form-avatar" name="avatar" value="{{ old('avatar', $review->avatar) }}"
                               placeholder="https://images.unsplash.com/... or leave blank"
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold text-gray-900 focus:bg-white focus:border-gray-900 focus:outline-none transition-colors">
                    </div>
                </div>

            </div>

            <!-- RIGHT 1 COLUMN: Visibility & Placement Controls -->
            <div class="space-y-6">

                <!-- Visibility Matrix Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-2xs space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-sliders text-amber-500"></i> Page Visibility & Placements
                        </h2>
                        <p class="text-xs text-gray-500">Select where this celebration review appears publicly.</p>
                    </div>

                    <!-- 1. Master Publication Status -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900">Master Status</span>
                            <span id="page-lbl-is_active" class="text-[11px] font-bold {{ old('is_active', $review->is_active ?? true) ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ old('is_active', $review->is_active ?? true) ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <input type="hidden" name="is_active" id="page-input-is_active" value="{{ old('is_active', $review->is_active ?? true) ? '1' : '0' }}">
                        <div class="flex rounded-lg bg-white p-0.5 border border-gray-200 shadow-2xs">
                            <button type="button" id="page-btn-is_active-on" onclick="setPageToggle('is_active', true)"
                                    class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer {{ old('is_active', $review->is_active ?? true) ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }} flex items-center justify-center gap-1">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Active
                            </button>
                            <button type="button" id="page-btn-is_active-off" onclick="setPageToggle('is_active', false)"
                                    class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer {{ !old('is_active', $review->is_active ?? true) ? 'bg-rose-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }} flex items-center justify-center gap-1">
                                <i class="fa-solid fa-ban text-[10px]"></i> Inactive
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-500 leading-tight">When Inactive, this review will not be shown on any public page.</p>
                    </div>

                    <!-- 2. Homepage Placement -->
                    <div id="page-box-show_on_home" class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-house text-amber-500 text-[11px]"></i> Homepage Carousel
                            </span>
                            <span id="page-lbl-show_on_home" class="text-[11px] font-bold {{ old('show_on_home', $review->show_on_home ?? true) ? 'text-amber-700' : 'text-gray-500' }}">
                                {{ old('show_on_home', $review->show_on_home ?? true) ? 'On Home' : 'Off Home' }}
                            </span>
                        </div>
                        <input type="hidden" name="show_on_home" id="page-input-show_on_home" value="{{ old('show_on_home', $review->show_on_home ?? true) ? '1' : '0' }}">
                        <div class="flex rounded-lg bg-white p-0.5 border border-gray-200 shadow-2xs">
                            <button type="button" id="page-btn-show_on_home-on" onclick="setPageToggle('show_on_home', true)"
                                    class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer {{ old('show_on_home', $review->show_on_home ?? true) ? 'bg-amber-500 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }} flex items-center justify-center gap-1">
                                <i class="fa-solid fa-check text-[10px]"></i> Home: ON
                            </button>
                            <button type="button" id="page-btn-show_on_home-off" onclick="setPageToggle('show_on_home', false)"
                                    class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer {{ !old('show_on_home', $review->show_on_home ?? true) ? 'bg-gray-800 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }} flex items-center justify-center gap-1">
                                <i class="fa-solid fa-xmark text-[10px]"></i> Home: OFF
                            </button>
                        </div>
                    </div>

                    <!-- 3. Dedicated /reviews Page Placement -->
                    <div id="page-box-show_on_reviews_page" class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-file-lines text-blue-500 text-[11px]"></i> Dedicated /reviews Page
                            </span>
                            <span id="page-lbl-show_on_reviews_page" class="text-[11px] font-bold {{ old('show_on_reviews_page', $review->show_on_reviews_page ?? true) ? 'text-blue-700' : 'text-gray-500' }}">
                                {{ old('show_on_reviews_page', $review->show_on_reviews_page ?? true) ? 'Visible' : 'Hidden' }}
                            </span>
                        </div>
                        <input type="hidden" name="show_on_reviews_page" id="page-input-show_on_reviews_page" value="{{ old('show_on_reviews_page', $review->show_on_reviews_page ?? true) ? '1' : '0' }}">
                        <div class="flex rounded-lg bg-white p-0.5 border border-gray-200 shadow-2xs">
                            <button type="button" id="page-btn-show_on_reviews_page-on" onclick="setPageToggle('show_on_reviews_page', true)"
                                    class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer {{ old('show_on_reviews_page', $review->show_on_reviews_page ?? true) ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }} flex items-center justify-center gap-1">
                                <i class="fa-solid fa-check text-[10px]"></i> Reviews: ON
                            </button>
                            <button type="button" id="page-btn-show_on_reviews_page-off" onclick="setPageToggle('show_on_reviews_page', false)"
                                    class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer {{ !old('show_on_reviews_page', $review->show_on_reviews_page ?? true) ? 'bg-gray-800 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }} flex items-center justify-center gap-1">
                                <i class="fa-solid fa-xmark text-[10px]"></i> Reviews: OFF
                            </button>
                        </div>
                    </div>

                    <!-- 4. Associated Event Details Placement -->
                    <div id="page-box-show_on_event_details" class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-bullseye text-purple-500 text-[11px]"></i> Event Details Page
                            </span>
                            <span id="page-lbl-show_on_event_details" class="text-[11px] font-bold {{ old('show_on_event_details', $review->show_on_event_details ?? true) ? 'text-purple-700' : 'text-gray-500' }}">
                                {{ old('show_on_event_details', $review->show_on_event_details ?? true) ? 'Visible' : 'Hidden' }}
                            </span>
                        </div>
                        <input type="hidden" name="show_on_event_details" id="page-input-show_on_event_details" value="{{ old('show_on_event_details', $review->show_on_event_details ?? true) ? '1' : '0' }}">
                        <div class="flex rounded-lg bg-white p-0.5 border border-gray-200 shadow-2xs">
                            <button type="button" id="page-btn-show_on_event_details-on" onclick="setPageToggle('show_on_event_details', true)"
                                    class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer {{ old('show_on_event_details', $review->show_on_event_details ?? true) ? 'bg-purple-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }} flex items-center justify-center gap-1">
                                <i class="fa-solid fa-check text-[10px]"></i> Event: ON
                            </button>
                            <button type="button" id="page-btn-show_on_event_details-off" onclick="setPageToggle('show_on_event_details', false)"
                                    class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer {{ !old('show_on_event_details', $review->show_on_event_details ?? true) ? 'bg-gray-800 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }} flex items-center justify-center gap-1">
                                <i class="fa-solid fa-xmark text-[10px]"></i> Event: OFF
                            </button>
                        </div>
                    </div>

                    <!-- Live Explainer Box -->
                    <div id="page-placement-explainer" class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl p-2.5 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info shrink-0"></i>
                        <span id="page-placement-text">Active & Visible across selected placements.</span>
                    </div>

                    <!-- Display Sort Order -->
                    <div>
                        <label for="form-sort-order" class="block text-xs font-bold text-gray-700 mb-1">
                            Display Sort Order
                        </label>
                        <input type="number" id="form-sort-order" name="sort_order" value="{{ old('sort_order', $review->sort_order ?: 1) }}"
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold text-gray-900 focus:bg-white focus:border-gray-900 focus:outline-none transition-colors">
                        <p class="text-[10px] text-gray-400 mt-1">Lower numbers appear first when custom sorting is applied.</p>
                    </div>

                </div>

                <!-- Form Action Buttons -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-2xs space-y-3">
                    <button type="submit" class="w-full py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-sm transition-all cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Update Review' : 'Save Review' }}
                    </button>
                    <a href="{{ route('admin.reviews') }}" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition-colors flex items-center justify-center cursor-pointer">
                        Cancel
                    </a>
                </div>

            </div>

        </div>

    </form>

</div>
@endsection

@push('page_scripts')
<script>
    const packageOptionsDB = @json($packageOptions);

    function setFormRating(val) {
        document.getElementById('form-rating-val').value = val;
        const btns = document.querySelectorAll('#star-rating-container .star-btn');
        btns.forEach(btn => {
            const bVal = parseInt(btn.getAttribute('data-val'));
            const icon = btn.querySelector('i');
            if (bVal <= val) {
                icon.className = 'fa-solid fa-star text-lg text-amber-400';
            } else {
                icon.className = 'fa-solid fa-star text-lg text-gray-300';
            }
        });
        const labels = {
            1: '1.0 / 5.0 - Poor',
            2: '2.0 / 5.0 - Fair',
            3: '3.0 / 5.0 - Good',
            4: '4.0 / 5.0 - Very Good',
            5: '5.0 / 5.0 - Outstanding'
        };
        const lbl = document.getElementById('star-rating-label');
        if (lbl) lbl.textContent = labels[val] || `${val}.0 / 5.0 Stars`;
    }

    function setPageToggle(field, value) {
        const hiddenInput = document.getElementById(`page-input-${field}`);
        if (hiddenInput) hiddenInput.value = value ? '1' : '0';

        const btnOn = document.getElementById(`page-btn-${field}-on`);
        const btnOff = document.getElementById(`page-btn-${field}-off`);
        const lbl = document.getElementById(`page-lbl-${field}`);

        if (field === 'is_active') {
            if (value) {
                if (btnOn) btnOn.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer bg-emerald-600 text-white shadow-xs flex items-center justify-center gap-1";
                if (btnOff) btnOff.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900 flex items-center justify-center gap-1";
                if (lbl) { lbl.textContent = "Active"; lbl.className = "text-[11px] font-bold text-emerald-700"; }
                ['show_on_home', 'show_on_reviews_page', 'show_on_event_details'].forEach(f => {
                    const box = document.getElementById(`page-box-${f}`);
                    if (box) box.classList.remove('opacity-40', 'pointer-events-none');
                });
            } else {
                if (btnOn) btnOn.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900 flex items-center justify-center gap-1";
                if (btnOff) btnOff.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer bg-rose-600 text-white shadow-xs flex items-center justify-center gap-1";
                if (lbl) { lbl.textContent = "Inactive (Hidden)"; lbl.className = "text-[11px] font-bold text-rose-600"; }
                ['show_on_home', 'show_on_reviews_page', 'show_on_event_details'].forEach(f => {
                    const box = document.getElementById(`page-box-${f}`);
                    if (box) box.classList.add('opacity-40', 'pointer-events-none');
                });
            }
        } else if (field === 'show_on_home') {
            if (value) {
                if (btnOn) btnOn.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer bg-amber-500 text-white shadow-xs flex items-center justify-center gap-1";
                if (btnOff) btnOff.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900 flex items-center justify-center gap-1";
                if (lbl) { lbl.textContent = "On Home"; lbl.className = "text-[11px] font-bold text-amber-700"; }
            } else {
                if (btnOn) btnOn.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900 flex items-center justify-center gap-1";
                if (btnOff) btnOff.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer bg-gray-800 text-white shadow-xs flex items-center justify-center gap-1";
                if (lbl) { lbl.textContent = "Off Home"; lbl.className = "text-[11px] font-bold text-gray-500"; }
            }
        } else if (field === 'show_on_reviews_page') {
            if (value) {
                if (btnOn) btnOn.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white shadow-xs flex items-center justify-center gap-1";
                if (btnOff) btnOff.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900 flex items-center justify-center gap-1";
                if (lbl) { lbl.textContent = "Visible"; lbl.className = "text-[11px] font-bold text-blue-700"; }
            } else {
                if (btnOn) btnOn.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900 flex items-center justify-center gap-1";
                if (btnOff) btnOff.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer bg-gray-800 text-white shadow-xs flex items-center justify-center gap-1";
                if (lbl) { lbl.textContent = "Hidden"; lbl.className = "text-[11px] font-bold text-gray-500"; }
            }
        } else if (field === 'show_on_event_details') {
            if (value) {
                if (btnOn) btnOn.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer bg-purple-600 text-white shadow-xs flex items-center justify-center gap-1";
                if (btnOff) btnOff.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900 flex items-center justify-center gap-1";
                if (lbl) { lbl.textContent = "Visible"; lbl.className = "text-[11px] font-bold text-purple-700"; }
            } else {
                if (btnOn) btnOn.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900 flex items-center justify-center gap-1";
                if (btnOff) btnOff.className = "flex-1 py-1.5 rounded-md text-xs font-bold transition-all cursor-pointer bg-gray-800 text-white shadow-xs flex items-center justify-center gap-1";
                if (lbl) { lbl.textContent = "Hidden"; lbl.className = "text-[11px] font-bold text-gray-500"; }
            }
        }

        updateExplainer();
    }

    function updateExplainer() {
        const isActiveEl = document.getElementById('page-input-is_active');
        const isHomeEl = document.getElementById('page-input-show_on_home');
        const isReviewsEl = document.getElementById('page-input-show_on_reviews_page');
        const isEventEl = document.getElementById('page-input-show_on_event_details');
        const explainerBox = document.getElementById('page-placement-explainer');
        const explainerText = document.getElementById('page-placement-text');

        if (!explainerBox || !explainerText) return;

        const isActive = isActiveEl ? isActiveEl.value === '1' : true;
        const isHome = isHomeEl ? isHomeEl.value === '1' : false;
        const isReviews = isReviewsEl ? isReviewsEl.value === '1' : false;
        const isEvent = isEventEl ? isEventEl.value === '1' : false;

        if (!isActive) {
            explainerBox.className = "text-[11px] font-semibold text-rose-800 bg-rose-50 border border-rose-200 rounded-xl p-2.5 flex items-center gap-2";
            explainerText.innerHTML = "<strong>Inactive:</strong> Completely hidden from all pages (Homepage, /reviews, and Event Details).";
            return;
        }

        let locs = [];
        if (isHome) locs.push("Homepage Carousel");
        if (isReviews) locs.push("/reviews Page");
        if (isEvent) locs.push("Associated Event Details");

        if (locs.length === 0) {
            explainerBox.className = "text-[11px] font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl p-2.5 flex items-center gap-2";
            explainerText.innerHTML = "<strong>Active, but no placement selected:</strong> Will not appear until at least one switch is ON.";
        } else {
            explainerBox.className = "text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl p-2.5 flex items-center gap-2";
            explainerText.innerHTML = `<strong>Active & Visible on:</strong> ${locs.join(" + ")}.`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateExplainer();
    });
</script>
@endpush
