<section id="categories" class="py-20 relative scroll-mt-20 md:scroll-mt-24 bg-[#FAF9F6] dark:bg-[#0c0c0c]">
        <div class="w-full px-4 md:px-12">

            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span
                    class="inline-flex items-center gap-1.5 text-[10px] md:text-xs font-bold text-gold uppercase tracking-wider mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold"></span> CATEGORIES
                </span>
                <h2
                    class="font-heading font-extrabold text-3xl md:text-5xl uppercase tracking-tight text-black mb-4 dark:text-white">
                    Browse By Category
                </h2>
            </div>

            @php
                $catList = $categories ?? [];
                if (empty($catList)) {
                    $catList = \App\Services\JsonStorageService::read('categories.json');
                }
            @endphp

            <!-- Category Marquees (Row 1 and Row 2) -->
            <div class="setup-category-row flex flex-col gap-6 overflow-hidden">
                <!-- Row 1: Left to Right (direction-right) -->
                <div class="marquee-row-wrapper">
                    <div class="marquee-track direction-right">
                        <div class="marquee-content">
                            @foreach($catList as $cat)
                                @if($cat['active'] ?? true)
                                    @php
                                        $catSlug = $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['title'] ?? '');
                                        $catImage = !empty($cat['image']) ? $cat['image'] : asset('assets/images/ic/artizen (2).png');
                                    @endphp
                                    <a href="{{ route('category.show', $catSlug) }}" class="category-tile">
                                        <div class="category-icon">
                                            <img src="{{ $catImage }}" alt="{{ $cat['title'] ?? 'Category' }}" class="w-full h-full object-cover rounded-full">
                                        </div>
                                        <span class="category-label">{{ $cat['title'] ?? '' }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        <!-- Identical Duplicate for Loop -->
                        <div class="marquee-content" aria-hidden="true">
                            @foreach($catList as $cat)
                                @if($cat['active'] ?? true)
                                    @php
                                        $catSlug = $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['title'] ?? '');
                                        $catImage = !empty($cat['image']) ? $cat['image'] : asset('assets/images/ic/artizen (2).png');
                                    @endphp
                                    <a href="{{ route('category.show', $catSlug) }}" class="category-tile">
                                        <div class="category-icon">
                                            <img src="{{ $catImage }}" alt="{{ $cat['title'] ?? 'Category' }}" class="w-full h-full object-cover rounded-full">
                                        </div>
                                        <span class="category-label">{{ $cat['title'] ?? '' }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Row 2: Right to Left (direction-left) -->
                <div class="marquee-row-wrapper">
                    <div class="marquee-track direction-left">
                        <div class="marquee-content">
                            @foreach(array_reverse($catList) as $cat)
                                @if($cat['active'] ?? true)
                                    @php
                                        $catSlug = $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['title'] ?? '');
                                        $catImage = !empty($cat['image']) ? $cat['image'] : asset('assets/images/ic/artizen (2).png');
                                    @endphp
                                    <a href="{{ route('category.show', $catSlug) }}" class="category-tile">
                                        <div class="category-icon">
                                            <img src="{{ $catImage }}" alt="{{ $cat['title'] ?? 'Category' }}" class="w-full h-full object-cover rounded-full">
                                        </div>
                                        <span class="category-label">{{ $cat['title'] ?? '' }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        <!-- Identical Duplicate for Loop -->
                        <div class="marquee-content" aria-hidden="true">
                            @foreach(array_reverse($catList) as $cat)
                                @if($cat['active'] ?? true)
                                    @php
                                        $catSlug = $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['title'] ?? '');
                                        $catImage = !empty($cat['image']) ? $cat['image'] : asset('assets/images/ic/artizen (2).png');
                                    @endphp
                                    <a href="{{ route('category.show', $catSlug) }}" class="category-tile">
                                        <div class="category-icon">
                                            <img src="{{ $catImage }}" alt="{{ $cat['title'] ?? 'Category' }}" class="w-full h-full object-cover rounded-full">
                                        </div>
                                        <span class="category-label">{{ $cat['title'] ?? '' }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>