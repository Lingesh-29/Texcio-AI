@extends('layouts.classic')

@section('content')

{{-- Custom CSS --}}
@section('custom-css')
{{-- AdSense status --}}
@if ($setting->adsense_code != "DISABLE")
@if ($setting->adsense_code != "")
{{-- AdSense code --}}
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $setting->adsense_code }}"
    crossorigin="anonymous"></script>
@endif
@endif
@endsection

@php
use App\Models\Page;
$page = Page::where('slug', 'features')->where('status', 1)->get();
@endphp

{{-- Topbar --}}
@include('website.classic.includes.topbar')

{{-- Features --}}
{!! $page[0]->body !!}

{{-- Footer --}}
@include('website.classic.includes.footer')
@endsection