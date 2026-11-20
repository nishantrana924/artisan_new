@props([
    'action' => null,
    'theme' => 'light',
    'size' => 'normal',
])

@php
    $siteKey = config('services.turnstile.site_key');
@endphp

@if(!empty($siteKey))
    <div class="my-2.5 flex justify-start select-none">
        <div class="cf-turnstile" 
             data-sitekey="{{ $siteKey }}" 
             data-theme="{{ $theme }}" 
             data-size="{{ $size }}"
             @if($action) data-action="{{ $action }}" @endif>
        </div>
    </div>
@endif

