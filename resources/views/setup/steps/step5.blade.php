<?php

use App\Models\Button; ?>

<style>
    .sortable-handle {
        margin-right: 25px;
        width: 25px;
        height: auto;
        transform: rotate(90deg);
        cursor: grab;
        cursor: -webkit-grabbing;
        fill: currentColor;
    }
</style>


<script src="{{ asset('assets/external-dependencies/fontawesome.js') }}" crossorigin="anonymous"></script>
<style>
    @media only screen and (max-width: 1500px) {
        .pre-side {
            display: none !important;
        }

        .pre-left {
            width: 100% !important;
        }

        .pre-bottom {
            display: block !important;
        }
    }

    @media only screen and (min-width: 1501px) {
        .pre-left {
            width: 70% !important;
        }

        .pre-right {
            width: 30% !important;
        }

        .pre-bottom {
            display: none !important;
        }
    }
</style>
<style>
    .delete {
        position: relative;
        color: gray;
        background-color: transparent;
        border-radius: 5px;
        left: 5px;
        padding: 5px 12px;
        cursor: pointer;
    }

    .delete:hover {
        color: transparent;
        background-color: #f13d1d;
    }

    html,
    body {
        max-width: 100%;
        overflow-x: hidden;
    }
</style>
@if(file_exists(base_path("assets/linkstack/images/").findFile('favicon')))
<link rel="icon" type="image/png" href="{{ asset('assets/linkstack/images/'.findFile('favicon')) }}">
@else
<link rel="icon" type="image/svg+xml" href="{{ asset('assets/linkstack/images/logo.svg') }}">
@endif

<!-- Library / Plugin Css Build -->
<link rel="stylesheet" href="{{asset('assets/css/core/libs.min.css')}}" />

<!-- Aos Animation Css -->
<link rel="stylesheet" href="{{asset('assets/vendor/aos/dist/aos.css')}}" />

@include('layouts.fonts')

<!-- Hope Ui Design System Css -->
<link rel="stylesheet" href="{{asset('assets/css/hope-ui.min.css?v=2.0.0')}}" />

<!-- Custom Css -->
<link rel="stylesheet" href="{{asset('assets/css/custom.min.css?v=2.0.0')}}" />

<!-- Dark Css -->
<link rel="stylesheet" href="{{asset('assets/css/dark.min.css')}}" />

<!-- Customizer Css -->
@if(file_exists(base_path("assets/dashboard-themes/dashboard.css")))
<link rel="stylesheet" href="{{asset('assets/dashboard-themes/dashboard.css')}}" />
@else
<link rel="stylesheet" href="{{asset('assets/css/customizer.min.css')}}" />
@endif

<!-- RTL Css -->
<link rel="stylesheet" href="{{asset('assets/css/rtl.min.css')}}" />

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('assets/linkstack/css/hover-min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/linkstack/css/animate.css') }}">
<link rel="stylesheet" href="{{ asset('assets/external-dependencies/bootstrap-icons.css') }}">

@include('components.favicon')
@include('components.favicon-extension')

<?php function strp($urlStrp)
{
    return str_replace(array('http://', 'https://'), '', $urlStrp);
} ?>
<div class="d-flex justify-content-center">
    <h6 style="font-size: 20px" class='form-label'>Add a Link!</h6>
</div>
<br>
<div class="row">
    <section class='pre-left text-gray-400' style="margin-bottom: 20px">
        <a class="btn btn-primary" id="addLinkButton">{{__('messages.Add new Link')}}</a>
        <!-- Hidden section to show when the button is clicked -->
        <div id="addLinkSection" style="display: none;">
            <!-- Your form or content goes here -->
            <section class='text-gray-400 d-flex'  >
                <div class='card-body'>
                    <x-modal title="{{__('messages.Select Block')}}" id="SelectLinkType">
                        <div class="d-flex flex-row  flex-wrap p-3">
                            @php
                            $custom_order = [1, 2, 8, 6, 7, 3, 4, 5,];
                            $sorted = $LinkTypes->sortBy(function ($item) use ($custom_order) {
                            return array_search($item['id'], $custom_order);
                            });
                            @endphp
                            @foreach ($sorted as $lt)
                            @php
                            $title = __('messages.block.title.'.$lt['typename']);
                            $description = __('messages.block.description.'.$lt['typename']);
                            @endphp
                            <a href="#" data-dismiss="modal" data-typeid="{{$lt['id']}}" data-typename="{{$title}}" class="hvr-grow m-2 w-100 d-block doSelectLinkType">
                                <div class="rounded mb-3 shadow-lg">
                                    <div class="row g-0">
                                        <div class="col-auto bg-light d-flex align-items-center justify-content-center p-3">
                                            <i class="{{$lt['icon']}} text-primary h1 mb-0"></i>
                                        </div>
                                        <div class="col">
                                            <div class="card-body">
                                                <h5 class="card-title text-dark mb-0">{{$title}}</h5>
                                                <p class="card-text text-muted">{{$description}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        <x-slot name="buttons">
                            <button type="button" class="btn btn-gray" data-dismiss="modal">{{__('messages.Close')}}</button>
                        </x-slot>
                    </x-modal>
                    <form action="" method="post" id="my-form">
                        @method('POST')
                        @csrf
                        <input type='hidden' name='linkid' value="{{ $LinkID }}" />
                        <div class="form-group col-lg-8 flex justify-around">
                            <div class="btn-group shadow m-2">
                                <select id="linkTypeSelect" class="form-control" style="width: 244px;">
                                    <option value="" disabled selected>Select a link type</option>
                                    @foreach ($sorted as $lt)
                                    @php
                                    $title = __('messages.block.title.'.$lt['typename']);
                                    @endphp
                                    <option value="{{$lt['id']}}" data-typename="{{$title}}">{{$title}}</option>
                                    @endforeach
                                </select>
                                <input type='hidden' name='linktype_id' id='linktype_id'>
                            </div>
                        </div>
                        <div id='link_params' class='col-lg-8'></div>
                        <div class="d-flex align-items-center pt-4">
                            <a class="btn btn-danger me-3" id="addLinkSection">{{__('messages.Cancel')}}</a>
                            <button type="submit" class="btn btn-primary me-3">Save</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Extract the code from the URL
            const url = new URL(window.location.href);
            const code = url.pathname.split('/').pop(); // Extracts the last part of the URL

            // Set the form action dynamically
            document.getElementById('my-form').action = `/setup-profile/${code}/save-link`;

            document.getElementById('my-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent the form from submitting normally
                var formData = new FormData(this); // Get form data

                // Include the current step in form data
                formData.append('currentStep', 4);

                // AJAX request to submit the form
                fetch(`/setup-profile/${code}/save-link`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Handle the response here
                        console.log(data);
                        // You can update the page based on the response without reloading
                        
                        // Reload the page
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });    
        });

        
    </script>
    <div style="font-size: 20px margin-top: 30px">
        <h6 class='form-label'>Your links</h6>
    </div>
    <div id="second-section">
    <section class='text-gray-400 d-flex' style="align-items: center; justify-content: center;">

        <div style="overflow-y: none;" class="col col-md-7 ms-3">

            <div id="links-table-body" data-page="{{request('page', 1)}}" data-per-page="{{$pagePage ? $pagePage : 0}}">
                @if($links->total() == 0)
                <div class="col-6 text-center">
                    <p class="mt-5">{{__('messages.No Link Added')}}</p>
                </div>
                @else
                @foreach($links as $link)
                @php $button = Button::find($link->button_id); if(isset($button->name)){$buttonName = $button->name;}else{$buttonName = 0;} @endphp
                @php if($buttonName == "default email"){$buttonName = "email";} if($buttonName == "default email_alt"){$buttonName = "email_alt";} @endphp
                @if($button->name !== 'icon')
                <div class='row pb-0 mb-2 border rounded hvr-glow' data-id="{{$link->id}}">
                    <div class="d-flex" style="align-items: center; justify-content: center;">


                        <div class='col-auto p-2 my-auto mr-2' title="{{ $link->link }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="sortable-handle" viewBox="0 0 16 16">
                                <path d="M1 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V4zM1 9a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V9z" />
                            </svg>
                        </div>

                        <div class='col h-80'>
                            <div class='row h-100'>
                                <div class='col-12 p-2' style="max-width:100%;overflow:hidden;" title="{{ $link->title }}">
                                    <span class='h6'>
                                        @if($button->name == "custom_website")
                                        <span class="bg-soft-secondary" style="border: 1px solid #d0d4d7 !important;border-radius:5px;width:25px!important;height:25px!important;"><img style="margin-bottom:3px;margin-left:4px;margin-right:4px;max-width:15px;max-height:15px;" alt="button-icon" class="icon hvr-icon" src="@if(file_exists(base_path(" assets/favicon/icons/").localIcon($link->id))){{url('assets/favicon/icons/'.localIcon($link->id))}}@else{{getFavIcon($link->id)}}@endif" onerror="this.onerror=null; this.src='{{asset('assets/linkstack/icons/website.svg')}}';"></span>
                                        @elseif($button->name == "space")
                                        <span class="bg-soft-secondary" style="border: 1px solid #d0d4d7 !important;border-radius:5px;width:25px!important;height:25px!important;"><i style="margin-left:2.83px;margin-right:-1px;color:#fff;" class='bi bi-distribute-vertical'>&nbsp;</i></span>
                                        @elseif($button->name == "heading")
                                        <span class="bg-soft-secondary" style="border: 1px solid #d0d4d7 !important;border-radius:5px;width:25px!important;height:25px!important;"><i style="margin-left:2.83px;margin-right:-1px;color:#fff;" class='bi bi-card-heading'>&nbsp;</i></span>
                                        @elseif($button->name == "text")
                                        <span class="bg-soft-secondary" style="border: 1px solid #d0d4d7 !important;border-radius:5px;width:25px!important;height:25px!important;"><i style="margin-left:2.83px;margin-right:-1px;color:#fff;" class='bi bi-fonts'>&nbsp;</i></span>
                                        @elseif($button->name == "buy me a coffee")
                                        <span class="bg-soft-secondary" style="border: 1px solid #d0d4d7 !important;border-radius:5px;width:25px!important;height:25px!important;"><img style="margin-left:6px!important;margin-right:6px!important;" alt="button-icon" height="15" class="m-1 " src="{{ asset('\/assets/linkstack/icons\/') . "coffee" }}.svg "></span>
                                        @else
                                        <span class="bg-soft-secondary" style="border: 1px solid #d0d4d7 !important;border-radius:5px;width:25px!important;height:25px!important;"><img style="max-width:15px !important;" alt="button-icon" height="15" class="m-1 " src="{{ asset('\/assets/linkstack/icons\/') . $buttonName }}.svg "></span>
                                        @endif

                                        {{strip_tags($link->title,'')}}</span>

                                    @if(!empty($link->link) and $button->name != "vcard")
                                    <br>
                                    <a title='{{$link->link}}' href="{{ $link->link}}" target="_blank" class="d-none d-md-block ml-4 text-muted small" style="font-size: 15px">{{Str::limit($link->link, 25 )}}</a>
                                    <a title='{{$link->link}}' href="{{ $link->link}}" target="_blank" class="d-md-none ml-4 text-muted small">{{Str::limit($link->link, 25 )}}</a>
                                    @elseif(!empty($link->link) and $button->name == "vcard")
                                    <br><a href="{{ url('vcard/'.$link->id) }}" target="_blank" class="ml-4 small">{{__('messages.Download')}}</a>

                                    @endif

                                </div>

                                <div class='col' class="text-right">
                                    {{Str::limit($link->params['text'] ?? null, 150)  }}

                                    @if($link->typename == 'video')
                                    @php
                                    $embed = OEmbed::get($link->link);
                                    if ($embed && $embed->hasThumbnail()) {
                                    echo "<img style='max-height: 150px;' src='".$embed->thumbnailUrl()."' />";

                                    }
                                    @endphp

                                    @endif
                                </div>


                                <div class='col-12 py-1 px-3 m-0 mt-2'>

                                    @if(!empty($link->link))
                                    <span><i class="bi bi-bar-chart-line"></i> {{ $link->click_number }} {{__('messages.Clicks')}}</span>

                                    @endif

                                    <a href="#" class="btn btn-sm me-1 btn-icon btn-danger delete-link" data-link-id="{{ $link->id }}" data-title="{{ addslashes($link->title) }}" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" data-bs-placement="top" data-original-title="{{__('messages.Delete')}}">
                                        <span class="btn-inner">
                                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                    </a>

                                    @if(env('ENABLE_BUTTON_EDITOR') === true)
                                    @if($link->button_id == '1' or $link->button_id == '2')
                                    <a style="float: right;" href="{{ route('editCSS', $link->id ) }}" class="btn btn-sm me-1 btn-icon btn-success" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Add" data-bs-placement="top" data-original-title="{{__('messages.Customize')}}">
                                        <span class="btn-inner">
                                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.8064 7.62361L20.184 6.54352C19.6574 5.6296 18.4905 5.31432 17.5753 5.83872V5.83872C17.1397 6.09534 16.6198 6.16815 16.1305 6.04109C15.6411 5.91402 15.2224 5.59752 14.9666 5.16137C14.8021 4.88415 14.7137 4.56839 14.7103 4.24604V4.24604C14.7251 3.72922 14.5302 3.2284 14.1698 2.85767C13.8094 2.48694 13.3143 2.27786 12.7973 2.27808H11.5433C11.0367 2.27807 10.5511 2.47991 10.1938 2.83895C9.83644 3.19798 9.63693 3.68459 9.63937 4.19112V4.19112C9.62435 5.23693 8.77224 6.07681 7.72632 6.0767C7.40397 6.07336 7.08821 5.98494 6.81099 5.82041V5.82041C5.89582 5.29601 4.72887 5.61129 4.20229 6.52522L3.5341 7.62361C3.00817 8.53639 3.31916 9.70261 4.22975 10.2323V10.2323C4.82166 10.574 5.18629 11.2056 5.18629 11.8891C5.18629 12.5725 4.82166 13.2041 4.22975 13.5458V13.5458C3.32031 14.0719 3.00898 15.2353 3.5341 16.1454V16.1454L4.16568 17.2346C4.4124 17.6798 4.82636 18.0083 5.31595 18.1474C5.80554 18.2866 6.3304 18.2249 6.77438 17.976V17.976C7.21084 17.7213 7.73094 17.6516 8.2191 17.7822C8.70725 17.9128 9.12299 18.233 9.37392 18.6717C9.53845 18.9489 9.62686 19.2646 9.63021 19.587V19.587C9.63021 20.6435 10.4867 21.5 11.5433 21.5H12.7973C13.8502 21.5001 14.7053 20.6491 14.7103 19.5962V19.5962C14.7079 19.088 14.9086 18.6 15.2679 18.2407C15.6272 17.8814 16.1152 17.6807 16.6233 17.6831C16.9449 17.6917 17.2594 17.7798 17.5387 17.9394V17.9394C18.4515 18.4653 19.6177 18.1544 20.1474 17.2438V17.2438L20.8064 16.1454C21.0615 15.7075 21.1315 15.186 21.001 14.6964C20.8704 14.2067 20.55 13.7894 20.1108 13.5367V13.5367C19.6715 13.284 19.3511 12.8666 19.2206 12.3769C19.09 11.8873 19.16 11.3658 19.4151 10.928C19.581 10.6383 19.8211 10.3982 20.1108 10.2323V10.2323C21.0159 9.70289 21.3262 8.54349 20.8064 7.63277V7.63277V7.62361Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <circle cx="12.1747" cy="11.8891" r="2.63616" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
                                            </svg>
                                        </span>
                                    </a>
                                    @endif
                                    @endif



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
                @endif
            </div>
            <script type="text/javascript">
                const linksTableOrders = "{{ implode(' | ', $links->pluck('id')->toArray()) }}"
            </script>


        </div>

        <ul class="pagination justify-content-center">
            {!! $links ?? ''->links() !!}
        </ul>

       
</div>
</section>
</div>
</div>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<!-- Library Bundle Script -->
<script src="{{asset('assets/js/core/libs.min.js')}}"></script>

<!-- External Library Bundle Script -->
<script src="{{asset('assets/js/core/external.min.js')}}"></script>

<!-- Widgetchart Script -->
<script src="{{asset('assets/js/charts/widgetcharts.js')}}"></script>

<!-- mapchart Script -->
<script src="{{asset('assets/js/charts/vectore-chart.js')}}"></script>
<script src="{{asset('assets/js/charts/dashboard.js')}}"></script>

<!-- fslightbox Script -->
<script src="{{asset('assets/js/plugins/fslightbox.js')}}"></script>

<!-- Settings Script -->
<script src="{{asset('assets/js/plugins/setting.js')}}"></script>

<!-- Slider-tab Script -->
<script src="{{asset('assets/js/plugins/slider-tabs.js')}}"></script>

<!-- Form Wizard Script -->
<script src="{{asset('assets/js/plugins/form-wizard.js')}}"></script>

<!-- AOS Animation Plugin-->
<script src="{{asset('assets/vendor/aos/dist/aos.js')}}"></script>

<!-- App Script -->
<script src="{{asset('assets/js/hope-ui.js')}}" defer></script>

<!-- Flatpickr Script -->
<script src="{{asset('assets/vendor/flatpickr/dist/flatpickr.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/flatpickr.js')}}" defer></script>

<script src="{{asset('assets/js/plugins/prism.mini.js')}}"></script>
<script src="{{ asset('assets/js/popper.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/Sortable.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-block-ui.js') }}"></script>
<script src="{{ asset('assets/js/main-dashboard.js') }}"></script>

<script>
    document.getElementById('addLinkButton').addEventListener('click', function() {
        // Toggle the display of the addLinkSection
        var section = document.getElementById('addLinkSection');
        if (section.style.display === "none") {
            section.style.display = "block";
        } else {
            section.style.display = "none";
        }
        var baseURL = <?php echo "\"" . url('') . "\""; ?>;
        console.log(baseURL)
    });
    $('#linkTypeSelect').change(function() {
        var typeId = $(this).val();
        var typeName = $(this).find('option:selected').data('typename');
        $("#btnLinkType").html(typeName);
        $("#linktype_id").val(typeId);
        LoadLinkTypeParams(typeId, $("input[name=linkid]").val());
    });

    function LoadLinkTypeParams($TypeId, $LinkId) {
        var baseURL = <?php echo "\"" . url('') . "\""; ?>;
        $("#link_params").html('<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>').load(baseURL + `/linkparamform_part/${$TypeId}/${$LinkId}`);
    }

    $(document).ready(function() {
        $('.delete-link').click(function(event) {
            event.preventDefault(); // Prevent the default behavior of the link
            
            var linkId = $(this).data('link-id');
            var title = $(this).data('title');
            
            if (confirm("Are you sure you want to delete '" + title + "'?")) {
                $.ajax({
                    type: 'GET', // Change the request type to GET
                    url: '{{ url("/removeLink") }}/' + linkId, // Include the ID in the URL
                    success: function(response) {
                        // Handle success response
                        alert(response.message);
                        // Optionally, you can remove the deleted link from the UI
                        // $(this).closest('.link-container').remove();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        alert('Error: ' + error);
                    }
                });
            }
        });
    });
</script>