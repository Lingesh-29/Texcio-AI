@extends('user.layouts.app')

{{-- Custom CSS --}}
@section('custom-css')
    <style>
        .border {
            border: 2px dotted #bbbcbe !important;
        }

        .addNew {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .fixed-title {
            width: 100%;
            overflow: hidden;
            text-wrap: nowrap;
            text-overflow: ellipsis;
        }
    </style>
@endsection

@section('content')
    <div class="page-wrapper">
        <div class="container-xl">
            <!-- Page title -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                            {{ __('Overview') }}
                        </div>
                        <h2 class="page-title">
                            {{ __('Recently Generated Images') }}
                        </h2>
                    </div>
                    {{-- Create new AI Image --}}
                    <div class="col-auto col-md-auto ms-auto">
                        <div class="btn-list">
                            <a href="{{ route('user.new.ai.image') }}" class="btn btn-primary d-sm-inline-block">
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
                                {{ __('Generate') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page body -->
        <div class="page-body">
            <div class="container-xl d-flex flex-column justify-content-center">

                {{-- Failed --}}
                @if (Session::has('failed'))
                    <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                {{ Session::get('failed') }}
                            </div>
                        </div>
                        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                {{-- Success --}}
                @if (Session::has('success'))
                    <div class="alert alert-important alert-success alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                {{ Session::get('success') }}
                            </div>
                        </div>
                        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                <div class="row row-cards row-deck mb-3">
                    {{-- Generated Images --}}
                    @foreach ($images as $image)
                        <div class="col-md-6 col-lg-3 col-6">
                            <div class="card card-sm">
                                @if ($image->format == 'b64_json')
                                    <a data-fslightbox="gallery"
                                        ref="data:image/png;base64,{{ json_decode($image->result)[0]->b64_json }}">
                                        <img src="data:image/png;base64,{{ json_decode($image->result)[0]->b64_json }}"
                                            alt="{{ $image->name }}" class="card-img-top">
                                    </a>
                                @endif
                                @if ($image->format == 'url')
                                    <a data-fslightbox="gallery" href="{{ url(json_decode($image->result)[0]) }}">
                                        <img src="{{ url(json_decode($image->result)[0]) }}" alt="{{ $image->name }}"
                                            class="card-img-top">
                                    </a>
                                @endif
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-red text-capitalize">{{ str_replace('-', ' ', $image->type) }}</span>
                                    </div>
                                    <h4 class="card-title text-uppercase my-1 fixed-title" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $image->name }}">{{ $image->name }}</h4>
                                    <div class="text-secondary">
                                        {{ __('Image Count') }} : <strong>{{ $image->n }}</strong> <br>
                                        {{ __('Size') }} : <strong>{{ $image->size }}</strong>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex">
                                        <strong>{{ __('Generated at') }}: <br> <span
                                                class="text-primary">{{ $image->updated_at->diffForHumans() }}</span></strong>
                                        {{-- View --}}
                                        <a class="btn btn-primary btn-icon ms-auto" data-bs-toggle="tooltip"
                                            data-bs-placement="right" title="{{ __('View') }}"
                                            href="{{ route('user.view.ai.image', $image->generate_id) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye"
                                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path
                                                    d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-lg-12 mt-3">
                        {{-- Pagination --}}
                        {{ $images->links() }}
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            @include('user.includes.footer')
        </div>
    </div>

    {{-- Delete content Modal --}}
    <div class="modal modal-blur fade" id="delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-title">{{ __('Are you sure?') }}</div>
                    <div id="deleteStatus"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <a class="btn btn-danger" id="deleteImageId">{{ __('Yes, proceed') }}</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom JS --}}
@section('custom-js')
    <script type="text/javascript" src="{{ asset('js/lightgallery.min.js') }}">
        < script >
            // Delete image
            function deleteImage(deleteImageId, deleteImageStatus) {
                "use strict";
                $("#delete-modal").modal("show");
                var deleteStatus = document.getElementById("deleteStatus");
                deleteStatus.innerHTML = "
                <?php echo __('If you proceed, you will'); ?> " + deleteImageStatus + "
                <?php echo __('this image.'); ?> "
                var delete_link = document.getElementById("deleteImageId");
                delete_link.getAttribute("href");
                delete_link.setAttribute("href", "{{ route('user.delete.ai.image') }}?id=" + deleteImageId);
            }
    </script>
@endsection
@endsection
