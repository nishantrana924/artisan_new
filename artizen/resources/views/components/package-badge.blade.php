@props(['badge' => null])

@php
    $badgeText = !empty($badge) ? trim((string)$badge) : null;
    $upper = $badgeText ? strtoupper($badgeText) : '';
    // If it's a generic discount or empty/null value, do not render
    if (!$badgeText || in_array($upper, ['15% OFF', 'NONE', 'NO', 'OFFER', 'NULL', 'FALSE', '0'])) {
        $badgeText = null;
    }
@endphp

@if(!empty($badgeText))
    @php
        $badgeStyle = 'bg-gray-900/90 text-white';
        $badgeIcon = 'fa-solid fa-tag';

        if (str_contains($upper, 'BESTSELLER')) {
            $badgeStyle = 'bg-[#EA741D] text-white';
            $badgeIcon = 'fa-solid fa-fire';
        } elseif (str_contains($upper, 'LUXURY')) {
            $badgeStyle = 'bg-gray-950 text-amber-300 border border-amber-400/40';
            $badgeIcon = 'fa-solid fa-crown text-amber-300';
        } elseif (str_contains($upper, 'TRENDING')) {
            $badgeStyle = 'bg-rose-600 text-white';
            $badgeIcon = 'fa-solid fa-arrow-trend-up';
        } elseif (str_contains($upper, 'PREMIUM')) {
            $badgeStyle = 'bg-emerald-700 text-white';
            $badgeIcon = 'fa-solid fa-gem text-emerald-200';
        } elseif (str_contains($upper, 'POPULAR')) {
            $badgeStyle = 'bg-indigo-600 text-white';
            $badgeIcon = 'fa-solid fa-bolt text-yellow-300';
        } elseif (str_contains($upper, 'HOT')) {
            $badgeStyle = 'bg-red-600 text-white';
            $badgeIcon = 'fa-solid fa-fire-flame-curved text-amber-200';
        }
    @endphp
    <!-- Promotional Tag Badge Top-Left -->
    <span class="absolute top-2.5 left-2.5 z-20 inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-extrabold uppercase tracking-wider {{ $badgeStyle }} shadow-xs select-none">
        <i class="{{ $badgeIcon }} text-[8.5px]"></i>
        <span>{{ $badgeText }}</span>
    </span>
@endif
