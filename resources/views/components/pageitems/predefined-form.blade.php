<label for='button' class='form-label'>{{__('messages.Select a predefined site')}}</label>

<?php use App\Models\Button; $button = Button::find($button_id); if(isset($button->name)){$buttonName = $button->name;}else{$buttonName = 0;} ?>



<select name='button' class='form-control' style="margin-bottom: 10px">

        @if($buttonName != 0)<option value='{{$buttonName}}'>{{ucfirst($buttonName)}}</option>@endif

    @foreach ($buttons as $b)

        @if($b["exclude"] != true)

        <option class='button button-{{$b["name"]}}' value='{{$b["name"]}}' {{ $b["selected"] == true ? "selected" : ""}}>{{$b["title"]}}</option>

        @endif

    @endforeach

</select>




<input type='text' name='title' placeholder="{{__('messages.Custom Title')}}" value='{{$link_title}}' class='form-control' />

<input type='url' name='link' value='{{$link_url}}' class='form-control' required style="margin-top: 10px" placeholder="{{__('messages.URL')}}"/>

<span class='small text-muted'>{{__('messages.Enter the link URL')}}</span>



