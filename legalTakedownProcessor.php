<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)
			
Date of creation : 2013-12-07
Last modified : 2013-12-18

Thanks to Quentinv57 (of the Wikimedia projects) for some of the inspiration for the start.

Universal form to assist in DMCA takedowns by LCA team.

Part 1. Simple form for all information and wiki code spit out- Complete 2013-12-09
Part 2. Submit data to chiling effects - in process 2013-12-18
			
---------------------------------------------   */

include_once ('legalTakedownConfig.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

if (!empty($_POST['files-affected'])) {
	$filearray=explode(',', $_POST['files-affected']);
}

foreach ($filearray as $value) {
	$linksarray[] = 'https://commons.wikimedia.org/wiki/File'.$value;
}


// Set up file uploads if they exist.
if (is_uploaded_file($_FILES['takedown-file1']['tmp_name'])) {
	$CE_post_files_tmp1 = array();
	$CE_post_files_tmp1['file_name'] = $_FILES['takedown-file1']['name'];
	$datatemp = file_get_contents($_FILES['takedown-file1']['tmp_name']);
	$datatemp = base64_encode($datatemp);
	$uri = 'data: '. $_FILES['takedown-file1']['type'].';base64,'.$datatemp;
	$CE_post_files_tmp1['file'] = $uri;
	$CE_post_files[] = $CE_post_files_tmp1;


}

if (is_uploaded_file($_FILES['takedown-file2']['tmp_name'])) {
	$CE_post_files_tmp2 = array();
	$CE_post_files_tmp2['file_name'] = $_FILES['takedown-file2']['name'];
	$datatemp = file_get_contents($_FILES['takedown-file2']['tmp_name']);
	$datatemp = base64_encode($datatemp);
	$uri = 'data: '. $_FILES['takedown-file2']['type'].';base64,'.$datatemp;
	$CE_post_files_tmp2['file'] = $uri;
	$CE_post_files[] = $CE_post_files_tmp2;
}


// Set up initial post data for Chilling Effects
$CE_post_data = array (
	'authentication_token' => $takedown_config['CE_apikey'],
	'notice' => array (
		'title' => $_POST['takedown-title'],
		'type' => $_POST['ce-report-type'],
		'subject' => $_POST['takedown-subject'],
		'date_sent' => $_POST['takedown-date'],
		'source' => $_POST['takedown-method'],
		'action_taken' => $_POST['action-taken'],
		'body' => $_POST['takedown-body'],
		'tag_list' => 'wikipedia, wikimedia',
		'jurisdiction_list' => 'US, CA',
		),
	);

$CE_post_entities = array (
	array (
		'name' => 'recipient',
		'entity_attributes' => $takedown_config['CE_recipient'],
		),
	array (
		'name' => 'sender',
		'entity_attributes' => array (
			'name' => $_POST['sender-name'],
			'address_line_1' => $_POST['sender-address1'],
			'address_line_2' => $_POST['sender-address2'],
			'city' => $_POST['sender-city'],
			'state' => $_POST['sender-state'],
			'zip' => $_POST['sender-zip'],
			),
		),
	);

$CE_post_data['notice']['entity_notice_roles_attributes'] = $CE_post_entities;

$CE_post_works[] = array (
	'infringing_urls_attributes' => $linksarray,
	);

$CE_post_data['notice']['works_attributes'] = $CE_post_works;

if (!empty($CE_post_files)) {
	$CE_post_data['notice']['file_uploads_attributes'] = $CE_post_files;
}

$CE_post = json_encode($CE_post_data);

// Set up headers for Chilling Effects submission
$CE_post_headers = array (
	'Accept: application/json',
	'Content-Type: application/json',
	'Content-Length: ' . strlen($CE_post),
	'AUTHENTICATION_TOKEN: '.$takedown_config['CE_apikey'],
	);

/*
$ch = curl_init($takedown_config['CE_apiurl']);
$f = fopen('request.txt', 'w');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $CE_post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $CE_post_headers);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $f);

$result = curl_exec($ch);
fclose($f);
curl_close($ch);
*/

?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>DMCA Takedowns</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/jquery-1.10.2.min.js'></script>
	<script src='scripts/moment.min.js'></script>
	<script src='scripts/pikaday.js'></script>
	<script src='scripts/pikaday.jquery.js'></script>
	<style type='text/css'>
	<!--/* <![CDATA[ */
	@import 'css/main.css'; 
	@import 'css/pikaday.css';
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
				<h1>Processed Takedown</h1>
				<br />
				<fieldset>
					<legend> wmfWiki post </legend>
					<table>
						<tr>
							<td>
								Please post the below text to <?php echo "<a target='_blank' href='https://www.wikimediafoundation.org/wiki/".htmlentities($_POST["takedown-wmf-title"])."?action=edit'>https://www.wikimediafoundation.org/wiki/".htmlentities($_POST["takedown-wmf-title"])."</a>"?>
							</td>
						</tr>
						<tr>
							<td>
								<textarea name='takedown-body-wmf' wrap='virtual' rows='18' cols='90'><?php 
								echo "<div class='mw-code' style='white-space: pre; word-wrap: break-word; ''><nowiki>".PHP_EOL.
								$_POST["takedown-body"].PHP_EOL.
								"</nowiki></div>".PHP_EOL.
								"[[Category:DMCA ".date("Y")."]]";?>
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
								Please post the below text to the Wikimedia Commons DMCA Board at <a target='_blank' href='https://commons.wikimedia.org/wiki/Commons:DMCA?action=edit&amp;section=new'>https://commons.wikimedia.org/wiki/Commons:DMCA</a>
							</td>
						</tr>
						<tr>
							<td>
								<textarea name='commons-dmca-post' wrap='virtual' rows='18' cols='90'><?php
								echo "=== ".$_POST["takedown-commons-title"]." ===".PHP_EOL.PHP_EOL.
								"{{subst:DMCA_takedown_notice|".$_POST["takedown-commons-title"].
								(!empty($_POST["takedown-wmf-title"]) ? "|".$_POST["takedown-wmf-title"] : "").
								(array_key_exists(0,$filearray) ? "|".$filearray[0] : "").
								(array_key_exists(1,$filearray) ? "|".$filearray[1] : "").
								(array_key_exists(2,$filearray) ? "|".$filearray[2] : "").
								(array_key_exists(3,$filearray) ? "|".$filearray[3] : "").
								(array_key_exists(4,$filearray) ? "|".$filearray[4] : "").
								"}}"?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Please post the below text to the Wikimedia Commons Village Pump at <a target='_blank' href='https://commons.wikimedia.org/wiki/Commons:Village_pump?action=edit&amp;section=new'>https://commons.wikimedia.org/wiki/Commons:Village_pump</a>
							</td>
						</tr>
						<tr>
							<td>
								<textarea name='commons-dmca-post' wrap='virtual' rows='18' cols='90'><?php
								echo "{{subst:DMCA_takedown_notice|".$_POST["takedown-commons-title"].
								(!empty($_POST["takedown-wmf-title"]) ? "|".$_POST["takedown-wmf-title"] : "").
								(array_key_exists(0,$filearray) ? "|".$filearray[0] : "").
								(array_key_exists(1,$filearray) ? "|".$filearray[1] : "").
								(array_key_exists(2,$filearray) ? "|".$filearray[2] : "").
								(array_key_exists(3,$filearray) ? "|".$filearray[3] : "").
								(array_key_exists(4,$filearray) ? "|".$filearray[4] : "").
								"}}"?></textarea>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>Debugging and double checking information for James</legend>
					<table>
						<tr>
							<td>
								Files affected (if given)
							</td>
							<td>
								<?php echo (!empty($_POST["files-affected"]) ? $_POST["files-affected"] : "") ?>
							</td>
						</tr>
						<tr>
							<td>
								Var dump of filearray
							</td>
							<td>
								<?php echo (!empty($filearray) ? var_dump($filearray) : "")?>
							</td>
						</tr>
						<tr>
							<td>
								print of php array for CE
							</td>
							<td>
								<?php echo var_dump($CE_post_data);?>
							</td>
						</tr>
						<tr>
							<td>
								print of json for CE
							</td>
							<td>
								<?php echo json_encode($CE_post_data, JSON_PRETTY_PRINT);?>
							</td>
						</tr>
						<tr>
							<td>
								Print of response from CE
							</td>
							<td>
								<?php echo print_r($result);?>
					</table>
				</fieldset>
			</div>
		</div>
		<div id='column-one'>
			<div class='portlet' id='p-logo' role='banner'>
				<a href='legalTakedown.html' title='Back to form'></a>
			</div>
				<div class='no-text-transform portlet' id='p-navigation' role='navigation'>
					<h3>LCA links</h3>
					<div class='pBody'>
						<ul>
							<li id='lca-central-link'>
								<a href='https://sites.google.com/a/wikimedia.org/lca-central/'>LCA Central</a>
							</li>
							<li id='officewiki-link'>
								<a href='https://office.wikimedia.org/wiki/Main_Page'>Office Wiki</a>
							</li>
							<li id='collabwiki-link'>
								<a href='https://collab.wikimedia.org/wiki/Main_Page'>Collab Wiki</a>
							</li>
						</ul>
					</div>
				</div>
				<div class='no-text-transform portlet' id='p-dmcalinks' role='navigation'>
					<h3>DMCA related links</h3>
					<div class='pBody'>
						<ul>
							<li id='comdmca-link'>
								<a href='https://commons.wikimedia.org/wiki/COM:DMCA'>Commons DMCA Page</a>
							</li>
							<li id='comvp-link'>
								<a href='https://commons.wikimedia.org/wiki/Commons:Village_pump'>Commons Village Pump</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>