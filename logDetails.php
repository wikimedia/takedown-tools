<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)
			
Date of creation : 2014-01-02

Show details of submitted entries (accessed by clicking on log title)
			
---------------------------------------------   */

include_once('multiuseFunctions.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$config = parse_ini_file('lcaToolsConfig.ini');

//Details available variable set to false (no details available) by default
$detailsavailable = false;

// What log entry are we looking up?
$drillto = (!empty($_GET['logid'])) ? intval($_GET['logid']) : null;

if ($drillto) {
	$mysql = new mysqli($dbaddress,$dbuser,$dbpw,$db);
	$mysql->set_charset("utf8");

	$initialLookup = 'SELECT id,type,title FROM centrallog WHERE id=?';

	$logLookup = $mysql->prepare($initialLookup);
		if ($logLookup === false) {
		echo 'Error while preparing: ' . $initialLookup . ' Error text: ' . $mysql->error, E_USER_ERROR;
		}

	$logLookup->bind_param('i',$drillto);
	$logLookup->execute();

	$logLookup->bind_result($id,$type,$title);

	while ($logLookup->fetch()) {
		$logData[] = array ('id' => $id,'type' => $type,'title' => $title);
	}

	if ($logData[0]['type']) {
		$logType = $logData[0]['type'];
	} else {$logType = null;}

	if ($logType = 'Release') {
		$detailLookup = 'SELECT * FROM basicrelease WHERE log_id='.$drillto;
		$detailResults = $mysql->query($detailLookup);
			if ($detailResults === false) {
			echo 'Error while querying: ' . $detailResults . ' Error text: ' . $mysql->error, E_USER_ERROR;
			}

			if ($detailResults->num_rows > 0) {
				$logDetails = $detailResults->fetch_assoc();
				$detailsavailable = true;

				$who_received = unserialize(stripslashes($logDetails['who_received']));
				$pre_approved = $logDetails['pre_approved'];
				$why_released = unserialize(stripslashes($logDetails['why_released']));
				$who_released = $logDetails['who_released'];
				$who_released_to = $logDetails['who_released_to'];
				$released_to_contact = $logDetails['released_to_contact'];
				$details = $logDetails['details'];
				$user = $logDetails['user'];
				$timestamp = $logDetails['timestamp'];
				$istest = $logDetails['test'];
			}
	}

	if ($logType = 'DMCA') {
		$detailLookup = 'SELECT * FROM dmcatakedowns WHERE log_id='.$drillto;
		$detailResults = $mysql->query($detailLookup);

	}

	$mysql->close();
}




?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>Release of Confidential Information</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/jquery-1.10.2.min.js'></script>
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
				<h1>Log Details</h1>
				<?php if (!$detailsavailable) {
					echo 'The details for the log entry that you clicked are not currently available, this could be for many reasons including:
					<ul>
					<li> You clicked on a very early log entry before data was saved for that log type. </li>
					<li> You visisted this page without actually clicking on a log entry (do you see a ?log=# piece of the url? where # is a number?).</li>
					<li> Logging for this type is not yet implemented. </li>
					<li> There has been a mysql error and we were unable to get the log details (if this is the case you likely see another error on top of of the page).</li>
					<li> The programmer screwed something up, in which case you should go dock him over the head (after verifying he screwed up) and/or give him booze. </li>
					</ul>
					<p> You can get back to the log by clicking <a href="centralLog.php">HERE</a>';
				} elseif ($logType='Release') {
					include('include/releaseDetail.php');
				} else {
					echo var_dump($logDetails);
				}

				?>
				</div>
			</div>
			<?php include('include/lcapage.php'); ?>
		</div>
	</body>
</html>