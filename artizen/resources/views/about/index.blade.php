@extends('layouts.app')

@section('title', 'About Us | Curated Celebrations in Indore | ARTIZEN')
@section('meta_description', 'Learn about Artizen, Indore\'s premier event booking platform for birthdays, proposals, anniversaries, house parties, and weddings with verified on-site execution and transparent offline pricing.')

@section('content')
<div class="min-h-screen bg-[#FCFBF8] text-gray-950 font-body select-none">

    <!-- ========================================================
         SECTION A: CLEAN MINIMAL HERO & BREADCRUMB
         ======================================================== -->
    <section class="border-b border-[#EFE7D8] bg-[#FAF7F2] py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs (Text only, no arrows) -->
            <nav class="text-xs text-gray-500 font-medium mb-4">
                <a href="{{ route('home') }}" class="text-gray-600">Home</a>
                <span class="mx-1.5 text-gray-400">/</span>
                <span class="text-gray-950 font-semibold">About Us</span>
            </nav>

            <div class="max-w-3xl">
                <h1 class="font-heading font-extrabold text-3xl sm:text-4xl md:text-5xl text-gray-950 tracking-tight leading-tight mb-3">
                    {{ $hero['title'] }}
                </h1>
                <p class="text-sm sm:text-base text-gray-600 font-normal leading-relaxed mb-6">
                    {{ $hero['subtitle'] }}
                </p>

                <!-- Clean Actions (Zero hover animation) -->
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('events.index') }}" 
                       class="inline-flex items-center justify-center bg-gray-950 text-white font-heading font-bold text-xs sm:text-[13px] px-5 py-2.5 rounded-xl cursor-pointer">
                        Explore Celebrations
                    </a>
                    <a href="https://wa.me/919131668156?text={{ urlencode('Hi Artizen, I would like to inquire about planning a celebration in Indore.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="inline-flex items-center justify-center bg-white text-gray-900 border border-[#E8DFC8] font-heading font-bold text-xs sm:text-[13px] px-5 py-2.5 rounded-xl cursor-pointer">
                        WhatsApp Concierge
                    </a>
                </div>
            </div>

        </div>
    </section>


    <!-- ========================================================
         SECTION B: CURATED PHOTOGRAPHY SHOWCASE (FULL-BLEED 2x2, ZERO EMPTY SPACING)
         ======================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 sm:gap-4 items-stretch">
            
            <!-- Main Featured Celebration Visual (Left 6 Columns, Full Height) -->
            <div class="lg:col-span-6 rounded-2xl overflow-hidden border border-[#E8DFC8] bg-white min-h-[300px] sm:min-h-[400px]">
                <img src="{{ $hero['main_image'] }}" 
                     alt="Artizen Celebration Setup Indore" 
                     class="w-full h-full object-cover block" 
                     loading="lazy">
            </div>

            <!-- Curated Celebration Grid (Right 6 Columns - 2x2 Grid, Completely Fills Height) -->
            <div class="lg:col-span-6 grid grid-cols-2 gap-3.5 sm:gap-4">
                @foreach($hero['gallery'] as $img)
                    <div class="rounded-2xl overflow-hidden border border-[#E8DFC8] bg-white aspect-[4/3]">
                        <img src="{{ $img }}" 
                             alt="Indore Celebration Setup" 
                             class="w-full h-full object-cover block" 
                             loading="lazy">
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    <!-- ========================================================
         SECTION C: KEY TRUST METRICS (PURE TYPOGRAPHY, ZERO ICONS)
         ======================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 sm:pb-16">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 lg:gap-4.5">
            @foreach($stats as $stat)
                <div class="bg-white border border-[#E8DFC8] rounded-2xl p-5 sm:p-6 text-left">
                    <div class="font-heading font-extrabold text-2xl sm:text-3xl md:text-4xl text-gray-950 tracking-tight leading-none mb-1.5">
                        {{ $stat['value'] }}
                    </div>
                    <div class="text-xs sm:text-sm font-heading font-bold text-gray-900">
                        {{ $stat['label'] }}
                    </div>
                    <div class="text-[11px] text-gray-500 font-normal mt-0.5">
                        {{ $stat['sub'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>


    <!-- ========================================================
         SECTION D: THE ARTIZEN DIFFERENCE (100% VISIBLE, ZERO DOTS)
         ======================================================== -->
    <section class="border-y border-[#EFE7D8] bg-[#FAF7F2] py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-2xl mb-8 text-left">
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-gray-950 tracking-tight mb-1.5">
                    A Better Way to Celebrate
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 font-normal">
                    We replaced vendor fragmentation and unpredictable costs with complete, transparent packages.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                
                <!-- The Traditional Way Card -->
                <div class="bg-[#FAF7F2] border border-[#E8DFC8] rounded-2xl p-6 sm:p-8 text-left flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-gray-400 block mb-1.5">
                            Traditional Planning
                        </span>
                        <h3 class="font-heading font-extrabold text-xl sm:text-2xl text-gray-950 mb-5">
                            Fragmented & Stressful
                        </h3>
                        <div class="divide-y divide-[#E8DFC8] text-xs sm:text-[13px] text-gray-600 font-normal">
                            @foreach($comparison['traditional'] as $point)
                                <div class="py-3 first:pt-0 last:pb-0">
                                    {{ $point }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- The Artizen Way Card -->
                <div class="bg-white border-2 border-gray-950 rounded-2xl p-6 sm:p-8 text-left flex flex-col justify-between shadow-xs">
                    <div>
                        <span class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-amber-700 block mb-1.5">
                            The Artizen Standard
                        </span>
                        <h3 class="font-heading font-extrabold text-xl sm:text-2xl text-gray-950 mb-5">
                            Curated & Effortless
                        </h3>
                        <div class="divide-y divide-[#E8DFC8] text-xs sm:text-[13px] text-gray-950 font-semibold">
                            @foreach($comparison['artizen'] as $point)
                                <div class="py-3 first:pt-0 last:pb-0">
                                    {{ $point }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- ========================================================
         SECTION E: 3-STEP EFFORTLESS JOURNEY (CLEAN CARDS)
         ======================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="max-w-2xl mb-8 text-left">
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-gray-950 tracking-tight mb-1.5">
                How It Works
            </h2>
            <p class="text-xs sm:text-sm text-gray-600 font-normal">
                Three simple steps from browsing to celebrating with zero online payment required.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            @foreach($steps as $step)
                <div class="bg-white border border-[#E8DFC8] rounded-2xl p-6 sm:p-7 text-left flex flex-col justify-between">
                    <div>
                        <div class="font-heading font-black text-2xl sm:text-3xl text-gray-300 tracking-tight leading-none mb-3">
                            {{ $step['number'] }}
                        </div>
                        <h3 class="font-heading font-bold text-base text-gray-950 mb-2">
                            {{ $step['title'] }}
                        </h3>
                        <p class="text-xs text-gray-600 leading-relaxed font-normal">
                            {{ $step['desc'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>


    <!-- ========================================================
         SECTION F: SPACES WE TRANSFORM (CLEAN 4-GRID, ZERO HOVER ANIMATION)
         ======================================================== -->
    <section class="border-t border-[#EFE7D8] bg-[#FAF7F2] py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-2xl mb-8 text-left">
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-gray-950 tracking-tight mb-1.5">
                    Spaces We Transform
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 font-normal">
                    Custom-fitted setups designed for residential homes, terraces, lawns, and banquet venues across Indore.
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 lg:gap-4.5">
                @foreach($spaces as $space)
                    <div class="bg-white border border-[#E8DFC8] rounded-2xl overflow-hidden text-left flex flex-col justify-between">
                        <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100">
                            <img src="{{ $space['image'] }}" 
                                 alt="{{ $space['title'] }} Indore" 
                                 class="w-full h-full object-cover block" 
                                 loading="lazy">
                        </div>
                        <div class="p-4 sm:p-5">
                            <h3 class="font-heading font-bold text-xs sm:text-sm text-gray-950 mb-1">
                                {{ $space['title'] }}
                            </h3>
                            <p class="text-[11px] text-gray-600 font-normal leading-relaxed">
                                {{ $space['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    <!-- ========================================================
         SECTION G: INDORE COVERAGE DIRECTORY (CLEAN TEXT, NO BADGES)
         ======================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="bg-white border border-[#E8DFC8] rounded-2xl p-6 sm:p-8 text-left">
            <h3 class="font-heading font-extrabold text-base sm:text-lg text-gray-950 mb-1">
                Serving All Prime Neighborhoods Across Indore
            </h3>
            <p class="text-xs sm:text-sm text-gray-600 font-normal mb-6">
                Our logistics vehicles and technical field crew provide on-time setups across every key hub:
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-y-3.5 gap-x-6 text-xs sm:text-[13px] text-gray-950">
                @foreach($indoreHubs as $hub)
                    <div class="border-b border-[#EFE7D8] pb-2 text-gray-950 font-bold">
                        {{ $hub }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- ========================================================
         SECTION H: CLEAN CALL-TO-ACTION BANNER (ZERO HOVER ANIMATION)
         ======================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-[#FAF7F2] border border-[#E8DFC8] rounded-2xl p-8 sm:p-12 text-center">
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-gray-950 tracking-tight mb-2">
                Ready to Plan Your Indore Celebration?
            </h2>
            <p class="text-xs sm:text-sm text-gray-600 font-normal max-w-xl mx-auto mb-6">
                Explore our complete collection of curated celebration packages or chat directly with our Indore concierge.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('events.index') }}" 
                   class="inline-flex items-center justify-center bg-gray-950 text-white font-heading font-bold text-xs sm:text-[13px] px-6 py-3 rounded-xl cursor-pointer">
                    Explore All Celebrations
                </a>
                <a href="https://wa.me/919131668156?text={{ urlencode('Hi Artizen, I would like to plan a celebration in Indore.') }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center justify-center bg-white text-gray-900 border border-[#E8DFC8] font-heading font-bold text-xs sm:text-[13px] px-6 py-3 rounded-xl cursor-pointer">
                    WhatsApp Concierge
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
