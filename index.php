<?php
$currency ="Insert Currency";
// $Amount is in non-decimal format.
$decAmount = 100;
// $decAmount converts the non-decimal format to tradtitional -
// two-digit-decimal.
$decAmount2 = number_format($decAmount/100, 2);
// Like the two variables above, Order Number is also required in -
// both the JWT Payload and the Order Object.
$OrderNumber = 'IMP-'.uniqid();
$redirect_url = "";
$retries = "";

// API Credentials
// Replace the following values between the '' marks with your own:
$cardinalApiKey = '';
$cardinalApiIdentifier = '';
$cardinalApiOrgUnitId = '';

// Build the JWT Payload with required elements.
$_SESSION['payload'] = array(
                "OrderDetails" => array(
                    "OrderNumber" =>  $OrderNumber,
                    "Amount" => $decAmount,
                    "CurrencyCode" => $currency)
            );
function base64_encode_urlsafe($source) {
    $rv = base64_encode($source);
    $rv = str_replace('=', '', $rv);
    $rv = str_replace('+', '-', $rv);
    $rv = str_replace('/', '_', $rv);
    return $rv;
}

function base64_decode_urlsafe($source) {
    $s = $source;
    $s = str_replace('-', '+', $s);
    $s = str_replace('_', '/', $s);
    $s = str_pad($s, strlen($s) + strlen($s) % 4, '=');
    $rv = base64_decode($s);
    return $rv;
}

function sign_jwt($header, $body) {
    global $cardinalApiKey;
    $plaintext = $header . '.' . $body;
    return base64_encode_urlsafe(hash_hmac(
        'sha256', $plaintext, $cardinalApiKey, true));
}

function generate_jwt($data) {
    $header = base64_encode_urlsafe(json_encode(array(
        'alg' => 'HS256', 'typ' => 'JWT'
    )));
    $body = base64_encode_urlsafe(json_encode($data));
    $signature = sign_jwt($header, $body);
    return $header . '.' . $body . '.' . $signature;
}

function generate_cruise_jwt($payload) {
    global $cardinalApiIdentifier, $cardinalApiOrgUnitId;
    $iat = time();
    $data = array(
        'jti' => uniqid(),
        'iat' => $iat,
        'exp' => $iat + 7200,
        'iss' => $cardinalApiIdentifier,
        'OrgUnitId' => $cardinalApiOrgUnitId,
    );
    // This is important: the CurrencyCode is required in the JWT Payload:
    $payload = 
    $data['Payload'] = $payload;
    $data['ObjectifyPayload'] = true;
    $_SESSION['jwtClaims'] = $data;
    $rv = generate_jwt($data);
    return $rv;
}

function parse_cruise_jwt($jwt) {
    $split = explode('.', $jwt);
    if (count($split) != 3) {
        return;
    }
    list($header, $body, $signature) = $split;
    if ($signature != sign_jwt($header, $body)) {
        return;
    }
    $payload = json_decode(base64_decode_urlsafe($body));
    return $payload;
}

// Generate JWT complete with JWT Payload.
//echo $_SESSION['payload'];
$jwt = generate_cruise_jwt($_SESSION['payload']);
?>
<html>
<head>
	<title>Simple Cardinal Commerce Project</title>
	<script src="https://songbirdstag.cardinalcommerce.com/cardinalcruise/v1/songbird.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<link type="text/css" rel="stylesheet" href="load/waitMe.css">
	<LINK REL="SHORTCUT ICON" HREF="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	
<style type="text/css">	
	#loader {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  background: rgba(0,0,0,0.75) url(images/loading2.gif) no-repeat center center;
  z-index: 10000;
}
</style>
</head>
<body>
	<div class="col-md-6 offset-md-3">
		<span class="anchor" id="formPayment"></span>
		<!-- form card cc payment -->
		<div class="card card-outline-secondary">
			<div class="card-body">
				<h3 class="text-center">Mam-laka</h3>
				<hr>
				<form class="form" role="form" autocomplete="off">
					<div type=hidden class="form-group">
					<input type=hidden id="JWTContainer"class="form-control" name="JWTContainer" value='<?php echo $jwt;?>'><br />
					</div>

					<div class="form-group">
					<label for="firstname">First Name</label>
					<input required name="firstname" id="firstname"  type="text" class="form-control"  
					autocomplete="off" maxlength="20" 
					title="First Name" >
					</div>

					<div class="form-group">
					<label for="lastname">Last Name</label>
					<input required name="lastname" id="lastname"  type="text" class="form-control"  
					autocomplete="off" maxlength="20" 
					title="Last Name" >
					</div>

					<div class="form-group">
					<label for="address1">Address</label>
					<input required name="address1" id="address1"  type="text" class="form-control"  
					autocomplete="off" maxlength="20" 
					title="Address" >
					</div>

					<div class="form-group">
					<label for="email">email</label>
					<input required name="email" id="email"  type="email" class="form-control"  
					autocomplete="off" maxlength="20" 
					title="email" >
					</div>

					<div class="form-group">
					<label for="city">City</label>
					<input required name="city" id="city"  type="text" class="form-control"  
					autocomplete="off" maxlength="20" 
					title="City" >
					</div>

					<div class="form-group">
					<label for="postalcode">Postal Code</label>
					<input required name="postalcode" id="postalcode"  type="text" class="form-control"  
					autocomplete="off" maxlength="20" 
					title="Postal Code" >
					</div>

					<div class="form-group">
					<label for="phonenumber">Phone Number</label>
					<input required name="phonenumber" id="phonenumber"  type="text" class="form-control"  
					autocomplete="off" maxlength="20" 
					title="Phone Number" >
					</div>
					<div class="form-group">
					
					 <select class="form-control" id="country" name="country">
						<option>select country</option>
						<option value="AF">Afghanistan</option>
						<option value="AX">Aland Islands</option>
						<option value="AL">Albania</option>
						<option value="DZ">Algeria</option>
						<option value="AS">American Samoa</option>
						<option value="AD">Andorra</option>
						<option value="AO">Angola</option>
						<option value="AI">Anguilla</option>
						<option value="AQ">Antarctica</option>
						<option value="AG">Antigua and Barbuda</option>
						<option value="AR">Argentina</option>
						<option value="AM">Armenia</option>
						<option value="AW">Aruba</option>
						<option value="AU">Australia</option>
						<option value="AT">Austria</option>
						<option value="AZ">Azerbaijan</option>
						<option value="BS">Bahamas</option>
						<option value="BH">Bahrain</option>
						<option value="BD">Bangladesh</option>
						<option value="BB">Barbados</option>
						<option value="BY">Belarus</option>
						<option value="BE">Belgium</option>
						<option value="BZ">Belize</option>
						<option value="BJ">Benin</option>
						<option value="BM">Bermuda</option>
						<option value="BT">Bhutan</option>
						<option value="BO">Bolivia</option>
						<option value="BQ">Bonaire, Sint Eustatius and Saba</option>
						<option value="BA">Bosnia and Herzegovina</option>
						<option value="BW">Botswana</option>
						<option value="BV">Bouvet Island</option>
						<option value="BR">Brazil</option>
						<option value="IO">British Indian Ocean Territory</option>
						<option value="BN">Brunei Darussalam</option>
						<option value="BG">Bulgaria</option>
						<option value="BF">Burkina Faso</option>
						<option value="BI">Burundi</option>
						<option value="KH">Cambodia</option>
						<option value="CM">Cameroon</option>
						<option value="CA">Canada</option>
						<option value="CV">Cape Verde</option>
						<option value="KY">Cayman Islands</option>
						<option value="CF">Central African Republic</option>
						<option value="TD">Chad</option>
						<option value="CL">Chile</option>
						<option value="CN">China</option>
						<option value="CX">Christmas Island</option>
						<option value="CC">Cocos (Keeling) Islands</option>
						<option value="CO">Colombia</option>
						<option value="KM">Comoros</option>
						<option value="CG">Congo</option>
						<option value="CD">Congo, Democratic Republic of the Congo</option>
						<option value="CK">Cook Islands</option>
						<option value="CR">Costa Rica</option>
						<option value="CI">Cote D'Ivoire</option>
						<option value="HR">Croatia</option>
						<option value="CU">Cuba</option>
						<option value="CW">Curacao</option>
						<option value="CY">Cyprus</option>
						<option value="CZ">Czech Republic</option>
						<option value="DK">Denmark</option>
						<option value="DJ">Djibouti</option>
						<option value="DM">Dominica</option>
						<option value="DO">Dominican Republic</option>
						<option value="EC">Ecuador</option>
						<option value="EG">Egypt</option>
						<option value="SV">El Salvador</option>
						<option value="GQ">Equatorial Guinea</option>
						<option value="ER">Eritrea</option>
						<option value="EE">Estonia</option>
						<option value="ET">Ethiopia</option>
						<option value="FK">Falkland Islands (Malvinas)</option>
						<option value="FO">Faroe Islands</option>
						<option value="FJ">Fiji</option>
						<option value="FI">Finland</option>
						<option value="FR">France</option>
						<option value="GF">French Guiana</option>
						<option value="PF">French Polynesia</option>
						<option value="TF">French Southern Territories</option>
						<option value="GA">Gabon</option>
						<option value="GM">Gambia</option>
						<option value="GE">Georgia</option>
						<option value="DE">Germany</option>
						<option value="GH">Ghana</option>
						<option value="GI">Gibraltar</option>
						<option value="GR">Greece</option>
						<option value="GL">Greenland</option>
						<option value="GD">Grenada</option>
						<option value="GP">Guadeloupe</option>
						<option value="GU">Guam</option>
						<option value="GT">Guatemala</option>
						<option value="GG">Guernsey</option>
						<option value="GN">Guinea</option>
						<option value="GW">Guinea-Bissau</option>
						<option value="GY">Guyana</option>
						<option value="HT">Haiti</option>
						<option value="HM">Heard Island and Mcdonald Islands</option>
						<option value="VA">Holy See (Vatican City State)</option>
						<option value="HN">Honduras</option>
						<option value="HK">Hong Kong</option>
						<option value="HU">Hungary</option>
						<option value="IS">Iceland</option>
						<option value="IN">India</option>
						<option value="ID">Indonesia</option>
						<option value="IR">Iran, Islamic Republic of</option>
						<option value="IQ">Iraq</option>
						<option value="IE">Ireland</option>
						<option value="IM">Isle of Man</option>
						<option value="IL">Israel</option>
						<option value="IT">Italy</option>
						<option value="JM">Jamaica</option>
						<option value="JP">Japan</option>
						<option value="JE">Jersey</option>
						<option value="JO">Jordan</option>
						<option value="KZ">Kazakhstan</option>
						<option value="KE">Kenya</option>
						<option value="KI">Kiribati</option>
						<option value="KP">Korea, Democratic People's Republic of</option>
						<option value="KR">Korea, Republic of</option>
						<option value="XK">Kosovo</option>
						<option value="KW">Kuwait</option>
						<option value="KG">Kyrgyzstan</option>
						<option value="LA">Lao People's Democratic Republic</option>
						<option value="LV">Latvia</option>
						<option value="LB">Lebanon</option>
						<option value="LS">Lesotho</option>
						<option value="LR">Liberia</option>
						<option value="LY">Libyan Arab Jamahiriya</option>
						<option value="LI">Liechtenstein</option>
						<option value="LT">Lithuania</option>
						<option value="LU">Luxembourg</option>
						<option value="MO">Macao</option>
						<option value="MK">Macedonia, the Former Yugoslav Republic of</option>
						<option value="MG">Madagascar</option>
						<option value="MW">Malawi</option>
						<option value="MY">Malaysia</option>
						<option value="MV">Maldives</option>
						<option value="ML">Mali</option>
						<option value="MT">Malta</option>
						<option value="MH">Marshall Islands</option>
						<option value="MQ">Martinique</option>
						<option value="MR">Mauritania</option>
						<option value="MU">Mauritius</option>
						<option value="YT">Mayotte</option>
						<option value="MX">Mexico</option>
						<option value="FM">Micronesia, Federated States of</option>
						<option value="MD">Moldova, Republic of</option>
						<option value="MC">Monaco</option>
						<option value="MN">Mongolia</option>
						<option value="ME">Montenegro</option>
						<option value="MS">Montserrat</option>
						<option value="MA">Morocco</option>
						<option value="MZ">Mozambique</option>
						<option value="MM">Myanmar</option>
						<option value="NA">Namibia</option>
						<option value="NR">Nauru</option>
						<option value="NP">Nepal</option>
						<option value="NL">Netherlands</option>
						<option value="AN">Netherlands Antilles</option>
						<option value="NC">New Caledonia</option>
						<option value="NZ">New Zealand</option>
						<option value="NI">Nicaragua</option>
						<option value="NE">Niger</option>
						<option value="NG">Nigeria</option>
						<option value="NU">Niue</option>
						<option value="NF">Norfolk Island</option>
						<option value="MP">Northern Mariana Islands</option>
						<option value="NO">Norway</option>
						<option value="OM">Oman</option>
						<option value="PK">Pakistan</option>
						<option value="PW">Palau</option>
						<option value="PS">Palestinian Territory, Occupied</option>
						<option value="PA">Panama</option>
						<option value="PG">Papua New Guinea</option>
						<option value="PY">Paraguay</option>
						<option value="PE">Peru</option>
						<option value="PH">Philippines</option>
						<option value="PN">Pitcairn</option>
						<option value="PL">Poland</option>
						<option value="PT">Portugal</option>
						<option value="PR">Puerto Rico</option>
						<option value="QA">Qatar</option>
						<option value="RE">Reunion</option>
						<option value="RO">Romania</option>
						<option value="RU">Russian Federation</option>
						<option value="RW">Rwanda</option>
						<option value="BL">Saint Barthelemy</option>
						<option value="SH">Saint Helena</option>
						<option value="KN">Saint Kitts and Nevis</option>
						<option value="LC">Saint Lucia</option>
						<option value="MF">Saint Martin</option>
						<option value="PM">Saint Pierre and Miquelon</option>
						<option value="VC">Saint Vincent and the Grenadines</option>
						<option value="WS">Samoa</option>
						<option value="SM">San Marino</option>
						<option value="ST">Sao Tome and Principe</option>
						<option value="SA">Saudi Arabia</option>
						<option value="SN">Senegal</option>
						<option value="RS">Serbia</option>
						<option value="CS">Serbia and Montenegro</option>
						<option value="SC">Seychelles</option>
						<option value="SL">Sierra Leone</option>
						<option value="SG">Singapore</option>
						<option value="SX">Sint Maarten</option>
						<option value="SK">Slovakia</option>
						<option value="SI">Slovenia</option>
						<option value="SB">Solomon Islands</option>
						<option value="SO">Somalia</option>
						<option value="ZA">South Africa</option>
						<option value="GS">South Georgia and the South Sandwich Islands</option>
						<option value="SS">South Sudan</option>
						<option value="ES">Spain</option>
						<option value="LK">Sri Lanka</option>
						<option value="SD">Sudan</option>
						<option value="SR">Suriname</option>
						<option value="SJ">Svalbard and Jan Mayen</option>
						<option value="SZ">Swaziland</option>
						<option value="SE">Sweden</option>
						<option value="CH">Switzerland</option>
						<option value="SY">Syrian Arab Republic</option>
						<option value="TW">Taiwan, Province of China</option>
						<option value="TJ">Tajikistan</option>
						<option value="TZ">Tanzania, United Republic of</option>
						<option value="TH">Thailand</option>
						<option value="TL">Timor-Leste</option>
						<option value="TG">Togo</option>
						<option value="TK">Tokelau</option>
						<option value="TO">Tonga</option>
						<option value="TT">Trinidad and Tobago</option>
						<option value="TN">Tunisia</option>
						<option value="TR">Turkey</option>
						<option value="TM">Turkmenistan</option>
						<option value="TC">Turks and Caicos Islands</option>
						<option value="TV">Tuvalu</option>
						<option value="UG">Uganda</option>
						<option value="UA">Ukraine</option>
						<option value="AE">United Arab Emirates</option>
						<option value="GB">United Kingdom</option>
						<option value="US">United States</option>
						<option value="UM">United States Minor Outlying Islands</option>
						<option value="UY">Uruguay</option>
						<option value="UZ">Uzbekistan</option>
						<option value="VU">Vanuatu</option>
						<option value="VE">Venezuela</option>
						<option value="VN">Viet Nam</option>
						<option value="VG">Virgin Islands, British</option>
						<option value="VI">Virgin Islands, U.s.</option>
						<option value="WF">Wallis and Futuna</option>
						<option value="EH">Western Sahara</option>
						<option value="YE">Yemen</option>
						<option value="ZM">Zambia</option>
						<option value="ZW">Zimbabwe</option>
						</select>
					
					</div>

					<div class="form-group">
					<label for="customer_credit_card_number">PAN</label>
					<input required name="customer_credit_card_number" id="customer_credit_card_number" data-cardinal-field="AccountNumber" type="text" class="form-control"  
					autocomplete="off" maxlength="20" pattern="\d{16}"
					title="Credit card number" >
					</div>

					<div class="form-group row">
					<label class="col-md-12">Card Exp. Date</label>
						<div class="col-md-4">
							<select  class="form-control" name="cc_expiration_month" id="cc_expiration_month" size="0">
								<option value="01">01</option>
								<option value="02">02</option>
								<option value="03">03</option>
								<option value="04">04</option>
								<option value="05">05</option>
								<option value="06">06</option>
								<option value="07">07</option>
								<option value="08">08</option>
								<option value="09">09</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
							</select>
						</div>
						<div class="col-md-4">
							<select class="form-control"  name="cc_expiration_year" id="cc_expiration_year" size="0">
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
								<option value="2021">2021</option>
								<option value="2022">2022</option>
								<option value="2023">2023</option>
								<option value="2024" selected>2024</option>
								<option value="2025">2025</option>
								<option value="2026">2026</option>
								<option value="2027">2027</option>
								<option value="2028">2028</option>
								<option value="2029">2029</option>
								<option value="2030">2030</option>
								<option value="2012">2031</option>
								<option value="2013">2032</option>
								<option value="2014">2033</option>
								<option value="2015">2034</option>
								<option value="2016">2035</option>
							</select>
						</div>
						<div class="col-md-4">
						<input type="text" name="cc_cvv2_number" id="cc_cvv2_number" class="form-control" 
						autocomplete="off" maxlength="3" pattern="\d{3}" title="Three digits at back of your card" 
						required placeholder="CVC">
						</div>
					</div>

					<div class="row">
					<label class="col-md-12">Amount</label>
					</div>

					<div class="form-inline">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text">$</span></div>
							<input type="text" class="form-control text-right" id="exampleInputAmount" placeholder="39" value='<?php echo $decAmount2;?>' disabled>
							<div class="input-group-append"><span class="input-group-text">.00</span></div>
						</div>
					</div>
					<hr>
					<hr>
					<div class="form-group row">
					<div class="col-md-6">
					<button type="reset" class="btn btn-default btn-lg btn-block">Cancel</button>
					</div>
					<div class="col-md-6">
					<button type="button" name="myButton" id="myButton"  value="Payment"  class="btn btn-success btn-lg btn-block" onclick="psd2Payment();">Submit</button>
					</div>
					</div>
				</form>
			</div>
		</div>
	</div> 
<div id="loader"></div>	
</body>
<script src="http://code.jquery.com/jquery.js"></script>
 
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>
</script>

    <script>   

    function psd2Payment() {
		
		
		//Loading Spinner for transaction to complete
		var spinner = $('#loader');


//Validate Text Fields
	//BIN
	var bin = document.getElementById('customer_credit_card_number').value;
	if (bin == "") {
		alert("ALL fields are compulsory");
		return false;
	}
	//First Name
	var fname = document.getElementById('firstname').value;
	if (fname == "") {
		alert("ALL fields are compulsory");
		return false;
	}
	//Last Name
	var lname = document.getElementById('lastname').value;
	if (lname == "") {
		alert("ALL fields are compulsory");
		return false;
	}
	//Adress
	var address1 = document.getElementById('address1').value;
	if (address1 == "") {
		alert("ALL fields are compulsory");
		return false;
	}
	//email
	var email = document.getElementById('email').value;
	if (email == "") {
		alert("ALL fields are compulsory");
		return false;
	}
	//city
	var city = document.getElementById('city').value;
	if (city == "") {
		alert("ALL fields are compulsory");
		return false;
	}
	//postalcode
	var postalcode = document.getElementById('postalcode').value;
	if (postalcode == "") {
		alert("ALL fields are compulsory");
		return false;
	}

	//phonenumber
	var phonenumber = document.getElementById('phonenumber').value;
	if (phonenumber == "") {
		alert("ALL fields are compulsory");
		return false;
	}
	//cvv
	var cc_cvv2_number = document.getElementById('cc_cvv2_number').value;
	if (cc_cvv2_number == "") {
		alert("ALL fields are compulsory");
		return false;
	}
	
	//country
	var country = document.getElementById('country').value;
	if (country == "") {
		alert("ALL fields are compulsory");
		return false;
	}

	//JWT
	var jwt = document.getElementById("JWTContainer").value;

	spinner.show();
	//Start Cardinal
	Cardinal.configure({
		logging: {
			level: "verbose"
		},
		timeout: 8000
	});
	Cardinal.setup("init", {
		jwt: jwt
	});

	Cardinal.on("payments.setupComplete", function () {
		console.log('%cConsumer Authentication Setup Complete.', 'color:green; background-color:LightGreen;');
	});
	var order = {
		OrderDetails: {
			Amount: "<?php echo $decAmount; ?>",
			CurrencyCode: "<?php echo $currency; ?>",
			OrderNumber: "<?php echo $OrderNumber; ?>",
			OrderDescription: "Input Your Purpose",
			OrderChannel: "S"
		},
		Consumer: {
			Account: {
				AccountNumber: bin,
				CardCode: cc_cvv2_number,
				ExpirationMonth: document.getElementById('cc_expiration_month').value,
				ExpirationYear: document.getElementById('cc_expiration_year').value,
				NameOnAccount: fname + ' ' + lname
			},
			Email1: email,
			BillingAddress: {
				FullName: fname + ' ' + lname,
				FirstName: fname,
				LastName: lname,
				Address1: address1,
				City: city,
				State: city,
				PostalCode: postalcode,
				CountryCode: country,
				Phone1: phonenumber
			},
			ShippingAddress: {
				FullName: fname + ' ' + lname,
				FirstName: fname,
				LastName: lname,
				Address1: address1,
				City: city,
				State: city,
				PostalCode: postalcode,
				CountryCode: country,
				Phone1: phonenumber
			}
		}
	}
	console.warn(order);
	Cardinal.trigger("bin.process", bin).then(function (results) {
		if (results.Status) {
			console.log('%cBin Profiling Complete.', 'color:green; background-color:LightGreen;');
			Cardinal.start("cca", order);
		} else {
			console.warn('BIN Profiling failed.  Continuing without Device Data.');
			Cardinal.start("cca", order);
		}
	}).catch(function (error) {
		console.warn('An error occurred during BIN Profiling.');
		      spinner.hide();
			alert('An error occurred during BIN Profiling.');
	});

	Cardinal.on('payments.validated', function(decodedResponseData, responseJWT) {

  switch (decodedResponseData.ErrorNumber) {


    case 0:
      console.log("dataxxxxxxxxxxxxxx :" + JSON.stringify(decodedResponseData));

							 
		var fName = document.getElementById('firstname').value;
		var lName = document.getElementById('lastname').value;
		var cardname = fName.concat(lName);
		
		//Request URL
		var url_request = '';
		//Request To rest API
      var datarequest = {
        transactionid: "<?php echo $OrderNumber; ?>",
        processortransactionid: decodedResponseData.Payment.ProcessorTransactionId,
        cardnumber: document.getElementById('customer_credit_card_number').value,
        amount: "<?php echo $decAmount2; ?>",
        currency: "KES",
		retries:"<?php echo $retries; ?>",
        requestEmail: document.getElementById('email').value,
        expMonth: document.getElementById('cc_expiration_month').value,
        expYear: document.getElementById('cc_expiration_year').value,
		cardname: cardname,
        fName: document.getElementById('firstname').value,
        lName: document.getElementById('lastname').value,
        addressCustomer: document.getElementById('address1').value,
        postCode: document.getElementById('postalcode').value,
		city: document.getElementById('city').value,
        countryCode: country,
        phonenumber: document.getElementById('phonenumber').value
      };
      var jsonrequest = JSON.stringify(datarequest);
       jQuery.ajax({
        type: "POST",
        url: url_request,
        dataType: "json",
        contentType: "application/json",
        data: jsonrequest,
        success: function(response) {
          console.log(response);
		  spinner.hide();
        }
      });

      break;
    case 1:
	spinner.hide();
      alert('TRANSACTION FAILED');
      break;
    case 2:
	spinner.hide();
      alert('TRANSACTION FAILED');
      break;
    case 3:
	spinner.hide();
      alert('TRANSACTION FAILED:' + data.ErrorDescription);
      break;
    default:
	spinner.hide();
      alert('TRANSACTION FAILED:');
      break;

  }

});
}

    </script>
</html>