<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2014-01-10

NCMEC reporting form for Child Protection takedowns
New processor with pretty output - Beta

---------------------------------------------   */

require_once 'include/multiuseFunctions.php';
date_default_timezone_set( 'UTC' );

// cast config and log variables
$config = parse_ini_file( 'lcaToolsConfig.ini' );
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

if ( !empty( $incprojectlang ) ) {
	$incprojectcombined = $incprojectlang.".".$incproject;
} else {
	$incprojectcombined = null;
}

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

$whoapproved = $_POST['who-approved'];
$whynotapproved = $_POST['why-not-approved'];
$logdata = $_POST['logging-metadata'];
$details = $_POST['logging-details'];
$legalapproved = $_POST['legal-approved'];

?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>NCMEC Submission</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/jquery-1.10.2.min.js'></script>
	<script src='scripts/lca.js'></script>
	<style type='text/css'>
	<!--/* <![CDATA[ */
	@import 'css/main.css';
	@import 'css/lca.css';
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
                    <legend>Submission processing</legend>
                    <table border='1' id='mw-movepage-table' style='font-weight:bold;'>
                        <tr>
                            <td >
                                <u>Step 1:</u> Data gathered and put together:
                            </td>
                            <td >
                                <img id='gathered' src='images/List-remove.svg' width='40px'/>
                            </td>
                            <td >
                                <u> Step 4:</u> File information sent:
                            </td>
                            <td >
                                <img id='file-info' src='images/List-remove.svg' width='40px'/>
                            </td>
                        </tr>
                        <tr>
                            <td >
                                <u>Step 2:</u> Report opened with NCMEC:
                            </td>
                            <td>
                                <img id='opened' src='images/List-remove.svg' width='40px'/>
                            </td>
                            <td >
                                <u>Step 5:</u> Report closed:
                            </td>
                            <td>
                                <img id='closed' src='images/List-remove.svg' width='40px'/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <u>Step 3:</u> File sent:
                            </td>
                            <td>
                                <img id='file-sent' src='images/List-remove.svg' width='40px'/>
                            </td>
                             <td >
                                <u>Step 6:</u> Log created and data stored:
                            </td>
                            <td>
                                <img id='logged' src='images/List-remove.svg' width='40px'/>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan='4'>
                        		<div id='result'></div>
                        	</td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
			        <legend> Logged info </legend>
			        <table border='1' id='mw-movepage-table'>
			        	<tr>
			                <td>
			                    <label for='legal-approved'> Was this release to NCMEC Approved by the legal department? </label>
			                </td>
			                <td>
			                    <?php
if ( !empty( $legalapproved ) ) {
	echo htmlspecialchars( $legalapproved );
} else {
	echo 'this field does not appear to have been set';
} ?>
			                </td>
			            </tr>
			            <tr>
			                <td>
			                    <label for='who-approved'>If Yes: Who in the legal department approved the release?</label>
			                </td>
			                <td>
			                    <?php
if ( $whoapproved ) {
	echo htmlspecialchars( $whoapproved );
} else {
	echo '';
} ?>
			                </td>
			            </tr>
			            <tr>
			                <td>
			                    <label for='why-not-approved'> If No: Why not? </label>
			                </td>
			                <td>
			                    <?php
if ( $whynotapproved ) {
	echo htmlspecialchars( $whynotapproved );
} else {
	echo '';
} ?>
			                </td>
			            </tr>
			            <tr>
			                <td>
			                    <label for='user-involved'> Which user uploaded the image(s) involved? </label>
			                </td>
			                <td>
			                    <?php
if ( $uploaderusername ) {
	echo htmlspecialchars( $uploaderusername );
} else {
	echo 'This option was not set';
} ?>
			                </td>
			            </tr>
			            <tr>
			                <td>
			                    <label for='project-involved'> Which project did the incident occur on?</label>
			                </td>
			                <td>
			                    <?php
if ( $incprojectcombined ) {
	echo htmlspecialchars( $incprojectcombined );
} elseif ( $incproject ) {
	echo htmlspecialchars( $incproject );
} else {
	echo 'This option was not set';
} ?>
			                </td>
			            </tr>
			            <tr>
			                <td>
			                    <label for='file-involved'> Which was the name of the file involved? </label>
			                </td>
			                <td>
			                    <?php
if ( $incfilename ) {
	echo htmlspecialchars( $incfilename );
} else { echo 'This option was not set'; } ?>
			                </td>
			            </tr>
			            <tr>
			                <td>
			                    <label for='hash'> File Hash: </label>
			                </td>
			                <td>
			                    <div id='hash'></div>
			                </td>
			            </tr>
			            <tr>
			                <td>
			                    <label for='report-id'>NCMEC Report ID# </label>
			                </td>
			                <td>
			                    <div id='report-id'></div>
			                </td>
			            </tr>
			            <tr>
			                <td >
			                    <label for='logging-metadata'> Please check all statements which are true </label>
			                </td>
			                <td>
			                    <?php
if ( !empty( $logdata ) ) {
	foreach ( $logdata as $value ) {
		echo htmlspecialchars( $value ) . '<br />';
	}
} else { echo 'This option was not set'; } ?>
			                </td>
			            </tr>
			        </table>
			    <fieldset>
			        <legend>specific notes for logging: </legend>
			        <textarea name='details' wrap='virtual' rows='18' cols='70' readonly><?php if ( !empty( $details ) ) {
	echo $details;
}  else { echo 'This option was not set'; } ?></textarea>
			    </fieldset>
			    </fieldset>
                <fieldset>
                    <legend>Debugging information</legend>
                     <table border='1' id='mw-movepage-table' style='font-weight:bold;'>
                     	<tr>
                            <td>
                                <label for='post-info'>Array submitted:</label>
                            </td>
                            <td>
                                <textarea id='post-info' readonly><?php
echo print_r( $_POST );
echo print_r( $_FILES );?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for='xml-report'>XML send to NCMEC for main report</label>
                            </td>
                            <td>
                                <textarea id='xml-report' readonly></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for='xml-report-val'>Validation info for main report</label>
                            </td>
                            <td>
                                <textarea id='xml-report-val' readonly></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for='open-report-rsp'>Response from NCMEC main report</label>
                            </td>
                            <td>
                                <textarea id='open-report-rsp' readonly></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for='file-upload-rsp'>Response from NCMEC file upload</label>
                            </td>
                            <td>
                                <textarea id='file-upload-rsp' readonly></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for='xml-file-details'>XML sent to NCMEC on file details</label>
                            </td>
                            <td>
                                <textarea id='xml-file-details' readonly></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for='file-details-rsp'>Response from NCMEC on file details</label>
                            </td>
                            <td>
                                <textarea id='file-details-rsp' readonly></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for='report-close-rsp'>Response from NCMEC on report close</label>
                            </td>
                            <td>
                                <textarea id='report-close-rsp' readonly></textarea>
                            </td>
                        </tr>
                    </table>
                </fieldset>
			</div>
		</div>
			<?php include 'include/lcapage.php'; ?>
	</div>
<?php
echo '...';
flush();
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

// var reportxml = $.parseXML(".json_encode($openReport->saveXML()).");

echo "<script> $('#xml-report').val(".json_encode( $openReport->saveXML() ).");</script>".PHP_EOL;

// DECISION POINT: Step 1
if ( !$openReport->schemaValidate( 'include/espsubmittal.xsd' ) ) {
	echo "<script> $('#xml-report-val').val('DOMDocument::schemaValidate() Generated Errors!".json_encode( libxml_display_errors() )."');</script>;";
	echo "<script> $('img#gathered').attr('src', 'images/Dialog-error-round.svg');</script>".PHP_EOL;
} else {
	echo "<script> $('#xml-report-val').val('There are no validation errors and the XML above matches the schema provided by NCMEC');</script>".PHP_EOL;
	echo "<script> $('img#gathered').attr('src', 'images/Dialog-accept.svg');</script>".PHP_EOL; $openReportValid = true;
}

echo '...';
flush();

if ( $openReportValid ) {
	$result = curlauthdAPIpost( $ncusername, $ncpassword, $openurl, $Report, $xmlHeader );
	//list($headers, $response) = explode("\r\n\r\n", $result, 2);
	//$headers = explode("\n", $headers);
	$responseXML = new DOMDocument();
	$responseXML->loadXML( $result );
	$reportIDNodes = $responseXML->getElementsByTagName( 'reportId' );
	// DECISION POINT: Step 2
	if ( $reportIDNodes->length==0 ) {
		$reportID = null;
		echo "<script> $('img#opened').attr('src', 'images/Dialog-error-round.svg'); $('div#report-id').html('<b><u>NO REPORT SENT: Response error</u></b>');</script>".PHP_EOL;
	} else {
		foreach ( $reportIDNodes as $ID ) {
			$reportID = $ID->nodeValue;
			echo "<script> $('img#opened').attr('src', 'images/Dialog-accept.svg'); $('div#report-id').text('".$reportID."');</script>".PHP_EOL;
		}
	}
} else {
	$reportID = null;
	echo "<script> $('img#opened').attr('src', 'images/Dialog-error-round.svg'); $('div#report-id').html('<b><u>NO REPORT SENT: Report not valid</u></b>');</script>".PHP_EOL;
}

if ( $reportID && $responseXML ) {
	echo "<script> $('#open-report-rsp').val(".json_encode( $result ).")</script>".PHP_EOL;
	$responseXML = null;
} else {
	echo "<script> $('#open-report-rsp').val('no response xml because no valid response recieved or no valid report sent'); </script>".PHP_EOL;
}

echo '...';
flush();

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

	// DECISION POINT: Step 3
	if ( $fileIdNodes->length==0 ) {
		$fileID = null;
		echo "<script> $('img#file-sent').attr('src', 'images/Dialog-error-round.svg');</script>".PHP_EOL;
	} else {
		foreach ( $fileIdNodes as $ID ) {
			$fileID = $ID->nodeValue;
			echo "<script> $('img#file-sent').attr('src', 'images/Dialog-accept.svg');</script>".PHP_EOL;
		}
	}

	//Get and set hash value
	if ( $fileHashNodes->length==0 ) {
		$filehash = null;
		echo "<script> $('div#hash').text('No hash available');</script>".PHP_EOL;
	} else {
		foreach ( $fileHashNodes as $hash ) {
			$filehash = $hash->nodeValue;
			echo "<script> $('div#hash').text('".$filehash."');</script>".PHP_EOL;
		}
	}
} else {
	echo "<script> $('div#hash').text('No hash available');</script>".PHP_EOL;
}

if ( $result ) {
	echo "<script> $('#file-upload-rsp').val(".json_encode( $result ).");</script>".PHP_EOL;
	$result=null;
} else {
	echo "<script> $('#file-upload-rsp').val('no result'); </script>".PHP_EOL;
}

echo '...';
flush();

if ( $fileID ) {
	// FIXME NOTE: Only sending file name at this time, all other exif should be contained in the file but would be nice to send seperately too.
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

	if ( !empty( $uploaderip ) ) {
		$uploadipevent = $filedetaildom->createElement( 'ipCaptureEvent' );
		$reportroot->appendChild( $uploadipevent );

		$uploadactualip = $filedetaildom->createElement( 'ipAddress' );
		$uploadactualiptext = $filedetaildom->createTextNode( $uploaderip );
		$uploadactualip->appendChild( $uploadactualiptext );

		$uploadiptype = $filedetaildom->createElement( 'eventName' );
		$uploadiptypetext = $filedetaildom->createTextNode( 'Upload' );
		$uploadiptype->appendChild( $uploadiptypetext );

		$uploaddatetime = $filedetaildom->createElement( 'dateTime' );
		$uploaddatetimetext = $filedetaildom->createTextNode( $incdatetime );
		$uploaddatetime->appendChild( $uploaddatetimetext );

		$uploadipevent->appendChild( $uploadactualip );
		$uploadipevent->appendChild( $uploadiptype );
		$uploadipevent->appendChild( $uploaddatetime );
	}

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
} else {
	echo "<script> $('#xml-file-details').val('no filedetail submission created'); </script>".PHP_EOL;
}

if ( $filedetaildom ) {
	echo "<script> $('#xml-file-details').val(".json_encode( $filedetaildom->saveXML() )."); </script>".PHP_EOL;

	if ( !$filedetaildom->schemaValidate( 'include/espsubmittal.xsd' ) ) {
		echo "<script>
		var filedetails = $('#xml-file-details');
		input.val( input.val() + 'DOMDocument::schemaValidate() Generated Errors!".libxml_display_errors()."'; </script>".PHP_EOL;
	}
}

if ( $result ) {
	echo "<script> $('#file-details-rsp').val(".json_encode( $result )."); </script>".PHP_EOL; $responseXML = null; $result = null;
} else {
	echo "<script> $('#file-details-rsp').val('no responseXML available'); </script>".PHP_EOL;
}

/* If we got a response code of success (0) mark as success, if we got another response code mark as fail,
if we don't have a response code at all probably didn't send anything and so don't do anything and leave at current symbol. */
if ( $responsecode === '0' ) {
	echo "<script> $('img#file-info').attr('src', 'images/Dialog-accept.svg');</script>".PHP_EOL;
} elseif ( $responsecode ) {
	echo "<script> $('img#file-info).attr('src', 'images/Dialog-error-round.svg');</script>".PHP_EOL;
}

echo '...';
flush();

if ( !empty( $reportID ) ) {
	$postdata = array ( 'id' => $reportID );
	$result = NCMECsimpleauthdcurlPost( $ncusername, $ncpassword, $finishurl, $postdata );
	echo "<script> $('#report-close-rsp').val(".json_encode( $result )."); </script>".PHP_EOL;
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
} else {
	echo "<script> $('#report-close-rsp').val('no report to close'); </script>".PHP_EOL;
}

/* If success code print result text, if another code error out, if no code do nothing */
if ( $responsecode === '0' ) {
	echo "<script> $('#closed').attr('src', 'images/Dialog-accept.svg'); </script>".PHP_EOL;
} elseif ( $responsecode ) {
	echo "<script> $('#closed').attr('src', 'images/Dialog-error-round.svg');
	$('#result').html('<u>It appears there may have been an issue either with closing the report or earlier in the process, please see possible errors above</u>'); </script>".PHP_EOL;
}

echo '...';
flush();

// Logging
if ( $reportID ) {
	// Central Log
	$log_type = 'Child Protection';
	$log_title = 'Report to NCMEC for file uploaded by '.$uploaderusername.' '.$incdate.' '.$inchour.':'.$incmin.' UTC - Report# '.$reportID;
	$log_row = lcalog( $user, $log_type, $log_title, $istest );
	// Log details
	$template = 'INSERT INTO ncmecrelease (log_id,user,timestamp,username,project,filename,legalapproved,whoapproved,whynotapproved,logging_metadata,logging_details,test,report_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
	$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
	$mysql->set_charset( "utf8" );

	$submittime = gmdate( "Y-m-d H:i:s", time() );
	$insert_user = $user;
	$insert_username = $uploaderusername;

	if ( $incprojectcombined ) {
		$insert_project = $incprojectcombined;
	} else {
		$insert_project = $incproject;
	}

	$insert_filename = $incfilename;
	$insert_whoapproved = $whoapproved;
	$insert_whynot = $whynotapproved;
	$insert_logdata = serialize( $logdata );
	$insert_details = $_POST['logging-details'];
	$insert_legalapproved = $legalapproved;

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

	/* If success code from close then put full result message AND check off logging,
	if error code or no success code then just check off logging since message already printed about failure */
	if ( $responsecode === '0' ) {
		echo "<script> $('#logged').attr('src', 'images/Dialog-accept.svg');
		$('#result').html('<u>Thank you, your report has been submitted with Report ID: ".$reportID." and all log information has been saved. <br /> Please remember to email legal@rt.wikimedia.org in order to get the image permanently deleted.</u>');</script>".PHP_EOL;
	} else {
		echo "<script>$('#logged').attr('src', 'images/Dialog-accept.svg');</script>".PHP_EOL;
	}
} else {
	echo "<script> $('#result').html('<u>It appears there may have been an issue either with closing the report or earlier in the process, please see possible errors above</u>'); </script>".PHP_EOL;
}
echo '...';
flush();
?>
</body>
</html>
