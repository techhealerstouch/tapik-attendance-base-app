<?php
use JeroenDesloovere\VCard\VCard;

?>
<div>
<div class="d-flex justify-content-center">
<h6 style="font-size: 20px" class='form-label'>Add a V-Card</h6>
</div>

<div class="mb-3">
<label for='title' class='form-label' style='display: none;'>{{__('messages.Custom Title')}}</label>
<div class="input-group">
<input type='text' name='link_title' value='Add to contacts' class='form-control' style='display: none;' />
</div>
</div>

<h4 style="margin-bottom: 10px; font-size: 20px">{{__('messages.Name')}}</h4>
<select name='prefix' class='form-control'>
    <option value="" disabled selected>Select Prefix</option>
    <option value="Mr.">Mr.</option>
    <option value="Ms.">Ms.</option>
    <option value="Mrs.">Mrs.</option>
    <!-- Add more options as needed -->
</select>
<br>

<input placeholder="{{__('messages.First Name')}}" type='text' name='first_name' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Middle Name')}}" type='text' name='middle_name' value='' class='form-control'/>
<br>


<input placeholder="{{__('messages.Last Name')}}" type='text' name='last_name' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Suffix')}}" type='text' name='suffix' value='' class='form-control'/>
<br>

<h4 style="margin-bottom: 10px; font-size: 20px">{{__('messages.Work')}}</h4>
<input placeholder="{{__('messages.Organization')}}" type='text' name='organization' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Title')}}" type='text' name='vtitle' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Role')}}" type='text' name='role' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Work URL')}}" type='url' name='work_url' value='' class='form-control'/>
<br>

<h4 style="margin-bottom: 10px; font-size: 20px">{{__('messages.Emails')}}</h4>
<input placeholder="{{__('messages.Email')}}" type='email' name='email' value='' class='form-control'/>
<span class='small text-muted'>{{__('messages.Enter your personal email')}}</span>
<br>

<input placeholder="{{__('messages.Work Email')}}" type='email' name='work_email' value='' class='form-control'/>
<span class='small text-muted'>{{__('messages.Enter your work email')}}</span>
<br>

<br><h4 style="margin-bottom: 10px; font-size: 20px">{{__('messages.Phones')}}</h4>
<input placeholder="{{__('messages.Home Phone')}}" type='tel' name='home_phone' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Work Phone')}}" type='tel' name='work_phone' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Cell Phone')}}" type='tel' name='cell_phone' value='' class='form-control'/>
<br>

<h4 style="margin-bottom: 10px; font-size: 20px">Home Address</h4>
<input placeholder="{{__('messages.Label')}}" type='text' name='home_address_label' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Street')}}" type='text' name='home_address_street' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.City')}}" type='text' name='home_address_city' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.State/Province')}}" type='text' name='home_address_state' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Zip/Postal Code')}}" type='text' name='home_address_zip' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Country')}}" type='text' name='home_address_country' value='' class='form-control'/>
<br>

<h4 style="margin-bottom: 10px; font-size: 20px">{{__('messages.Work Address')}}</h4>
<input placeholder="{{__('messages.Label')}}" type='text' name='work_address_label' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Street')}}" type='text' name='work_address_street' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.City')}}" type='text' name='work_address_city' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.State/Province')}}" type='text' name='work_address_state' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Zip/Postal Code')}}" type='text' name='work_address_zip' value='' class='form-control'/>
<br>

<input placeholder="{{__('messages.Country')}}" type='text' name='work_address_country' value='' class='form-control'/>

</div>
