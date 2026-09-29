@extends('layouts.app')

@section('content')
    @include('home.hero', ['slides' => $cms['hero_slides']])
    @include('home.featured-events')
    @include('home.categories')
    @include('home.reels')
    @include('home.about', ['about' => $cms['about']])
    @include('home.testimonials')
    @include('home.faq')
@endsection