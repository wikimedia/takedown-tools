<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2013-12-07
Last modified : 2014-01-02

Thanks to Quentinv57 (of the Wikimedia projects) for some of the inspiration for the start.

Universal form to assist in DMCA takedowns by LCA team.

Part 1. Simple form for all information and wiki code spit out- Complete 2013-12-09
Part 2. Submit data to Chilling Effects - in process 2013-12-18

---------------------------------------------   */

require_once dirname( __FILE__ ) . '/../include/multiuseFunctions.php';
date_default_timezone_set( 'UTC' );

//null variables that may or may not be set later depending on how it goes
$locationURL = null;
$filessent = null;

// cast config and log variables
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$sendtoCE = $config['sendtoCE'];
$user = $_SERVER['PHP_AUTH_USER'];
$log_type = 'DMCA';
$log_title = $_POST['takedown-wmf-title'];
if ( $_POST['is-test'] === 'No' ) {
	$istest = 'N';
} elseif ( $_POST['is-test'] === 'Yes' ) {
	$istest = 'Y';
} else {
	$istest = '?';
}
$log_row = lcalog( $user, $log_type, $log_title, $istest );

$involved_user = !isset( $_POST['involved-user'] ) ? null : $_POST['involved-user'];
$logging_metadata = !isset( $_POST['logging-metadata'] ) ? null : $_POST['logging-metadata'];
$strike_note = !isset( $_POST['strike-note'] ) ? null : $_POST['strike-note'];
if ( is_array( $strike_note ) && in_array( 'other', $strike_note ) ) {
	$strike_note[array_search( 'other', $strike_note )] = 'Other: ' . $_POST['strike-note-other'];
}
$sender_name = !isset( $_POST['sender-name'] ) ? null : $_POST['sender-name'];
$sender_person = !isset( $_POST['sender-person'] ) ? null : $_POST['sender-person'];
$sender_firm = !isset( $_POST['sender-firm']) ? null : $_POST['sender-firm'];
$sender_address1 = !isset( $_POST['sender-address1'] ) ? null : $_POST['sender-address1'];
$sender_address2 = !isset( $_POST['sender-address2'] ) ? null : $_POST['sender-address2'];
$sender_city = !isset( $_POST['sender-city'] ) ? null : $_POST['sender-city'];
$sender_zip = !isset( $_POST['sender-zip'] ) ? null : $_POST['sender-zip'];
$sender_state = !isset( $_POST['sender-state'] ) ? null : $_POST['sender-state'];
$sender_country = !isset( $_POST['sender-country'] ) ? null : $_POST['sender-country'];
$takedown_date = !isset( $_POST['takedown-date'] ) ? null : $_POST['takedown-date'];
$action_taken = !isset( $_POST['action-taken'] ) ? null : $_POST['action-taken'];
$takedown_title = !isset( $_POST['takedown-title'] ) ? null : $_POST['takedown-title'];
$commons_title = !isset( $_POST['takedown-commons-title'] ) ? null : $_POST['takedown-commons-title'];
$wmfwiki_title = !isset( $_POST['takedown-wmf-title'] ) ? null : $_POST['takedown-wmf-title'];
$takedown_method = !isset( $_POST['takedown-method'] ) ? null : $_POST['takedown-method'];
$takedown_subject = !isset( $_POST['takedown-subject'] ) ? null : $_POST['takedown-subject'];
$takedown_text = !isset( $_POST['takedown-body'] ) ? null : $_POST['takedown-body'];


// cast form ce-send variable.
if ( isset( $_POST['ce-send'] ) && $_POST['ce-send'] === 'Yes' ) {
	$formsendtoCE = true;
} else {
	$formsendtoCE = false;
}

// cast test variable
if ( isset( $_POST['is-test'] ) && $_POST['is-test'] === 'Yes' ) {
	$istest = 'Y';
} else {
	$istest = 'N';
}

if ( !empty( $_POST['files-affected'] ) ) {
	$filearray = explode( '|', $_POST['files-affected'] );
	// Error check for file prefix
	foreach ($filearray as $key => $value) {
		if ( substr( $value, 0, 5 ) == 'File:' || substr( $value, 0, 5 ) == 'file:' ) {
			$filearray[$key] = substr( $value, 5 );
		}
	}
}

if ( !empty( $_POST['pages-affected'] ) ) {
	$pagesarray = explode( ',', $_POST['pages-affected'] );
}

if ( !empty( $filearray ) ) {
	foreach ( $filearray as $value ) {
		$linksarray[] = 'https://commons.wikimedia.org/wiki/File:'.$value;
	}
}


// Set up file uploads if they exist.
if ( is_uploaded_file( $_FILES['takedown-file1']['tmp_name'] ) ) {
	$CE_post_files[] = setupdataurl( $_FILES['takedown-file1'] );
	$filessent[] = $_FILES['takedown-file1']['name'];
}

if ( is_uploaded_file( $_FILES['takedown-file2']['tmp_name'] ) ) {
	$CE_post_files[] = setupdataurl( $_FILES['takedown-file2'] );
	$filessent[] = $_FILES['takedown-file2']['name'];
}

// Set up initial post data for Chilling Effects
$CE_post_data = array (
	'authentication_token' => $config['CE_apikey'],
	'notice' => array (
		'title' => $takedown_title,
		'type' => $_POST['ce-report-type'],
		'subject' => $takedown_subject,
		'date_sent' => $takedown_date,
		'source' => $takedown_method,
		'action_taken' => $action_taken,
		'body' => $takedown_text,
		'tag_list' => 'wikipedia, wikimedia',
		'jurisdiction_list' => 'US, CA',
	),
);

$CE_post_entities = array (
	array (
		'name' => 'recipient',
		'entity_attributes' => $config['CE_recipient'],
	),
	array (
		'name' => 'sender',
		'entity_attributes' => array (
			'name' => $sender_name,
			'address_line_1' => $sender_address1,
			'address_line_2' => $sender_address2,
			'city' => $sender_city,
			'state' => $sender_city,
			'zip' => $sender_zip,
			'country_code' => $sender_country,
		),
	),
);

$CE_post_data['notice']['entity_notice_roles_attributes'] = $CE_post_entities;

if ( !empty( $linksarray ) ) {

	foreach ( $linksarray as $key => $value ) {
		$urlarray[] = array ( 'url' => $value );
	}
	$CE_post_works[] = array (
		'infringing_urls_attributes' => $urlarray,
	);
}

$CE_post_data['notice']['works_attributes'] = $CE_post_works;

if ( !empty( $CE_post_files ) ) {
	$CE_post_data['notice']['file_uploads_attributes'] = $CE_post_files;
}

$CE_post = json_encode( $CE_post_data );

// Set up headers for Chilling Effects submission
$CE_post_headers = array (
	'Accept: application/json',
	'Content-Type: application/json',
	'Content-Length: ' . strlen( $CE_post ),
	'AUTHENTICATION_TOKEN: '.$config['CE_apikey'],
);

// Debug info
/*if (!$sendtoCE || !$formsendtoCE) {
	echo 'sendtoCE set to False? - ' . $sendtoCE . ' origin says ' . $config['sendtoCE'];
	echo 'formsendtoCE set to False? - ' . $formsendtoCE . ' origin says ' . $_POST['ce-send'];
}*/

// send to Chilling Effects
// Add new argument 1 to end of function to write to request.txt for debug
if ( $sendtoCE && $formsendtoCE ) {
	/*echo 'sendtoCE set to True? - ' . $sendtoCE . ' origin says ' . $config['sendtoCE'];;
	echo 'formsendtoCE set to True? - ' . $formsendtoCE . ' origin says ' . $_POST['ce-send'];*/
	$result = curlAPIpost( $config['CE_apiurl'], $CE_post, $CE_post_headers );

	$headers = explode( "\n", $result );
	foreach ( $headers as $header ) {
		if ( stripos( $header, 'Location:' ) !== false ) {
			$locationURL = substr( $header, 10 );
		}
	}
}

// Get ready to store in database

$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
$mysql->set_charset( "utf8" );

$submittime = gmdate( "Y-m-d H:i:s", time() );
$insert_user = $user;
$insert_sender_city = $sender_city;
$insert_sender_zip = $sender_zip;
$insert_sender_state = $sender_state;
$insert_sender_country = $sender_country;
$insert_takedown_date = $takedown_date;
$insert_action_taken = $action_taken;
$insert_takedown_title = $takedown_title;
$insert_commons_title = $commons_title;
$insert_wmfwiki_title = $wmfwiki_title;
$insert_takedown_method = $takedown_method;
$insert_takedown_subject = $takedown_subject;
$insert_involved_user = $involved_user;
$insert_logging_metadata = serialize( $logging_metadata );
$insert_strike_note = serialize( $strike_note );
$insert_ce_url = $locationURL;
$insert_filessent = serialize( $filessent );
$insert_files_affected = serialize( $linksarray );

// do it

$template = 'INSERT INTO dmcatakedowns (log_id,user,timestamp,sender_city,sender_zip,sender_state,sender_country,takedown_date,action_taken,takedown_title,commons_title,wmfwiki_title,takedown_method,takedown_subject,involved_user,logging_metadata,strike_note,ce_url,files_sent,files_affected,test) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';

$insert = $mysql->prepare( $template );
if ( $insert === false ) {
	echo 'Error while preparing: ' . $template . ' Error text: ' . $mysql->error, E_USER_ERROR;
}

$insert->bind_param( 'issssssssssssssssssss', $log_row, $insert_user, $submittime, $insert_sender_city, $insert_sender_zip, $insert_sender_state, $insert_sender_country, $insert_takedown_date, $insert_action_taken, $insert_takedown_title, $insert_commons_title, $insert_wmfwiki_title, $insert_takedown_method, $insert_takedown_subject, $insert_involved_user, $insert_logging_metadata, $insert_strike_note, $insert_ce_url, $insert_files_sent, $insert_files_affected, $istest );

$insert->execute();
$insert->close();

$apiurl = 'https://commons.wikimedia.org/w/api.php';
$usertable = getUserData( $user );
$mwsecret = $usertable['mwsecret'];
$mwtoken = $usertable['mwtoken'];

?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>Legal Takedowns</title>
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
	<script>
	$(document).ready(function(){
		$("#editdmcapage").click( function() {
			$("#dmcapageresult").html("<img src='/images/progressbar.gif' alt='waiting for edit progressbar'>");
			var dpagetitle = "Commons:Office_actions/DMCA_notices";
			var dmwtoken = <?php echo '"'.$mwtoken.'"' ?>;
			var dmwsecret = <?php echo '"'.$mwsecret.'"' ?>;
			var dapiurl = <?php echo '"'.$apiurl.'"' ?>;
			var deditsummary = "new takedown";
			var dtext = $("#commonsdmcapost").val();
			var daction = "appendtext";

			postdata = { action: daction, pagetitle: dpagetitle, mwtoken: dmwtoken, mwsecret: dmwsecret, apiurl: dapiurl, editsummary: deditsummary, text: dtext };

			$.post( "../mwoauth/mwOAuthProcessor.php", postdata, function(data) {
			if ( data && data.edit && data.edit.result == 'Success' ) {
				$('#dmcapageresult').html(data.edit.result + '! You can see the results at <a href="https://commons.wikimedia.org/wiki/Commons:Office_actions/DMCA_notices#'+dsectiontitle+'" target="_blank">Commons:DMCA#'+dsectiontitle+'</a>'); }
			else if ( data && data.error ) {
				$('#dmcapageresult').html(data.edit.error ); }
			else {
				$('#dmcapageresult').html('hmmm something weird happened');
					} },"json");
		});

		$("#editvppage").click( function() {
			$("#commonsvpresult").html("<img src='/images/progressbar.gif' alt='waiting for edit progressbar'>");
			var dpagetitle = "Commons:Village_pump";
			var dmwtoken = <?php echo '"'.$mwtoken.'"' ?>;
			var dmwsecret = <?php echo '"'.$mwsecret.'"' ?>;
			var dapiurl = <?php echo '"'.$apiurl.'"' ?>;
			var deditsummary = "new DMCA takedown notifcation";
			var dtext = $("#commonsvppost").val();
			var daction = "appendtext";

			postdata = { action: daction, pagetitle: dpagetitle, mwtoken: dmwtoken, mwsecret: dmwsecret, apiurl: dapiurl, editsummary: deditsummary, text: dtext };

			$.post( "../mwoauth/mwOAuthProcessor.php", postdata, function(data) {
			if ( data && data.edit && data.edit.result == 'Success' ) {
				$('#commonsvpresult').html(data.edit.result + '! You can see the results at <a href="https://commons.wikimedia.org/wiki/Commons:Village_pump" target="_blank">Commons:Village_pump</a>'); }
			else if ( data && data.error ) {
				$('#commonsvpresult').html(data.edit.error); }
			else {
				$('#commonsvpresult').html('hmmm something weird happened');
					} },"json");
		});

		$("#editusertalk").click( function() {
			$("#usertalkresult").html("<img src='/images/progressbar.gif' alt='waiting for edit progressbar'>");
			var dpagetitle = <?php echo '"User_talk:'.$involved_user.'"';?>;
			var dsectiontitle = "Notice of upload removal";
			var dmwtoken = <?php echo '"'.$mwtoken.'"' ?>;
			var dmwsecret = <?php echo '"'.$mwsecret.'"' ?>;
			var dapiurl = <?php echo '"'.$apiurl.'"' ?>;
			var deditsummary = "Notice of upload removal";
			var dtext = $("#commonsusertalk").val();
			var daction = "newsection";

			postdata = { action: daction, pagetitle: dpagetitle, sectiontitle: dsectiontitle, mwtoken: dmwtoken, mwsecret: dmwsecret, apiurl: dapiurl, editsummary: deditsummary, text: dtext };

			$.post( "../mwoauth/mwOAuthProcessor.php", postdata, function(data) {
			if ( data && data.edit && data.edit.result == 'Success' ) {
				$('#usertalkresult').html(data.edit.result + '! You can see the results at <a href="https://commons.wikimedia.org/wiki/'+dpagetitle+'#Notice_of_upload_removal" target="_blank">'+dpagetitle+'#'+dsectiontitle+'</a>'); }
			else if ( data && data.error ) {
				$('#usertalkresult').html(data.edit.error); }
			else {
				$('#usertalkresult').html('hmmm something weird happened');
					} },"json");
		});


	});
	</script>
</head>
<body class='mediawiki'>
	<div id='globalWrapper'>
		<div id='column-content'>
			<div id='content'>
				<h1>Processed Takedown</h1>
				<br />
				<?php if ( $locationURL ) {
	echo '<p> The DMCA Takedown was sent to Chilling Effects and you can find the submission at <a href="'.$locationURL.'" target="_blank">'.$locationURL.'</a>';
} else echo '<p> It does not appear that a report was sent to Chilling Effects (either because you asked the report not to, reporting is turned off on the server level or there was an error) <br /> If there is a problem please see James or look at the debug section at the button of the page for the response from CE'; ?>
				<fieldset>
					<legend> wmfWiki post </legend>
					<table>
						<tr>
							<td>
								Please post the below text to <?php echo "<a target='_blank' href='https://www.wikimediafoundation.org/wiki/".htmlentities( $wmfwiki_title )."?action=edit'>https://www.wikimediafoundation.org/wiki/".htmlentities( $wmfwiki_title )."</a>"?>
							</td>
						</tr>
						<tr>
							<td>
								<textarea name='takedown-body-wmf' wrap='virtual' rows='18' cols='90'><?php
echo "<div class='mw-code' style='white-space: pre; word-wrap: break-word;'><nowiki>".PHP_EOL.
	$takedown_text.PHP_EOL.
	"</nowiki></div>".PHP_EOL.
	"[[Category:DMCA ".date( "Y" )."]]";?>
								</textarea>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend> Wikimedia Commons Posts </legend>
					<table>
						<tr>
							<td>
								Please post the below text to the <u>BOTTOM</u> of the Wikimedia Commons DMCA Board at <?php echo '<a target="_blank" href="https://commons.wikimedia.org/wiki/Commons:Office_actions/DMCA_notices?action=edit">https://commons.wikimedia.org/wiki/Commons:DMCA</a>';?>
							</td>
						</tr>
						<tr>
							<td>
								<textarea id='commonsdmcapost' name='commons-dmca-post' wrap='virtual' rows='18' cols='90'>

<?php
echo "{{subst:DMCA_takedown_notice|".$commons_title.
	( !empty( $wmfwiki_title ) ? "|".$wmfwiki_title : "" ).
	( array_key_exists( 0, $filearray ) ? "|".$filearray[0] : "" ).
	( array_key_exists( 1, $filearray ) ? "|".$filearray[1] : "" ).
	( array_key_exists( 2, $filearray ) ? "|".$filearray[2] : "" ).
	( array_key_exists( 3, $filearray ) ? "|".$filearray[3] : "" ).
	( array_key_exists( 4, $filearray ) ? "|".$filearray[4] : "" ).
	"}}"; 
	if ( array_key_exists( 5, $filearray ) ) {
		echo "
* Additional Files:
";
		foreach ($filearray as $index => $file) {
			if ( $index < 5 ) {
				continue;
			} else {
				echo "** [[File:".$file."|".$file."]]
";
			}
		}
	}
		?></textarea>
							</td>
						</tr>
						<tr>
							<table border='1'>
								<tr>
									<td><?php
if ( $usertable['mwtoken'] ) {
	echo 'You are logged in with OAuth information, please make any edits necessary above and then click the button to the right to send the edit.     <input id="editdmcapage" type="button" value="send edit">';
} else {
	echo 'We did not find your OAuth information, you can register using the link on the sidebar but for this time please use the links provided on the top of the page and paste the template in the edit box';
}?>
									</td>
								</tr>
								<tr>
									<td id='dmcapageresult'></td>
								</tr>
							</table>
						</tr>
						<tr>
							<td>
								Please post the below text to the Wikimedia Commons Village Pump at <a target='_blank' href='https://commons.wikimedia.org/wiki/Commons:Village_pump?action=edit&amp;section=new'>https://commons.wikimedia.org/wiki/Commons:Village_pump</a>
							</td>
						</tr>
						<tr>
							<td>
								<textarea id='commonsvppost' name='commons-dmca-post' wrap='virtual' rows='18' cols='90'>

<?php
echo "{{subst:DMCA_takedown_notice|".$commons_title.
	( !empty( $commons_title ) ? "|".$wmfwiki_title : "" ).
	( array_key_exists( 0, $filearray ) ? "|".$filearray[0] : "" ).
	( array_key_exists( 1, $filearray ) ? "|".$filearray[1] : "" ).
	( array_key_exists( 2, $filearray ) ? "|".$filearray[2] : "" ).
	( array_key_exists( 3, $filearray ) ? "|".$filearray[3] : "" ).
	( array_key_exists( 4, $filearray ) ? "|".$filearray[4] : "" ).
	"}}";
	if ( array_key_exists( 5, $filearray ) ) {
		echo "
* Additional Files:
";
		foreach ($filearray as $index => $file) {
			if ( $index < 5 ) {
				continue;
			} else {
				echo "** [[File:".$file."|".$file."]]
";
			}
		}
	}?></textarea>
							</td>
						</tr>
						<tr>
							<table border='1'>
								<tr>
									<td><?php
if ( $usertable['mwtoken'] ) {
	echo 'You are logged in with OAuth information, please make any edits necessary above and then click the button to the right to send the edit.     <input id="editvppage" type="button" value="send edit">';
} else {
	echo 'We did not find your OAuth information, you can register using the link on the sidebar but for this time please use the links provided on the top of the page and paste the template in the edit box';
}?>
									</td>
								</tr>
								<tr>
									<td id='commonsvpresult'></td>
								</tr>
							</table>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>Warning to uploader</legend>
					<table>
						<tr>
							<td> Please post the below text to the Wikimedia Commons user talk page of the user who uploaded the File. According to the information you submitted earlier this is <?php echo htmlspecialchars( $involved_user );?>. <br />
								You can leave them a new message by following this link: <?php echo '<a target="_blank" href="https://commons.wikimedia.org/wiki/User talk:'.htmlspecialchars( $involved_user ).'?action=edit&section=new&preloadtitle=Notice of upload removal"/> https://commons.wikimedia.org/wiki/User talk:'.htmlspecialchars( $involved_user ).'</a>'; ?>
							</td>
						</tr>
						<tr>
							<td>
								<textarea id ='commonsusertalk' name='commons-user-warning' wrap='virtual' rows='18' cols='90'>

Dear <?php echo $involved_user; ?>:

The Wikimedia Foundation (“Wikimedia”) has taken down content that you posted at [[:File:<?echo htmlspecialchars($filearray[0]);?>]] due to Wikimedia’s receipt of a validly formulated notice that your posted content was infringing an existing copyright.  When someone sends us a validly formulated notice of copyright infringement, the Digital Millennium Copyright Act (“DMCA”) Section (c)(1)(C) requires Wikimedia to take the content down, and to notify you that we have removed that content.  This notice, by itself, does not mean that the party requesting that the content be taken down are suing you.  The party requesting the take down might only be interested in removing the content from our site.

'''What Can You Do?'''

You are not obligated to take any action.  However, if you feel that your content does not infringe upon any copyrights, you may contest the take down request by submitting a ‘counter notice’ to Wikimedia.  Before doing so, you should understand your legal position, and you may wish to consult with an attorney.  If you choose to submit a counter notice, the alleged copyright holder can either refuse to contest the counter notice or decide to file a lawsuit against you to restrain Wikimedia from re-posting the content.  Please note that Wikimedia will not be a party to any legal action that arises from you sending a counter notice, and that Wikimedia is unable to provide you with legal advice.

'''Filing a Counter Notice'''

If you choose to submit a counter notice, you must send a letter asking Wikimedia to restore your content to [mailto:legal@wikimedia.org legal@wikimedia.org], or to our service processor at the following address:  Wikimedia Foundation, c/o CT Corporation System, 818 West Seventh Street, Los Angeles, California, 90017.  The letter must comply with DMCA standards, set out in Section (g)(3)(A-D), and must contain the following:

* A link to where the content was before we took it down and a description of the material that was removed;
* A statement, under penalty of perjury, that you have a good faith belief that the content was removed or disabled as a result of mistake or misidentification of the material to be removed or disabled;
* Your name, address, and phone number;
* If your address is in the United States, a statement that says “I consent to the jurisdiction of the Federal District Court for the district where my address is located, and I will accept service of process from the person who complained about the content I posted”; alternatively, if your address is outside the United States, a statement that says “I agree to accept service of process in any jurisdiction where the Wikimedia Foundation can be found, and I will accept service of process from the person who complained about the content I posted”; and finally,
* Your physical or electronic signature.

Pursuant to the DMCA, Wikimedia must inform the alleged copyright holder that you sent us a counter notice, and give the alleged copyright holder a copy of the counter notice. The alleged copyright holder will then have fourteen (14) business days to file a lawsuit against you to restrain Wikimedia from reposting the content.  If Wikimedia does not receive proper notification that the alleged copyright holder has initiated such a lawsuit against you, we will repost your content within ten (10) to fourteen (14) business days.

'''Miscellaneous'''

As a matter of policy and under appropriate circumstances, Wikimedia will block the accounts of repeat infringers as provided by Section 512(i)(1)(A) of the DMCA.

If you would like to learn more about Wikimedia’s policies, please refer to the Wikimedia Terms of Use, available at [[wmf:Terms_of_use|Terms of use]], and the Wikimedia Legal Policies, available at [[m:Legal/Legal_Policies]].  More information on DMCA compliance may also be found at:

* [http://www.chillingeffects.org/dmca512/faq http://www.chillingeffects.org/dmca512/faq]
* [https://www.eff.org/issues/dmca https://www.eff.org/issues/dmca]
* [http://www.copyright.gov/onlinesp/ http://www.copyright.gov/onlinesp/]


Wikimedia appreciates your support.  Please do not hesitate to contact us if you have any questions regarding this notice.


Sincerely,
~~~~</textarea>
							</td>
						</tr>
						<tr>
							<table border='1'>
								<tr>
									<td><?php
if ( $usertable['mwtoken'] ) {
	echo 'You are logged in with OAuth information, please make any edits necessary above and then click the button to the right to send the edit.     <input id="editusertalk" type="button" value="send edit">';
} else {
	echo 'We did not find your OAuth information, you can register using the link on the sidebar but for this time please use the links provided on the top of the page and paste the template in the edit box';
}?>
									</td>
								</tr>
								<tr>
									<td id='usertalkresult'></td>
								</tr>
							</table>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>Debugging and double checking information for James</legend>
					<table>
						<tr>
							<td>
								User table info:
							</td>
							<td>
								<textarea><?php echo print_r( $usertable ) ?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								CE info being sent?
							</td>
							<td>
								<textarea><?php echo $sendtoCE.$formsendtoCE?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Files affected (if given)
							</td>
							<td>
								<textarea><?php echo !empty( $files_affected ) ? $files_affected : ""?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Var dump of filearray
							</td>
							<td>
								<textarea><?php echo !empty( $filearray ) ? var_dump( $filearray ) : ""?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								print of php array for CE
							</td>
							<td>
								<textarea><?php echo var_dump( $CE_post_data );?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								print of json for CE
							</td>
							<td>
								<textarea><?php echo json_encode( $CE_post_data, JSON_PRETTY_PRINT );?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Print of response from CE
							</td>
							<td>
								<textarea><?php echo print_r( $result );?></textarea>
					</table>
				</fieldset>
			</div>
		</div>
			<?php include dirname( __FILE__ ) . '/../include/lcapage.php'; ?>
		</div>
	</body>
</html>
