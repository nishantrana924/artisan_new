@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-8 pb-16 px-4 md:px-12 bg-white dark:bg-[#0c0c0c] text-black dark:text-white relative">
    <div class="max-w-7xl mx-auto">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-black dark:hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('events.index') }}" class="hover:text-black dark:hover:text-white transition-colors">Categories</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-black dark:text-white font-bold">{{ $currentCategory['title'] ?? 'Category' }}</span>
        </nav>

        <!-- Category Banner Hero Header -->
        <div class="relative rounded-sm overflow-hidden mb-12 border border-gray-200 dark:border-white/10 bg-white dark:bg-[#121212] shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12 items-center">
                <!-- Text Content -->
                <div class="lg:col-span-7 p-6 sm:p-10 flex flex-col justify-center gap-4 text-left">
                    <span class="inline-flex items-center gap-2 text-xs font-bold text-gold uppercase tracking-widest bg-gold/10 px-3 py-1 rounded-full w-max border border-gold/30">
                        <i class="fa-solid fa-sparkles"></i> Artizen Celebration Packages
                    </span>

                    <h1 class="font-heading font-extrabold text-3xl sm:text-4xl lg:text-5xl uppercase tracking-tight text-gray-900 dark:text-white leading-tight">
                        {{ $currentCategory['title'] ?? 'Event Setup' }}
                    </h1>

                    <p class="text-sm md:text-base text-gray-600 dark:text-gray-300 font-normal leading-relaxed max-w-2xl">
                        {{ $currentCategory['desc'] ?? 'Browse and book curated celebration setups for ' . ($currentCategory['title'] ?? 'events') . ' in Indore. Premium decoration, sound, lighting, and coordination included.' }}
                    </p>

                    <div class="flex items-center gap-4 pt-2">
                        <div class="flex items-center gap-2 bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 px-3.5 py-1.5 rounded-sm text-xs font-bold">
                            <i class="fa-solid fa-box text-gold"></i>
                            <span>{{ count($categoryPackages) }} {{ count($categoryPackages) === 1 ? 'Package' : 'Packages Available' }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 px-3.5 py-1.5 rounded-sm text-xs font-bold text-emerald-800 dark:text-emerald-400">
                            <i class="fa-solid fa-shield-check"></i>
                            <span>Instant Booking Indore</span>
                        </div>
                    </div>
                </div>

                <!-- Banner Image -->
                <div class="lg:col-span-5 h-64 lg:h-80 relative overflow-hidden bg-gray-100 dark:bg-white/5 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-white/10">
                    <img src="{{ !empty($currentCategory['image']) ? $currentCategory['image'] : asset('assets/images/hero/1.jpg') }}" alt="{{ $currentCategory['title'] ?? '' }}" class="w-full h-full object-cover" onerror="this.src='/assets/images/hero/1.jpg'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                </div>
            </div>
        </div>

        <!-- Sticky Category Navigation Bar -->
        <div class="category-sticky-nav-wrapper mb-12">
            <div class="category-sticky-nav scrollbar-hide flex items-center gap-3 overflow-x-auto pb-2">
                <a href="{{ route('events.index') }}" class="px-4 py-2 rounded-sm text-xs font-bold shrink-0 bg-white dark:bg-[#121212] text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-white/10 hover:border-gold transition-all flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-xs"></i> All Categories
                </a>
                @foreach($categoriesList as $cat)
                    @if($cat['active'] ?? true)
                        @php
                            $cSlug = $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['title'] ?? '');
                            $isCurrent = ($cSlug === ($currentCategory['slug'] ?? ''));
                        @endphp
                        <a href="{{ route('category.show', $cSlug) }}" class="px-4 py-2 rounded-sm text-xs font-bold shrink-0 transition-all flex items-center gap-2 {{ $isCurrent ? 'bg-[#FFD600] text-[#171719] shadow-md font-extrabold' : 'bg-white dark:bg-[#121212] text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-white/10 hover:border-gold' }}">
                            <span>{{ $cat['title'] ?? '' }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Category Packages Grid -->
        @if(count($categoryPackages) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 text-left">
                @foreach($categoryPackages as $pkg)
                    @php
                        $priceFormatted = '₹' . number_format($pkg['price']);
                        $mrpFormatted = !empty($pkg['original_price']) ? ('₹' . number_format($pkg['original_price'])) : null;
                    @endphp
                    <div class="bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 rounded-sm overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            <!-- Package Image -->
                            <div class="h-52 relative overflow-hidden bg-gray-100 dark:bg-white/5 border-b border-gray-100 dark:border-white/5 experience-card-img-container" data-card-images='@json($pkg['all_images'] ?? [$pkg['image']])'>
                                <img src="{{ $pkg['image'] }}" alt="{{ $pkg['name'] }}" class="w-full h-full object-cover experience-card-img primary-img" onerror="this.src='/assets/images/hero/1.jpg'">
                                @if(!empty($pkg['secondary_image']) && $pkg['secondary_image'] !== $pkg['image'])
                                    <img src="{{ $pkg['secondary_image'] }}" alt="{{ $pkg['name'] }}" class="w-full h-full object-cover experience-card-img secondary-img" onerror="this.src='/assets/images/hero/1.jpg'">
                                @endif
                                <span class="absolute top-3 left-3 bg-black/80 backdrop-blur-md text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-sm tracking-wider z-10">
                                    {{ $currentCategory['title'] ?? 'Setup' }}
                                </span>
                                @if(!empty($pkg['badge']))
                                    <span class="absolute top-3 right-3 bg-[#FFD600] text-[#171719] text-[10px] font-extrabold uppercase px-2 py-1 rounded-sm tracking-wider shadow-sm z-10">
                                        {{ $pkg['badge'] }}
                                    </span>
                                @endif
                                @if(!empty($pkg['all_images']) && count($pkg['all_images']) > 1)
                                    <div class="card-img-indicators">
                                        @foreach($pkg['all_images'] as $i => $img)
                                            <span class="card-img-dot {{ $i === 0 ? 'active' : '' }}"></span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Package Details -->
                            <div class="p-6 flex flex-col gap-3">
                                <h3 class="font-heading font-extrabold text-xl text-gray-900 dark:text-white leading-tight">
                                    {{ $pkg['name'] }}
                                </h3>

                                <p class="text-xs text-gray-600 dark:text-gray-400 font-normal leading-relaxed line-clamp-2">
                                    {{ $pkg['desc'] }}
                                </p>

                                <!-- Price Box -->
                                <div class="flex items-baseline gap-2 my-1 bg-gray-50 dark:bg-white/5 p-3 rounded-sm border border-gray-100 dark:border-white/5">
                                    <span class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $priceFormatted }}</span>
                                    @if($mrpFormatted)
                                        <span class="text-xs text-gray-400 line-through font-medium">MRP {{ $mrpFormatted }}</span>
                                    @endif
                                </div>

                                <!-- Inclusions List -->
                                @if(!empty($pkg['inclusions']) && count($pkg['inclusions']) > 0)
                                    <div class="border-t border-gray-100 dark:border-white/5 pt-3">
                                        <span class="text-[10px] font-bold text-gold uppercase tracking-widest block mb-2">
                                            What's Included
                                        </span>
                                        <ul class="flex flex-col gap-1.5 text-xs text-gray-700 dark:text-gray-300 font-medium">
                                            @foreach(array_slice($pkg['inclusions'], 0, 5) as $inc)
                                                <li class="flex items-start gap-2">
                                                    <i class="fa-solid fa-check text-emerald-600 text-xs mt-0.5 shrink-0"></i>
                                                    <span>{{ $inc }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="px-6 py-4 bg-gray-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-white/5 flex items-center justify-between mt-auto">
                            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase">Offline Payment</span>
                            <a href="{{ route('booking.index', ['category' => $currentCategory['title'] ?? '', 'package' => $pkg['name'], 'price' => $pkg['price']]) }}" class="px-5 py-2.5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-bold rounded-sm shadow-md transition-all flex items-center gap-2">
                                <i class="fa-solid fa-calendar-check"></i> Book Package
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 p-12 rounded-sm text-center flex flex-col items-center justify-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gold/10 text-gold border border-gold/20 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">No Packages Available</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md">Packages for {{ $currentCategory['title'] ?? 'this category' }} are currently being updated by the Artizen team.</p>
                <a href="{{ route('events.index') }}" class="px-5 py-2.5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] text-xs font-bold rounded-sm shadow-md">
                    Explore All Categories
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
