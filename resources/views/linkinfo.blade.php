


<!doctype html>
@include('layouts.lang')
  <head>
    <meta charset="utf-8">
    @php $GLOBALS['themeName'] = config('advanced-config.home_theme'); @endphp
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      @if(env('CUSTOM_META_TAGS') == 'true' and config('advanced-config.title') != '')
      <title>{{ config('advanced-config.title') }}</title>
      @else
      <title>{{ config('app.name') }}</title>
      @endif
      
      <!-- Favicon -->
      @if(file_exists(base_path("assets/linkstack/images/").findFile('favicon')))
      <link rel="icon" type="image/png" href="{{ asset('assets/linkstack/images/'.findFile('favicon')) }}">
      @else
      <link rel="icon" type="image/svg+xml" href="{{ asset('assets/linkstack/images/logo.svg') }}">
      @endif
      
      <script src="{{asset('assets/js/detect-dark-mode.js')}}"></script>
      <link rel="stylesheet" href="{{ asset('assets/external-dependencies/bootstrap-icons.css') }}">

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
  </head>

<body>
        <!--Nav Start-->
        <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
            <div class="container-fluid navbar-inner">
              <a href="{{ route('panelIndex') }}" class="navbar-brand">
                  
                  <!--Logo start-->
                  <div class="logo-main">
                    @if(file_exists(base_path("assets/linkstack/images/").findFile('avatar')))
                    <div class="logo-normal">
                      <img class="img logo" src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" style="width:auto;height:30px;">
                  </div>
                  <div class="logo-mini">
                    <img class="img logo" src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" style="width:auto;height:30px;">
                  </div>
                    @else
                    <div class="logo-normal">
                      <img class="img logo" type="image/svg+xml" src="{{ asset('assets/linkstack/images/logo.svg') }}" width="30px" height="30px">
                  </div>
                  <div class="logo-mini">
                    <img class="img logo" type="image/svg+xml" src="{{ asset('assets/linkstack/images/logo.svg') }}" width="30px" height="30px">
                  </div>
                    @endif
                    </div>
                  <!--logo End-->
                  
                  <!--<h4 class="logo-title">{{env('APP_NAME')}}</h4>-->
              </a>
            </div>
          </nav>
          <!--Nav End-->

          <div class="container mt-4">
            <div style="height: 89vh;" class="row align-items-center justify-content-center">
                <div class="col-md-8">
                    <div>
                        <h1>{{url('/going'.'/'.$linkID)}}</h1>
                        <p><i class="bi bi-arrow-return-right"></i> <a href="{{$link}}">{{$link}} <i style="font-size:80%" class="bi bi-box-arrow-up-right"></i></a></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="p-2 mb-3">
                                @if(file_exists(base_path(findAvatar($id))))
                                    <img alt="avatar" class="rounded-avatar fadein" src="{{ url(findAvatar($id)) }}" height="128px" width="128px" style="object-fit: cover;">
                                @elseif(file_exists(base_path("assets/linkstack/images/").findFile('avatar')))
                                    <img alt="avatar" class="fadein" src="{{ url("assets/linkstack/images/")."/".findFile('avatar') }}" height="128px" width="128px" style="object-fit: cover;">
                                @else
                                    <img alt="avatar" class="fadein" src="{{ asset('assets/linkstack/images/logo.svg') }}" height="128px" style="width:auto;min-width:128px;object-fit: cover;">
                                @endif
                            </div>
                            <h5 class="card-title">{{$userData->name}}</h5>
                            <p class="card-text"><a href="{{url("/@".$userData->littlelink_name)}}">{{url("/@".$userData->littlelink_name)}} <i style="font-size:80%" class="bi bi-box-arrow-up-right"></i></a></p>
                            <p class="card-text mt-2">{!!$userData->littlelink_description!!}</p>
                        </div>
                    </div>
                </div>
    
                @if(auth()->check() && auth()->user()->role == "admin")
                <hr class="my-4 border-top border-2 border-gray">
    
                <div class="tab-pane bd-heading-1 fade show active" id="content-Buttongroup-code" role="tabpanel">
                    <div class="section-block">
                    <pre class=" language-markup" tabindex="0">
                        <code class=" language-markup">
                            {{__('messages.ID')}}: {{$id}}
                            {{__('messages.Name')}}: {{$userData->name}}
                            {{__('messages.Handle:')}} {{$userData->littlelink_name}}
                            {{__('messages.Email')}}: {{$userData->email}}
                            {{__('messages.Role')}}: {{$userData->role}}
                            {{__('messages.Created at')}}: {{$userData->created_at}}
                            {{__('messages.Last seen')}}: {{$userData->updated_at}}
                            {{__('messages.Link Clicks:')}} {{$clicks}}</code>
                    </pre>
                    </div>                        
                </div>
    
                <div class="d-flex flex-column flex-md-row align-items-md-center">
                    <button class="btn btn-primary me-md-3 mb-3 mb-md-0"><a href="{{ route('deleteLink', $linkID ) }}" target="_blank" class="text-white confirmation">{{__('messages.Delete')}} {{strtolower(__('messages.Link'))}}</a></button>
                    <button class="btn btn-danger me-md-3 mb-3 mb-md-0"><a href="{{ route('deleteUser', ['id' => $id]) }}" target="_blank" class="text-white confirmation">{{__('messages.Delete')}} {{strtolower(__('messages.User'))}}</a></button>
                  </div>

                  <script type="text/javascript">
                    var elems = document.getElementsByClassName('confirmation');
                    var confirmIt = function (e) {
                        if (!confirm("{{__('messages.confirm.delete.user')}}")) e.preventDefault();
                    };
                    for (var i = 0, l = elems.length; i < l; i++) {
                        elems[i].addEventListener('click', confirmIt, false);
                    }
                  </script>
                @endif


    <!-- Footer -->
    <footer class="text-center mt-5">
        <ul class="mb-0 p-0 footer-content">
          @if(env('DISPLAY_FOOTER') === true)
            @if(env('DISPLAY_FOOTER_HOME') === true)<li class="list-inline-item"><a class="list-inline-item" href="@if(str_replace('"', "", EnvEditor::getKey('HOME_FOOTER_LINK')) === "" ){{ url('') }}@else{{ str_replace('"', "", EnvEditor::getKey('HOME_FOOTER_LINK')) }}@endif">{{footer('Home')}}</a></li>@endif
            @if(env('DISPLAY_FOOTER_TERMS') === true)<li class="list-inline-item"><a class="list-inline-item" href="{{ url('') }}/pages/{{ strtolower(footer('Terms')) }}">{{footer('Terms')}}</a></li>@endif
            @if(env('DISPLAY_FOOTER_PRIVACY') === true)<li class="list-inline-item"><a class="list-inline-item" href="{{ url('') }}/pages/{{ strtolower(footer('Privacy')) }}">{{footer('Privacy')}}</a></li>@endif
            @if(env('DISPLAY_FOOTER_CONTACT') === true)<li class="list-inline-item"><a class="list-inline-item" href="{{ url('') }}/pages/{{ strtolower(footer('Contact')) }}">{{footer('Contact')}}</a></li>@endif
          @endif
        </ul>
        <div class="right-panel">
          {{__('messages.Copyright')}} &copy; @php echo date('Y'); @endphp {{ config('app.name') }}
          @if(env('DISPLAY_CREDIT_FOOTER') === true)
                
             <a href="https://id.philtoa.com/" target="_blank" rel="noopener noreferrer" style="display: flex; justify-content: center; align-items: center;">

            <img class="img logo" src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" style="max-width: 100px; max-height: 100px;">
            
            </a>

              @endif
        </div>
    </footer>


            </div>
        </div>


</body>


    <!-- Library Bundle Script -->
    <script src="{{asset('assets/js/core/libs.min.js')}}"></script>
    
    <!-- External Library Bundle Script -->
    <script src="{{asset('assets/js/core/external.min.js')}}"></script>
    
    <!-- Widgetchart Script -->
    <script src="{{asset('assets/js/charts/widgetcharts.js')}}"></script>
    
    <!-- mapchart Script -->
    <script src="{{asset('assets/js/charts/vectore-chart.js')}}"></script>
    <script src="{{asset('assets/js/charts/dashboard.js')}}" ></script>
    
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
    
  </body>
</html>