@php
// Get plan details
use App\Models\User;
$plan = User::where('id', Auth::user()->id)->where('status', 1)->first();
$plan_details = json_decode($plan->plan_details);
@endphp

<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">

                    {{-- Dashboard --}}
                    <li class="nav-item {{ request()->is('user/dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <polyline points="5 12 3 12 12 3 21 12 19 12" />
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Dashboard') }}
                            </span>
                        </a>
                    </li>

                    {{-- AI Content --}}
                    <li
                        class="nav-item {{ request()->is('user/ai/gc') ? 'active' : '' }} {{ request()->is('user/ai/gc/*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.all.ai.content') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-file-description" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                    </path>
                                    <path d="M9 17h6"></path>
                                    <path d="M9 13h6"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('AI Contents') }}
                            </span>
                        </a>
                    </li>

                    {{-- AI Images --}}
                    <li
                        class="nav-item {{ request()->is('user/ai/gi') ? 'active' : '' }} {{ request()->is('user/ai/gi/*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.all.ai.images') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M15 8l.01 0"></path>
                                    <path
                                        d="M4 4m0 3a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-10a3 3 0 0 1 -3 -3z">
                                    </path>
                                    <path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5"></path>
                                    <path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('AI Images') }}
                            </span>
                        </a>
                    </li>

                    @if (isset($plan_details))
                    @if (isset($plan_details->ai_speech_to_text) == 1)
                    {{-- AI Speech to Text --}}
                    <li
                        class="nav-item {{ request()->is('user/ai/gst') ? 'active' : '' }} {{ request()->is('user/ai/gst/*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.all.ai.speech.to.text') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-file-text-ai" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                    <path d="M10 21h-3a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v3.5"></path>
                                    <path d="M9 9h1"></path>
                                    <path d="M9 13h2.5"></path>
                                    <path d="M9 17h1"></path>
                                    <path d="M14 21v-4a2 2 0 1 1 4 0v4"></path>
                                    <path d="M14 19h4"></path>
                                    <path d="M21 15v6"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('AI Speech to Text') }}
                            </span>
                        </a>
                    </li>
                    @endif

                    @if (isset($plan_details->ai_text_to_speech) == 1)
                    {{-- AI Text to Speech --}}
                    <li
                        class="nav-item {{ request()->is('user/ai/gts') ? 'active' : '' }} {{ request()->is('user/ai/gts/*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.all.ai.text.to.speech') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-file-text-ai" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                    <path d="M10 21h-3a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v3.5"></path>
                                    <path d="M9 9h1"></path>
                                    <path d="M9 13h2.5"></path>
                                    <path d="M9 17h1"></path>
                                    <path d="M14 21v-4a2 2 0 1 1 4 0v4"></path>
                                    <path d="M14 19h4"></path>
                                    <path d="M21 15v6"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('AI Text to Speech') }}
                            </span>
                        </a>
                    </li>
                    @endif

                    @if (isset($plan_details->ai_code) == 1)
                    {{-- AI Code --}}
                    <li
                        class="nav-item {{ request()->is('user/ai/gcode') ? 'active' : '' }} {{ request()->is('user/ai/gcode/*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.all.ai.code') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-code-circle"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 14l-2 -2l2 -2"></path>
                                    <path d="M14 10l2 2l-2 2"></path>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('AI Code') }}
                            </span>
                        </a>
                    </li>
                    @endif
                    @endif

                    {{-- Plans --}}
                    <li class="nav-item {{ request()->is('user/plans') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.plans') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-id"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="3" y="4" width="18" height="16" rx="3"></rect>
                                    <circle cx="9" cy="10" r="2"></circle>
                                    <line x1="15" y1="8" x2="17" y2="8"></line>
                                    <line x1="15" y1="12" x2="17" y2="12"></line>
                                    <line x1="7" y1="16" x2="17" y2="16"></line>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Plans') }}
                            </span>
                        </a>
                    </li>

                    {{-- Transactions --}}
                    <li class="nav-item {{ request()->is('user/transactions') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.transactions') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                    <rect x="9" y="3" width="6" height="4" rx="2" />
                                    <path d="M14 11h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
                                    <path d="M12 17v1m0 -8v1" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Transactions') }}
                            </span>
                        </a>
                    </li>

                    @if (isset($plan_details))
                    @if ($plan_details->additional_tools == 1)
                    {{-- Additional Tools --}}
                    <li class="nav-item dropdown {{ request()->is('user/tools*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tools"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M3 21h4l13 -13a1.5 1.5 0 0 0 -4 -4l-13 13v4"></path>
                                    <line x1="14.5" y1="5.5" x2="18.5" y2="9.5"></line>
                                    <polyline points="12 8 7 3 3 7 8 12"></polyline>
                                    <line x1="7" y1="8" x2="5.5" y2="9.5"></line>
                                    <polyline points="16 12 21 17 17 21 12 16"></polyline>
                                    <line x1="16" y1="17" x2="14.5" y2="18.5"></line>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Addtional Tools') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('user.whois-lookup') }}">
                                {{ __('Whois Lookup') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.dns-lookup') }}">
                                {{ __('DNS Lookup') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.ip-lookup') }}">
                                {{ __('IP Lookup') }}
                            </a>
                        </div>
                    </li>
                    @endif
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>