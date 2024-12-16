@extends('layouts.admin')
@section('content')

<script type="text/javascript">
    $(document).ready(function () {
          $('#email').keyup(function() {
           
            $(this).val($(this).val().replace(/ +?/g, ''));
          });
        $('#password').keyup(function() {
            $(this).val($(this).val().replace(/ +?/g, ''));
          });
        $.validator.addMethod("alphanumeric", function (value, element) {
            return this.optional(element) || /^[\w.]+$/i.test(value);
        }, "Only letters, numbers and underscore allowed.");
        $.validator.addMethod("passworreq", function (input) {
            var reg = /[0-9]/; //at least one number
            var reg2 = /[a-z]/; //at least one small character
            var reg3 = /[A-Z]/; //at least one capital character
            var reg4 = /[\W_]/; //at least one special character
            return reg.test(input) && reg2.test(input) && reg3.test(input) && reg4.test(input);
        }, "Password must be at least 8 characters long, contains an upper case letter, a lower case letter, a number and a symbol.");

        $("#adminForm").validate();
		
		$("#phone").keyup(function () {
			$("#phMinLngthErr").html('');
			if (!isNaN(this.value) && this.value != "") {
            // if (this.value.length < 6) {
            //     //$(this).next('.opt_input').focus();
				
			// 	$("#phMinLngthErr").html('Please enter at least 6 digits.');
            // }
			// else {
			// 	$("#phMinLngthErr").html('');
			// }
			}
			/*else if (isNaN(this.value)) {
			  $("#phMinLngthErr").html('Use digits only');	
			}*/
        });
		
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
        <h1>Add Business User</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/merchants')}}"><i class="fa fa-users"></i> <span>Manage Business Users</span></a></li>
            <li class="active"> Add Business User</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{ Form::open(array('method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data",'onsubmit'=>'return disable_submit();')) }}
            <div class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('business_name', null, ['class'=>'form-control required', 'placeholder'=>'Business Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    
                    <div class="form-group add-personal-field">    
                        <label class="col-sm-2 control-label">Director Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                        	 <div class="add-select-option">
	                            {{Form::text('director_name', null, ['class'=>'form-control required', 'placeholder'=>'Director Name', 'autocomplete' => 'off'])}}
	                            <div class="perosnal-select-box">
                                <?php global $sernameList; ?>    
                                {{Form::select('gender', $sernameList,null, ['class' => 'required'])}}  
                                </div>   
	                            </div>
	                        </div>
                        </div>
                    </div>  

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Email <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('email', null, ['class'=>'form-control email required', 'placeholder'=>'Business Email', 'autocomplete' => 'off', 'id'=>'email'])}}
                        </div>
                    </div>  
                    
                                        <div class="form-group">
                        <label class="col-sm-2 control-label">Mobile Number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('phone', null, ['id'=>'phone','class'=>'form-control required digits', 'placeholder'=>'Mobile Number', 'autocomplete' => 'off','minlength' => 6])}}
<span id="phMinLngthErr"></span>
                        </div>
                    </div> 
					
<!--					<div class="form-group">
					<label class="col-sm-2 control-label">Mobile Number <span class="require">*</span></label>
				    <div class="col-sm-10 flex-input">
					<div class="county-code">
					<div class="select">
					<select name="country_code" id="country_code" class="valid">
					<option data-countrycode="DZ" value="+213">Algeria (+213)</option>
		<option value="+376">Andorra (+376)</option>
		<option value="+244">Angola (+244)</option>
		<option value="+1264">Anguilla (+1264)</option>
		<option value="+1268">Antigua &amp; Barbuda (+1268)</option>
		<option value="+54">Argentina (+54)</option>
		<option value="+374">Armenia (+374)</option>
		<option value="+297">Aruba (+297)</option>
		<option value="+61">Australia (+61)</option>
		<option value="+43">Austria (+43)</option>
		<option value="+994">Azerbaijan (+994)</option>
		<option value="+1242">Bahamas (+1242)</option>
		<option value="+973">Bahrain (+973)</option>
		<option value="+880">Bangladesh (+880)</option>
		<option value="+1246">Barbados (+1246)</option>
		<option value="+375">Belarus (+375)</option>
		<option value="+32">Belgium (+32)</option>
		<option value="+501">Belize (+501)</option>
		<option value="+229">Benin (+229)</option>
		<option value="+1441">Bermuda (+1441)</option>
		<option value="+975">Bhutan (+975)</option>
		<option value="+591">Bolivia (+591)</option>
		<option value="+387">Bosnia Herzegovina (+387)</option>
		<option value="+267">Botswana (+267)</option>
		<option value="+55">Brazil (+55)</option>
		<option value="+673">Brunei (+673)</option>
		<option value="+359">Bulgaria (+359)</option>
		<option value="+226">Burkina Faso (+226)</option>
		<option value="+257">Burundi (+257)</option>
		<option value="+855">Cambodia (+855)</option>
		<option value="+237">Cameroon (+237)</option>
		<option value="+1">Canada (+1)</option>
		<option value="+238">Cape Verde Islands (+238)</option>
		<option value="+1345">Cayman Islands (+1345)</option>
		<option value="+236">Central African Republic (+236)</option>
		<option value="+56">Chile (+56)</option>
		<option value="+86">China (+86)</option>
		<option value="+57">Colombia (+57)</option>
		<option value="+269">Comoros (+269)</option>
		<option value="+242">Congo (+242)</option>
		<option value="+682">Cook Islands (+682)</option>
		<option value="+506">Costa Rica (+506)</option>
		<option value="+385">Croatia (+385)</option>
		<option value="+53">Cuba (+53)</option>
		<option value="+90392">Cyprus North (+90392)</option>
		<option value="+357">Cyprus South (+357)</option>
		<option value="+42">Czech Republic (+42)</option>
		<option value="+45">Denmark (+45)</option>
		<option value="+253">Djibouti (+253)</option>
		<option value="+1809">Dominica (+1809)</option>
		<option value="+1809">Dominican Republic (+1809)</option>
		<option value="+593">Ecuador (+593)</option>
		<option value="+20">Egypt (+20)</option>
		<option value="+503">El Salvador (+503)</option>
		<option value="+240">Equatorial Guinea (+240)</option>
		<option value="+291">Eritrea (+291)</option>
		<option value="+372">Estonia (+372)</option>
		<option value="+251">Ethiopia (+251)</option>
		<option value="+500">Falkland Islands (+500)</option>
		<option value="+298">Faroe Islands (+298)</option>
		<option value="+679">Fiji (+679)</option>
		<option value="+358">Finland (+358)</option>
		<option value="+33">France (+33)</option>
		<option value="+594">French Guiana (+594)</option>
		<option value="+689">French Polynesia (+689)</option>
		<option value="+241">Gabon (+241)</option>
		<option value="+220">Gambia (+220)</option>
		<option value="+7880">Georgia (+7880)</option>
		<option value="+49">Germany (+49)</option>
		<option value="+233">Ghana (+233)</option>
		<option value="+350">Gibraltar (+350)</option>
		<option value="+30">Greece (+30)</option>
		<option value="+299">Greenland (+299)</option>
		<option value="+1473">Grenada (+1473)</option>
		<option value="+590">Guadeloupe (+590)</option>
		<option value="+671">Guam (+671)</option>
		<option value="+502">Guatemala (+502)</option>
		<option value="+224">Guinea (+224)</option>
		<option value="+245">Guinea - Bissau (+245)</option>
		<option value="+592">Guyana (+592)</option>
		<option value="+509">Haiti (+509)</option>
		<option value="+504">Honduras (+504)</option>
		<option value="+852">Hong Kong (+852)</option>
		<option value="+36">Hungary (+36)</option>
		<option value="+354">Iceland (+354)</option>
		<option selected="" value="+91">India (+91)</option>
		<option value="+62">Indonesia (+62)</option>
		<option value="+98">Iran (+98)</option>
		<option value="+964">Iraq (+964)</option>
		<option value="+353">Ireland (+353)</option>
		<option value="+972">Israel (+972)</option>
		<option value="+39">Italy (+39)</option>
		<option value="+1876">Jamaica (+1876)</option>
		<option value="+81">Japan (+81)</option>
		<option value="+962">Jordan (+962)</option>
		<option value="+7">Kazakhstan (+7)</option>
		<option value="+254">Kenya (+254)</option>
		<option value="+686">Kiribati (+686)</option>
		<option value="+850">Korea North (+850)</option>
		<option value="+82">Korea South (+82)</option>
		<option value="+965">Kuwait (+965)</option>
		<option value="+996">Kyrgyzstan (+996)</option>
		<option value="+856">Laos (+856)</option>
		<option value="+371">Latvia (+371)</option>
		<option value="+961">Lebanon (+961)</option>
		<option value="+266">Lesotho (+266)</option>
		<option value="+231">Liberia (+231)</option>
		<option value="+218">Libya (+218)</option>
		<option value="+417">Liechtenstein (+417)</option>
		<option value="+370">Lithuania (+370)</option>
		<option value="+352">Luxembourg (+352)</option>
		<option value="+853">Macao (+853)</option>
		<option value="+389">Macedonia (+389)</option>
		<option value="+261">Madagascar (+261)</option>
		<option value="+265">Malawi (+265)</option>
		<option value="+60">Malaysia (+60)</option>
		<option value="+960">Maldives (+960)</option>
		<option value="+223">Mali (+223)</option>
		<option value="+356">Malta (+356)</option>
		<option value="+692">Marshall Islands (+692)</option>
		<option value="+596">Martinique (+596)</option>
		<option value="+222">Mauritania (+222)</option>
		<option value="+269">Mayotte (+269)</option>
		<option value="+52">Mexico (+52)</option>
		<option value="+691">Micronesia (+691)</option>
		<option value="+373">Moldova (+373)</option>
		<option value="+377">Monaco (+377)</option>
		<option value="+976">Mongolia (+976)</option>
		<option value="+1664">Montserrat (+1664)</option>
		<option value="+212">Morocco (+212)</option>
		<option value="+258">Mozambique (+258)</option>
		<option value="+95">Myanmar (+95)</option>
		<option value="+264">Namibia (+264)</option>
		<option value="+674">Nauru (+674)</option>
		<option value="+977">Nepal (+977)</option>
		<option value="+31">Netherlands (+31)</option>
		<option value="+687">New Caledonia (+687)</option>
		<option value="+64">New Zealand (+64)</option>
		<option value="+505">Nicaragua (+505)</option>
		<option value="+227">Niger (+227)</option>
		<option value="+234">Nigeria (+234)</option>
		<option value="+683">Niue (+683)</option>
		<option value="+672">Norfolk Islands (+672)</option>
		<option value="+670">Northern Marianas (+670)</option>
		<option value="+47">Norway (+47)</option>
		<option value="+968">Oman (+968)</option>
		<option value="+680">Palau (+680)</option>
		<option value="+507">Panama (+507)</option>
		<option value="+675">Papua New Guinea (+675)</option>
		<option value="+595">Paraguay (+595)</option>
		<option value="+51">Peru (+51)</option>
		<option value="+63">Philippines (+63)</option>
		<option value="+48">Poland (+48)</option>
		<option value="+351">Portugal (+351)</option>
		<option value="+1787">Puerto Rico (+1787)</option>
		<option value="+974">Qatar (+974)</option>
		<option value="+262">Reunion (+262)</option>
		<option value="+40">Romania (+40)</option>
		<option value="+7">Russia (+7)</option>
		<option value="+250">Rwanda (+250)</option>
		<option value="+378">San Marino (+378)</option>
		<option value="+239">Sao Tome &amp; Principe (+239)</option>
		<option value="+966">Saudi Arabia (+966)</option>
		<option value="+221">Senegal (+221)</option>
		<option value="+381">Serbia (+381)</option>
		<option value="+248">Seychelles (+248)</option>
		<option value="+232">Sierra Leone (+232)</option>
		<option value="+65">Singapore (+65)</option>
		<option value="+421">Slovak Republic (+421)</option>
		<option value="+386">Slovenia (+386)</option>
		<option value="+677">Solomon Islands (+677)</option>
		<option value="+252">Somalia (+252)</option>
		<option value="+27">South Africa (+27)</option>
		<option value="+34">Spain (+34)</option>
		<option value="+94">Sri Lanka (+94)</option>
		<option value="+290">St. Helena (+290)</option>
		<option value="+1869">St. Kitts (+1869)</option>
		<option value="+1758">St. Lucia (+1758)</option>
		<option value="+249">Sudan (+249)</option>
		<option value="+597">Suriname (+597)</option>
		<option value="+268">Swaziland (+268)</option>
		<option value="+46">Sweden (+46)</option>
		<option value="+41">Switzerland (+41)</option>
		<option value="+963">Syria (+963)</option>
		<option value="+886">Taiwan (+886)</option>
		<option value="+7">Tajikstan (+7)</option>
		<option value="+66">Thailand (+66)</option>
		<option value="+228">Togo (+228)</option>
		<option value="+676">Tonga (+676)</option>
		<option value="+1868">Trinidad &amp; Tobago (+1868)</option>
		<option value="+216">Tunisia (+216)</option>
		<option value="+90">Turkey (+90)</option>
		<option value="+7">Turkmenistan (+7)</option>
		<option value="+993">Turkmenistan (+993)</option>
		<option value="+1649">Turks &amp; Caicos Islands (+1649)</option>
		<option value="+688">Tuvalu (+688)</option>
		<option value="+256">Uganda (+256)</option>
		<option value="+44">UK (+44)</option>
		<option value="+380">Ukraine (+380)</option>
		<option value="+971">United Arab Emirates (+971)</option>
		<option value="+598">Uruguay (+598)</option>
		<option value="+1">USA (+1)</option>
		<option value="+7">Uzbekistan (+7)</option>
		<option value="+678">Vanuatu (+678)</option>
		<option value="+379">Vatican City (+379)</option>
		<option value="+58">Venezuela (+58)</option>
		<option value="+84">Vietnam (+84)</option>
		<option value="+1284">Virgin Islands - British (+1284)</option>
		<option value="+1340">Virgin Islands - US (+1340)</option>
		<option value="+681">Wallis &amp; Futuna (+681)</option>
		<option value="+969">Yemen (North)(+969)</option>
		<option value="+967">Yemen (South)(+967)</option>
		<option value="+260">Zambia (+260)</option>
		<option value="+263">Zimbabwe (+263)</option>
		</select>
		</div>
		</div>
				<input id="phone" class="form-control required digits" placeholder="Mobile Number" autocomplete="off" name="phone" type="text" value="995065465821">
				<span id="phMinLngthErr"></span>
		</div>
		</div>-->

                    
                   <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">Mobile Number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('phone', null, ['id'=>'phone','class'=>'form-control required digits', 'placeholder'=>'Mobile Number', 'autocomplete' => 'off', 'minlength' => 8, 'maxlength' => 16])}}

                        </div>
                    </div> -->					
                     
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
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Type <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <?php global $businessType; ?>
                            {{Form::select('business_type', $businessType,null, ['class' => 'form-control required','placeholder' => 'Select Business Type'])}}
                        </div>
                    </div>  
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business registration number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('registration_number', null, ['class'=>'form-control required', 'placeholder'=>'Business registration number', 'autocomplete' => 'off'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Registration Document <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('registration_document', ['class'=>'form-control required', 'accept'=>IMAGE_DOC_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        </div>
                    </div>
                    <?php /*<div class="form-group">
                        <label class="col-sm-2 control-label">Identity Card Type <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <?php global $identityType; ?>
                            {{Form::select('identity_card_type', $identityType,null, ['class' => 'form-control','placeholder' => 'Select Document Type'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Enter Document Number (if applicable) <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('national_identity_number', null, ['class'=>'form-control ', 'placeholder'=>'Enter Document Number (if applicable)', 'autocomplete' => 'off','maxlength'=>50])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Upload identity document<span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('identity_image', ['class'=>'form-control required', 'accept'=>IMAGE_DOC_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Proof Of Address Type <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <?php global $addressType; ?>
                            {{Form::select('address_proof_type', $addressType,null, ['class' => 'form-control','placeholder' => 'Select Document Type'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Enter Document Number (if applicable) <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('address_proof_number', null, ['class'=>'form-control ', 'placeholder'=>'Enter Document Number (if applicable)', 'autocomplete' => 'off','maxlength' => 50])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Upload proof of address document<span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('address_document', ['class'=>'form-control required', 'accept'=>IMAGE_DOC_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        </div>
                    </div>*/?>
					
					 <div class="form-group">
                        <label class="col-sm-2 control-label">Address Line 1 <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('addrs_line1', null, ['class'=>'form-control required', 'placeholder'=>'Enter Address Line 1', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Address Line 2 <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('addrs_line2', null, ['class'=>'form-control ', 'placeholder'=>'Enter Address Line 2', 'autocomplete' => 'off'])}}
                        </div>
                    </div> 

<!--                    <div class="form-group">
                        <label class="col-sm-2 control-label">Selfie <span class="require">*</span></label>
                        <div class="col-sm-10">
                        {{Form::file('profile_image', ['class'=>'form-control required', 'accept'=>IMAGE_EXT])}}
                        <span class="help-text"> Supported File Types: jpg, jpeg, png (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        </div>
                    </div>    -->

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Password <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::password('password', ['class'=>'form-control required passworreq', 'placeholder' => 'Password', 'minlength' => 8, 'id'=>'password'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Profile Image <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('image', ['class'=>'form-control required', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        </div>
                    </div>                  
                    

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        <input type="hidden" name="contryCode" id="contryCode" value="">
                        {{Form::submit('Submit', ['class' => 'btn btn-info button_disable','onclick'=>"setCountryCode();"])}}
                        {{Form::reset('Reset', ['class' => 'btn btn-default canlcel_le'])}}
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
    preferredCountries:false,
    //onlyCountries: ['iq'],
    hiddenInput: "phone",
    utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
    });

    $("#adminForm").validate(function () {
    var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
    $("input[name='phone'").val(full_number);
    alert(full_number)
    });
    
    function setCountryCode()
	{
	  var coCode = $('.iti__selected-dial-code').html();
	  document.getElementById('contryCode').value = coCode;
	  //alert(co
   //   Code+" :: "+document.getElementById('contryCode').value);	
	}
 
    function disable_submit()
    { 
    var empty_field=0;    
    $(".required").each(function() {
    if($(this).val()=="")
    {
    empty_field=1;  
    }
    });
    if(empty_field==0 && $(".required").hasClass('error')==false)
    {
    $('.button_disable').prop('disabled', true);   
    return true;
    }
    return false;
    }
    </script>
 @endsection