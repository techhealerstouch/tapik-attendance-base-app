<!-- Step 1 - V-Card -->
<?php
use JeroenDesloovere\VCard\VCard;
?>

<style>
.step-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 15px;
}

.step-header {
    text-align: center;
    margin-bottom: 30px;
}

.step-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.step-subtitle {
    font-size: 14px;
    color: #6c757d;
}

.form-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.form-group-custom {
    margin-bottom: 15px;
}

.form-control-custom {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control-custom:focus {
    outline: none;
    border-color: #db5363;
    box-shadow: 0 0 0 3px rgba(219, 83, 99, 0.1);
}

.form-select-custom {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    background-color: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-select-custom:focus {
    outline: none;
    border-color: #db5363;
    box-shadow: 0 0 0 3px rgba(219, 83, 99, 0.1);
}

.help-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
    display: block;
}

@media (max-width: 768px) {
    .step-title {
        font-size: 20px;
    }
    
    .section-title {
        font-size: 16px;
    }
    
    .form-section {
        padding: 15px;
    }
}
</style>

<div class="step-container">
    <div class="step-header">
        <h6 class="step-title">Add Contact Card</h6>
        <p class="step-subtitle">Create your digital business card</p>
    </div>

    <!-- Hidden Title Field -->
    <input type='hidden' name='link_title' value='Add to contacts' />

    <!-- Name Section -->
    <div class="form-section">
        <h4 class="section-title">{{__('messages.Name')}}</h4>
        
        <div class="form-group-custom">
            <select name='prefix' class='form-select-custom'>
                <option value="" disabled selected>Select Prefix</option>
                <option value="Mr.">Mr.</option>
                <option value="Ms.">Ms.</option>
                <option value="Mrs.">Mrs.</option>
                <option value="Dr.">Dr.</option>
                <option value="Prof.">Prof.</option>
            </select>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.First Name')}}" type='text' name='first_name' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Middle Name')}}" type='text' name='middle_name' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Last Name')}}" type='text' name='last_name' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Suffix')}}" type='text' name='suffix' class='form-control-custom'/>
        </div>
    </div>

    <!-- Work Section -->
    <div class="form-section">
        <h4 class="section-title">{{__('messages.Work')}}</h4>
        
        <div class="form-group-custom">
            <input placeholder="{{__('messages.Organization')}}" type='text' name='organization' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Title')}}" type='text' name='vtitle' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Role')}}" type='text' name='role' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Work URL')}}" type='url' name='work_url' class='form-control-custom'/>
        </div>
    </div>

    <!-- Email Section -->
    <div class="form-section">
        <h4 class="section-title">{{__('messages.Emails')}}</h4>
        
        <div class="form-group-custom">
            <input placeholder="{{__('messages.Email')}}" type='email' name='email' class='form-control-custom'/>
            <span class='help-text'>{{__('messages.Enter your personal email')}}</span>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Work Email')}}" type='email' name='work_email' class='form-control-custom'/>
            <span class='help-text'>{{__('messages.Enter your work email')}}</span>
        </div>
    </div>

    <!-- Phone Section -->
    <div class="form-section">
        <h4 class="section-title">{{__('messages.Phones')}}</h4>
        
        <div class="form-group-custom">
            <input placeholder="{{__('messages.Home Phone')}}" type='tel' name='home_phone' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Work Phone')}}" type='tel' name='work_phone' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Cell Phone')}}" type='tel' name='cell_phone' class='form-control-custom'/>
        </div>
    </div>

    <!-- Home Address Section -->
    <div class="form-section">
        <h4 class="section-title">Home Address</h4>
        
        <div class="form-group-custom">
            <input placeholder="{{__('messages.Label')}}" type='text' name='home_address_label' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Street')}}" type='text' name='home_address_street' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.City')}}" type='text' name='home_address_city' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.State/Province')}}" type='text' name='home_address_state' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Zip/Postal Code')}}" type='text' name='home_address_zip' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Country')}}" type='text' name='home_address_country' class='form-control-custom'/>
        </div>
    </div>

    <!-- Work Address Section -->
    <div class="form-section">
        <h4 class="section-title">{{__('messages.Work Address')}}</h4>
        
        <div class="form-group-custom">
            <input placeholder="{{__('messages.Label')}}" type='text' name='work_address_label' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Street')}}" type='text' name='work_address_street' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.City')}}" type='text' name='work_address_city' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.State/Province')}}" type='text' name='work_address_state' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Zip/Postal Code')}}" type='text' name='work_address_zip' class='form-control-custom'/>
        </div>

        <div class="form-group-custom">
            <input placeholder="{{__('messages.Country')}}" type='text' name='work_address_country' class='form-control-custom'/>
        </div>
    </div>
</div>