@extends('layouts.admin')
@section('content')
<?php

function matchSel($first, $second) {
    if ($first == $second)
        return "selected";
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        
        var str = $('#phone').val();
        str = str.replace(' ', '');
        $('#phone').val(str);
        
        $('#email').keyup(function () {
            $(this).val($(this).val().replace(/ +?/g, ''));
        });
        $('#password').keyup(function () {
            $(this).val($(this).val().replace(/ +?/g, ''));
        });
        $.validator.addMethod("alphanumeric", function (value, element) {
            return this.optional(element) || /^[\w.]+$/i.test(value);
        }, "Only letters, numbers and underscore allowed.");

        $("#adminForm").validate();

        $("#phone").keyup(function () {
            $("#phMinLngthErr").html('');
            if (!isNaN(this.value) && this.value != "") {
                // if (this.value.length < 6) {
                //     //$(this).next('.opt_input').focus();

                //     $("#phMinLngthErr").html('Please enter at least 6 digits.');
                // } else {
                //     $("#phMinLngthErr").html('');
                // }
            }
            /*else if (isNaN(this.value)) {
             $("#phMinLngthErr").html('Use digits only');	
             }*/
        });
        $.validator.addMethod("passworreq", function (input) {
            var reg = /[0-9]/; //at least one number
            var reg2 = /[a-z]/; //at least one small character
            var reg3 = /[A-Z]/; //at least one capital character
            var reg4 = /[\W_]/; //at least one special character
            return reg.test(input) && reg2.test(input) && reg3.test(input) && reg4.test(input);
        }, "Password must be at least 8 characters long, contains an upper case letter, a lower case letter, a number and a symbol.");
    });
    
    


</script>

<style type="text/css">
    .county-code {
        width: 100px;
    }

    .select select {
        -webkit-appearance: none;
        -moz-appearance: none;
        -ms-appearance: none;
        appearance: none;
        outline: 0;
        box-shadow: none;
        border: 0 !important;
        background: #e6e6e6;
        background-image: none;
        font-size: 12px;
    }

    /* Remove IE arrow */
    .select select::-ms-expand {
        display: none;
    }

    /* Custom Select */
    .select {
        position: relative;
        display: flex;
        width: 100%;
        height: 34px;
        background: #eaeaea;
        overflow: hidden;
        border-radius: 0;
    }

    .select select {
        flex: 1;
        padding: 0 .5em;
        color: #000;
        cursor: pointer;
    }

    /* Arrow */
    .select::after {
        content: '\25BC';
        position: absolute;
        top: 0;
        right: 0;
        padding: 0 8px;
        cursor: pointer;
        pointer-events: none;
        -webkit-transition: .25s all ease;
        -o-transition: .25s all ease;
        transition: .25s all ease;
        font-size: 10px;
        height: 40px;
        display: flex;
        align-items: center;
    }

    /* Transition */
    .select:hover::after {
        color: #000;
    }

    .flex-input {
        display: flex;
    }

    .county-code {
        width: 228px;
    }

    .select::after {
        content: '\25BC';
        position: absolute;
        top: 0;
        right: 0;
        padding: 0 8px;
        cursor: pointer;
        pointer-events: none;
        -webkit-transition: .25s all ease;
        -o-transition: .25s all ease;
        transition: .25s all ease;
        font-size: 10px;
        height: 34px;
        display: flex;
        align-items: center;
    }
</style>

{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Personal User</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/merchants')}}"><i class="fa fa-user"></i> <span>Manage Personal Users</span></a></li>
            <li class="active"> Edit Personal User</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($recordInfo, ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}            
            <div class="form-horizontal">
                <div class="box-body">

               <!--  <div class="form-group">
                        <label class="col-sm-2 control-label">First Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <div class="add-select-option perosnal-edit">
                                {{Form::text('first_name', null, ['class'=>'form-control required', 'placeholder'=>'First Name', 'autocomplete' => 'off'])}}
                                <div class="perosnal-select-box">
                                <?php global $sernameList; ?>    
                                {{Form::select('gender', $sernameList,null, ['class' => 'required','placeholder'=>'Select Prefix'])}}  
                                </div>
                            </div>
                        </div>
                    </div>   -->

                    <div class="form-group">
                        <label class="col-sm-2 control-label">First Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <div class="row perosnal-edit">
                                <div class="col-sm-1 pr-0">
                                    <div class="perosnal-select-box">
                                    <?php global $sernameList; ?>    
                                    {{Form::select('gender', $sernameList,null, ['class' => 'required form-control','placeholder'=>'Select Prefix'])}}  
                                    </div>
                                </div>
                                <div class="col-sm-11 pl-0">
                                     <div class="add-select-option">
                                        {{Form::text('first_name', null, ['class'=>'form-control required', 'placeholder'=>'First Name', 'autocomplete' => 'off'])}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">Last Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('last_name', null, ['class'=>'form-control required', 'placeholder'=>'Last Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Personal Email <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('email', null, ['class'=>'form-control email required', 'placeholder'=>'Personal Email', 'autocomplete' => 'off', 'id'=>'email'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Mobile Number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('phone', null, ['id'=>'phone','class'=>'form-control required digits', 'placeholder'=>'Mobile Number', 'autocomplete' => 'off', 'minlength' => 6])}}
                            <span id="phMinLngthErr"></span>
                        </div>
                    </div> 

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Address Line 1 <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('addrs_line1', null, ['id'=>'addrs1','class'=>'form-control required', 'placeholder'=>'Enter Address Line 1', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Address Line 2 (optional)<span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('addrs_line2', null, ['id'=>'addrs2','class'=>'form-control', 'placeholder'=>'Enter Address Line 2', 'autocomplete' => 'off'])}}
                        </div>
                    </div>	

                    <!--					<div class="form-group">
                                                            <label class="col-sm-2 control-label">Mobile Number <span class="require">*</span></label>
                                                        <div class="col-sm-10 flex-input">
                                                            <div class="county-code">
                                                            <div class="select">
                                                            <select name="country_code" id="country_code">
                                                            <option data-countryCode="DZ" value="+213">Algeria (+213)</option>
                                    <option <?php echo matchSel("+376", $recordInfo->country_code); ?> value="+376">Andorra (+376)</option>
                                    <option <?php echo matchSel("+244", $recordInfo->country_code); ?> value="+244">Angola (+244)</option>
                                    <option <?php echo matchSel("+1264", $recordInfo->country_code); ?> value="+1264">Anguilla (+1264)</option>
                                    <option <?php echo matchSel("+1268", $recordInfo->country_code); ?> value="+1268">Antigua &amp; Barbuda (+1268)</option>
                                    <option <?php echo matchSel("+54", $recordInfo->country_code); ?> value="+54">Argentina (+54)</option>
                                    <option <?php echo matchSel("+374", $recordInfo->country_code); ?> value="+374">Armenia (+374)</option>
                                    <option <?php echo matchSel("+297", $recordInfo->country_code); ?> value="+297">Aruba (+297)</option>
                                    <option <?php echo matchSel("+61", $recordInfo->country_code); ?> value="+61">Australia (+61)</option>
                                    <option <?php echo matchSel("+43", $recordInfo->country_code); ?> value="+43">Austria (+43)</option>
                                    <option <?php echo matchSel("+994", $recordInfo->country_code); ?> value="+994">Azerbaijan (+994)</option>
                                    <option <?php echo matchSel("+1242", $recordInfo->country_code); ?> value="+1242">Bahamas (+1242)</option>
                                    <option <?php echo matchSel("+973", $recordInfo->country_code); ?> value="+973">Bahrain (+973)</option>
                                    <option <?php echo matchSel("+880", $recordInfo->country_code); ?> value="+880">Bangladesh (+880)</option>
                                    <option <?php echo matchSel("+1246", $recordInfo->country_code); ?> value="+1246">Barbados (+1246)</option>
                                    <option <?php echo matchSel("+375", $recordInfo->country_code); ?> value="+375">Belarus (+375)</option>
                                    <option <?php echo matchSel("+32", $recordInfo->country_code); ?> value="+32">Belgium (+32)</option>
                                    <option <?php echo matchSel("+501", $recordInfo->country_code); ?> value="+501">Belize (+501)</option>
                                    <option <?php echo matchSel("+229", $recordInfo->country_code); ?> value="+229">Benin (+229)</option>
                                    <option <?php echo matchSel("+1441", $recordInfo->country_code); ?> value="+1441">Bermuda (+1441)</option>
                                    <option <?php echo matchSel("+975", $recordInfo->country_code); ?> value="+975">Bhutan (+975)</option>
                                    <option <?php echo matchSel("+591", $recordInfo->country_code); ?> value="+591">Bolivia (+591)</option>
                                    <option <?php echo matchSel("+387", $recordInfo->country_code); ?> value="+387">Bosnia Herzegovina (+387)</option>
                                    <option <?php echo matchSel("+267", $recordInfo->country_code); ?> value="+267">Botswana (+267)</option>
                                    <option <?php echo matchSel("+55", $recordInfo->country_code); ?> value="+55">Brazil (+55)</option>
                                    <option <?php echo matchSel("+673", $recordInfo->country_code); ?> value="+673">Brunei (+673)</option>
                                    <option <?php echo matchSel("+359", $recordInfo->country_code); ?> value="+359">Bulgaria (+359)</option>
                                    <option <?php echo matchSel("+226", $recordInfo->country_code); ?> value="+226">Burkina Faso (+226)</option>
                                    <option <?php echo matchSel("+257", $recordInfo->country_code); ?> value="+257">Burundi (+257)</option>
                                    <option <?php echo matchSel("+855", $recordInfo->country_code); ?> value="+855">Cambodia (+855)</option>
                                    <option <?php echo matchSel("+237", $recordInfo->country_code); ?> value="+237">Cameroon (+237)</option>
                                    <option <?php echo matchSel("+1", $recordInfo->country_code); ?> value="+1">Canada (+1)</option>
                                    <option <?php echo matchSel("+238", $recordInfo->country_code); ?> value="+238">Cape Verde Islands (+238)</option>
                                    <option <?php echo matchSel("+1345", $recordInfo->country_code); ?> value="+1345">Cayman Islands (+1345)</option>
                                    <option <?php echo matchSel("+236", $recordInfo->country_code); ?> value="+236">Central African Republic (+236)</option>
                                    <option <?php echo matchSel("+56", $recordInfo->country_code); ?> value="+56">Chile (+56)</option>
                                    <option <?php echo matchSel("+86", $recordInfo->country_code); ?> value="+86">China (+86)</option>
                                    <option <?php echo matchSel("+57", $recordInfo->country_code); ?> value="+57">Colombia (+57)</option>
                                    <option <?php echo matchSel("+269", $recordInfo->country_code); ?> value="+269">Comoros (+269)</option>
                                    <option <?php echo matchSel("+242", $recordInfo->country_code); ?> value="+242">Congo (+242)</option>
                                    <option <?php echo matchSel("+682", $recordInfo->country_code); ?> value="+682">Cook Islands (+682)</option>
                                    <option <?php echo matchSel("+506", $recordInfo->country_code); ?> value="+506">Costa Rica (+506)</option>
                                    <option <?php echo matchSel("+385", $recordInfo->country_code); ?> value="+385">Croatia (+385)</option>
                                    <option <?php echo matchSel("+53", $recordInfo->country_code); ?> value="+53">Cuba (+53)</option>
                                    <option <?php echo matchSel("+90392", $recordInfo->country_code); ?> value="+90392">Cyprus North (+90392)</option>
                                    <option <?php echo matchSel("+357", $recordInfo->country_code); ?> value="+357">Cyprus South (+357)</option>
                                    <option <?php echo matchSel("+42", $recordInfo->country_code); ?> value="+42">Czech Republic (+42)</option>
                                    <option <?php echo matchSel("+45", $recordInfo->country_code); ?> value="+45">Denmark (+45)</option>
                                    <option <?php echo matchSel("+253", $recordInfo->country_code); ?> value="+253">Djibouti (+253)</option>
                                    <option <?php echo matchSel("+1809", $recordInfo->country_code); ?> value="+1809">Dominica (+1809)</option>
                                    <option <?php echo matchSel("+1809", $recordInfo->country_code); ?> value="+1809">Dominican Republic (+1809)</option>
                                    <option <?php echo matchSel("+593", $recordInfo->country_code); ?> value="+593">Ecuador (+593)</option>
                                    <option <?php echo matchSel("+20", $recordInfo->country_code); ?> value="+20">Egypt (+20)</option>
                                    <option <?php echo matchSel("+503", $recordInfo->country_code); ?> value="+503">El Salvador (+503)</option>
                                    <option <?php echo matchSel("+240", $recordInfo->country_code); ?> value="+240">Equatorial Guinea (+240)</option>
                                    <option <?php echo matchSel("+291", $recordInfo->country_code); ?> value="+291">Eritrea (+291)</option>
                                    <option <?php echo matchSel("+372", $recordInfo->country_code); ?> value="+372">Estonia (+372)</option>
                                    <option <?php echo matchSel("+251", $recordInfo->country_code); ?> value="+251">Ethiopia (+251)</option>
                                    <option <?php echo matchSel("+500", $recordInfo->country_code); ?> value="+500">Falkland Islands (+500)</option>
                                    <option <?php echo matchSel("+298", $recordInfo->country_code); ?> value="+298">Faroe Islands (+298)</option>
                                    <option <?php echo matchSel("+679", $recordInfo->country_code); ?> value="+679">Fiji (+679)</option>
                                    <option <?php echo matchSel("+358", $recordInfo->country_code); ?> value="+358">Finland (+358)</option>
                                    <option <?php echo matchSel("+33", $recordInfo->country_code); ?> value="+33">France (+33)</option>
                                    <option <?php echo matchSel("+594", $recordInfo->country_code); ?> value="+594">French Guiana (+594)</option>
                                    <option <?php echo matchSel("+689", $recordInfo->country_code); ?> value="+689">French Polynesia (+689)</option>
                                    <option <?php echo matchSel("+241", $recordInfo->country_code); ?> value="+241">Gabon (+241)</option>
                                    <option <?php echo matchSel("+220", $recordInfo->country_code); ?> value="+220">Gambia (+220)</option>
                                    <option <?php echo matchSel("+7880", $recordInfo->country_code); ?> value="+7880">Georgia (+7880)</option>
                                    <option <?php echo matchSel("+49", $recordInfo->country_code); ?> value="+49">Germany (+49)</option>
                                    <option <?php echo matchSel("+233", $recordInfo->country_code); ?> value="+233">Ghana (+233)</option>
                                    <option <?php echo matchSel("+350", $recordInfo->country_code); ?> value="+350">Gibraltar (+350)</option>
                                    <option <?php echo matchSel("+30", $recordInfo->country_code); ?> value="+30">Greece (+30)</option>
                                    <option <?php echo matchSel("+299", $recordInfo->country_code); ?> value="+299">Greenland (+299)</option>
                                    <option <?php echo matchSel("+1473", $recordInfo->country_code); ?> value="+1473">Grenada (+1473)</option>
                                    <option <?php echo matchSel("+590", $recordInfo->country_code); ?> value="+590">Guadeloupe (+590)</option>
                                    <option <?php echo matchSel("+671", $recordInfo->country_code); ?> value="+671">Guam (+671)</option>
                                    <option <?php echo matchSel("+502", $recordInfo->country_code); ?> value="+502">Guatemala (+502)</option>
                                    <option <?php echo matchSel("+224", $recordInfo->country_code); ?> value="+224">Guinea (+224)</option>
                                    <option <?php echo matchSel("+245", $recordInfo->country_code); ?> value="+245">Guinea - Bissau (+245)</option>
                                    <option <?php echo matchSel("+592", $recordInfo->country_code); ?> value="+592">Guyana (+592)</option>
                                    <option <?php echo matchSel("+509", $recordInfo->country_code); ?> value="+509">Haiti (+509)</option>
                                    <option <?php echo matchSel("+504", $recordInfo->country_code); ?> value="+504">Honduras (+504)</option>
                                    <option <?php echo matchSel("+852", $recordInfo->country_code); ?> value="+852">Hong Kong (+852)</option>
                                    <option <?php echo matchSel("+36", $recordInfo->country_code); ?> value="+36">Hungary (+36)</option>
                                    <option <?php echo matchSel("+354", $recordInfo->country_code); ?> value="+354">Iceland (+354)</option>
                                    <option <?php echo matchSel("+91", $recordInfo->country_code); ?> value="+91">India (+91)</option>
                                    <option <?php echo matchSel("+62", $recordInfo->country_code); ?> value="+62">Indonesia (+62)</option>
                                    <option <?php echo matchSel("+98", $recordInfo->country_code); ?> value="+98">Iran (+98)</option>
                                    <option <?php echo matchSel("+964", $recordInfo->country_code); ?> value="+964">Iraq (+964)</option>
                                    <option <?php echo matchSel("+353", $recordInfo->country_code); ?> value="+353">Ireland (+353)</option>
                                    <option <?php echo matchSel("+972", $recordInfo->country_code); ?> value="+972">Israel (+972)</option>
                                    <option <?php echo matchSel("+39", $recordInfo->country_code); ?> value="+39">Italy (+39)</option>
                                    <option <?php echo matchSel("+1876", $recordInfo->country_code); ?> value="+1876">Jamaica (+1876)</option>
                                    <option <?php echo matchSel("+81", $recordInfo->country_code); ?> value="+81">Japan (+81)</option>
                                    <option <?php echo matchSel("+962", $recordInfo->country_code); ?> value="+962">Jordan (+962)</option>
                                    <option <?php echo matchSel("+7", $recordInfo->country_code); ?> value="+7">Kazakhstan (+7)</option>
                                    <option <?php echo matchSel("+254", $recordInfo->country_code); ?> value="+254">Kenya (+254)</option>
                                    <option <?php echo matchSel("+686", $recordInfo->country_code); ?> value="+686">Kiribati (+686)</option>
                                    <option <?php echo matchSel("+850", $recordInfo->country_code); ?> value="+850">Korea North (+850)</option>
                                    <option <?php echo matchSel("+82", $recordInfo->country_code); ?> value="+82">Korea South (+82)</option>
                                    <option <?php echo matchSel("+965", $recordInfo->country_code); ?> value="+965">Kuwait (+965)</option>
                                    <option <?php echo matchSel("+996", $recordInfo->country_code); ?> value="+996">Kyrgyzstan (+996)</option>
                                    <option <?php echo matchSel("+856", $recordInfo->country_code); ?> value="+856">Laos (+856)</option>
                                    <option <?php echo matchSel("+371", $recordInfo->country_code); ?> value="+371">Latvia (+371)</option>
                                    <option <?php echo matchSel("+961", $recordInfo->country_code); ?> value="+961">Lebanon (+961)</option>
                                    <option <?php echo matchSel("+266", $recordInfo->country_code); ?> value="+266">Lesotho (+266)</option>
                                    <option <?php echo matchSel("+231", $recordInfo->country_code); ?> value="+231">Liberia (+231)</option>
                                    <option <?php echo matchSel("+218", $recordInfo->country_code); ?> value="+218">Libya (+218)</option>
                                    <option <?php echo matchSel("+417", $recordInfo->country_code); ?> value="+417">Liechtenstein (+417)</option>
                                    <option <?php echo matchSel("+370", $recordInfo->country_code); ?> value="+370">Lithuania (+370)</option>
                                    <option <?php echo matchSel("+352", $recordInfo->country_code); ?> value="+352">Luxembourg (+352)</option>
                                    <option <?php echo matchSel("+853", $recordInfo->country_code); ?> value="+853">Macao (+853)</option>
                                    <option <?php echo matchSel("+389", $recordInfo->country_code); ?> value="+389">Macedonia (+389)</option>
                                    <option <?php echo matchSel("+261", $recordInfo->country_code); ?> value="+261">Madagascar (+261)</option>
                                    <option <?php echo matchSel("+265", $recordInfo->country_code); ?> value="+265">Malawi (+265)</option>
                                    <option <?php echo matchSel("+60", $recordInfo->country_code); ?> value="+60">Malaysia (+60)</option>
                                    <option <?php echo matchSel("+960", $recordInfo->country_code); ?> value="+960">Maldives (+960)</option>
                                    <option <?php echo matchSel("+223", $recordInfo->country_code); ?> value="+223">Mali (+223)</option>
                                    <option <?php echo matchSel("+356", $recordInfo->country_code); ?> value="+356">Malta (+356)</option>
                                    <option <?php echo matchSel("+692", $recordInfo->country_code); ?> value="+692">Marshall Islands (+692)</option>
                                    <option <?php echo matchSel("+596", $recordInfo->country_code); ?> value="+596">Martinique (+596)</option>
                                    <option <?php echo matchSel("+222", $recordInfo->country_code); ?> value="+222">Mauritania (+222)</option>
                                    <option <?php echo matchSel("+269", $recordInfo->country_code); ?> value="+269">Mayotte (+269)</option>
                                    <option <?php echo matchSel("+52", $recordInfo->country_code); ?> value="+52">Mexico (+52)</option>
                                    <option <?php echo matchSel("+691", $recordInfo->country_code); ?> value="+691">Micronesia (+691)</option>
                                    <option <?php echo matchSel("+373", $recordInfo->country_code); ?> value="+373">Moldova (+373)</option>
                                    <option <?php echo matchSel("+377", $recordInfo->country_code); ?> value="+377">Monaco (+377)</option>
                                    <option <?php echo matchSel("+976", $recordInfo->country_code); ?> value="+976">Mongolia (+976)</option>
                                    <option <?php echo matchSel("+1664", $recordInfo->country_code); ?> value="+1664">Montserrat (+1664)</option>
                                    <option <?php echo matchSel("+212", $recordInfo->country_code); ?> value="+212">Morocco (+212)</option>
                                    <option <?php echo matchSel("+258", $recordInfo->country_code); ?> value="+258">Mozambique (+258)</option>
                                    <option <?php echo matchSel("+95", $recordInfo->country_code); ?> value="+95">Myanmar (+95)</option>
                                    <option <?php echo matchSel("+264", $recordInfo->country_code); ?> value="+264">Namibia (+264)</option>
                                    <option <?php echo matchSel("+674", $recordInfo->country_code); ?> value="+674">Nauru (+674)</option>
                                    <option <?php echo matchSel("+977", $recordInfo->country_code); ?> value="+977">Nepal (+977)</option>
                                    <option <?php echo matchSel("+31", $recordInfo->country_code); ?> value="+31">Netherlands (+31)</option>
                                    <option <?php echo matchSel("+687", $recordInfo->country_code); ?> value="+687">New Caledonia (+687)</option>
                                    <option <?php echo matchSel("+64", $recordInfo->country_code); ?> value="+64">New Zealand (+64)</option>
                                    <option <?php echo matchSel("+505", $recordInfo->country_code); ?> value="+505">Nicaragua (+505)</option>
                                    <option <?php echo matchSel("+227", $recordInfo->country_code); ?> value="+227">Niger (+227)</option>
                                    <option <?php echo matchSel("+234", $recordInfo->country_code); ?> value="+234">Nigeria (+234)</option>
                                    <option <?php echo matchSel("+683", $recordInfo->country_code); ?> value="+683">Niue (+683)</option>
                                    <option <?php echo matchSel("+672", $recordInfo->country_code); ?> value="+672">Norfolk Islands (+672)</option>
                                    <option <?php echo matchSel("+670", $recordInfo->country_code); ?> value="+670">Northern Marianas (+670)</option>
                                    <option <?php echo matchSel("+47", $recordInfo->country_code); ?> value="+47">Norway (+47)</option>
                                    <option <?php echo matchSel("+968", $recordInfo->country_code); ?> value="+968">Oman (+968)</option>
                                    <option <?php echo matchSel("+680", $recordInfo->country_code); ?> value="+680">Palau (+680)</option>
                                    <option <?php echo matchSel("+507", $recordInfo->country_code); ?> value="+507">Panama (+507)</option>
                                    <option <?php echo matchSel("+675", $recordInfo->country_code); ?> value="+675">Papua New Guinea (+675)</option>
                                    <option <?php echo matchSel("+595", $recordInfo->country_code); ?> value="+595">Paraguay (+595)</option>
                                    <option <?php echo matchSel("+51", $recordInfo->country_code); ?> value="+51">Peru (+51)</option>
                                    <option <?php echo matchSel("+63", $recordInfo->country_code); ?> value="+63">Philippines (+63)</option>
                                    <option <?php echo matchSel("+48", $recordInfo->country_code); ?> value="+48">Poland (+48)</option>
                                    <option <?php echo matchSel("+351", $recordInfo->country_code); ?> value="+351">Portugal (+351)</option>
                                    <option <?php echo matchSel("+1787", $recordInfo->country_code); ?> value="+1787">Puerto Rico (+1787)</option>
                                    <option <?php echo matchSel("+974", $recordInfo->country_code); ?> value="+974">Qatar (+974)</option>
                                    <option <?php echo matchSel("+262", $recordInfo->country_code); ?> value="+262">Reunion (+262)</option>
                                    <option <?php echo matchSel("+40", $recordInfo->country_code); ?> value="+40">Romania (+40)</option>
                                    <option <?php echo matchSel("+7", $recordInfo->country_code); ?> value="+7">Russia (+7)</option>
                                    <option <?php echo matchSel("+250", $recordInfo->country_code); ?> value="+250">Rwanda (+250)</option>
                                    <option <?php echo matchSel("+378", $recordInfo->country_code); ?> value="+378">San Marino (+378)</option>
                                    <option <?php echo matchSel("+239", $recordInfo->country_code); ?> value="+239">Sao Tome &amp; Principe (+239)</option>
                                    <option <?php echo matchSel("+966", $recordInfo->country_code); ?> value="+966">Saudi Arabia (+966)</option>
                                    <option <?php echo matchSel("+221", $recordInfo->country_code); ?> value="+221">Senegal (+221)</option>
                                    <option <?php echo matchSel("+381", $recordInfo->country_code); ?> value="+381">Serbia (+381)</option>
                                    <option <?php echo matchSel("+248", $recordInfo->country_code); ?> value="+248">Seychelles (+248)</option>
                                    <option <?php echo matchSel("+232", $recordInfo->country_code); ?> value="+232">Sierra Leone (+232)</option>
                                    <option <?php echo matchSel("+65", $recordInfo->country_code); ?> value="+65">Singapore (+65)</option>
                                    <option <?php echo matchSel("+421", $recordInfo->country_code); ?> value="+421">Slovak Republic (+421)</option>
                                    <option <?php echo matchSel("+386", $recordInfo->country_code); ?> value="+386">Slovenia (+386)</option>
                                    <option <?php echo matchSel("+677", $recordInfo->country_code); ?> value="+677">Solomon Islands (+677)</option>
                                    <option <?php echo matchSel("+252", $recordInfo->country_code); ?> value="+252">Somalia (+252)</option>
                                    <option <?php echo matchSel("+27", $recordInfo->country_code); ?> value="+27">South Africa (+27)</option>
                                    <option <?php echo matchSel("+34", $recordInfo->country_code); ?> value="+34">Spain (+34)</option>
                                    <option <?php echo matchSel("+94", $recordInfo->country_code); ?> value="+94">Sri Lanka (+94)</option>
                                    <option <?php echo matchSel("+290", $recordInfo->country_code); ?> value="+290">St. Helena (+290)</option>
                                    <option <?php echo matchSel("+1869", $recordInfo->country_code); ?> value="+1869">St. Kitts (+1869)</option>
                                    <option <?php echo matchSel("+1758", $recordInfo->country_code); ?> value="+1758">St. Lucia (+1758)</option>
                                    <option <?php echo matchSel("+249", $recordInfo->country_code); ?> value="+249">Sudan (+249)</option>
                                    <option <?php echo matchSel("+597", $recordInfo->country_code); ?> value="+597">Suriname (+597)</option>
                                    <option <?php echo matchSel("+268", $recordInfo->country_code); ?> value="+268">Swaziland (+268)</option>
                                    <option <?php echo matchSel("+46", $recordInfo->country_code); ?> value="+46">Sweden (+46)</option>
                                    <option <?php echo matchSel("+41", $recordInfo->country_code); ?> value="+41">Switzerland (+41)</option>
                                    <option <?php echo matchSel("+963", $recordInfo->country_code); ?> value="+963">Syria (+963)</option>
                                    <option <?php echo matchSel("+886", $recordInfo->country_code); ?> value="+886">Taiwan (+886)</option>
                                    <option <?php echo matchSel("+7", $recordInfo->country_code); ?> value="+7">Tajikstan (+7)</option>
                                    <option <?php echo matchSel("+66", $recordInfo->country_code); ?> value="+66">Thailand (+66)</option>
                                    <option <?php echo matchSel("+228", $recordInfo->country_code); ?> value="+228">Togo (+228)</option>
                                    <option <?php echo matchSel("+676", $recordInfo->country_code); ?> value="+676">Tonga (+676)</option>
                                    <option <?php echo matchSel("+1868", $recordInfo->country_code); ?> value="+1868">Trinidad &amp; Tobago (+1868)</option>
                                    <option <?php echo matchSel("+216", $recordInfo->country_code); ?> value="+216">Tunisia (+216)</option>
                                    <option <?php echo matchSel("+90", $recordInfo->country_code); ?> value="+90">Turkey (+90)</option>
                                    <option <?php echo matchSel("+7", $recordInfo->country_code); ?> value="+7">Turkmenistan (+7)</option>
                                    <option <?php echo matchSel("+993", $recordInfo->country_code); ?> value="+993">Turkmenistan (+993)</option>
                                    <option <?php echo matchSel("+1649", $recordInfo->country_code); ?> value="+1649">Turks &amp; Caicos Islands (+1649)</option>
                                    <option <?php echo matchSel("+688", $recordInfo->country_code); ?> value="+688">Tuvalu (+688)</option>
                                    <option <?php echo matchSel("+256", $recordInfo->country_code); ?> value="+256">Uganda (+256)</option>
                                    <option <?php echo matchSel("+44", $recordInfo->country_code); ?> value="+44">UK (+44)</option>
                                    <option <?php echo matchSel("+380", $recordInfo->country_code); ?> value="+380">Ukraine (+380)</option>
                                    <option <?php echo matchSel("+971", $recordInfo->country_code); ?> value="+971">United Arab Emirates (+971)</option>
                                    <option <?php echo matchSel("+598", $recordInfo->country_code); ?> value="+598">Uruguay (+598)</option>
                                    <option <?php echo matchSel("+1", $recordInfo->country_code); ?> value="+1">USA (+1)</option>
                                    <option <?php echo matchSel("+7", $recordInfo->country_code); ?> value="+7">Uzbekistan (+7)</option>
                                    <option <?php echo matchSel("+678", $recordInfo->country_code); ?> value="+678">Vanuatu (+678)</option>
                                    <option <?php echo matchSel("+379", $recordInfo->country_code); ?> value="+379">Vatican City (+379)</option>
                                    <option <?php echo matchSel("+58", $recordInfo->country_code); ?> value="+58">Venezuela (+58)</option>
                                    <option <?php echo matchSel("+84", $recordInfo->country_code); ?> value="+84">Vietnam (+84)</option>
                                    <option <?php echo matchSel("+1284", $recordInfo->country_code); ?> value="+1284">Virgin Islands - British (+1284)</option>
                                    <option <?php echo matchSel("+1340", $recordInfo->country_code); ?> value="+1340">Virgin Islands - US (+1340)</option>
                                    <option <?php echo matchSel("+681", $recordInfo->country_code); ?> value="+681">Wallis &amp; Futuna (+681)</option>
                                    <option <?php echo matchSel("+969", $recordInfo->country_code); ?> value="+969">Yemen (North)(+969)</option>
                                    <option <?php echo matchSel("+967", $recordInfo->country_code); ?> value="+967">Yemen (South)(+967)</option>
                                    <option <?php echo matchSel("+260", $recordInfo->country_code); ?> value="+260">Zambia (+260)</option>
                                    <option <?php echo matchSel("+263", $recordInfo->country_code); ?> value="+263">Zimbabwe (+263)</option>
                                    </select>
                                                            </div>
                                                                    </div>
                                                                    @php
                                     $phn = str_replace($recordInfo->country_code,"",$recordInfo->phone);
                                    @endphp
                                                                    <input id="phone" class="form-control required digits" placeholder="Mobile Number" autocomplete="off" name="phone" type="text" value="<?php echo $phn; ?>">
                                                                    <span id="phMinLngthErr"></span>
                                                            </div>
                                                            </div>	-->

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Country <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::select('country', $countrList,null, ['class' => 'form-control required','placeholder' => 'Select Country'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Currency <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <?php 
                            global $currencyList;
                            ?>
                            {{Form::select('currency', $currencyList,null, ['class' => 'form-control required','placeholder' => 'Select Currency'])}}
                        </div>
                    </div> 
                    <?php /*<div class="form-group">
                        <label class="col-sm-2 control-label">Identity Card Type <span class="require"></span></label>
                        <div class="col-sm-10">
                            <?php global $identityType; ?>
                            {{Form::select('identity_card_type', $identityType,$recordInfo->identity_card_type, ['class' => 'form-control ','placeholder' => 'Select Document Type'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Enter Document Number (if applicable) <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('national_identity_number', $recordInfo->national_identity_number, ['class'=>'form-control ', 'placeholder'=>'Enter Document Number (if applicable)', 'autocomplete' => 'off','maxlength'=>50])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Upload identity document<span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('identity_image', ['class'=>'form-control', 'accept'=>IMAGE_DOC_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                            @if($recordInfo->identity_image != '')
                            <div class="showeditimage">{{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image, SITE_TITLE,['style'=>"max-width: 200px"])}}</div>
                            <!--<div class="help-text"><a href="{{ URL::to('admin/users/deleteimage/'.$recordInfo->slug)}}" title="Delete Image" class="canlcel_le"  onclick="return confirm('Are you sure you want to delete?')">Delete Image</a></div>-->
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Proof Of Address Type <span class="require"></span></label>
                        <div class="col-sm-10">
                            <?php global $addressType; ?>
                            {{Form::select('address_proof_type', $addressType,$recordInfo->address_proof_type, ['class' => 'form-control','placeholder' => 'Select Document Type'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Enter Document Number (if applicable) <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('address_proof_number', $recordInfo->address_proof_number, ['class'=>'form-control ', 'placeholder'=>'Enter Document Number (if applicable)', 'autocomplete' => 'off','maxlength'=>50])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Upload proof of address document<span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::file('address_document', ['class'=>'form-control', 'accept'=>IMAGE_DOC_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                            @if($recordInfo->address_document != '')
                            <div class="showeditimage">
                                {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->address_document, SITE_TITLE,['style'=>"max-width: 200px"])}}</div>
                            <!--<div class="help-text"><a href="{{ URL::to('admin/users/deleteimage/'.$recordInfo->slug)}}" title="Delete Image" class="canlcel_le"  onclick="return confirm('Are you sure you want to delete?')">Delete Image</a></div>-->
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Selfie <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::file('profile_image', ['class'=>'form-control', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                            @if($recordInfo->image != '')
                            <div class="showeditimage">{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->profile_image, SITE_TITLE,['style'=>"max-width: 200px"])}}</div>
                            <!-- <div class="help-text"><a href="{{ URL::to('admin/users/deleteimage/'.$recordInfo->slug)}}" title="Delete Image" class="canlcel_le"  onclick="return confirm('Are you sure you want to delete?')">Delete Image</a></div> -->
                            @endif
                        </div>
                    </div>    
*/?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Password <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::password('password', ['class'=>'form-control ', 'placeholder' => 'Password', 'minlength' => 8, 'id'=>'password'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Profile Image <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::file('image', ['class'=>'form-control', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                            @if($recordInfo->image != '')
                            <div class="showeditimage">{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->image, SITE_TITLE,['style'=>"max-width: 200px"])}}</div>
                            <!-- <div class="help-text"><a href="{{ URL::to('admin/users/deleteimage/'.$recordInfo->slug)}}" title="Delete Image" class="canlcel_le"  onclick="return confirm('Are you sure you want to delete?')">Delete Image</a></div> -->
                            @endif
                        </div>
                    </div>                        




                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        <input type="hidden" name="country_code" id="contryCode" value="{{$recordInfo->country_code}}">
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        <a href="{{ URL::to( 'admin/merchants')}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
                    </div>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>
    {{ HTML::script('public/assets/js/intlTelInput.js')}}
    <script>
        var phone_number = window.intlTelInput(document.querySelector("#phone"), {
            separateDialCode: true,
            preferredCountries: false,
            //onlyCountries: ['iq'],
            hiddenInput: "phone",
            formatOnDisplay: false,
            utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
        });

        $("#adminForm").validate(function () {
            var full_number = phone_number.getNumber();
            $("input[name='phone'").val(full_number);
            alert(full_number)
        });

        function setCountryCode()
        {
            var coCode = $('.iti__selected-dial-code').html();
            document.getElementById('contryCode').value = coCode;
            //alert(coCode+" :: "+document.getElementById('contryCode').value);	
        }

    </script>
    @endsection