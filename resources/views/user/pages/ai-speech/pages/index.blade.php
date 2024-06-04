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
                        {{ __('Speech to Text') }}
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
                    <form action="javascript:void(0)" id="saveForm" method="POST" enctype="multipart/form-data"
                        class="card">
                        @csrf
                        <div class="card-body">
                            <div class="row row-cards">
                                {{-- Choose Audio / Video File --}}
                                <div class="col-sm-6 col-md-6">
                                    <div class="mb-3">
                                        <div class="form-label required">{{ __('Choose Audio / Video File') }}</div>
                                        <input type="file" class="form-control" accept="audio/*,video/*" name="audio" id="audio" required>
                                        <small class="form-hint">
                                            {{ __('.mp3, .mp4, .mpeg, .mpga, .m4a, .wav, .webm allowed. Max audio /
                                            video file size: ') }} {{ env('SIZE_LIMIT') / 1024 }} {{ __('MB') }}
                                        </small>
                                    </div>
                                </div>
                                {{-- Audio / Video Description --}}
                                <div class="col-sm-6 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Audio / Video Description')
                                            }}</label>
                                        <textarea class="form-control" name="description" id="description" rows="3"
                                            maxlength="{{ $config[30]->config_value == null ? '600' : $config[30]->config_value }}"></textarea>
                                        <small class="form-hint">{{ __('Describe what your audio / video is about')
                                            }}</small>
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
                        <form action="{{ route('user.update.ai.speech.to.text') }}" id="saveForm" method="POST" class="card">
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
<script src="{{ asset('js/tom-select.base.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        "use strict";
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('lang'), {
    		copyClassesToDropdown: false,
    		dropdownClass: 'dropdown-menu ts-dropdown',
    		optionClass:'dropdown-item',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
</script>
<script>
    tinymce.init({
      selector: 'textarea#result',
      plugins: 'preview importcss searchreplace autolink autosave save directionality visualblocks visualchars link template table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
      menubar: 'file edit view insert format tools table help',
      toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | preview save print | insertfile template link anchor | ltr rtl',
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

                var formData = new FormData();

                let description = $("input[name=description]").val();
                var audio = $('#audio').prop('files')[0];   

                formData.append('description', description);
                formData.append('audio', audio);

                $.ajax({
                    url: "{{ route('user.generate.ai.speech.to.text') }}",
                    type: "POST",
                    data: formData,
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
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