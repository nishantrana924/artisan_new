@extends('layouts.admin')

@section('page_title', 'Celebrations for Everyone Management')
@section('page_heading', 'Celebrations for Everyone')
@section('page_subheading', 'Manage homepage celebration personas, custom recipient links, and card visuals')

@section('content')
<div class="space-y-6">

    <!-- 1. Metric Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <!-- Total Personas -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Total Personas</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-gray-900 leading-none">{{ $totalCount }}</span>
                <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-users-viewfinder"></i>
                </span>
            </div>
            <span class="text-[10px] text-gray-500 mt-2 block">Homepage celebration cards</span>
        </div>

        <!-- Active / Published -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Published</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-600 leading-none">{{ $publishedCount }}</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <span class="text-[10px] text-emerald-700 mt-2 block font-medium">Visible on homepage carousel</span>
        </div>

        <!-- Inactive / Drafts -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Hidden / Drafts</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-gray-500 leading-none">{{ $draftCount }}</span>
                <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-eye-slash"></i>
                </span>
            </div>
            <span class="text-[10px] text-gray-400 mt-2 block">Not displayed on live site</span>
        </div>

    </div>

    <!-- 2. Action Bar & Add Button -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-bold text-gray-900">Celebration Persona Cards</h3>
            <p class="text-xs text-gray-500 mt-0.5">Control the order, images, and destination routes for the "Celebrations for Everyone" carousel.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.recipients.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Recipient Persona</span>
            </a>
        </div>
    </div>

    <!-- 3. Recipients Data Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200/80 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">Order</th>
                        <th class="py-3 px-4 w-28">Preview</th>
                        <th class="py-3 px-4">Title / Persona</th>
                        <th class="py-3 px-4">Storefront Filter Link</th>
                        <th class="py-3 px-4 w-28 text-center">Status</th>
                        <th class="py-3 px-4 w-32 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($recipients as $item)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <!-- Display Order -->
                            <td class="py-3 px-4 text-center font-bold text-gray-400">
                                <span class="w-7 h-7 rounded-lg bg-gray-100 text-gray-700 font-mono inline-flex items-center justify-center text-xs">
                                    {{ $item->display_order }}
                                </span>
                            </td>

                            <!-- 16:10 Thumbnail Card Preview -->
                            <td class="py-3 px-4">
                                <div class="w-20 aspect-[16/10.5] rounded-xl overflow-hidden bg-[#FFF5F5] border border-gray-200/80 shadow-2xs shrink-0">
                                    @if($item->image)
                                        <img src="{{ asset($item->image) }}" 
                                             alt="{{ $item->name }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fa-regular fa-image text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Title / Slug -->
                            <td class="py-3 px-4">
                                <span class="font-bold text-gray-900 text-sm block">{{ $item->name }}</span>
                                <span class="text-[11px] text-gray-400 font-mono">slug: {{ $item->slug }}</span>
                            </td>

                            <!-- Storefront Filter Link -->
                            <td class="py-3 px-4">
                                <a href="{{ route('events.index', ['for' => $item->slug]) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#FAF7F2] text-amber-900 border border-[#E8DFC8] font-mono text-[11px] hover:bg-amber-100 hover:border-amber-400 transition-colors">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-amber-700"></i>
                                    <span>/events?for={{ $item->slug }}</span>
                                </a>
                            </td>

                            <!-- Status Toggle -->
                            <td class="py-3 px-4 text-center">
                                <form action="{{ route('admin.recipients.toggle-status', $item->id) }}" method="POST" class="inline-block m-0">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition-colors cursor-pointer {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200' }}"
                                            title="Click to toggle active status">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        <span>{{ $item->is_active ? 'Active' : 'Draft' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.recipients.edit', $item->id) }}" 
                                       class="w-8 h-8 rounded-lg border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-black flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
                                       title="Edit recipient">
                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.recipients.delete', $item->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete \'{{ $item->name }}\'?');" 
                                          class="inline-block m-0">
                                        @csrf
                                        <button type="submit" 
                                                class="w-8 h-8 rounded-lg border border-gray-200 hover:border-rose-300 text-gray-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
                                                title="Delete recipient">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-users-viewfinder"></i>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800">No Celebration Personas Yet</h4>
                                <p class="text-xs text-gray-400 mt-1">Get started by creating your first celebration recipient persona card.</p>
                                <a href="{{ route('admin.recipients.create') }}" 
                                   class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-black shadow-xs">
                                    <i class="fa-solid fa-plus text-xs"></i> Add Persona
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
