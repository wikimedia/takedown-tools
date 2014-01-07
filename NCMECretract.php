<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)
			
Date of creation : 2014-01-06

NCMEC reporting form for Child Protection takedowns
			
---------------------------------------------   */

include_once('multiuseFunctions.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

// cast config and log variables
$config = parse_ini_file('lcaToolsConfig.ini');
$user = $_SERVER['PHP_AUTH_USER'];


?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>Retract NCMEC Submission</title>
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
				<h1>Retract NCMEC Submission</h1>
				<fieldset>
					<legend>Report ID</legend>
					<form id='reportit' method='POST'>
					<table>
						<tr>
							<td>
								<label for='reportid'>Report ID to Retract </label>
							</td>
						</tr>
						<tr>
							<td>
								<input type='text' size='10' id='reportid' name='reportid' /> <input type='submit' value='Retract ID' />
							</td>
						</tr>
					</table>
				</form>
				</fieldset>
				<fieldset>
					<legend> Processing </legend>
					<textarea name='reportxml' wrap='virtual' rows='18' cols='70'><?php if (!empty($_POST['reportid'])) {
						echo 'Report ID: '.$_POST['reportid'].' detected for retraction'.PHP_EOL.PHP_EOL;
						$postdata = array('id'=>$_POST['reportid']);
						$url = $config['NCMEC_URL'].'retract';
						$result = NCMECsimpleauthdcurlPost($url,$postdata);
						echo $result; } else {echo 'No reportID detected in post';}
						?></textarea>
				</fieldset>


			</div>
		</div>
			<?php include('include/lcapage.php'); ?>
	</div>
</body>
</html>