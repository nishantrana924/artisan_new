@extends('layouts.app')

@section('content')
    @include('home.hero', ['slides' => $cms['hero_slides'] ?? []])
    @include('home.featured-events')
    @include('home.categories')
    @include('home.relatable-celebrations')
    @include('home.offers-banner', ['offers' => $cms['offers'] ?? []])
    @include('home.testimonials')
    @include('home.promise-banner', ['promiseBanner' => $cms['promise_banner'] ?? []])
    @include('home.faq')
    @include('home.seo-content')
    @include('home.trust-bar')
@endsection