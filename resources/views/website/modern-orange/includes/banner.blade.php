@php
// Settings
use App\Models\Config;
use App\Models\Page;

$config = Config::get();
$page = Page::where('theme_id', '317109101703740')->where('slug', 'hero')->where('status', 1)->get();
@endphp

@if (request()->is('/'))
    {!!  Blade::compileString($page[0]->body) !!}
@endif