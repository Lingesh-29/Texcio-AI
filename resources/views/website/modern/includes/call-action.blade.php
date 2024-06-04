@php
// Settings
use App\Models\Config;
use App\Models\Page;

$config = Config::get();
$page = Page::where('theme_id', '330599619570398')->where('slug', 'newsletter')->where('status', 1)->get();
@endphp

{{-- Hero section --}}
{!!  Blade::compileString($page[0]->body) !!}