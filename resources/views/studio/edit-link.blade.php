@extends('layouts.sidebar')

@section('content')

<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-12">
            <div class="card rounded">
                <div class="card-body p-3 p-md-4">
                    <div class="row">
                        <div class="col-12">
                            @push('sidebar-stylesheets')
                            <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
                            @endpush

                            <section class='text-gray-400'>
                                <!-- Mobile-friendly header -->
                                <div class="card-header bg-transparent border-0 px-0 mb-3">
                                    <h3 class="mb-0 d-flex align-items-center flex-wrap">
                                        <i class="bi bi-journal-plus me-2"></i>
                                        <span class="text-break">
                                            @if($LinkID !== 0) {{__('messages.Edit')}} @else {{__('messages.Add')}} @endif 
                                            {{__('messages.Block')}}
                                        </span>
                                    </h3>
                                </div>

                                <div class='card-body px-0'>
                                    <form action="{{ route('addLink') }}" method="post" id="my-form">
                                        @method('POST')
                                        @csrf
                                        <input type='hidden' name='linkid' value="{{ $LinkID }}" />

                                        <!-- Mobile responsive button group -->
                                        <div class="form-group mb-4">
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <button type="button" 
                                                        id='btnLinkType' 
                                                        class="btn btn-primary rounded-pill px-3 px-md-4 py-2" 
                                                        title='{{__('messages.Click to change link blocks')}}' 
                                                        data-toggle="modal" 
                                                        data-target="#SelectLinkType">
                                                    <span class="d-none d-sm-inline">{{__('messages.Select Block')}}</span>
                                                    <span class="d-inline d-sm-none">Select</span>
                                                    <span class="btn-inner ms-2">
                                                        <i class="bi bi-window-plus"></i>
                                                    </span>
                                                </button>
                                                <span class="d-none d-md-inline">
                                                    {{infoIcon(__('messages.Click for a list of available link blocks'))}}
                                                </span>
                                                <input type='hidden' name='linktype_id' value='{{$linkTypeID}}'>
                                            </div>
                                        </div>

                                        <!-- Dynamic link parameters -->
                                        <div id='link_params' class='mb-4'></div>

                                        <!-- Mobile-friendly action buttons -->
                                        <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 pt-4">
                                            <a class="btn btn-danger order-3 order-sm-1" href="{{ url('studio/links') }}">
                                                {{__('messages.Cancel')}}
                                            </a>
                                            <button type="submit" class="btn btn-primary order-1 order-sm-2">
                                                {{__('messages.Save')}}
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-soft-primary order-2 order-sm-3" 
                                                    onclick="submitFormWithParam('add_more')">
                                                <span class="d-none d-md-inline">{{__('messages.Save and Add More')}}</span>
                                                <span class="d-inline d-md-none">{{__('messages.Save')}} and Add More</span>
                                            </button>
                                        </div>

                                        <script>
                                            function submitFormWithParam(paramValue) {
                                                var form = document.getElementById("my-form");
                                                var requiredFields = form.querySelectorAll("[required]");
                                                
                                                for (var i = 0; i < requiredFields.length; i++) {
                                                    if (!requiredFields[i].value) {
                                                        alert("Please fill out all required fields.");
                                                        return false;
                                                    }
                                                }
                                                
                                                var paramField = document.createElement("input");
                                                paramField.setAttribute("type", "hidden");
                                                paramField.setAttribute("name", "param");
                                                paramField.setAttribute("value", paramValue);
                                                form.appendChild(paramField);
                                                form.submit();
                                            }
                                        </script>
                                    </form>
                                </div>
                            </section>

                            <!-- Modal with mobile improvements -->
                            <style>
                                .modal-title {
                                    color: #000 !important;
                                }
                                
                                /* Mobile modal improvements */
                                @media (max-width: 576px) {
                                    .modal-dialog {
                                        margin: 0.5rem;
                                    }
                                    
                                    .modal-content {
                                        border-radius: 0.5rem;
                                    }
                                    
                                    .link-type-card {
                                        margin-bottom: 0.75rem;
                                    }
                                    
                                    .link-type-icon {
                                        font-size: 1.5rem !important;
                                    }
                                }
                                
                                /* Improve touch targets on mobile */
                                .doSelectLinkType {
                                    display: block;
                                    text-decoration: none;
                                }
                                
                                .doSelectLinkType:hover,
                                .doSelectLinkType:focus {
                                    text-decoration: none;
                                    transform: scale(1.01);
                                    transition: transform 0.2s ease;
                                }
                                
                                .link-type-card {
                                    overflow: hidden;
                                    min-height: 85px;
                                }
                                
                                .card-title,
                                .card-text {
                                    word-wrap: break-word;
                                    overflow-wrap: break-word;
                                }
                            </style>

                            <x-modal title="{{__('messages.Select Block')}}" id="SelectLinkType">
                                <div class="d-flex flex-column p-2 p-md-3" style="max-height: 70vh; overflow-y: auto;">
                                    @php
                                    $custom_order = [1, 2, 8, 6, 7, 3, 4, 5];
                                    $sorted = $LinkTypes->sortBy(function ($item) use ($custom_order) {
                                        return array_search($item['id'], $custom_order);
                                    });
                                    @endphp

                                    @foreach ($sorted as $lt)
                                    @php 
                                    $title = __('messages.block.title.'.$lt['typename']); 
                                    $description = __('messages.block.description.'.$lt['typename']); 
                                    @endphp
                                    <a href="#" 
                                       data-dismiss="modal" 
                                       data-typeid="{{$lt['id']}}" 
                                       data-typename="{{$title}}" 
                                       class="doSelectLinkType">
                                        <div class="rounded shadow-sm link-type-card mb-3">
                                            <div class="row g-0">
                                                <div class="col-3 col-sm-2 bg-light d-flex align-items-start justify-content-center pt-4 pb-3 px-2">
                                                    <i class="{{$lt['icon']}} text-primary link-type-icon mb-0" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div class="col-9 col-sm-10">
                                                    <div class="card-body py-3 px-3 pe-2">
                                                        <h5 class="card-title text-dark mb-2 fw-semibold" style="font-size: 1rem; line-height: 1.3;">{{$title}}</h5>
                                                        <p class="card-text text-muted mb-0" style="font-size: 0.813rem; line-height: 1.5;">{{$description}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>

                                <x-slot name="buttons">
                                    <button type="button" class="btn btn-gray w-100 w-sm-auto" data-dismiss="modal">
                                        {{__('messages.Close')}}
                                    </button>
                                </x-slot>
                            </x-modal>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push("sidebar-scripts")
<script>
    $(function() {
        LoadLinkTypeParams($("input[name='linktype_id']").val(), $("input[name=linkid]").val());

        $('.doSelectLinkType').on('click', function(e) {
            e.preventDefault();
            $("input[name='linktype_id']").val($(this).data('typeid'));
            $("#btnLinkType").html($(this).data('typename') + ' <span class="btn-inner ms-2"><i class="bi bi-window-plus"></i></span>');
            
            LoadLinkTypeParams($(this).data('typeid'), $("input[name=linkid]").val());
            
            $('#SelectLinkType').modal('hide');
        });

        function LoadLinkTypeParams($TypeId, $LinkId) {
            var baseURL = <?php echo "\"" . url('') . "\""; ?>;
            $("#link_params").html('<div class="d-flex justify-content-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>')
                .load(baseURL + `/studio/linkparamform_part/${$TypeId}/${$LinkId}`);
        }
    });
</script>
@endpush