<?php use App\Models\UserData; ?>



        @php $initial = 1; @endphp

            @foreach($links as $link)

                @php $linkName = str_replace('default ','',$link->title) @endphp

                @switch($link->name)

                    @case('icon')

                        @break

                    @case('phone')

                    <div style="--delay: {{ $initial++ }}s" class="button-entrance"><a id="{{ $link->id }}" class="button button-curve button-default button-click button-hover icon-hover" rel="noopener noreferrer nofollow noindex" href="{{ $link->link }}"><img alt="{{ $link->name }}" class="icon hvr-icon" src="@if(theme('use_custom_icons') == "true"){{ url('themes/' . $GLOBALS['themeName'] . '/extra/custom-icons')}}/phone{{theme('custom_icon_extension')}} @else{{ asset('\/assets/linkstack/icons\/')}}phone.svg @endif"></i>{{ $link->title }}</a></div>

                        @break

                    @case('default email')

                    @case('default email_alt')

                    <div style="--delay: {{ $initial++ }}s" class="button-entrance"><a id="{{ $link->id }}" class="button button-curve button-default button-click button-hover icon-hover" rel="noopener noreferrer nofollow noindex" href="{{ $link->link }}"><img alt="email" class="icon hvr-icon" src="@if(theme('use_custom_icons') == "true"){{ url('themes/' . $GLOBALS['themeName'] . '/extra/custom-icons')}}/email{{theme('custom_icon_extension')}} @else{{ asset('\/assets/linkstack/icons\/')}}email.svg @endif"></i>{{ $link->title }}</a></div>

                        @break

                    @case('buy me a coffee')

                    <div style="--delay: {{ $initial++ }}s" class="button-entrance"><a id="{{ $link->id }}" class="button button-curve button-coffee button-click button-hover icon-hover" rel="noopener noreferrer nofollow noindex" href="{{ $link->link }}" @if((UserData::getData($userinfo->id, 'links-new-tab') != false))target="_blank"@endif ><img alt="{{ $link->name }}" class="icon hvr-icon" src="@if(theme('use_custom_icons') == "true"){{ url('themes/' . $GLOBALS['themeName'] . '/extra/custom-icons')}}/coffee{{theme('custom_icon_extension')}} @else{{ asset('\/assets/linkstack/icons\/')}}coffee.svg @endif">Buy me a Coffee</a></div>

                        @break

                        @case('space')

                        @php $title = $link->title; if (is_numeric($title)) { echo str_repeat("<br>", $title < 10 ? $title : 10); } else { echo "<br><br><br>"; } @endphp

                        @break

                    @case('heading')

                    <div class="fadein"><h2>{{ $link->title }}</h2></div>

                        @break

                    @case('text')

                    <div class="fadein"><span style="">@if(env('ALLOW_USER_HTML') === true){!! $link->title !!}@else{{ $link->title }}@endif</span></div>

                        @break

                    @case('vcard')

                        <div style="--delay: {{ $initial++ }}s" class="button-entrance"><a id="{{ $link->id }}" class="button button-curve button-default button-click button-hover icon-hover" rel="noopener noreferrer nofollow noindex" href="{{ route('vcard') . '/' . $link->id }}"><img alt="{{ $link->name }}" class="icon hvr-icon" src="@if(theme('use_custom_icons') == "true"){{ url('themes/' . $GLOBALS['themeName'] . '/extra/custom-icons')}}/vcard{{theme('custom_icon_extension')}} @else{{ asset('\/assets/linkstack/icons\/')}}vcard.svg @endif"></i>{{ $link->title }}</a></div>

                        

                        @break

                    @case('custom')

                    @if($link->custom_css === "" or $link->custom_css === "NULL" or (theme('allow_custom_buttons') == "false"))

                    <div style="--delay: {{ $initial++ }}s" class="button-entrance"><a id="{{ $link->id }}" class="button button-curve button-custom button-click button-hover icon-hover" rel="noopener noreferrer nofollow noindex" href="{{ $link->link }}" @if((UserData::getData($userinfo->id, 'links-new-tab') != false))target="_blank"@endif ><i style="color: {{$link->custom_icon}}" class="icon hvr-icon fa {{$link->custom_icon}}"></i>{{ $link->title }}</a></div>

                        @break

                    @elseif($link->custom_css != "")

                    <div style="--delay: {{ $initial++ }}s" class="button-entrance"><a id="{{ $link->id }}" class="button button-curve button-custom button-click button-hover icon-hover" style="{{ $link->custom_css }}" rel="noopener noreferrer nofollow noindex" href="{{ $link->link }}" @if((UserData::getData($userinfo->id, 'links-new-tab') != false))target="_blank"@endif ><i style="color: {{$link->custom_icon}}" class="icon hvr-icon fa {{$link->custom_icon}}"></i>{{ $link->title }}</a></div>

                        @break

                        @endif

                    @case('custom_website')

                    @if($link->custom_css === "" or $link->custom_css === "NULL" or (theme('allow_custom_buttons') == "false"))

                        <div style="--delay: {{ $initial++ }}s" class="button-entrance"><a id="{{ $link->id }}" class="button button-curve button-custom_website button-click button-hover icon-hover" rel="noopener noreferrer nofollow noindex" href="{{ $link->link }}" @if((UserData::getData($userinfo->id, 'links-new-tab') != false))target="_blank"@endif ><img alt="{{ $link->name }}" class="icon hvr-icon" src="@if(file_exists(base_path("assets/favicon/icons/").localIcon($link->id))){{url('assets/favicon/icons/'.localIcon($link->id))}}@else{{getFavIcon($link->id)}}@endif" onerror="this.onerror=null; this.src='{{asset('assets/linkstack/icons/website.svg')}}';">{{ $link->title }}</a></div>

                        @break

                    @elseif($link->custom_css != "")

                        <div style="--delay: {{ $initial++ }}s" class="button-entrance"><a id="{{ $link->id }}" class="button button-curve button-custom_website button-click button-hover icon-hover" style="{{ $link->custom_css }}" rel="noopener noreferrer nofollow noindex" href="{{ $link->link }}" @if((UserData::getData($userinfo->id, 'links-new-tab') != false))target="_blank"@endif ><img alt="{{ $link->name }}" class="icon hvr-icon" src="@if(file_exists(base_path("assets/favicon/icons/").localIcon($link->id))){{url('assets/favicon/icons/'.localIcon($link->id))}}@else{{getFavIcon($link->id)}}@endif" onerror="this.onerror=null; this.src='{{asset('assets/linkstack/icons/website.svg')}}';">{{ $link->title }}</a></div>

                        @break

                    @endif

                    @default

                    <?php include base_path('config/button-names.php'); $newLinkName = $linkName; $isNewName = "false"; foreach($buttonNames as $key => $value) { if($newLinkName == $key) { $newLinkName = $value; $isNewName = "true"; }} ?>

                    <div style="--delay: {{ $initial++ }}s" class="button-entrance"><a id="{{ $link->id }}" class="button button-curve button-{{ $link->name }} button-click button-hover icon-hover" rel="noopener noreferrer nofollow noindex" href="{{ $link->link }}" @if((UserData::getData($userinfo->id, 'links-new-tab') != false))target="_blank"@endif ><img alt="{{ $link->name }}" class="icon hvr-icon" src="@if(theme('use_custom_icons') == "true"){{ url('themes/' . $GLOBALS['themeName'] . '/extra/custom-icons')}}/{{$link->name}}{{theme('custom_icon_extension')}} @else{{ asset('\/assets/linkstack/icons\/') . $link->name }}.svg @endif">@if($isNewName == "true"){{ ucfirst($newLinkName) }}@else{{ ucfirst($newLinkName) }}@endif</a></div>

                @endswitch

            @endforeach

            @if (UserData::getData($userinfo->id, 'show-professional') == "true")

            <div style="--delay: {{ $initial++ }}s" class="accordion-component">

                <a class="accordion btns-details button button-professional button-click" style="margin-bottom: 0px; !important;">Professional</a>

                <div class="panel">

                @if($userinfo && $userinfo->name)

                    <label for="name" class="lbl-prof">{{ $userinfo->name }}</label>

                    <label for="name" class="lbl-subf lbl-sub">Name</label>

                @endif



                @if($profinfo)

                    @if($profinfo->title)

                        <label for="title" class="lbl-prof">{{ $profinfo->title }}</label>

                        <label for="title" class="lbl-subf lbl-sub">Job Title</label>

                    @endif



                    @if($profinfo->company)

                        <label for="company" class="lbl-prof">{{ $profinfo->company }}</label>

                        <label for="company" class="lbl-subf lbl-sub">Company / Organization</label>

                    @endif



                    @if($profinfo->location)

                        <label for="location" class="lbl-prof">{{ $profinfo->location }}</label>

                        <label for "location" class="lbl-subf lbl-sub">Location</label>

                    @endif



                    @if($profinfo->country)

                        <label for="country" class="lbl-prof">{{ $profinfo->country }}</label>

                        <label for="country" class="lbl-subf lbl-sub">Country</label>

                    @endif



                    @if($profinfo->email)

                        <label for="email" class="lbl-prof">{{ $profinfo->email }}</label>

                        <label for="email" class="lbl-subf lbl-sub">E-Mail</label>

                    @endif



                    @if($profinfo->mobile)

                        <label for="mobile" class="lbl-prof">{{ $profinfo->mobile }}</label>

                        <label for="mobile" class="lbl-subf lbl-sub">Mobile</label>

                    @endif



                    @if($profinfo->role)

                        <label for="role" class="lbl-prof">{{ $profinfo->role }}</label>

                        <label for="role" class="lbl-subf lbl-sub">Role</label>

                    @endif

                @endif

                </div>

            </div>

            @endif

            @if (UserData::getData($userinfo->id, 'show-send-details') == "true")

            <div style="--delay: {{ $initial++ }}s" class="accordion-component">

                <a class="btn-details button button-professional button-click" onclick="openModal()">Send your details</a>

            </div>

            @endif

            <!-- <div style="--delay: {{ $initial++ }}s" class="accordion-component">
                <a class="btn-details button button-professional button-click" id="sendLocationBtn">Send Location</a>
            </div>
            <p id="locationDisplay"></p>-->

            <!-- Modal -->

            <div id="myModal" class="modal">

                <div class="modal-content">

                    <span class="close" onclick="closeModal()">&times;</span>

                    <!-- Your modal content here -->

                    <a href="{{ config('app.url') }}" target="_blank" rel="noopener noreferrer" style="display: flex; justify-content: center; align-items: center;">

                        <img class="img logo" src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" style="width:auto;height:50px;">

                    </a>

                    <form id="detailsForm" method="POST">

                        <span class="modal-header">Send your details to {{ $userinfo->name }}</span>

                        @csrf

                        <input type="text" id="firstname" required name="firstname" placeholder="First Name">

                        <input type="text" id="lastname" required name="lastname" placeholder="Last Name">

                        <input type="email" id="email" required name="email" placeholder="Email">

                        <input type="text" id="mobile" required name="mobile" placeholder="Phone Number">


                        <input type="hidden" id="receipient" name="receipient" value="{{ $userinfo->email }}">

                        <input style="background:color: #052884 !important" type="submit" value="Send">

                        

                    </form>
                        
                    <div id="loading" class="lds-dual-ring loading-container" style="display: none;"></div>

                    <div id="successMessage" style="display: none; margin-top: 20px;">

                        <p style="color: black;">Success! Details sent.</p>

                        <a onclick="closeModal()" class="button button-professional button-click" style="color: white; margin-bottom: 0px; !important;">Close</a>

                    </div>

                </div>

            </div>

            

    <script>

        $(document).ready(function () {

            $('#detailsForm').submit(function (event) {

                event.preventDefault(); // Prevent default form submission

                $('#detailsForm').hide();

                // Show loading spinner

                $('#loading').show();



                // Serialize form data

                var formData = $(this).serialize();



                // Submit form data via AJAX

                $.ajax({

                    type: 'POST',

                    url: '{{ route("send.details") }}',

                    data: formData,

                    success: function (response) {

                        // Hide loading spinner

                        $('#loading').hide();

                        // Hide form

                        $('#detailsForm').hide();

                        // Display success message

                        $('#successMessage').show();

                    },

                    error: function (error) {

                        // Hide loading spinner

                        $('#loading').hide();

                        // Handle errors if any

                        console.error('Error:', error);

                    }

                });

            });

        });



        $(document).ready(function () {

            $('#goBack').click(function (event) {

                event.preventDefault(); // Prevent default button behavior



                // Show the details form and hide the success message

                $('#detailsForm').show();

                $('#successMessage').hide();

            });

        });





        document.addEventListener('DOMContentLoaded', function () {

            function handleClickOrTouch(event) {

                if (event.target.classList.contains('button-click')) {

                    var id = event.target.id;

                    if (!sessionStorage.getItem('clicked-' + id)) {

                        var url = '{{ route("clickNumber") }}/' + id;

                        fetch(url, {

                            method: 'GET',

                            headers: {

                                'Content-Type': 'application/json',

                            },

                        });

                        sessionStorage.setItem('clicked-' + id, 'true');

                    }

                }

            }

    

            document.addEventListener('mousedown', function (event) {

                if (event.button === 0 || event.button === 1) {

                    handleClickOrTouch(event);

                }

            });

    

            document.addEventListener('touchstart', handleClickOrTouch);

        });



        var acc = document.getElementsByClassName("accordion");

        var i;



        for (i = 0; i < acc.length; i++) {

        acc[i].addEventListener("click", function() {

            this.classList.toggle("active");

            var panel = this.nextElementSibling;

            if (panel.style.display === "block") {

            panel.style.display = "none";

            } else {

            panel.style.display = "block";

            }

        });

        }





        function openModal() {

        var modal = document.getElementById("myModal");

        modal.style.display = "block";

        }



        function closeModal() {

            var modal = document.getElementById("myModal");

            modal.style.display = "none";

        }

        


        document.getElementById('sendLocationBtn').addEventListener('click', (event) => {
            event.preventDefault(); // Prevents the default action of the anchor tag
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                document.getElementById('locationDisplay').innerText = "Geolocation is not supported by this browser.";
            }
        });

        function showPosition(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            document.getElementById('locationDisplay').innerText = `Latitude: ${latitude}, Longitude: ${longitude}`;
            
            // Here you can send the coordinates to your server if needed
            // Example:
            // fetch('your-server-endpoint', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json'
            //     },
            //     body: JSON.stringify({ latitude, longitude })
            // }).then(response => response.json())
            // .then(data => console.log(data));
        }

        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    document.getElementById('locationDisplay').innerText = "User denied the request for Geolocation.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    document.getElementById('locationDisplay').innerText = "Location information is unavailable.";
                    break;
                case error.TIMEOUT:
                    document.getElementById('locationDisplay').innerText = "The request to get user location timed out.";
                    break;
                case error.UNKNOWN_ERROR:
                    document.getElementById('locationDisplay').innerText = "An unknown error occurred.";
                    break;
            }
        }
    </script>



<style>



    .modal-header{

        display: flex;

        align-items: center;

        justify-content: center;

        flex-direction: column;

        font-size: 15px;

        margin-top: 15px;

        margin-bottom: 10px;

        color: #861619;

        font-weight: 400;

    }



.modal {

    display: none;

    align-items: center;

    justify-content: center;

    flex-direction: column;

    position: fixed; /* Stay in place */

    z-index: 1000; /* Sit on top of everything else */

    left: 0;

    top: 0;

    width: 100%; /* Full width */

    height: 100%; /* Full height */

    overflow: auto; /* Enable scroll if needed */

    background-color: rgba(0,0,0,0.5); /* Black w/ opacity */

    

}



/* Modal Content */

.modal-content {

    background-color: #fefefe;

    margin: 15% auto; /* 15% from the top and centered */

    padding: 20px;

    border: 1px solid #888;

    width: 80%; /* Could be more or less, depending on screen size */

    max-width: 500px;

    border-radius: 15px;

}



@media only screen and (max-width: 600px) {

    .modal-content {

        width: 80%; /* Adjust as needed for smaller screens */

    }



    .modal-header{

        font-size: 15px;

    }

}



/* Close Button */

.close {

    color: #d60024;

    position: absolute; /* Position relative to parent */

    top: 10px; /* Adjust top position as needed */

    right: 10px; /* Adjust right position as needed */

    font-size: 28px;

    font-weight: bold;

}



.close:hover,

.close:focus {

    color: black;

    text-decoration: none;

    cursor: pointer;

}



input[type=text], select {

  width: 100%;

  padding: 12px 20px;

  margin: 8px 0;

  display: inline-block;

  border: 1px solid #ccc;

  border-radius: 30px;

  box-sizing: border-box;

}



input[type=email], select {

  width: 100%;

  padding: 12px 20px;

  margin: 8px 0;

  display: inline-block;

  border: 1px solid #ccc;

  border-radius: 30px;

  box-sizing: border-box;

}



input[type=submit] {

  width: 100%;

  background-color: #052884;

  color: white;

  padding: 14px 20px;

  margin: 8px 0;

  border: none;

  border-radius: 30px;

  cursor: pointer;

}



input[type=submit]:hover {

  background-color: #264594;

}



input[type=text]:focus, input[type=email]:focus, input[type=password]:focus, select:focus {

  outline-color: #3050a1;

  outline-width: 1px;

}

.loading-container {
  position: relative;
  top: 50%;
  left: 50%;
  margin-top: 80px;
  transform: translate(-50%, -50%);
}

.lds-dual-ring {

  /* change color here */

  color: #052884

}

.lds-dual-ring,

.lds-dual-ring:after {

  box-sizing: border-box;

}

.lds-dual-ring {

  display: inline-block;

  width: 80px;

  height: 80px;

}

.lds-dual-ring:after {

  content: " ";

  display: block;

  width: 64px;

  height: 64px;

  margin: 8px;

  border-radius: 50%;

  border: 6.4px solid currentColor;

  border-color: currentColor transparent currentColor transparent;

  animation: lds-dual-ring 1.2s linear infinite;

}

@keyframes lds-dual-ring {

  0% {

    transform: rotate(0deg);

  }

  100% {

    transform: rotate(360deg);

  }

}

</style>