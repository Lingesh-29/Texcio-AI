@extends('admin.layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        {{ __('Overview') }}
                    </div>
                    <h2 class="page-title">
                        {{ __('Edit Payment Method') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">

            {{-- Failed --}}
            @if (Session::has("failed"))
            <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('failed')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Success --}}
            @if(Session::has("success"))
            <div class="alert alert-important alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        {{Session::get('success')}}
                    </div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Update payment gateway --}}
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <form action="{{ route('admin.update.payment.method') }}" method="post"
                        enctype="multipart/form-data" class="card">
                        @csrf
                        <div class="card-header">
                            <h4 class="page-title">{{ __('Payment Method Details') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-10">
                                    <div class="row">

                                        {{-- Payment gateway id --}}
                                        <input type="hidden" class="form-control" name="payment_gateway_id"
                                            placeholder="{{ __('Payment Method ID') }}"
                                            value="{{ $gateway_details->id }}" required readonly>

                                        {{-- Payment gateway name --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Payment Method Name')
                                                    }}</label>
                                                <input type="text" class="form-control" name="name"
                                                    placeholder="{{ __('Payment Method') }}"
                                                    value="{{ $gateway_details->name }}" required>
                                            </div>
                                        </div>

                                        {{-- Display name --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Display Name')
                                                    }}</label>
                                                <input type="text" class="form-control" name="display_name"
                                                    placeholder="{{ __('Display Name') }}"
                                                    value="{{ $gateway_details->display_name }}" required>
                                            </div>
                                        </div>

                                        {{-- Client ID --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Client ID') }} <span
                                                        class="text-muted">({{ __('Set 0 to disable it')
                                                        }})</span></label>
                                                <input type="text" class="form-control" name="client_id"
                                                    value="{{ $gateway_details->client_id }}"
                                                    placeholder="{{ __('Client ID') }}" required>
                                            </div>
                                        </div>

                                        {{-- Secret Key --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Secret Key') }} <span
                                                        class="text-muted">({{ __('Set 0 to disable it')
                                                        }})</span></label>
                                                <input type="text" class="form-control" name="secret_key"
                                                    value="{{ $gateway_details->secret_key }}"
                                                    placeholder="{{ __('Secret Key') }}" required>
                                            </div>
                                        </div>

                                        {{-- Status --}}
                                        <div class="col-md-6 col-xl-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Installed Status') }}</label>
                                                <div class="divide-y">
                                                    <div>
                                                        <label class="row">
                                                            <span class="col">{{ __('Status') }}</span>
                                                            <span class="col-auto">
                                                                <label class="form-check form-check-single form-switch">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="is_status" {{ $gateway_details->is_status
                                                                    == 'enabled' ? 'checked' : '' }}>
                                                                </label>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="text-end">
                                            <div class="d-flex">
                                                <button type="submit" class="btn btn-primary btn-md ms-auto">
                                                    {{ __('Update') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    @include('admin.includes.footer')
</div>
@endsection