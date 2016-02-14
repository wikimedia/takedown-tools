<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2016-02-07

Quick and Dirty tool to grab editors comments, process them and store the data for the January 2016 Strategy process

---------------------------------------------   */

require_once 'strategy2016.class.php';
date_default_timezone_set( 'UTC' );
ini_set('max_execution_time', 300);
mb_internal_encoding( 'UTF-8' );

// cast config and log variables
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
//$user = $_SERVER['PHP_AUTH_USER'];
$user ='jalexander';
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
$consumerKey = $config['mwconsumer_key'];
$secretKey = file_get_contents( dirname( __FILE__ ) . '/../configs/lcatoolskey.pem' );
$useragent = $config['useragent'];
$geolocateapi = $config['geolocateapi'];
$geolocatekey = $config['geolocatekey'];

if ( empty( $secretKey ) ) {
	die( 'You do not seem to have the required RSA Private key in the configs folder, please alert your nearest developer and tell them to get their shit together' );
}

$mwapiurl = 'https://meta.wikimedia.org'.'/w/api.php';
$usertable = getUserData( $user );
$mwsecret = $usertable['mwsecret'];
$mwtoken = $usertable['mwtoken'];

$strategy = new strategy2016( $dbaddress, $dbuser, $dbpw, $db, $mwapiurl, $consumerKey, $secretKey, $mwtoken, $mwsecret, $geolocateapi, $geolocatekey)

?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>Pull and stash consultation comments</title>
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
				<h1>2016 Strategy Consultation (Pull)</h1>
				<br />
			<?php if ( !isset( $_POST['page'] ) ) { ?>
				<fieldset>
					<legend>What page do you want to grab sections from?</legend>
					This form is designed to look for 2nd level sections and store those as individual users comments for display and analysis later. <br />
					Please make sure to put the page name you want to pull and use the lowest level page (use /Day 1 not the base page). <br />
					<b> Note: This currently assumes you are asking for a page on meta </b>
					<form id='inputform' method='POST'>
					<table>
						<tr>
							<td><?php
if ( $usertable['mwtoken'] ) {
	//do nothing
} else {
	echo 'Did not find user OAuth information, please register using the link on the sidebar'.'<script> $("#page").attr("readonly", true);</script>';
}?>
							</td>
						</tr>
						<tr>
							<td> <label for='page'> page title: </label> </td>
							<td>
								<input id='page' name='page' size='30' type='td' value=''>
							</td>
						</tr>
						<tr>
							<td> <label for='type'> What type of comment page is this? </label> </td>
							<td>
								<select name='type'>
									<option value='reach'>Reach</option>
									<option value='communities'>Communities</option>
									<option value='knowledge'>Knowledge</option>
									<option value='general'>General</option>
								</select>
							</td>
						</tr>
						<tr>
							<td> <input type='submit' value='Get text' />
						</tr>
					</table>
				</fieldset>
			<?php } else { ?>
				<fieldset>
					<legend> Results: </legend>
					<?php
if ( $usertable['mwtoken'] ) {
	//do nothing
} else {
	echo '<table><tr><td style="color:red;">Did not find user OAuth information, please register using the link on the sidebar</td></tr></table>';
}?>
					<table border='1' id='results'></table>
				</fieldset>
			<?php } ?>

				</div>
		</div>
			<?php include dirname( __FILE__ ) . '/../project-include/page.php'; ?>
	</div>
	<?php
flush();
if ( isset( $usertable['mwtoken'] ) && isset( $_POST['page'] ) ) {
	$page = $_POST['page'];
	$type = mb_strtolower( $_POST['type'] );

	$topull = $strategy->getPageSections($page);

	echo '<script> $("#results").append("<tr><th>Section</th><th>User</th><th>Country</th><th>Home Wiki</th><th>HomeWiki registration</th><th>Global edit count</th><th>Meta registration</th><th>Meta edit count</><th>Page Comment</th></tr>");</script>';

	//set up for the loop
	// hack to make sure I don't delete everything in the loop for now
	reset($topull);

	foreach ( $topull as $mainkey => $section ) {

		$rawwikitext = $strategy->getSectionText( $page, $section );

		$commentator = get_string_between( $rawwikitext, '<small> Response by [[Special:Contributions/', '|');

		if ( $commentator == '' ) {
			$commentator = 'Unknown';
			$strategy->newUserFromUsername( $commentator );
		} else {
			$userObject = $strategy->newUserFromUsername( $commentator, true);
		}

		$strategy->insertMetaData( $userObject );
		$userObject = $strategy->setComments( $rawwikitext, $userObject, $type);
		$strategy->insertComments( $userObject, $type ); 

		$country = ( $userObject->isip ) ? $userObject->country : 'Logged in user';
		$homewiki = ( $userObject->homewiki ) ? $userObject->homewiki : 'Unknown';
		$metaeditcount = ( $userObject->metaedits ) ? $userObject->metaedits : 'Unknown';
		$globaleditcount = ( $userObject->globaledits ) ? $userObject->globaledits : 'Unknown';
		$metaregistration = ( $userObject->metaregistration ) ? $userObject->metaregistration : 'Unknown';
		$homeregistration = ( $userObject->homeregistration ) ? $userObject->homeregistration : 'Unknown';


		echo '<script> $("#results").append("<tr><td>'.$section.'</td><td>'.$userObject->username.'</td><td>'.$country.'</td><td>'.$homewiki.'</td><td>'.$homeregistration.'</td><td>'.$globaleditcount.'</td><td>'.$metaregistration.'</td><td>'.$metaeditcount.'</td><td>'.htmlspecialchars( json_encode( $rawwikitext)  ).'</td> </tr>");</script>';
		flush();

	}
}

?>
</body>
</html>