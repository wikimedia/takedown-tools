<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)
			
Date of creation : 2014-01-01

Processor for basic information release form basicRelease.php
			
---------------------------------------------   */

require_once('include/multiuseFunctions.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

// cast config and log variables
$config = parse_ini_file('lcaToolsConfig.ini');
$user = $_SERVER['PHP_AUTH_USER'];
$log_type = 'Release';
$log_title = 'Release of information to ' . $_POST['who-released-to'];
if ($_POST['is-test'] === 'No') {
	$istest = 'N';
} elseif ($_POST['is-test'] === 'Yes') {
	$istest = 'Y';
} else {
	$istest = '?';
}
$log_row = lcalog($user,$log_type,$log_title,$istest);

$mysql = new mysqli($dbaddress,$dbuser,$dbpw,$db);
$mysql->set_charset("utf8");

$who_received = $_POST['who-received'];
if (in_array('Other', $who_received)) {
	$who_received[array_search('Other', $who_received)] = 'Other: ' . $_POST['who-received-other'];
}
$serializedwho_received = serialize($who_received);

$pre_approved = $_POST['pre-approved'];
$why_released = $_POST['why-released'];
if (in_array('Other', $why_released)) {
	$why_released[array_search('Other', $why_released)] = 'Other: ' . $_POST['why-released-other'];
}
$serializedwhy_released = serialize($why_released);
$who_released = $_POST['who-released'];
$who_released_to = $_POST['who-released-to'];
$released_to_contact = $_POST['released-to-contact'];
$details = $_POST['details'];


$template = 'INSERT INTO basicrelease (log_id,user,timestamp,who_received,pre_approved,why_released,who_released,who_released_to,released_to_contact,details,test) VALUES (?,?,?,?,?,?,?,?,?,?,?)';

$submittime = gmdate("Y-m-d H:i:s", time());

$insert = $mysql->prepare($template);
	if ($insert === false) {
		echo 'Error while preparing: ' . $template . ' Error text: ' . $mysql->error, E_USER_ERROR;
	}

$insert->bind_param('issssssssss',$log_row,$user,$submittime,$serializedwho_received,$pre_approved,$serializedwhy_released,$who_released,$who_released_to,$released_to_contact,$details,$istest);

$insert->execute();
$insert->close();
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>Release of Confidential Information</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/jquery-1.10.2.min.js'></script>
	<script src='scripts/lca.js'></script>
	<style type='text/css'>
	<!--/* <![CDATA[ */
	@import 'css/main.css'; 
	@import 'css/lca.css';
	/* ]]> */-->
	td { vertical-align: top; }
	.external, .external:visited { color: #222222; }
	.autocomment{color:gray}
	</style>
</head>
<body class='mediawiki'>
	<div id='globalWrapper'>
		<div id='column-content'>
			<div id='content'>
				<h1>Processed Release</h1>
				<p> See below for the information that has been submitted. <?php if ($istest === 'Y') { echo '<b> NOTE: This was marked as a test submission </b>'; } ?> </p>
				<fieldset>
					<legend> Release info </legend>
					<table border='1' id='mw-movepage-table'> 
						<tr>
							<td>
								<label for='who-received'> Who was this released too? </label> 
								</td>
								<td>
									<?php if(!empty($who_received)) { 
										foreach ($who_received as $value) {
											echo htmlspecialchars($value) . '<br />';
										}
									} else { echo 'This option was not set'; } ?>
								</td>
							</tr>
							<tr>
								<td>
									<label for='pre-approved'> Was this release pre-approved through the Office of the General Counsel, <br /> <b>OR</b> was it an urgent matter of life and limb? </label>
								</td>
								<td>
									<?php if(!empty($pre_approved)) {
										echo htmlspecialchars($pre_approved);
									} else { echo 'This option was not set'; } ?>
								</td>
							</tr>
							<tr>
								<td>
									<label for='why-released'> Reason for the release.</label>
								</td>
								<td>
									<?php if(!empty($why_released)) {
										foreach ($why_released as $value) {
											echo htmlspecialchars($value) . '<br />';
										}
									} else { echo 'This option was not set'; } ?>
								</td>
							</tr>
							<tr>
								<td>
									<label for='who-released'> Who released the information? </label> 
								<td>
									<?php if(!empty($who_released)) {
										echo htmlspecialchars($who_released);
									} else { echo 'This option was not set'; } ?>
								</td>
							</tr>
							<tr>
								<td>
									<label for='who-released-to'>What is the name of the person to whom it was released?</label>
								</td>
								<td>
									<?php if(!empty($who_released_to)) {
										echo htmlspecialchars($who_released_to);
									} else { echo 'This option was not set'; } ?>
								</td>
							</tr>
							<tr>
								<td>
									<label for='released-to-contact'> If to a non-WMF contact, how could we contact that person if necessary? </label>
								</td>
								<td>
									<?php if(!empty($released_to_contact)) {
										echo htmlspecialchars($released_to_contact);
									} else { echo 'This option was not set'; } ?>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend>Please describe the situation, including any applicable links. </legend>
						<textarea name='details' wrap='virtual' rows='18' cols='70' readonly> <?php if(!empty($details)) {
							echo $details;
						}  else { echo 'This option was not set'; } ?></textarea>
					</fieldset>
				</div>
			</div>
			<?php include('include/lcapage.php'); ?>
		</div>
	</body>
</html>
