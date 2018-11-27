<?php
$recaptchaOK = true;
/* if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
    // site secret key
    $secret = '6Lcp4yIUAAAAAH8s9bgLhMsA_YcxUcf4nTZVJPwT';
    //get verify response data
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
    $responseData = json_decode($verifyResponse);
    if($responseData->success) {
        $recaptchaOK = true;
    }
} */



$isNew = false;

$errLabel = '<span class="formError">PLEASE FILL IN THIS FIELD</span>';
$errLabelPhone = '<span class="formError">PLEASE ENTER ONLY NUMBERS/SPACES</span>';
$errLabelEmail = '<span class="formError">PLEASE ENTER A VALID EMAIL ADDRESS</span>';
$fields = array("first_name","last_name","phone","email","postcode");

$api_root = "https://finestdental.co.uk/access/api/";
$api_url = $api_root.'save_lead/';


if($_POST['address']!=''||$_GET['address']!='') {
	print "<h1><a href='http://finestdental.co.uk'>Finest Dental</a></h1>";
	print "<h2>Spambot Submission Detected</h2>";
	print "<p>Our anti-spam system has identified your form submission as suspicious.</p><p>If this is in error, please accept our apologies and call us on <a href='tel:08082739355'>0808 273 9355</a> to arrange a consultation.";
	exit();
}



if($_GET['redo']=='y') {
	// sent back from consultation page, transfer any passed form fields from GET to POST
	foreach($_GET as $key=>$value)
		$_POST["$key"]=$value;
}


foreach($fields as $field) {
	if(trim($_POST["$field"])=='') {
		$errs[] = $field;
	}	else {
		// field-specific validation
		if(isset($_POST['phone'])&&$field=='phone')  {
			if(!preg_match("/^[0-9\s]+$/D",$_POST['phone']))
				$errs[] = $field;
		} else if ($field=='email') {
			if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$errs[] = 'email';
			}
		}
	}
}




if(is_numeric($_POST['status']))
	$myStatus = $_POST['status'];
else {
	$myStatus = 2;
}
$trackingFields = explode(',','SID,LPID,TID,CID,KID,ASID,AID');

if(isset($_POST['email'])&&!isset($errs)&&$recaptchaOK) {
			
	// preprocess phone
	if(substr($_POST['phone'],0,3)=='440') {
		$_POST['phone'] = substr($_POST['phone'],2); // remove extraneous country code
	} else if(substr($_POST['phone'],0,2)=='44') {
		$_POST['phone'] = '0'.substr($_POST['phone'],2); // replace country code with leading 0
	} else if (substr($_POST['phone'],0,1)!='0') {
		$_POST['phone'] = '0'.$_POST['phone']; // add missing leading 0
	}
	
	$sourceID = intval($_POST['SID']);
	if($sourceID==0) {
		$sourceID = intval($_POST['source']);
	}
	if($sourceID==0) {
		// set to Not Set code
		$sourceID = null;
	}
	
	
	
	$array = array(
		'account_number' => '12341',
		'api_key' => '237be778d6a246080c3bf88f50d2ced3',
		'name' => trim($_POST['first_name']).' '.trim($_POST['last_name']),
	    'first_name' => trim($_POST['first_name']),
	    'last_name' => trim($_POST['last_name']),
	    'postcode' => trim($_POST['postcode']),
	    'phone' => trim($_POST['phone']),
	    'email' => trim($_POST['email']),
	    'treatment' => trim($_POST['treatment']),
	    'company' => trim($_POST['company']),
	    'note' => trim($_POST['note']),
	    'product' => trim($_POST['product']),
	    'sourceForm' => trim($_POST['sourceForm']),
	    'CID' => trim($_POST['CID']),
	    'KID' => trim($_POST['KID']),
	    'LPID' => trim($_POST['LPID']),
	    'TID' => trim($_POST['TID']),
	    'SID' => $sourceID,
	    'ASID' => trim($_POST['ASID']),
	    'AID' => trim($_POST['AID']),
	    'referer' => $_SERVER['HTTP_REFERER'],
	    'status' => $myStatus,
	    'age' => trim($_POST['age']),
	    'how_long_have_teeth_been_missing' => trim($_POST['how_long_have_teeth_been_missing']),
	    'how_many_teeth_missing' => trim($_POST['how_many_teeth_missing'])
 	);		

	/* Add to CSV file */
	
	$arrayCSV = array(
		'source' => trim($_POST['sourceForm']),
		'name' => trim($_POST['first_name']).' '.trim($_POST['last_name']),
		'phone' => trim($_POST['phone']),
		'postcode' => trim($_POST['postcode']),
		'date' => date("Y/m/d h:i:sa"),
		'refer' => trim($_SERVER['HTTP_REFERER'])
	);
	if(strstr($_POST['sourceForm'],"Dental Implants Old Landing Page 4"))
		$csvFile = "oldleads.csv";
	else
		$csvFile = "leads.csv";
	$fp = fopen('newsite/ppc/leads/'.$csvFile, 'a');
	fputcsv($fp, $arrayCSV);
	fclose($fp);
	
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_URL, $api_url);
	
	curl_setopt($curl, CURLOPT_VERBOSE, true);

	$fp = fopen(dirname(__FILE__).'/errorlog.txt', 'w');
	curl_setopt($curl, CURLOPT_STDERR, $fp);

	
	if(is_array($array)){
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $array);
	}
	
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	
	$result = json_decode(curl_exec($curl),true);
	// $result['status'] = 'OK';
	// $result['code'] = 200;
	
	if($result['status']=='OK'&&$result['code']==200) {
		
		$_POST['timestamp'] = time();
		
		// okay, redirect to form page
		$url = "http://finestdental.co.uk/newsite/ppc/confirmation/";
		if($_POST['sourceForm']=="New Lead Entry Form")
			$url .= "nle.php";
		
		// old-fashioned confirmation page for old landing page submissions
		if(strstr($_POST['sourceForm'],"Dental Implants Old Landing Page 4"))
			$url = "http://finestdental.co.uk/confirmation/";
		
		
		$url .= "?";
		// pass all post variables as get parameters
		foreach($_POST as $key=>$value) {
			$url .= "$key=".urlencode($value)."&";
		}
		
		
		// report: log environment and form variables
		$log = "RESULT: $result[status]\nCODE: $result[code]\n\n";
		$log .= "POST:\n";
		foreach($_POST as $key=>$value) {
			$log .= "$key: $value\n";
		}
		$log .= "\nGET:\n";
		foreach($_GET as $key=>$value) {
			$log .= "$key: $value\n";
		}
		$log .= "\nSERVER:\n";
		foreach($_SERVER as $key=>$value) {
			$log .= "$key: $value\n";
		}				
		$dummy = mail("sales@finestdental.co.uk","Finest Dental CRM: new lead added",$log);
		// $frontMsg = "An error has been detected, and reported to our technical team. While it's being resolved, please call our <strong>phone number</strong> instead.";
		
		header( "Location: $url" ) ;
		exit();
	} else {
		// problem; log environment and form variables
		$log = "RESULT: $result[status]\nCODE: $result[code]\n\n";
		$log .= "POST:\n";
		foreach($_POST as $key=>$value) {
			$log .= "$key: $value\n";
		}
		$log .= "\nGET:\n";
		foreach($_GET as $key=>$value) {
			$log .= "$key: $value\n";
		}
		$log .= "\nSERVER:\n";
		foreach($_SERVER as $key=>$value) {
			$log .= "$key: $value\n";
		}				
		$dummy = mail("jamie@finestdental.co.uk,samhealy@yahoo.com","Finest Dental CRM error",$log);
		$frontMsg = "An error has been detected, and reported to our technical team. While it's being resolved, please call our <strong>phone number</strong> instead.";
	}
	curl_close($curl);
}

?>

<html>
	<head>
		<title>Form Submission - Finest Dental</title>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1.0, user-scalable=0">
	    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel="stylesheet">	 		
		<link href='css/stylesheet.css?r=n' rel='stylesheet' type='text/css'>
		<!--[if IE]>
   			<link href='css/stylesheetIE.css' rel='stylesheet' type='text/css'>
		<!--[endif]-->
		
		<!-- <link href='css/msc-style.css' rel='stylesheet' type='text/css'> -->
		
		
	</head>
	<body class="fixedheader site nonproduct text interstitialForm">

		
						
		
		<div id="foldx">
			<div class="row" id="header">
				<div class="container">
					
					<div class="column" id="logo">
						<a href="http://finestdental.co.uk/" title="Back to Finest Dental homepage"><img src="images/fd_logo.png" alt="Finest Dental logo image" /></a>
					</div>
					<ul id="topnav">
<li ><a href="/treatments.html">Treatments</a><span></span></li>
<li ><a href="/about.html">About</a><span></span></li>
<li ><a href="/membership.html">Membership</a><span></span></li>
<li ><a href="/prices.html">Prices</a><span></span></li>
<li ><a href="/locations.html">Locations</a><span></span></li>
<li  class="last"><a href="/contact.html">Contact</a><span></span></li>

	<li class="mob"><a href="#reviews">Reviews</a></li>
	<li class="mob"><a href="#testimonialGallery">Testimonials</a></li>
</ul>
					
					
					<div id="phone"><i class="lnr lnr-phone-handset"></i><a class="rTapNumber236260" href="tel:08082784122">0808 <span>2784122</span></a></div>
				</div>
			</div>
			<div id="pageBreadcrumbs">
				<div class="container">
					<h3><a href="http://finestdental.co.uk/" class="lnr lnr-home"></a> &gt; Complete Form</h3>
				</div>
			</div>
			
		</div>
		<div class="row" id="content">
			<div class="container">
				
					<h2 style="text-align:center;padding-top:10px;padding-bottom:30px">Book Your <span>Free</span> Consultation</h2>
					<? if ($frontMsg) print "<h3 class='formFrontMsg'>$frontMsg</h3>"; ?>
					<form action="/consultation.php" method="POST">
						<input type=hidden name="status" value="<? if(isset($_POST['status'])) print $_POST['status']; else print ""; ?>">		
						<input type=hidden name="company" value="1">		

						<?php foreach($trackingFields as $trackingField) { ?>

							<input type="hidden" name="<?php echo $trackingField; ?>" value="<?php if($trackingField=='SID') echo $sourceID; else echo $_POST["$trackingField"]; ?>" />
 						<? } ?>

						<input type=hidden name="sourceForm" value="<? if(isset($_POST['sourceForm'])) print $_POST['sourceForm']; else print "Direct submission from API submitter form"; ?>">
						<input type=hidden name="product" value="<? if(isset($_POST['product'])) print $_POST['product']; else print ""; ?>">
						<input type=hidden name="treatment" value="<? if(isset($_POST['treatment'])) print $_POST['treatment']; else print "0"; ?>">
						<div class="formRow">
							<? if(in_array('first_name',$errs)) print $errLabel; ?>
							<input type="text" name="first_name" id="first_name" value="<? print $_POST['first_name']; ?>" placeholder="FIRST NAME" required />
						</div>
						<div class="formRow">
							<? if(in_array('last_name',$errs)) print $errLabel; ?>
							<input type="text" name="last_name" id="last_name" value="<? print $_POST['last_name']; ?>" placeholder="SECOND NAME" required />
						</div>
						<div class="formRow">
							<? if(in_array('phone',$errs)) print $errLabelPhone; ?>
							<input type="text" name="phone" placeholder="PHONE" value="<? print $_POST['phone']; ?>" required />
						</div>	
						<div class="formRow">
							<? if(in_array('email',$errs)) print $errLabelEmail; ?>
							<input type="email" name="email" placeholder="EMAIL" value="<? print $_POST['email']; ?>" required />
						</div>
						<div class="formRow">
							<? if(in_array('postcode',$errs)) print $errLabel; ?>
							<input type="text" name="postcode" placeholder="POSTCODE" value="<? print $_POST['postcode']; ?>" required />
							
						</div>	
						<? if (isset($_POST['note'])) { ?>	
						
						<div class="formRow">
							<? if(in_array('note',$errs)) print $errLabel; ?>
							<textarea name="note" placeholder="NOTES"><? print $_POST['note']; ?></textarea>
							
						</div>	
						<div class="formRow required">
							<input type="text" name="address" placeholder="REQUIRED ADDRESS" value="" />			
						</div>	
						<? } ?>
						<div class="formRow">
							<input type="submit" class="submit" name="submitF" value="BOOK NOW" />
						</div>	
						
						
						
						<?php if(!$recaptchaOK) { ?>
							<span class="formError">PLEASE COMPLETE THE RECAPTCHA TEST BELOW</span>
						<?php } ?>				
					</form>					
				</div>
	  		</div>
		</div>
				
			</div>
		</div>
		
		
		<div class="row" id="widget">
			<div class="container">
				<a name="reviews"></a>
				<h2>Read The <span>Reviews</span></h2>
				
			<script src="https://widget.reviews.co.uk/carousel/dist.js"></script>
<div id="carousel-widget-360" style="width:100%;margin:0 auto;"></div>
<script>
carouselWidget('carousel-widget-360',{
  store: 'finest-dental',
  primaryClr: '#1d8fd5',
  neutralClr: '#f4f4f4',
  reviewTextClr: '#494949',
  layout:'fullWidth',
  numReviews: 21
});
</script>
				
			</div>
		</div>
		
		<div class="row" id="footer">
			<div class="container">
				<div class="column" id="footerCol1">
					<ul><li><a href="/site-pages/terms-and-conditions.html" title="Terms and Conditions" >Terms and Conditions</a></li>
<li><a href="/site-pages/privacy-policy.html" title="Privacy Policy" >Privacy Policy</a></li>
<li class="last"><a href="/site-pages/patient-complaints-procedure.html" title="Patient Complaints Procedure" >Patient Complaints Procedure</a></li>
</ul>
					
				</div>
				<div class="column" id="footerCol2">
					<h3>Call Us</h3>
					<h2><a class="rTapNumber236260" href="tel:08082784122">0808 <span>2784122</span></a></h2>
					
					<div class="row">&copy; 2016 Finest Dental Ltd. All rights reserved.</div>
				</div>
				<div class="column" id="footerCol3">
					
					<ul>
						<li><strong>CLINIC LOCATIONS</strong></li>
						<li><a href="locations/central-london.html">Central London</a></li><li><a href="locations/barkingside.html">Barkingside</a></li><li><a href="locations/winchester.html">Winchester</a></li><li><a href="locations/wokingham.html">Wokingham</a></li><li><a href="locations/milton-keynes.html">Milton Keynes</a></li><li><a href="locations/leicester.html">Leicester</a></li>
					</ul>
				</div>
			</div>
		</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>		

<script type="text/javascript">
	
	function grcCallback() {
		$("form[action='/consultation.php']").each(function(){
			var grDiv = $('<div class="grDiv"></div>');
			$(this).append(grDiv);
			var grDivInPlace = $(this).find('.grDiv')[0];
			var myGR = grecaptcha.render(grDivInPlace, {
				'sitekey':'6Lcp4yIUAAAAAEjncuAFjF9hqmYIpE2Tt584Ts5l'
				
			});
		});
	}
	
	
</script>	

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1477526785873521'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1477526785873521&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->


<!-- <script src="https://www.google.com/recaptcha/api.js?onload=grcCallback&render=explicit"
		async defer></script> -->

	</body>
</html>