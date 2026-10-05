@extends('layouts.admin')

@section('page_title', ($isEdit ? 'Edit Persona: ' . $recipient->name : 'Add New Persona') . ' | Admin')
@section('page_heading', $isEdit ? 'Edit Celebration Persona' : 'Add New Celebration Persona')
@section('page_subheading', 'Configure title, target link, and upload persona thumbnail (Auto WebP converted)')

@push('head_scripts')
    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <style>
        .cropper-view-box, .cropper-face {
            border-radius: 12px;
        }
        .cropper-line, .cropper-point {
            background-color: #EA741D;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Top Action Breadcrumb Bar -->
    <div class="flex items-center justify-between gap-4 pb-2 border-b border-gray-200/80">
        <div class="flex items-center gap-2.5 text-xs text-gray-500 font-medium">
            <a href="{{ route('admin.recipients') }}" 
               class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:border-gray-900 text-gray-700 hover:text-black font-semibold transition-colors shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Back to Personas</span>
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-bold">{{ $isEdit ? 'Edit ' . $recipient->name : 'Create New' }}</span>
        </div>

        @if($isEdit)
            <div class="flex items-center gap-2">
                <span class="text-xs font-mono text-gray-400">ID: #{{ $recipient->id }}</span>
                <form action="{{ route('admin.recipients.delete', $recipient->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('Delete this persona permanently?');" 
                      class="inline m-0">
                    @csrf
                    <button type="submit" 
                            class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors cursor-pointer flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-regular fa-trash-can text-xs"></i>
                        <span>Delete Persona</span>
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Grid Form -->
    <form action="{{ route('admin.recipients.save') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          id="recipient-form"
          class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf
        @if($isEdit)
            <input type="hidden" name="id" value="{{ $recipient->id }}">
        @endif

        {{-- Hidden input for existing image path --}}
        <input type="hidden" name="existing_image" id="recipient-existing-image" value="{{ old('existing_image', $recipient->image) }}">

        <!-- =========================================================
             LEFT COLUMN: Form Inputs & Image Upload (7 cols)
             ========================================================= -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Card 1: Title & Target Link -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-5">
                <div class="border-b border-gray-100 pb-3 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 flex items-center justify-center text-sm shrink-0">
                        <i class="fa-solid fa-user-tag"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Persona Details</h2>
                        <p class="text-xs text-gray-500">Define recipient headline label and destination route.</p>
                    </div>
                </div>

                {{-- Row 1: Name and Slug --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="recipient-name" class="block text-xs font-bold text-gray-700">
                            Persona Name / Title <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="recipient-name" 
                               name="name" 
                               value="{{ old('name', $recipient->name) }}" 
                               required
                               placeholder="e.g. Him, Her, Kids, Wife" 
                               class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-medium text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all shadow-2xs"
                               oninput="syncName(this.value)">
                        <span class="text-[11px] text-gray-400">Displayed below the carousel card image.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label for="recipient-slug" class="block text-xs font-bold text-gray-700">
                            URL Slug
                        </label>
                        <input type="text" 
                               id="recipient-slug" 
                               name="slug" 
                               value="{{ old('slug', $recipient->slug) }}" 
                               placeholder="e.g. him, her, kids" 
                               class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-mono font-medium text-gray-700 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all shadow-2xs">
                        <span class="text-[11px] text-gray-400">Used for URL query filter (?for=slug).</span>
                    </div>
                </div>

                {{-- Row 2: Display Order & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div class="space-y-1.5">
                        <label for="recipient-display-order" class="block text-xs font-bold text-gray-700">
                            Display Sort Position
                        </label>
                        <input type="number" 
                               id="recipient-display-order" 
                               name="display_order" 
                               value="{{ old('display_order', $recipient->display_order ?: 1) }}" 
                               min="1" 
                               class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3.5 text-xs font-mono font-medium text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition-all shadow-2xs">
                        <span class="text-[11px] text-gray-400">Lower numbers appear first in the carousel.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700">
                            Visibility Status
                        </label>
                        <div class="h-11 px-3.5 rounded-xl border border-gray-200 bg-gray-50/70 flex items-center justify-between cursor-pointer"
                             onclick="toggleActiveSwitch()">
                            <span id="active-status-text" class="text-xs font-bold {{ $recipient->is_active ? 'text-emerald-700' : 'text-gray-500' }}">
                                {{ $recipient->is_active ? 'Published & Active' : 'Hidden / Draft' }}
                            </span>
                            <input type="checkbox" 
                                   id="is-active-checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $recipient->is_active) ? 'checked' : '' }} 
                                   class="sr-only">
                            <div id="active-switch-knob" class="w-9 h-5 rounded-full p-0.5 transition-colors {{ $recipient->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                <div class="w-4 h-4 rounded-full bg-white transition-transform {{ $recipient->is_active ? 'translate-x-4' : 'translate-x-0' }}"></div>
                            </div>
                        </div>
                        <span class="text-[11px] text-gray-400">Toggle whether this card appears on the live homepage.</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Image Upload (Drag & Drop, Clipboard Paste, WebP Auto-convert) -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 border border-purple-100 text-purple-700 flex items-center justify-center text-sm shrink-0">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Persona Image Asset</h2>
                            <p class="text-xs text-gray-500">Aspect 16:10.5 (~600&times;400px). Auto-converted to WebP with interactive crop.</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                        <i class="fa-solid fa-bolt text-[9px]"></i> WebP Auto-Convert
                    </span>
                </div>

                {{-- Interactive Dropzone --}}
                <div id="recipient-dropzone" 
                     tabindex="0"
                     class="group relative border-2 border-dashed border-gray-300 rounded-2xl p-5 transition-all duration-200 bg-gray-50/60 flex flex-col items-center justify-center text-center min-h-[220px]"
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)" 
                     ondrop="handleDrop(event)" 
                     onpaste="handleDropzonePaste(event)">
                    
                    {{-- Hidden Native File Input --}}
                    <input type="file" 
                           id="recipient-file-input" 
                           name="image_file" 
                           accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" 
                           class="hidden" 
                           onchange="handleFileSelect(this)">

                    {{-- Image Thumbnail Container (Shown when image exists) --}}
                    <div id="dropzone-thumb-container" class="{{ $recipient->image ? '' : 'hidden' }} w-full max-w-sm mx-auto flex flex-col items-center">
                        <div class="relative w-full aspect-[16/10.5] rounded-xl overflow-hidden bg-[#FFF5F5] border border-gray-200/90 shadow-sm mb-3 group/thumb">
                            <img id="dropzone-thumb-img" 
                                 src="{{ $recipient->image ? asset($recipient->image) : '' }}" 
                                 alt="Persona Preview" 
                                 class="w-full h-full object-cover">
                            
                            {{-- Clickable hover overlay for Adjust / Crop --}}
                            <div onclick="openCropModal(event)" 
                                 class="absolute inset-0 bg-black/45 opacity-0 group-hover/thumb:opacity-100 transition-opacity flex flex-col items-center justify-center text-white cursor-pointer select-none">
                                <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mb-1 text-sm border border-white/30">
                                    <i class="fa-solid fa-crop-simple"></i>
                                </div>
                                <span class="text-xs font-bold drop-shadow">Click to Adjust & Crop</span>
                            </div>

                            {{-- Remove Overlay Button (Close Icon) --}}
                            <button type="button" 
                                    id="btn-remove-image"
                                    onclick="removeImage(event)" 
                                    class="absolute top-2.5 right-2.5 z-20 w-8 h-8 rounded-lg bg-black/80 hover:bg-rose-600 active:scale-95 text-white flex items-center justify-center text-sm transition-all shadow-md cursor-pointer"
                                    title="Remove this image">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        
                        {{-- Controls Bar under image --}}
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    onclick="openCropModal(event)" 
                                    class="px-3 py-1.5 rounded-lg border border-gray-300 hover:border-gray-900 bg-white text-gray-700 hover:text-black text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-crop-simple text-[11px] text-purple-600"></i>
                                <span>Adjust Crop</span>
                            </button>

                            <button type="button" 
                                    onclick="triggerFileInput(event)" 
                                    class="px-3 py-1.5 rounded-lg border border-gray-300 hover:border-gray-900 bg-white text-gray-700 hover:text-black text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-arrow-rotate-right text-[11px] text-gray-500"></i>
                                <span>Replace Image</span>
                            </button>

                            <button type="button" 
                                    onclick="removeImage(event)" 
                                    class="px-3 py-1.5 rounded-lg border border-rose-200 hover:border-rose-400 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                                <span>Remove</span>
                            </button>
                        </div>
                    </div>

                    {{-- Empty Dropzone Prompt (Shown when no image) --}}
                    <div id="dropzone-prompt-container" 
                         onclick="triggerFileInput(event)" 
                         class="{{ $recipient->image ? 'hidden' : '' }} space-y-2 cursor-pointer w-full py-6 select-none">
                        <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-gray-400 group-hover:text-gray-900 group-hover:scale-105 transition-all flex items-center justify-center mx-auto shadow-2xs">
                            <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-800 block">Drag & Drop Image Here</span>
                            <span class="text-[11px] text-gray-400">or click to browse from device, or paste from clipboard (Ctrl+V / &#8984;V)</span>
                        </div>
                        <div class="flex items-center justify-center gap-2 pt-1 text-[10px] text-gray-400 font-mono">
                            <span>PNG</span> &bull; <span>JPG</span> &bull; <span>WEBP</span> &bull; <span>Max 5MB</span>
                        </div>
                    </div>

                    {{-- Feedback Toast Notification --}}
                    <div id="dropzone-feedback" 
                         class="absolute bottom-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-[11px] font-bold bg-gray-900 text-white shadow-md hidden transition-opacity pointer-events-none z-30">
                    </div>
                </div>

                {{-- Copy-Paste Hint Box --}}
                <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/60 flex items-start gap-2.5 text-xs text-amber-900">
                    <i class="fa-solid fa-paste text-amber-700 text-xs mt-0.5 shrink-0"></i>
                    <div>
                        <span class="font-bold">Copy & Paste Supported:</span>
                        <span class="text-amber-800"> You can copy any picture from Photoshop, Figma, or your browser and simply press <kbd class="px-1.5 py-0.5 bg-white border border-amber-300 rounded text-[10px] font-mono font-bold">Ctrl+V</kbd> or <kbd class="px-1.5 py-0.5 bg-white border border-amber-300 rounded text-[10px] font-mono font-bold">&#8984;V</kbd> anywhere on this page to paste it instantly. It will automatically convert to WebP.</span>
                    </div>
                </div>
            </div>

        </div>


        <!-- =========================================================
             RIGHT COLUMN: Live Storefront Preview & Actions (5 cols)
             ========================================================= -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Action / Save Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-2xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Actions</h3>
                
                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-gray-900 hover:bg-black active:scale-[0.99] text-white text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>{{ $isEdit ? 'Update Persona Card' : 'Save Persona Card' }}</span>
                </button>

                <a href="{{ route('admin.recipients') }}" 
                   class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors flex items-center justify-center text-center">
                    Cancel & Return
                </a>
            </div>

            <!-- Live Preview Card (Exact 1:1 replica of Homepage Carousel) -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-900">Homepage Carousel Preview</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400">Live 1:1</span>
                </div>

                <p class="text-[11px] text-gray-400">This is exactly how this celebration persona appears to customers on the homepage carousel:</p>

                {{-- Interactive Carousel Card Mockup --}}
                <div class="pt-2 flex justify-center">
                    <div class="w-full max-w-[240px] flex flex-col items-center select-none pointer-events-none">
                        <!-- Card Image Box (Matching reference aspect 16/10.5) -->
                        <div class="w-full aspect-[16/10.5] rounded-2xl overflow-hidden bg-[#FFF5F5] border border-black/5 shadow-2xs relative flex items-center justify-center">
                            <img id="live-preview-img" 
                                 src="{{ $recipient->image ? asset($recipient->image) : '' }}" 
                                 alt="Persona Preview" 
                                 class="w-full h-full object-cover {{ $recipient->image ? '' : 'hidden' }}">
                            
                            <div id="live-preview-placeholder" class="{{ $recipient->image ? 'hidden' : 'flex' }} flex-col items-center justify-center text-center p-3 select-none">
                                <div class="w-10 h-10 rounded-xl bg-white border border-gray-200/80 text-gray-400 flex items-center justify-center mb-1.5 shadow-2xs">
                                    <i class="fa-regular fa-image text-base"></i>
                                </div>
                                <span class="text-[11px] font-bold text-gray-500">No Image</span>
                                <span class="text-[9.5px] text-gray-400">Upload or paste an image</span>
                            </div>
                        </div>

                        <!-- Label Below Card -->
                        <span id="live-preview-title" 
                              class="block text-center font-heading font-bold text-[14px] sm:text-[15px] text-gray-900 mt-2.5">
                            {{ $recipient->name ?: 'Persona Title' }}
                        </span>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400 font-medium">
                    <span>Card Aspect: <b class="text-gray-700">16 : 10.5</b></span>
                    <span>Style: <b class="text-gray-700">Swiper Item</b></span>
                </div>
            </div>

        </div>

    </form>

</div>

<!-- =========================================================
     CROP ADJUSTMENT MODAL (Cropper.js)
     ========================================================= -->
<div id="crop-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5">
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[92vh] animate-in fade-in zoom-in duration-150">
        <!-- Modal Header -->
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-sm font-bold border border-purple-100">
                    <i class="fa-solid fa-crop-simple"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Adjust & Crop Persona Image</h3>
                    <p class="text-xs text-gray-500">Framing ratio: 16:10.5 (~640&times;420px). Drag, zoom, and adjust framing.</p>
                </div>
            </div>
            <button type="button" 
                    onclick="closeCropModal(event)" 
                    class="w-8 h-8 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center text-sm transition-colors cursor-pointer"
                    title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Cropping Canvas -->
        <div class="p-3 sm:p-4 bg-gray-950 flex items-center justify-center overflow-hidden flex-1 min-h-[320px] max-h-[55vh]">
            <div class="w-full h-full flex items-center justify-center">
                <img id="cropper-image" src="" alt="Crop Source" class="max-w-full max-h-[50vh] block">
            </div>
        </div>

        <!-- Cropping Controls Toolbar -->
        <div class="px-4 sm:px-5 py-2.5 bg-gray-50 border-t border-gray-200/80 flex flex-wrap items-center justify-between gap-2.5 shrink-0">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="button" 
                        onclick="cropperZoom(0.1)" 
                        class="px-2.5 py-1.5 rounded-lg border border-gray-300 hover:border-gray-900 bg-white text-gray-700 hover:text-black text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1 cursor-pointer"
                        title="Zoom In">
                    <i class="fa-solid fa-magnifying-glass-plus text-gray-500"></i>
                    <span>Zoom In</span>
                </button>
                <button type="button" 
                        onclick="cropperZoom(-0.1)" 
                        class="px-2.5 py-1.5 rounded-lg border border-gray-300 hover:border-gray-900 bg-white text-gray-700 hover:text-black text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1 cursor-pointer"
                        title="Zoom Out">
                    <i class="fa-solid fa-magnifying-glass-minus text-gray-500"></i>
                    <span>Zoom Out</span>
                </button>
                <button type="button" 
                        onclick="cropperRotate(-90)" 
                        class="px-2.5 py-1.5 rounded-lg border border-gray-300 hover:border-gray-900 bg-white text-gray-700 hover:text-black text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1 cursor-pointer"
                        title="Rotate -90°">
                    <i class="fa-solid fa-rotate-left text-gray-500"></i>
                    <span>-90&deg;</span>
                </button>
                <button type="button" 
                        onclick="cropperRotate(90)" 
                        class="px-2.5 py-1.5 rounded-lg border border-gray-300 hover:border-gray-900 bg-white text-gray-700 hover:text-black text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1 cursor-pointer"
                        title="Rotate +90°">
                    <i class="fa-solid fa-rotate-right text-gray-500"></i>
                    <span>+90&deg;</span>
                </button>
                <button type="button" 
                        onclick="cropperReset()" 
                        class="px-2.5 py-1.5 rounded-lg border border-gray-300 hover:border-gray-900 bg-white text-gray-700 hover:text-black text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1 cursor-pointer"
                        title="Reset Crop Frame">
                    <i class="fa-solid fa-arrows-rotate text-gray-500"></i>
                    <span>Reset</span>
                </button>
            </div>

            <div class="text-[11px] text-gray-500 font-medium">
                Fixed Aspect: <span class="font-bold text-gray-800">16 : 10.5</span>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="px-5 py-3 border-t border-gray-100 bg-white flex items-center justify-end gap-2.5 shrink-0">
            <button type="button" 
                    onclick="closeCropModal(event)" 
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="button" 
                    onclick="applyCrop()" 
                    class="px-5 py-2 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-check text-xs text-emerald-400"></i>
                <span>Apply Crop & Save</span>
            </button>
        </div>
    </div>
</div>

<!-- Interactive JavaScript for Drag-Drop, Copy-Paste, Cropper, and Real-Time Live Preview -->
<script>
    let cropperInstance = null;
    let currentRawDataUrl = null;
    let currentSourceFileName = 'persona_image.webp';

    // Live Title Sync
    function syncName(val) {
        const previewTitle = document.getElementById('live-preview-title');
        if (previewTitle) {
            previewTitle.textContent = val.trim() || 'Persona Title';
        }
        
        // Auto-fill slug if user hasn't edited slug manually
        const slugInput = document.getElementById('recipient-slug');
        if (slugInput && (!slugInput.value || slugInput.dataset.touched !== 'true')) {
            const generatedSlug = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            slugInput.value = generatedSlug;
            const hint = document.getElementById('slug-hint-code');
            if (hint) hint.textContent = generatedSlug || 'slug';
        }
    }

    document.getElementById('recipient-slug')?.addEventListener('input', function() {
        this.dataset.touched = 'true';
        const hint = document.getElementById('slug-hint-code');
        if (hint) hint.textContent = this.value || 'slug';
    });

    // Toggle Active Switch
    function toggleActiveSwitch() {
        const checkbox = document.getElementById('is-active-checkbox');
        const knob = document.getElementById('active-switch-knob');
        const label = document.getElementById('active-status-text');

        if (!checkbox) return;
        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            knob.className = 'w-9 h-5 rounded-full p-0.5 transition-colors bg-emerald-500';
            knob.firstElementChild.className = 'w-4 h-4 rounded-full bg-white transition-transform translate-x-4';
            label.className = 'text-xs font-bold text-emerald-700';
            label.textContent = 'Published & Active';
        } else {
            knob.className = 'w-9 h-5 rounded-full p-0.5 transition-colors bg-gray-300';
            knob.firstElementChild.className = 'w-4 h-4 rounded-full bg-white transition-transform translate-x-0';
            label.className = 'text-xs font-bold text-gray-500';
            label.textContent = 'Hidden / Draft';
        }
    }

    // Trigger File Dialog
    function triggerFileInput(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        document.getElementById('recipient-file-input')?.click();
    }

    // Drag and Drop Handlers
    function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        const dropzone = document.getElementById('recipient-dropzone');
        if (dropzone) dropzone.classList.add('border-gray-900', 'bg-gray-100');
    }

    function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        const dropzone = document.getElementById('recipient-dropzone');
        if (dropzone) dropzone.classList.remove('border-gray-900', 'bg-gray-100');
    }

    function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        handleDragLeave(e);

        const files = e.dataTransfer?.files;
        if (files && files.length > 0) {
            processAndPreviewFile(files[0], true);
            showToast('Image uploaded! Adjust crop below.');
        }
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            processAndPreviewFile(input.files[0], true);
            showToast('Image selected! Adjust crop below.');
        }
    }

    // Clipboard Paste Handlers
    function handleDropzonePaste(e) {
        e.preventDefault();
        e.stopPropagation();
        extractFileFromClipboard(e);
    }

    window.addEventListener('paste', function(e) {
        const targetTag = (e.target.tagName || '').toLowerCase();
        if ((targetTag === 'input' && e.target.type === 'text') || targetTag === 'textarea') {
            return; // Allow regular typing inside text fields
        }
        extractFileFromClipboard(e);
    });

    function extractFileFromClipboard(e) {
        const items = (e.clipboardData || e.originalEvent?.clipboardData)?.items;
        if (!items) return;

        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                e.preventDefault();
                const blob = items[i].getAsFile();
                if (blob) {
                    const file = new File([blob], `pasted_persona_${Date.now()}.png`, { type: blob.type || 'image/png' });
                    processAndPreviewFile(file, true);
                    showToast('Pasted image from clipboard! Adjust crop.');
                    break;
                }
            }
        }
    }

    function assignFileToInput(file) {
        const input = document.getElementById('recipient-file-input');
        if (!input) return;
        try {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        } catch(err) {
            console.warn('DataTransfer files assignment', err);
        }
    }

    function processAndPreviewFile(file, autoOpenCrop = false) {
        currentSourceFileName = file.name || 'persona_image.webp';
        assignFileToInput(file);

        const reader = new FileReader();
        reader.onload = function(e) {
            const dataUrl = e.target.result;
            currentRawDataUrl = dataUrl;
            updatePreviewImages(dataUrl);

            if (autoOpenCrop) {
                openCropModal();
            }
        };
        reader.readAsDataURL(file);
    }

    function updatePreviewImages(dataUrl) {
        // Update dropzone thumb
        const thumbContainer = document.getElementById('dropzone-thumb-container');
        const thumbImg = document.getElementById('dropzone-thumb-img');
        const promptContainer = document.getElementById('dropzone-prompt-container');

        if (thumbImg) thumbImg.src = dataUrl;
        if (thumbContainer) thumbContainer.classList.remove('hidden');
        if (promptContainer) promptContainer.classList.add('hidden');

        // Update live sidebar mockup
        const liveImg = document.getElementById('live-preview-img');
        const livePlaceholder = document.getElementById('live-preview-placeholder');
        if (liveImg) {
            liveImg.src = dataUrl;
            liveImg.classList.remove('hidden');
        }
        if (livePlaceholder) {
            livePlaceholder.classList.add('hidden');
        }
    }

    // =========================================================
    // CROPPER.JS MODAL CONTROLS
    // =========================================================
    function openCropModal(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
            if (e.stopImmediatePropagation) e.stopImmediatePropagation();
        }

        const thumbImg = document.getElementById('dropzone-thumb-img');
        const sourceSrc = currentRawDataUrl || thumbImg?.getAttribute('src');

        if (!sourceSrc || sourceSrc.trim() === '') {
            triggerFileInput();
            return;
        }

        const modal = document.getElementById('crop-modal');
        const cropImg = document.getElementById('cropper-image');
        if (!modal || !cropImg) return;

        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }

        cropImg.src = sourceSrc;
        modal.classList.remove('hidden');

        // Initialize Cropper after modal is visible
        setTimeout(() => {
            if (typeof Cropper === 'undefined') {
                console.error('Cropper.js library not loaded yet');
                return;
            }
            cropperInstance = new Cropper(cropImg, {
                aspectRatio: 16 / 10.5,
                viewMode: 1,
                autoCropArea: 0.95,
                responsive: true,
                movable: true,
                zoomable: true,
                rotatable: true,
                scalable: true,
                background: true,
                checkCrossOrigin: false,
            });
        }, 60);
    }

    function closeCropModal(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const modal = document.getElementById('crop-modal');
        if (modal) modal.classList.add('hidden');
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
    }

    function cropperZoom(ratio) {
        if (cropperInstance) cropperInstance.zoom(ratio);
    }

    function cropperRotate(deg) {
        if (cropperInstance) cropperInstance.rotate(deg);
    }

    function cropperReset() {
        if (cropperInstance) cropperInstance.reset();
    }

    function applyCrop() {
        if (!cropperInstance) {
            closeCropModal();
            return;
        }

        const canvas = cropperInstance.getCroppedCanvas({
            width: 640,
            height: 420,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            closeCropModal();
            return;
        }

        canvas.toBlob(function(blob) {
            if (!blob) {
                closeCropModal();
                return;
            }

            const baseName = (currentSourceFileName || 'persona').replace(/\.[^/.]+$/, "");
            const croppedFile = new File([blob], `${baseName}.webp`, { type: 'image/webp' });
            
            assignFileToInput(croppedFile);

            const croppedDataUrl = canvas.toDataURL('image/webp', 0.92);
            updatePreviewImages(croppedDataUrl);

            closeCropModal();
            showToast('Crop applied successfully!');
        }, 'image/webp', 0.92);
    }

    // =========================================================
    // REMOVE IMAGE
    // =========================================================
    function removeImage(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
            if (e.stopImmediatePropagation) e.stopImmediatePropagation();
        }

        currentRawDataUrl = null;
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }

        const input = document.getElementById('recipient-file-input');
        if (input) input.value = '';

        const hiddenExisting = document.getElementById('recipient-existing-image');
        if (hiddenExisting) hiddenExisting.value = '';

        const thumbContainer = document.getElementById('dropzone-thumb-container');
        const thumbImg = document.getElementById('dropzone-thumb-img');
        const promptContainer = document.getElementById('dropzone-prompt-container');

        if (thumbImg) thumbImg.src = '';
        if (thumbContainer) thumbContainer.classList.add('hidden');
        if (promptContainer) promptContainer.classList.remove('hidden');

        // Reset sidebar preview to empty placeholder
        const liveImg = document.getElementById('live-preview-img');
        const livePlaceholder = document.getElementById('live-preview-placeholder');
        if (liveImg) {
            liveImg.src = '';
            liveImg.classList.add('hidden');
        }
        if (livePlaceholder) {
            livePlaceholder.classList.remove('hidden');
        }

        showToast('Image removed.');
    }

    function showToast(msg) {
        const toast = document.getElementById('dropzone-feedback');
        if (!toast) return;
        toast.textContent = msg;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 2500);
    }
</script>
@endsection
