@extends('user.layouts.app')

{{-- Custom CSS --}}
@section('custom-css')
<!-- Bootstrap core CSS -->
<script src="https://cdn.tiny.cloud/1/{{ $config[36]->config_value }}/tinymce/6/tinymce.min.js" referrerpolicy="origin">
</script>
<script src="{{ asset('js/sweetalert.min.js') }}"></script>
@endsection

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
                        {{ __('AI Code') }}
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

            <div class="row row-deck row-cards">
                {{-- Parameters --}}
                <div class="col-xl-12">
                    <form action="javascript:void(0)" id="saveForm" method="POST" class="card">
                        @csrf
                        <div class="card-body">
                            <div class="row row-cards">
                                {{-- Which program do you need? --}}
                                <div class="col-sm-12 col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Which program do you need?')
                                            }}</label>
                                        <textarea class="form-control" name="description" id="description" rows="3"
                                            placeholder="{{ __("Write a PHP program to 'Hello World'") }}"
                                            maxlength="{{ $config[30]->config_value == null ? '600' : $config[30]->config_value }}"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" id="submit" class="btn btn-primary">{{
                                __('Generate')}}</button>
                        </div>
                    </form>
                </div>

                {{-- Result data --}}
                <div class="col-xl-12 px-3 d-none" id="response">
                    <div class="row row-cards">
                        <form action="{{ route('user.update.ai.code') }}" id="saveForm" method="POST" class="card">
                            @csrf
                            <div class="p-3">
                                <input type="hidden" name="generateId" id="generateId">
                                <textarea class="form-control" name="result" id="result"></textarea>

                                <div class="card-footer text-end">
                                    <button type="submit" id="submit" class="btn btn-primary">{{
                                        __('Update')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Footer --}}
@include('user.includes.footer')
</div>

{{-- Custom JS --}}
@section('custom-js')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script>
    tinymce.init({
      selector: 'textarea#result',
      plugins: 'preview importcss searchreplace autolink autosave save directionality visualblocks visualchars link template table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons code',
      menubar: 'file edit view tools insert format tools table help',
      toolbar: 'undo redo | code | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | preview save print | insertfile template link anchor | ltr rtl',
      toolbar_sticky: true,
      autosave_interval: '30s',
      autosave_prefix: '{path}{query}-{id}-',
      autosave_restore_when_empty: false,
      autosave_retention: '2m',
      content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
    });

    // Fill
    (function($) { 
        "use strict";
        $("#saveForm").validate({
            submitHandler: function(form) 
            {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#submit').html(`{{ __('Please Wait...') }}`);
                $("#submit"). attr("disabled", true);
                $.ajax({
                    url: "{{ route('user.generate.ai.code') }}",
                    type: "POST",
                    data: $('#saveForm').serialize(),
                    success: function(response) {
                        // Check result
                        if (response[0] != null) {
                            // Remove attribute
                            $('#response').removeClass('d-none');
                            $('#submit').html(`{{ __('Generate') }}`);
                            $("#submit").attr("disabled", false);
                        
                            // Get value
                            var myContent = tinymce.activeEditor.getContent();
                            // Set value
                            var textarea = myContent +'<br>' +response[0];

                            textarea = textarea.replace(/\n/g,'<br/>');

                            $("#generateId").val(response[1]);
                            tinymce.activeEditor.setContent(textarea);
                        } else {
                            Swal.fire(
                                `{{ __('Content Creation Failed') }}`,
                                ``,
                                'error'
                            );
                            // Remove attribute
                            $('#submit').html(`{{ __('Generate') }}`);
                            $("#submit").attr("disabled", false);
                        }
                    }
                });
            }
        })
    })(jQuery);
</script>
@endsection
@endsection