@extends('layouts.app')

@section('title', ($page['title'] ?? 'Booking & Payment Policy') . ' | ARTIZEN Event Booking Platform Indore')
@section('meta_description', 'Learn about ARTIZEN’s offline payment model, instant booking confirmation process, and setup fulfillment guidelines in Indore.')

@section('content')
<div class="min-h-screen bg-[#FCFBF8] text-gray-950 font-body select-none py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="border-b border-[#EFE7D8] pb-6 mb-8 text-left">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#FAF7F2] border border-[#E8DFC8] text-[11px] font-heading font-extrabold text-gray-800 uppercase tracking-wider mb-3">
                <i class="fa-solid fa-calendar-check text-amber-600 text-xs"></i> Booking Framework
            </span>
            <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-gray-950 tracking-tight mb-2">
                {{ $page['title'] ?? 'Booking & Payment Policy' }}
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 font-normal">
                {{ $page['subtitle'] ?? 'Effective Date: October 2, 2026 • ARTIZEN Event Booking Platform (Indore, MP)' }}
            </p>
        </div>

        <!-- Body Content (Dynamic Rich Text Content from Admin Panel) -->
        <div class="bg-white border border-[#E8DFC8] rounded-2xl p-6 sm:p-10 text-left text-sm text-gray-700 leading-relaxed shadow-none legal-prose-content">
            {!! $page['content'] ?? '' !!}
        </div>

    </div>
</div>

<style>
    .legal-prose-content h2 {
        font-family: var(--font-heading, sans-serif);
        font-weight: 800;
        font-size: 1.125rem;
        color: #030712;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .legal-prose-content h2::before {
        content: "";
        display: inline-block;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 9999px;
        background-color: #f59e0b;
        flex-shrink: 0;
    }
    .legal-prose-content h3 {
        font-family: var(--font-heading, sans-serif);
        font-weight: 700;
        font-size: 1rem;
        color: #111827;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
    }
    .legal-prose-content p {
        margin-bottom: 1rem;
        color: #374151;
        line-height: 1.625;
    }
    .legal-prose-content ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .legal-prose-content ol {
        list-style-type: decimal;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .legal-prose-content li {
        margin-bottom: 0.4rem;
        color: #4b5563;
    }
    .legal-prose-content blockquote {
        border-left: 4px solid #e8dfc8;
        background-color: #faf7f2;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        font-style: italic;
        margin-bottom: 1rem;
    }
</style>
@endsection
