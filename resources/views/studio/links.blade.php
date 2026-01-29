@extends('layouts.sidebar')

@section('content')
<?php use App\Models\Button; 

// Check if the LinkCount cookie is set
if (isset($_COOKIE['LinkCount'])) {
  // Set the expiration time of the cookie to one hour in the past
  setcookie('LinkCount', '', time() - 3600);
}

?>

<style>
/* Drag Handle Styles */
.sortable-handle {
    margin-right: 12px;
    width: 18px;
    height: auto;
    transform: rotate(90deg);
    cursor: grab;
    fill: #9ca3af;
    flex-shrink: 0;
    transition: fill 0.2s ease;
    touch-action: none;
    -webkit-user-drag: none;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.sortable-handle:hover {
    fill: #6b7280;
}

.sortable-handle:active {
    cursor: grabbing;
}

@media (min-width: 768px) {
    .sortable-handle {
        margin-right: 16px;
        width: 20px;
    }
}

/* Link Card - Prevent text selection during drag */
.link-card {
    padding: 1rem 1.25rem;
    transition: all 0.2s ease;
    background: #ffffff;
    border: 1px solid #e5e7eb !important;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

/* Allow text selection only on link URLs when not dragging */
.link-card .link-url {
    user-select: text;
    -webkit-user-select: text;
    -moz-user-select: text;
    -ms-user-select: text;
}

/* Disable text selection when dragging */
.link-card.sortable-ghost,
.link-card.sortable-drag {
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
}

.link-card.sortable-ghost .link-url,
.link-card.sortable-drag .link-url {
    user-select: none !important;
    -webkit-user-select: none !important;
}

.link-card:hover {
    border-color: #d1d5db !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

/* Dragging states */
.link-card.sortable-ghost {
    opacity: 0.4;
    background: #f9fafb;
}

.link-card.sortable-drag {
    opacity: 1;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    transform: rotate(2deg);
    cursor: grabbing;
}

/* Button Icon Wrapper */
.button-icon-wrapper {
    border: 1px solid #e5e7eb !important;
    border-radius: 10px;
    width: 40px !important;
    height: 40px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: #f9fafb;
    transition: all 0.2s ease;
    margin-right: 12px;
}

.link-card:hover .button-icon-wrapper {
    background: #f3f4f6;
}

.button-icon-wrapper img,
.button-icon-wrapper i {
    max-width: 20px;
    max-height: 20px;
    opacity: 0.7;
    pointer-events: none;
}

.button-icon-wrapper i {
    color: #4b5563 !important;
    font-size: 16px;
}

/* Link Content Wrapper */
.link-content-wrapper {
    flex-grow: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.link-title-text {
    font-weight: 600;
    color: #111827;
    font-size: 0.9375rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    pointer-events: none;
}

.link-url {
    font-size: 0.8125rem;
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

.link-url:hover {
    color: #374151;
}

.link-url i {
    font-size: 0.75rem;
    opacity: 0.7;
}

/* Stats and Actions */
.link-stats-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
    margin-left: 1rem;
}

.stats-text {
    font-size: 0.8125rem;
    color: #6b7280;
    font-weight: 500;
    padding: 0.375rem 0.625rem;
    background: #f9fafb;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    border: 1px solid #f3f4f6;
    white-space: nowrap;
    pointer-events: none;
}

.stats-text i {
    color: #9ca3af;
    font-size: 0.75rem;
}

/* Action Buttons */
.link-actions-wrapper {
    display: flex;
    flex-wrap: nowrap;
    gap: 0.375rem;
    align-items: center;
}

.link-actions-wrapper .btn-icon {
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.link-actions-wrapper .btn-icon:hover {
    transform: translateY(-1px);
}

.link-actions-wrapper .btn-warning:hover {
    color: #374151;
    border-color: #d1d5db;
    background: #f9fafb;
}

.link-actions-wrapper .btn-success:hover {
    color: #2563eb;
    border-color: #bfdbfe;
    background: #eff6ff;
}

.link-actions-wrapper .btn-danger:hover {
    color: #dc2626;
    border-color: #fecaca;
    background: #fef2f2;
}

.link-actions-wrapper .btn svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
    pointer-events: none;
}

.icon-refresh-btn {
    color: #6b7280;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
}

.icon-refresh-btn:hover {
    color: #374151;
    background: #f3f4f6;
    transform: translateY(-1px);
}

.icon-refresh-btn i {
    font-size: 0.875rem;
}

/* Text Content */
.link-text-content {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    line-height: 1.5;
    pointer-events: none;
}

/* Video Thumbnail */
.link-card img.img-fluid {
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    margin-top: 0.5rem;
    pointer-events: none;
}

/* Mobile Responsive - Improved Layout */
@media (max-width: 768px) {
    .link-card {
        padding: 0.875rem;
        gap: 0.5rem;
    }
    
    /* Stack layout on mobile */
    .link-card {
        flex-direction: row;
        flex-wrap: wrap;
    }
    
    /* Drag handle stays on left */
    .sortable-handle {
        margin-right: 8px;
        width: 16px;
        align-self: flex-start;
        margin-top: 2px;
    }
    
    /* Icon wrapper smaller on mobile */
    .button-icon-wrapper {
        width: 36px !important;
        height: 36px !important;
        margin-right: 10px;
    }
    
    .button-icon-wrapper img,
    .button-icon-wrapper i {
        max-width: 18px;
        max-height: 18px;
    }
    
    /* Content takes remaining space in first row */
    .link-content-wrapper {
        flex: 1;
        min-width: 0;
    }
    
    .link-title-text {
        font-size: 0.875rem;
    }
    
    .link-url {
        font-size: 0.75rem;
    }
    
    /* Stats and actions on new row - full width */
    .link-stats-actions {
        width: 100%;
        margin-left: 0;
        padding-left: 40px; /* Align with content (drag handle + icon width) */
        margin-top: 0.625rem;
        gap: 0.5rem;
        justify-content: space-between;
    }
    
    /* Smaller stats badge */
    .stats-text {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        gap: 0.25rem;
    }
    
    /* Compact action buttons */
    .link-actions-wrapper {
        gap: 0.25rem;
    }
    
    .link-actions-wrapper .btn-icon,
    .icon-refresh-btn {
        width: 32px;
        height: 32px;
        padding: 0.375rem;
    }
    
    .link-actions-wrapper .btn svg {
        width: 14px;
        height: 14px;
    }
    
    .icon-refresh-btn i {
        font-size: 0.75rem;
    }
    
    /* Text content full width */
    .link-text-content {
        width: 100%;
        margin-left: 40px;
        font-size: 0.8125rem;
    }
    
    /* Video thumbnail adjustments */
    .link-card img.img-fluid {
        width: 100%;
        margin-left: 40px;
        max-height: 100px;
    }
}

/* Extra small devices */
@media (max-width: 480px) {
    .link-card {
        padding: 0.75rem;
    }
    
    .link-title-text {
        font-size: 0.8125rem;
    }
    
    .link-stats-actions {
        padding-left: 36px;
    }
    
    .link-actions-wrapper .btn-icon,
    .icon-refresh-btn {
        width: 30px;
        height: 30px;
    }
    
    .link-actions-wrapper .btn svg {
        width: 13px;
        height: 13px;
    }
}
</style>

@push('sidebar-stylesheets')
<script src="{{ asset('assets/external-dependencies/fontawesome.js') }}" crossorigin="anonymous"></script>
<style>
/* Responsive layout breakpoints */
@media only screen and (max-width: 1500px) {
  .pre-side{display:none!important;}
  .pre-left{width:100%!important;}
  .pre-bottom{display:block!important;}
}

@media only screen and (min-width: 1501px) {
  .pre-left{width:70%!important;}
  .pre-right{width:30%!important;}
  .pre-bottom{display:none!important;}
}

/* Mobile optimizations */
@media only screen and (max-width: 768px) {
  .card-header h3 {
    font-size: 1.25rem;
  }
  
  .btn-float-end-mobile {
    width: 100%;
    margin-top: 10px;
  }
}

html, body {
  max-width: 100%;
  overflow-x: hidden;
}

/* Professional minimalist link cards */
.link-card {
  padding: 1rem 1.25rem;
  transition: all 0.2s ease;
  background: #ffffff;
  border: 1px solid #e5e7eb !important;
  border-radius: 12px;
  position: relative;
  overflow: hidden;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0;
}

.link-card:hover {
  border-color: #d1d5db !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

/* Minimalist button icon */
.button-icon-wrapper {
  border: 1px solid #e5e7eb !important;
  border-radius: 10px;
  width: 40px !important;
  height: 40px !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  background: #f9fafb;
  transition: all 0.2s ease;
  margin-right: 12px;
}

.link-card:hover .button-icon-wrapper {
  background: #f3f4f6;
}

.button-icon-wrapper img,
.button-icon-wrapper i {
  max-width: 20px;
  max-height: 20px;
  opacity: 0.7;
}

.button-icon-wrapper i {
  color: #4b5563 !important;
  font-size: 16px;
}

/* Link title and content */
.link-content-wrapper {
  flex-grow: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.link-title-text {
  font-weight: 600;
  color: #111827;
  font-size: 0.9375rem;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.link-url {
  font-size: 0.8125rem;
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s ease;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
}

.link-url:hover {
  color: #374151;
}

.link-url i {
  font-size: 0.75rem;
  opacity: 0.7;
}

/* Stats and actions wrapper */
.link-stats-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-shrink: 0;
  margin-left: 1rem;
}

body.no-select {
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
}

/* Stats text minimalist */
.stats-text {
  font-size: 0.8125rem;
  color: #6b7280;
  font-weight: 500;
  padding: 0.375rem 0.625rem;
  background: #f9fafb;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  border: 1px solid #f3f4f6;
  white-space: nowrap;
}

.stats-text i {
  color: #9ca3af;
  font-size: 0.75rem;
}

/* Minimalist action buttons */
.link-actions-wrapper {
  display: flex;
  flex-wrap: nowrap;
  gap: 0.375rem;
  align-items: center;
}

.link-actions-wrapper .btn-icon {
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s ease;
  border: 1px solid #e5e7eb;
  background: #ffffff;
  width: 36px;
  height: 36px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
}

.link-actions-wrapper .btn-icon:hover {
  transform: translateY(-1px);
}

/* Edit button */
.link-actions-wrapper .btn-warning:hover {
  color: #374151;
  border-color: #d1d5db;
  background: #f9fafb;
}

/* Customize button */
.link-actions-wrapper .btn-success:hover {
  color: #2563eb;
  border-color: #bfdbfe;
  background: #eff6ff;
}

/* Delete button */
.link-actions-wrapper .btn-danger:hover {
  color: #dc2626;
  border-color: #fecaca;
  background: #fef2f2;
}

/* Icon styling within buttons */
.link-actions-wrapper .btn svg {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

/* Refresh icon button */
.icon-refresh-btn {
  color: #6b7280;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: 1px solid #e5e7eb;
  background: #ffffff;
}

.icon-refresh-btn:hover {
  color: #374151;
  background: #f3f4f6;
  transform: translateY(-1px);
}

.icon-refresh-btn i {
  font-size: 0.875rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .link-card {
    flex-wrap: wrap;
    padding: 1rem;
  }
  
  .link-stats-actions {
    width: 100%;
    margin-left: 52px;
    margin-top: 0.75rem;
    justify-content: space-between;
  }
  
  .stats-text {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
  }
  
  .link-actions-wrapper .btn-icon {
    width: 32px;
    height: 32px;
    padding: 0.375rem;
  }
  
  .link-actions-wrapper .btn svg {
    width: 14px;
    height: 14px;
  }
}

/* Primary action button - minimalist */
.btn-primary {
  background: #111827;
  border-color: #111827;
  color: #ffffff;
  font-weight: 500;
  padding: 0.625rem 1.25rem;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.btn-primary:hover {
  background: #1f2937;
  border-color: #1f2937;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Form improvements for mobile */
@media (max-width: 768px) {
  .form-group {
    margin-bottom: 1rem;
  }
  
  .input-group {
    flex-wrap: nowrap;
  }
  
  .input-group .btn {
    flex-shrink: 0;
  }
}

/* Icon section responsive */
.icon-form-wrapper {
  width: 100%;
}

@media (min-width: 992px) {
  .icon-form-wrapper {
    width: 66.666667%;
  }
}

/* Preview iframe improvements */
.preview-wrapper {
  width: 100%;
  max-width: 100%;
}

@media (min-width: 768px) {
  .preview-wrapper iframe {
    max-width: 500px !important;
  }
}

/* Input styling */
input {
  border-top-right-radius: 0.25rem !important;
  border-bottom-right-radius: 0.25rem !important;
}

/* Card header improvements */
.card-header h3 {
  font-weight: 600;
  color: #111827;
  font-size: 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.card-header h3 i {
  color: #6b7280;
}

/* Video thumbnail styling */
.link-card img.img-fluid {
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  margin-top: 0.5rem;
}

/* Text content */
.link-text-content {
  color: #6b7280;
  font-size: 0.875rem;
  margin-top: 0.5rem;
  line-height: 1.5;
}
</style>
@endpush

@include('components.favicon')
@include('components.favicon-extension')

<?php function strp($urlStrp){return str_replace(array('http://', 'https://'), '', $urlStrp);} ?>

<div class="conatiner-fluid content-inner mt-n5 py-0">
    <div class="row">   

        <div class="col-lg-12">
            <div class="card rounded">
               <div class="card-body">
                  <div class="row">
                      <div class="col-sm-12">  
    
                        <div class="row">
                            <section class='pre-left text-gray-400'>
                                <div class="card-header mb-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                                    <h3 class="mb-2 mb-md-0">
                                        <i class="bi bi-link-45deg"></i>{{__('messages.My Links')}}
                                    </h3>
                                    <a class="btn btn-primary btn-float-end-mobile" href="{{ url('/studio/add-link') }}">
                                        {{__('messages.Add new Link')}}
                                    </a>
                                </div>
                            
                                <div>
                            
                                <div style="overflow-y: none;" class="col-12 col-md-11 col-lg-7 mx-auto mx-md-3">
                            
                                    <div id="links-table-body" data-page="{{request('page', 1)}}" data-per-page="{{$pagePage ? $pagePage : 0}}">
                                        @if($links->total() == 0)
                                              <div class="col-12 text-center">
                                                <p class="mt-5">{{__('messages.No Link Added')}}</p>
                                              </div>
                                        @else
                                        @foreach($links as $link)
                                        @php $button = Button::find($link->button_id); if(isset($button->name)){$buttonName = $button->name;}else{$buttonName = 0;} @endphp
                                        @php if($buttonName == "default email"){$buttonName = "email";} if($buttonName == "default email_alt"){$buttonName = "email_alt";} @endphp
                                        @if($button->name !== 'icon')
                                        <div class='link-card' data-id="{{$link->id}}">
                                            {{-- Drag Handle --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="sortable-handle" viewBox="0 0 16 16" title="{{ $link->link }}">
                                                <path d="M1 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V4zM1 9a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V9z"/>
                                            </svg>

                                            {{-- Icon --}}
                                            @if($button->name == "custom_website")
                                            <span class="button-icon-wrapper">
                                                <img alt="button-icon" class="icon hvr-icon" src="@if(file_exists(base_path("assets/favicon/icons/").localIcon($link->id))){{url('assets/favicon/icons/'.localIcon($link->id))}}@else{{getFavIcon($link->id)}}@endif" onerror="this.onerror=null; this.src='{{asset('assets/linkstack/icons/website.svg')}}';">
                                            </span>
                                            @elseif($button->name == "space")
                                            <span class="button-icon-wrapper">
                                                <i class='bi bi-distribute-vertical'></i>
                                            </span>
                                            @elseif($button->name == "heading")
                                            <span class="button-icon-wrapper">
                                                <i class='bi bi-card-heading'></i>
                                            </span>
                                            @elseif($button->name == "text")
                                            <span class="button-icon-wrapper">
                                                <i class='bi bi-fonts'></i>
                                            </span>
                                            @elseif($button->name == "buy me a coffee")
                                            <span class="button-icon-wrapper">
                                                <img alt="button-icon" src="{{ asset('\/assets/linkstack/icons\/') . "coffee" }}.svg ">
                                            </span>
                                            @else
                                            <span class="button-icon-wrapper">
                                                <img alt="button-icon" src="{{ asset('\/assets/linkstack/icons\/') . $buttonName }}.svg ">
                                            </span>
                                            @endif

                                            {{-- Content (Title & URL) --}}
                                            <div class="link-content-wrapper">
                                                <h6 class="link-title-text" title="{{ $link->title }}">
                                                    {{strip_tags($link->title,'')}}
                                                </h6>

                                                @if(!empty($link->link) and $button->name != "vcard")
                                                <a title='{{$link->link}}' href="{{ $link->link}}" target="_blank" class="link-url d-none d-md-block">
                                                    <i class="bi bi-link-45deg"></i>{{Str::limit($link->link, 60)}}
                                                </a>
                                                <a title='{{$link->link}}' href="{{ $link->link}}" target="_blank" class="link-url d-md-none">
                                                    <i class="bi bi-link-45deg"></i>{{Str::limit($link->link, 25)}}
                                                </a>
                                                @elseif(!empty($link->link) and $button->name == "vcard")
                                                <a href="{{ url('vcard/'.$link->id) }}" target="_blank" class="link-url">
                                                    <i class="bi bi-download"></i>{{__('messages.Download')}}
                                                </a>
                                                @endif

                                                {{-- Text content or video --}}
                                                @if(!empty($link->params['text']))
                                                <div class="link-text-content">
                                                    {{Str::limit($link->params['text'], 150)}}
                                                </div>
                                                @endif

                                                @if($link->typename == 'video')
                                                    @php
                                                        $embed = OEmbed::get($link->link);
                                                        if ($embed && $embed->hasThumbnail()) {
                                                            echo "<img class='img-fluid' style='max-height: 120px;' src='".$embed->thumbnailUrl()."' />";
                                                        }
                                                    @endphp
                                                @endif
                                            </div>

                                            {{-- Stats & Actions --}}
                                            <div class="link-stats-actions">
                                                {{-- Click Stats --}}
                                                @if(!empty($link->link))
                                                <span class="stats-text">
                                                    <i class="bi bi-bar-chart-line"></i>
                                                    <span>{{ $link->click_number }}</span>
                                                </span>
                                                @endif

                                                {{-- Action Buttons --}}
                                                <div class="link-actions-wrapper">
                                                    <a href="{{ route('editLink', $link->id ) }}" class="btn btn-sm btn-icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="{{__('messages.Edit')}}" aria-label="Edit">
                                                       <span class="btn-inner">
                                                          <svg width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                             <path d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                             <path fill-rule="evenodd" clip-rule="evenodd" d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                             <path d="M15.1655 4.60254L19.7315 9.16854" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                          </svg>
                                                       </span>
                                                    </a>

                                                    @if(env('ENABLE_BUTTON_EDITOR') === true)
                                                    @if($link->button_id == '1' or $link->button_id == '2')
                                                        <a href="{{ route('editCSS', $link->id ) }}" class="btn btn-sm btn-icon btn-success" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Customize" data-original-title="{{__('messages.Customize')}}">
                                                            <span class="btn-inner">
                                                                <svg width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                                  <path fill-rule="evenodd" clip-rule="evenodd" d="M20.8064 7.62361L20.184 6.54352C19.6574 5.6296 18.4905 5.31432 17.5753 5.83872V5.83872C17.1397 6.09534 16.6198 6.16815 16.1305 6.04109C15.6411 5.91402 15.2224 5.59752 14.9666 5.16137C14.8021 4.88415 14.7137 4.56839 14.7103 4.24604V4.24604C14.7251 3.72922 14.5302 3.2284 14.1698 2.85767C13.8094 2.48694 13.3143 2.27786 12.7973 2.27808H11.5433C11.0367 2.27807 10.5511 2.47991 10.1938 2.83895C9.83644 3.19798 9.63693 3.68459 9.63937 4.19112V4.19112C9.62435 5.23693 8.77224 6.07681 7.72632 6.0767C7.40397 6.07336 7.08821 5.98494 6.81099 5.82041V5.82041C5.89582 5.29601 4.72887 5.61129 4.20229 6.52522L3.5341 7.62361C3.00817 8.53639 3.31916 9.70261 4.22975 10.2323V10.2323C4.82166 10.574 5.18629 11.2056 5.18629 11.8891C5.18629 12.5725 4.82166 13.2041 4.22975 13.5458V13.5458C3.32031 14.0719 3.00898 15.2353 3.5341 16.1454V16.1454L4.16568 17.2346C4.4124 17.6798 4.82636 18.0083 5.31595 18.1474C5.80554 18.2866 6.3304 18.2249 6.77438 17.976V17.976C7.21084 17.7213 7.73094 17.6516 8.2191 17.7822C8.70725 17.9128 9.12299 18.233 9.37392 18.6717C9.53845 18.9489 9.62686 19.2646 9.63021 19.587V19.587C9.63021 20.6435 10.4867 21.5 11.5433 21.5H12.7973C13.8502 21.5001 14.7053 20.6491 14.7103 19.5962V19.5962C14.7079 19.088 14.9086 18.6 15.2679 18.2407C15.6272 17.8814 16.1152 17.6807 16.6233 17.6831C16.9449 17.6917 17.2594 17.7798 17.5387 17.9394V17.9394C18.4515 18.4653 19.6177 18.1544 20.1474 17.2438V17.2438L20.8064 16.1454C21.0615 15.7075 21.1315 15.186 21.001 14.6964C20.8704 14.2067 20.55 13.7894 20.1108 13.5367V13.5367C19.6715 13.284 19.3511 12.8666 19.2206 12.3769C19.09 11.8873 19.16 11.3658 19.4151 10.928C19.581 10.6383 19.8211 10.3982 20.1108 10.2323V10.2323C21.0159 9.70289 21.3262 8.54349 20.8064 7.63277V7.63277V7.62361Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                  <circle cx="12.1747" cy="11.8891" r="2.63616" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>   
                                                              </svg>
                                                             </span>
                                                        </a>
                                                    @endif
                                                    @endif

                                                    <a href="{{ route('deleteLink', $link->id ) }}" onclick="return confirm('{{ __('messages.confirm_delete', ['title' => addslashes($link->title)]) }}')" class="btn btn-sm btn-icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" data-original-title="{{__('messages.Delete')}}">
                                                        <span class="btn-inner">
                                                           <svg width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                              <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                              <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                              <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                           </svg>
                                                        </span>
                                                     </a>

                                                    @if(file_exists(base_path("assets/favicon/icons/").localIcon($link->id)))
                                                    <a href="{{ route('clearIcon', $link->id ) }}" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Clear cache" data-original-title="Clear icon cache" class="icon-refresh-btn">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </a>
                                                    @endif
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
                            
                                @if(count($links) > 3)
                                <div class="text-center text-md-start">
                                    <a class="btn btn-primary" href="{{ url('/studio/add-link') }}">{{__('messages.Add new Link')}}</a>
                                </div>
                                @endif
                                </div>
                            </section>
                            
                            <section class='pre-right text-gray-400 pre-side'>
                                <h3 class="card-header"><i class="bi bi-window-fullscreen" style="font-style:normal!important;"> {{__('messages.Preview')}}</i></h3>
                                    <div class='card-body p-0 p-md-3'>
                                            <center><iframe allowtransparency="true" id="frPreview1" style=" border-radius:0.25rem !important; background: #FFFFFF; min-height:600px; height:100%; max-width:500px !important;" class='w-100' src="{{ url('') }}/@<?= Auth::user()->littlelink_name ?>">{{__('messages.No compatible browser')}}</iframe></center>
                                     </div>
                            </section>
                            </div>
                            
                            <br>
                            <section style="margin-left:-15px;margin-right:-15px;" class='pre-bottom text-gray-400 pre-side'>
                                <h3 class="card-header"><i class="bi bi-window-fullscreen" style="font-style:normal!important;">{{__('messages.Preview')}}</i></h3>
                                    <div class='card-body p-0 p-md-3'>
                                            <center><iframe allowtransparency="true" id="frPreview2" style=" border-radius:0.25rem !important; background: #FFFFFF; min-height:600px; height:100%; width:100% !important;" class='w-100' src="{{ url('') }}/@<?= Auth::user()->littlelink_name ?>">{{__('messages.No compatible browser')}}</iframe></center>
                                     </div>
                            </section><br>
                            
                            <section style="margin-left:-15px;margin-right:-15px;" class='text-gray-400'>
                            <a name="icons"></a>
                            <h3 class="mb-4 card-header"><i class="fa-solid fa-icons"></i> {{__('messages.Page Icons')}}</h3>
                            <div class="card-body p-2 p-md-3">
                            
                            <form action="{{ route('editIcons') }}" enctype="multipart/form-data" method="post">
                                @csrf
                                <div class="form-group col-12 col-lg-8 icon-form-wrapper">
                            
                                        @php
                                        function iconLink($icon){
                                        $iconLink = DB::table('links')
                                        ->where('user_id', Auth::id())
                                        ->where('title', $icon)
                                        ->where('button_id', 94)
                                        ->value('link');
                                          if (is_null($iconLink)){
                                               return false;
                                          } else {
                                                return $iconLink;}}
                                        function searchIcon($icon)
                                    {$iconId = DB::table('links')
                                        ->where('user_id', Auth::id())
                                        ->where('title', $icon)
                                        ->where('button_id', 94)
                                        ->value('id');
                                    if(is_null($iconId)){return false;}else{return $iconId;}}
                                        function iconclicks($icon){
                                        $iconClicks = searchIcon($icon);
                                        $iconClicks = DB::table('links')->where('id', $iconClicks)->value('click_number');
                                          if (is_null($iconClicks)){return 0;}
                                          else {return $iconClicks;}}
                            
                                          function icon($name, $label) {
                                              echo '<div class="mb-3">
                                                      <label class="form-label">'.$label.'</label>
                                                      <span class="form-text d-block" style="font-size: 90%; font-style: italic;">'.__('messages.Clicks').': '.iconclicks($name).'</span>
                                                      <div class="input-group">
                                                        <span class="input-group-text"><i class="fab fa-'.$name.'"></i></span>
                                                        <input type="url" class="form-control" name="'.$name.'" value="'.iconLink($name).'" />
                                                        '.(searchIcon($name) != NULL ? '<a href="'.route("deleteLink", searchIcon($name)).'" class="btn btn-danger"><i class="bi bi-trash-fill"></i></a>' : '').'
                                                      </div>
                                                    </div>';
                                            }
                                        @endphp
                            
                            
                                {!!icon('mastodon', 'Mastodon')!!}
                            
                                {!!icon('instagram', 'Instagram')!!}
                            
                                {!!icon('twitter', 'Twitter')!!}
                            
                                {!!icon('facebook', 'Facebook')!!}
                            
                                {!!icon('github', 'GitHub')!!}
                            
                                {!!icon('twitch', 'Twitch')!!}
                            
                                {!!icon('linkedin', 'LinkedIn')!!}
                            
                                {!!icon('tiktok', 'TikTok')!!}
                            
                                {!!icon('discord', 'Discord')!!}
                            
                                {!!icon('youtube', 'YouTube')!!}
                            
                                {!!icon('snapchat', 'Snapchat')!!}
                            
                                {!!icon('reddit', 'Reddit')!!}
                            
                                {!!icon('pinterest', 'Pinterest')!!}
                            
                                {{-- {!!icon('telegram', 'Telegram')!!}
                            
                                {!!icon('whatsapp', 'WhatsApp')!!} --}}
                            
                            
                                <button type="submit" class="mt-3 ms-0 ms-md-3 btn btn-primary w-100 w-md-auto">{{__('messages.Save links')}}</button>
                            </form>
                            
                            
                            </div>
                            </section>
    
                      </div>
                  </div>
               </div>
            </div>
         </div>


      </div>
    </div>

<script src="{{ asset('assets/external-dependencies/jquery-1.12.4.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script type="text/javascript">$("iframe").load(function() { $("iframe").contents().find("a").each(function(index) { $(this).on("click", function(event) { event.preventDefault(); event.stopPropagation(); }); }); });</script>

<script>
// Wait for DOM and ensure script only runs once
// COMPLETE ISOLATED SORTABLE SOLUTION
// This runs in its own context to avoid conflicts with other scripts

// NUCLEAR OPTION: Completely suppress ALL errors during page load
// Add this as the VERY FIRST SCRIPT on your page

(// ALTERNATIVE SOLUTION: Pure HTML5 Drag & Drop
// This doesn't rely on Sortable.js at all
// REPLACE your entire Sortable script with this

(function() {
    'use strict';
    
    console.log('📱 Native drag starting...');
    
    setTimeout(function() {
        const container = document.getElementById('links-table-body');
        
        if (!container) {
            console.error('Container not found');
            return;
        }
        
        const cards = container.querySelectorAll('.link-card');
        console.log('Found ' + cards.length + ' cards');
        
        if (cards.length === 0) {
            console.warn('No cards to make draggable');
            return;
        }
        
        let draggedElement = null;
        let placeholder = null;
        
        // Make each card draggable
        cards.forEach(function(card, index) {
            const handle = card.querySelector('.sortable-handle');
            
            if (!handle) {
                console.warn('No handle found for card', index);
                return;
            }
            
            // DESKTOP: Mouse drag
            handle.addEventListener('mousedown', function(e) {
                e.preventDefault();
                startDrag(card, e.clientY);
            });
            
            // MOBILE: Touch drag
            handle.addEventListener('touchstart', function(e) {
                e.preventDefault();
                const touch = e.touches[0];
                startDrag(card, touch.clientY);
            });
        });
        
        function startDrag(card, startY) {
            console.log('🎯 Drag started');
            
            draggedElement = card;
            const startIndex = Array.from(container.children).indexOf(card);
            
            // Create placeholder
            placeholder = document.createElement('div');
            placeholder.className = 'link-card sortable-ghost';
            placeholder.style.height = card.offsetHeight + 'px';
            
            // Style dragged card
            card.classList.add('sortable-drag');
            card.style.position = 'fixed';
            card.style.zIndex = '9999';
            card.style.width = card.offsetWidth + 'px';
            card.style.pointerEvents = 'none';
            
            // Insert placeholder
            container.insertBefore(placeholder, card);
            
            let currentY = startY;
            
            function onMove(e) {
                e.preventDefault();
                
                const clientY = e.clientY || (e.touches && e.touches[0].clientY);
                if (!clientY) return;
                
                currentY = clientY;
                
                // Move the card
                const rect = card.getBoundingClientRect();
                const offsetY = clientY - startY;
                card.style.top = (rect.top + offsetY) + 'px';
                card.style.left = rect.left + 'px';
                
                // Find card under cursor
                const allCards = Array.from(container.querySelectorAll('.link-card:not(.sortable-drag)'));
                let hoveredCard = null;
                
                allCards.forEach(function(otherCard) {
                    const otherRect = otherCard.getBoundingClientRect();
                    if (clientY >= otherRect.top && clientY <= otherRect.bottom) {
                        hoveredCard = otherCard;
                    }
                });
                
                // Reorder placeholder
                if (hoveredCard && hoveredCard !== placeholder) {
                    const hoveredRect = hoveredCard.getBoundingClientRect();
                    const isAbove = clientY < hoveredRect.top + hoveredRect.height / 2;
                    
                    if (isAbove) {
                        container.insertBefore(placeholder, hoveredCard);
                    } else {
                        container.insertBefore(placeholder, hoveredCard.nextSibling);
                    }
                }
            }
            
            function onEnd(e) {
                console.log('✋ Drag ended');
                
                // Remove listeners
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onEnd);
                document.removeEventListener('touchmove', onMove);
                document.removeEventListener('touchend', onEnd);
                
                // Reset styles
                card.classList.remove('sortable-drag');
                card.style.position = '';
                card.style.zIndex = '';
                card.style.width = '';
                card.style.top = '';
                card.style.left = '';
                card.style.pointerEvents = '';
                
                // Replace placeholder with actual card
                if (placeholder && placeholder.parentNode) {
                    container.insertBefore(card, placeholder);
                    container.removeChild(placeholder);
                }
                
                const endIndex = Array.from(container.children).indexOf(card);
                
                if (startIndex !== endIndex) {
                    console.log('Position changed: ' + startIndex + ' -> ' + endIndex);
                    saveOrder();
                } else {
                    console.log('Position unchanged');
                }
                
                draggedElement = null;
                placeholder = null;
            }
            
            // Add move and end listeners
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onEnd);
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onEnd);
        }
        
        function saveOrder() {
            const ids = [];
            const cards = container.querySelectorAll('.link-card');
            
            cards.forEach(function(card) {
                const id = card.getAttribute('data-id');
                if (id) ids.push(id);
            });
            
            console.log('💾 Saving order:', ids);
            
            const csrf = document.querySelector('meta[name="csrf-token"]');
            if (!csrf) {
                console.warn('No CSRF token');
                return;
            }
            
            fetch('/studio/update-link-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf.content
                },
                body: JSON.stringify({ order: ids })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) { console.log('✅ Saved:', data); })
            .catch(function(err) { console.error('❌ Save failed:', err); });
        }
        
        console.log('✅ Native drag ready!');
        
    }, 500);
    
})();
</script>
@endsection