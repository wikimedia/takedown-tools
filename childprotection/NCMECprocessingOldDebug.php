<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2014-01-06

NCMEC reporting form for Child Protection takedowns

---------------------------------------------   */

require_once dirname( __FILE__ ) . '/../include/multiuseFunctions.php';
date_default_timezone_set( 'UTC' );

// cast config and log variables
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$user = $_SERVER['PHP_AUTH_USER'];
$istest = $_POST['is-test'];

if ( $istest === 'N' ) {
	$NCMECurl = $config['NCMEC_URL_Production'];
	$ncusername = $config['NCMEC_user_prod'];
	$ncpassword = $config['NCMEC_password_prod'];
} else {
	$NCMECurl = $config['NCMEC_URL_Test'];
	$ncusername = $config['NCMEC_user_test'];
	$ncpassword = $config['NCMEC_password_test'];
}

$accessdate = $_POST['access-date'];
$accesshour = $_POST['access-time-hour'];
$accessmin = $_POST['access-time-min'];

$accessdatetime = $accessdate.'T'.$accesshour.':'.$accessmin.':00Z';

$reporterfname = $_POST['reporter-fName'];
$reporterlname = $_POST['reporter-lName'];
$reporteremail = $_POST['reporter-email'];

//REMOVED FOR NOW: Contact info being sent via contact person field
//$reporterphone = $_POST['reporter-phone'];
//$reporterext = $_POST['reporter-phone-ext'];

$incfilename = $_POST['file-name'];

// FIXME ASSUMPTION: setup url, for now just assuming commons (bad but works for now)
$incurl = 'https://commons.wikimedia.org/wiki/File:'.$incfilename;

$incproject = $_POST['project'];
$incprojectlang = $_POST['project-language'];
$incdate = $_POST['incident-date'];
$inchour = $_POST['incident-time-hour'];
$incmin = $_POST['incident-time-min'];

$incdatetime = $incdate.'T'.$inchour.':'.$incmin.':00Z';

$incloc = $_POST['incident-location'];
$uploaderusername = $_POST['uploader-username'];
$uploaderip = $_POST['uploader-ip'];
$uploaderemail = $_POST['uploader-email'];
$comments = $_POST['comments'];

$uploadedfilename = $_FILES['takedown-file1']['name'];
$uploadedfilesize = $_FILES['takedown-file1']['size'];
$uploadedfiletype = $_FILES['takedown-file1']['type'];
$uploadedfiletmploc = $_FILES['takedown-file1']['tmp_name'];


// Set up report open - Notes being placed above where I'm making assumptions for first rollout
$openReport = new DOMDocument();
$openReport->formatOutput = true;
$openReport->encoding='UTF-8';

$reportroot = $openReport->createElement( 'report' );
$openReport->appendChild( $reportroot );

$incidentSummary = $openReport->createElement( 'incidentSummary' );
$reportroot->appendChild( $incidentSummary );

$incidentType = $openReport->createElement( 'incidentType' );
//FIXME ASSUMPTION, not even asking yet
$incidentTypeText = $openReport->createTextNode( 'Child Pornography (possession, manufacture, and distribution)' );
$incidentType->appendChild( $incidentTypeText );

$incidentDateTime = $openReport->createElement( 'incidentDateTime', $incdatetime );

$incidentSummary->appendChild( $incidentType );
$incidentSummary->appendChild( $incidentDateTime );

$internetDetails = $openReport->createElement( 'internetDetails' );
$reportroot->appendChild( $internetDetails );

//FIXME ASSUMPTION, asked but not checking
$webPageIncident = $openReport->createElement( 'webPageIncident' );
$internetDetails->appendChild( $webPageIncident );

$webPageURL = $openReport->createElement( 'url' );
$webPageURLvalue = $openReport->createTextNode( $incurl );
$webPageURL->appendChild( $webPageURLvalue );
$webPageIncident->appendChild( $webPageURL );

$reporter = $openReport->createElement( 'reporter' );
$reportroot->appendChild( $reporter );

$reportingPerson = $openReport->createElement( 'reportingPerson' );
$reporter->appendChild( $reportingPerson );

// set up reporting person elements
$reporterfirstname = $openReport->createElement( 'firstName' );
$reporterfirstnametext = $openReport->createTextNode( $reporterfname );
$reporterfirstname->appendChild( $reporterfirstnametext );

$reporterlastname = $openReport->createElement( 'lastName' );
$reporterlastnametext = $openReport->createTextNode( $reporterlname );
$reporterlastname->appendChild( $reporterlastnametext );

$reporteremailpost = $openReport->createElement( 'email' );
$reporteremailtext = $openReport->createTextNode( $reporteremail );
$reporteremailpost->appendChild( $reporteremailtext );

$reporteraddress = $openReport->createElement( 'address' );
$reporteraddress->setAttribute( 'type', 'Business' );

//set up reporter address elements
$reportersaddress = $openReport->createElement( 'address' );
$reportersaddresstext = $openReport->createTextNode( $config['NCMEC_Contact_saddress'] );
$reportersaddress->appendChild( $reportersaddresstext );

$reportercity = $openReport->createElement( 'city' );
$reportercitytext = $openReport->createTextNode( $config['NCMEC_Contact_City'] );
$reportercity->appendChild( $reportercitytext );

$reporterstate = $openReport->createElement( 'state' );
$reporsterstatetext = $openReport->createTextNode( $config['NCMEC_Contact_State'] );
$reporterstate->appendChild( $reporsterstatetext );

$reportercountry = $openReport->createElement( 'country' );
$reportercountrytext = $openReport->createTextNode( $config['NCMEC_Contact_Country'] );
$reportercountry->appendChild( $reportercountrytext );

$reporterzip = $openReport->createElement( 'zipCode' );
$reporterziptext = $openReport->createTextNode( $config['NCMEC_Contact_Zip'] );
$reporterzip->appendChild( $reporterziptext );

// attach address elements to reporteraddress
$reporteraddress->appendChild( $reportersaddress );
$reporteraddress->appendChild( $reportercity );
$reporteraddress->appendChild( $reporterzip );
$reporteraddress->appendChild( $reporterstate );
$reporteraddress->appendChild( $reportercountry );

// attach reporting person elements
$reportingPerson->appendChild( $reporterfirstname );
$reportingPerson->appendChild( $reporterlastname );
$reportingPerson->appendChild( $reporteremailpost );
$reportingPerson->appendChild( $reporteraddress );

$contact = $openReport->createElement( 'contactPerson' );
$reporter->appendChild( $contact );

//set up contact person elements (this looks familiar)
$contactfirstname = $openReport->createElement( 'firstName' );
$contactfirstnametext = $openReport->createTextNode( $config['NCMEC_Contact_fname'] );
$contactfirstname->appendChild( $contactfirstnametext );

$contactlastname = $openReport->createElement( 'lastName' );
$contactlastnametext = $openReport->createTextNode( $config['NCMEC_Contact_lname'] );
$contactlastname->appendChild( $contactlastnametext );

$contactemailpost = $openReport->createElement( 'email' );
$contactemailtext = $openReport->createTextNode( $config['NCMEC_Contact_Email'] );
$contactemailpost->appendChild( $contactemailtext );

$contactphone = $openReport->createElement( 'phone' );
$contactphone->setAttribute( 'type', 'Business' );
$contactphonetext = $openReport->createTextNode( $config['NCMEC_Contact_phone'] );
$contactphone->appendChild( $contactphonetext );

$contactaddress = $openReport->createElement( 'address' );
$contactaddress->setAttribute( 'type', 'Business' );

//set up reporter address elements
$contactsaddress = $openReport->createElement( 'address' );
$contactsaddresstext = $openReport->createTextNode( $config['NCMEC_Contact_saddress'] );
$contactsaddress->appendChild( $contactsaddresstext );

$contactcity = $openReport->createElement( 'city' );
$contactcitytext = $openReport->createTextNode( $config['NCMEC_Contact_City'] );
$contactcity->appendChild( $contactcitytext );

$contactstate = $openReport->createElement( 'state' );
$contactstatetext = $openReport->createTextNode( $config['NCMEC_Contact_State'] );
$contactstate->appendChild( $contactstatetext );

$contactcountry = $openReport->createElement( 'country' );
$contactcountrytext = $openReport->createTextNode( $config['NCMEC_Contact_Country'] );
$contactcountry->appendChild( $contactcountrytext );

$contactzip = $openReport->createElement( 'zipCode' );
$contactziptext = $openReport->createTextNode( $config['NCMEC_Contact_Zip'] );
$contactzip->appendChild( $contactziptext );

// attach address elements to reporteraddress
$contactaddress->appendChild( $contactsaddress );
$contactaddress->appendChild( $contactcity );
$contactaddress->appendChild( $contactzip );
$contactaddress->appendChild( $contactstate );
$contactaddress->appendChild( $contactcountry );

// attach reporting person elements
$contact->appendChild( $contactfirstname );
$contact->appendChild( $contactlastname );
$contact->appendChild( $contactphone );
$contact->appendChild( $contactemailpost );
$contact->appendChild( $contactaddress );


$incidentUser = $openReport->createElement( 'personOrUserReported' );
$reportroot->appendChild( $incidentUser );

if ( !empty( $uploaderemail ) ) {
	$usernameperson = $openReport->createElement( 'personOrUserReportedPerson' );
	$incidentUser->appendChild( $usernameperson );
	$usernameemail = $openReport->createElement( 'email' );
	$usernameemailtext = $openReport->createTextNode( $uploaderemail );
	$usernameemail->appendChild( $usernameemailtext );
	$usernameperson->appendChild( $usernameemail );
}

$username = $openReport->createElement( 'screenName' );
$usernametext = $openReport->createTextNode( $uploaderusername );
$username->appendChild( $usernametext );
$incidentUser->appendChild( $username );

if ( !empty( $uploaderip ) ) {
	$uploadipevent = $openReport->createElement( 'ipCaptureEvent' );
	$incidentUser->appendChild( $uploadipevent );

	$uploadactualip = $openReport->createElement( 'ipAddress' );
	$uploadactualiptext = $openReport->createTextNode( $uploaderip );
	$uploadactualip->appendChild( $uploadactualiptext );

	$uploadiptype = $openReport->createElement( 'eventName' );
	$uploadiptypetext = $openReport->createTextNode( 'Upload' );
	$uploadiptype->appendChild( $uploadiptypetext );

	$uploaddatetime = $openReport->createElement( 'dateTime' );
	$uploaddatetimetext = $openReport->createTextNode( $incdatetime );
	$uploaddatetime->appendChild( $uploaddatetimetext );

	$uploadipevent->appendChild( $uploadactualip );
	$uploadipevent->appendChild( $uploadiptype );
	$uploadipevent->appendChild( $uploaddatetime );
}

$details = $openReport->createElement( 'additionalInfo' );
$detailstext = $openReport->createTextNode( $comments );
$details->appendChild( $detailstext );
$incidentUser->appendChild( $details );

$Report = $openReport->saveXML();
$xmlHeader = array (
	'Content-Type: text/xml; charset=utf-8',
	'Accept: text/xml',
);
$openurl = $NCMECurl.'submit';
$fileurl = $NCMECurl.'upload';
$fileinfourl = $NCMECurl.'fileinfo';
$finishurl = $NCMECurl.'finish';
$retracturl = $NCMECurl.'retract';

//null variables
$responseXML = null;
$result = null;
$filedetaildom = null;
$filehash = null;

?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>NCMEC Submission</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='/scripts/jquery-1.10.2.min.js'></script>
	<script src='/scripts/lca.js'></script>
	<style type='text/css'>
	<!--/* <![CDATA[ */
	@import '/css/main.css';
	@import '/css/lca.css';
	/* ]]> */-->
	.external, .external:visited { color: #222222; }
	.autocomment{color:gray}
	</style>
</head>
<body class='mediawiki'>
	<div id='globalWrapper'>
		<div id='column-content'>
			<div id='content'>
				<h1>Processed Submission</h1>
				<fieldset>
					<legend> Temp debugging: Array of submitted data </legend>
					<?php
echo '<pre>';
echo print_r( $_POST );
echo '</pre>';
echo '<pre>';
echo print_r( $_FILES );
echo '</pre>'; ?>
				</fieldset>
				<fieldset>
					<legend> Initial Data displayed and verified, errors from the same.</legend>
					<textarea name='reportxml' wrap='virtual' rows='18' cols='70'><?php
echo $openReport->saveXML();
?></textarea>
					<textarea name='reportxmlvalidate' wrap='virtual' rows='18' cols='70'><?php
if ( !$openReport->schemaValidate( 'espsubmittal.xsd' ) ) {
	echo '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
	libxml_display_errors(); } else { echo "There are no validation errors and the XML above matches the schema provided by NCMEC"; $openReportValid = true;}?></textarea>
				</fieldset>
				<fieldset>
					<legend>Submit initial data and report back with response </legend>
					<?php
if ( $openReportValid ) {
	$result = curlauthdAPIpost( $ncusername, $ncpassword, $openurl, $Report, $xmlHeader );
	//list($headers, $response) = explode("\r\n\r\n", $result, 2);
	//$headers = explode("\n", $headers);
	$responseXML = new DOMDocument();
	$responseXML->loadXML( $result );
	$reportIDNodes = $responseXML->getElementsByTagName( 'reportId' );
	if ( $reportIDNodes->length==0 ) {
		$reportID = null;
	} else {
		foreach ( $reportIDNodes as $ID ) {
			$reportID = $ID->nodeValue;
		}
	}
} else {
	$reportID = null;
	echo '<b><u>NO REPORT SENT: Report not valid</u></b>';}
?>
					<textarea name='reportresponsexmlvalidate' wrap='virtual' rows='18' cols='20'><?php
if ( $reportID ) {
	if ( !$responseXML->schemaValidate( 'espsubmittal.xsd' ) ) {
		echo '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
		libxml_display_errors(); } else { echo "There are no validation errors and the XML recieved matches the schema provided by NCMEC"; } } else {echo 'no verification because no valid report sent'; }?></textarea>
					<textarea name='reportresponsexml' wrap='virtual' rows='18' cols='20'><?php
if ( $responseXML ) {
	echo $responseXML->saveXML();
	$responseXML = null;
} else { echo 'no response xml because no valid response recieved or no valid report sent';}?></textarea>
					<textarea name='responsefull' wrap='virtual' rows='18' cols='20'><?php
if ( $result ) { echo $result; $result = null; } else { echo 'no result';} ?></textarea>
					<p> The report ID is: <?php echo $reportID; ?> </p>
				</fieldset>
				<fieldset>
					<legend>File setup, sending and processing</legend>
					<?php
if ( !empty( $uploadedfiletmploc ) && !empty( $reportID ) ) {
	$file = '@'.$uploadedfiletmploc;
	$postdata = array (
		'id' => $reportID,
		'file' => $file, );
	$result = NCMECsimpleauthdcurlPost( $ncusername, $ncpassword, $fileurl, $postdata );
	$responseXML = new DOMDocument();
	$responseXML->loadXML( $result );
	$fileIdNodes = $responseXML->getElementsByTagName( 'fileId' );
	$fileHashNodes = $responseXML->getElementsByTagName( 'hash' );
	if ( $fileIdNodes->length==0 ) {
		$fileID = null;
	} else {
		foreach ( $fileIdNodes as $ID ) {
			$fileID = $ID->nodeValue;
		}
	}
	if ( $fileHashNodes->length==0 ) {
		$filehash = null;
	} else {
		foreach ( $fileHashNodes as $hash ) {
			$filehash = $hash->nodeValue;
		}
	}
} else {echo 'something is wrong! Either there is no report ID or no file info!';} ?>
						<textarea name='reportresponsexmlvalidate' wrap='virtual' rows='18' cols='20'><?php
if ( !empty( $uploadedfiletmploc ) && !empty( $reportID ) ) {
	if ( !$responseXML->schemaValidate( 'espsubmittal.xsd' ) ) {
		echo '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
		libxml_display_errors(); } else { echo "There are no validation errors and the XML recieved matches the schema provided by NCMEC"; } } else { echo 'no file sent'; }?></textarea>
					<textarea name='reportresponsexml' wrap='virtual' rows='18' cols='20'<?php
if ( $responseXML ) {
	echo $responseXML->saveXML();
	$responseXML = null; } else { echo 'no response XML because no file sent or no valid report sent';}
?></textarea>
					<textarea name='responsefull' wrap='virtual' rows='18' cols='20'><?php
if ( $result ) {
	echo $result; $result=null; } else { echo 'no result'; } ?></textarea>
					<p> The File ID is: <?php echo $fileID; ?> </p>
				</fieldset>
				<fieldset>
					<legend> File data set up, processing and sending.</legend>
					<?php
if ( $fileID ) {
	// FIXME NOTE: Only sending file name at this time, all other exif should be contained in the file.
	$filedetaildom = new DOMDocument();
	$filedetaildom->formatOutput = true;
	$filedetaildom->encoding='UTF-8';

	$reportroot = $filedetaildom->createElement( 'fileDetails' );
	$filedetaildom->appendChild( $reportroot );

	$filereportid = $filedetaildom->createElement( 'reportId' );
	$filereportidtext = $filedetaildom->createTextNode( $reportID );
	$filereportid->appendChild( $filereportidtext );
	$reportroot->appendChild( $filereportid );

	$fileidpost = $filedetaildom->createElement( 'fileId' );
	$fileidposttext = $filedetaildom->createTextNode( $fileID );
	$fileidpost->appendChild( $fileidposttext );
	$reportroot->appendChild( $fileidpost );

	$filename = $filedetaildom->createElement( 'fileName' );
	$filenametext = $filedetaildom->createTextNode( $uploadedfilename );
	$filename->appendChild( $filenametext );
	$reportroot->appendChild( $filename );

	$filedetailXML = $filedetaildom->saveXML();

	$result = curlauthdAPIpost( $ncusername, $ncpassword, $fileinfourl, $filedetailXML, $xmlHeader );
	$responseXML = new DOMDocument();
	$responseXML->loadXML( $result );
	$responseNodes = $responseXML->getElementsByTagName( 'responseCode' );
	if ( $responseNodes->length==0 ) {
		$responsecode = null;
	} else {
		foreach ( $responseNodes as $r ) {
			$responsecode = $r->nodeValue;
		}
	}
}?>
					<textarea name='filedetailxml' wrap='virtual' rows='18' cols='70'><?php
if ( $filedetaildom ) {
	echo $filedetaildom->saveXML();} else { echo 'no filedetail submission created';}
?></textarea>
					<textarea name='filedetailxmlvalidate' wrap='virtual' rows='18' cols='70'><?php
if ( $filedetaildom ) {
	if ( !$filedetaildom->schemaValidate( 'espsubmittal.xsd' ) ) {
		echo '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
		libxml_display_errors(); } else { echo "There are no validation errors and the XML above matches the schema provided by NCMEC"; }} else { echo 'no filedetail submission created'; } ?></textarea>
					<textarea name='fileinforesponsexmlvalidate' wrap='virtual' rows='18' cols='20'><?php
if ( $responseXML ) {
	if ( !$responseXML->schemaValidate( 'espsubmittal.xsd' ) ) {
		echo '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
		libxml_display_errors(); } else { echo "There are no validation errors and the XML recieved matches the schema provided by NCMEC"; }} else { echo 'no responseXML available'; }?></textarea>
					<textarea name='fileinforesponsexml' wrap='virtual' rows='18' cols='20'><?php
if ( $responseXML ) {
	echo $responseXML->saveXML(); $responseXML = null;} else {echo 'no responseXML available';}
?></textarea>
					<p> <?php if ( $responsecode == 0 ) {echo 'File details were recieved successfully';} else {echo 'there appears to have been a problem sending file details, check response'; }?> </p>
				</fieldset>
				<fieldset>
					<legend>Closing Report</legend>
					<textarea name='reportxml' wrap='virtual' rows='18' cols='70'><?php
if ( !empty( $reportID ) ) {
	echo 'Report ID: '.$reportID.' marked for closure'.PHP_EOL.PHP_EOL;
	$postdata = array ( 'id' => $reportID );
	$result = NCMECsimpleauthdcurlPost( $ncusername, $ncpassword, $finishurl, $postdata );
	echo $result;
	$responseXML = new DOMDocument();
	$responseXML->loadXML( $result );
	$responseNodes = $responseXML->getElementsByTagName( 'responseCode' );
	if ( $responseNodes->length==0 ) {
		$responsecode = null;
	} else {
		foreach ( $responseNodes as $r ) {
			$responsecode = $r->nodeValue;
		}
	}} else {echo 'No reportID detected, did you ever actually open a report?';}?></textarea>
						<p> <?php if ( $responsecode == 0 ) { echo '<b><u>Thank you, your report has been submitted with Report ID: '.$reportID.' and all log information has been saved. Please remember to email legal@rt.wikimedia.org in order to get the image permanently deleted.</u></b>'; } else { echo 'It appears there may have been an issue either with closing the report or earlier in the process, please see possible errors above';} ?></p>
				</fieldset>
			</div>
		</div>
			<?php include dirname( __FILE__ ) . '/../include/lcapage.php'; ?>
	</div>
</body>
</html><?php
if ( $reportID ) {
	// Central Log
	$log_type = 'Child Protection';
	$log_title = 'Report to NCMEC for file uploaded by '.$uploaderusername.' '.$incdate.' '.$inchour.':'.$incmin.' UTC - Report# '.$reportID;
	$log_row = lcalog( $user, $log_type, $log_title, $istest );
	// Log details
	$template = 'INSERT INTO ncmecrelease (log_id,user,timestamp,username,project,filename,legalapproved,whoapproved,whynotapproved,logging_metadata,logging_details,test,report_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
	$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
	$mysql->set_charset( "utf8" );

	if ( !empty( $incprojectlang ) ) {
		$incproject = $incprojectlang.".".$incproject;
	}
	$submittime = gmdate( "Y-m-d H:i:s", time() );
	$insert_user = $user;
	$insert_username = $uploaderusername;
	$insert_project = $incproject;
	$insert_filename = $incfilename;
	$insert_whoapproved = $_POST['who-approved'];
	$insert_whynot = $_POST['why-not-approved'];
	$insert_logdata = serialize( $_POST['logging-metadata'] );
	$insert_details = $_POST['logging-details'];
	$insert_legalapproved = $_POST['legal-approved'];

	$insert = $mysql->prepare( $template );
	if ( $insert === false ) {
		echo 'Error while preparing: ' . $template . ' Error text: ' . $mysql->error, E_USER_ERROR;
	}
	$insert->bind_param( 'isssssssssssi', $log_row, $insert_user, $submittime, $insert_username, $insert_project, $insert_filename, $insert_legalapproved, $insert_whoapproved, $insert_whynot, $insert_logdata, $insert_details, $istest, $reportID );

	$insert->execute();

	if ( $filehash ) {
		$details_row = $insert->insert_id;

		// filehash log
		$template = 'INSERT INTO submittedfilehashes (clog_id, type, tlog_id, hash) VALUES (?,?,?,UNHEX(?))';
		$insert = $mysql->prepare( $template );
		if ( $insert === false ) {
			echo 'Error while preparing: ' . $template . ' Error text: ' . $mysql->error, E_USER_ERROR;
		}
		$insert->bind_param( 'isis', $log_row, $log_type, $details_row, $filehash );
		$insert->execute();
	}

	$mysql->close();
}

?>
