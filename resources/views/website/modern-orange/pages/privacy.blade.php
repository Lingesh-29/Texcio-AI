@extends('layouts.modern-orange')

{{-- Custom JS --}}
@section('custom-css')
    {{-- AdSense status --}}
    @if ($setting->adsense_code != 'DISABLE')
        @if ($setting->adsense_code != '')
            {{-- AdSense code --}}
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $setting->adsense_code }}"
                crossorigin="anonymous"></script>
        @endif
    @endif
@endsection

@php
    // Settings
    use App\Models\Config;
    use App\Models\Page;

    $config = Config::get();
    $page = Page::where('theme_id', '317109101703740')->where('slug', 'privacy-policy')->where('status', 1)->get();
@endphp

@section('content')
    {{-- Topbar --}}
    @include('website.modern-orange.includes.topbar')

    {{-- Start home page sections --}}
    {!! Blade::compileString($page[0]->body) !!}

    {{-- Start call action section --}}
    @include('website.modern-orange.includes.call-action')
    {{-- End call action section --}}

    {{-- Footer --}}
    @include('website.modern-orange.includes.footer')
@endsection
