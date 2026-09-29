@include('layouts.header')
@include('layouts.navbar')

<main class="min-h-[70vh] flex items-center justify-center py-16 px-4 bg-gray-50">
    <div class="max-w-md w-full text-center bg-white border border-gray-200 p-8 rounded-3xl shadow-xl">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center text-2xl mx-auto mb-5 font-bold">
            <i class="fa-solid fa-compass"></i>
        </div>
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-2">404</h1>
        <h2 class="text-lg font-bold text-gray-800 mb-3">Page Not Found</h2>
        <p class="text-xs text-gray-500 font-normal leading-relaxed mb-6">
            The page or package setup you are searching for does not exist or has been relocated.
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="px-5 py-2.5 bg-[#1E1E24] hover:bg-black text-white text-xs font-bold rounded-xl transition-colors shadow-md">
                <i class="fa-solid fa-house mr-1"></i> Return Home
            </a>
            <a href="{{ route('events.index') }}" class="px-5 py-2.5 border border-gray-300 hover:bg-gray-100 text-gray-800 text-xs font-bold rounded-xl transition-colors">
                Browse Packages
            </a>
        </div>
    </div>
</main>

@include('layouts.footer')
