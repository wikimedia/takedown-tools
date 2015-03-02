<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2015-03-01

Quick and Dirty tool to grab 2nd level sections (feedback) from 2015 Strategy Consultation

---------------------------------------------   */

require_once dirname( __FILE__ ) . '/../include/multiuseFunctions.php';
require_once dirname( __FILE__ ) . '/../include/OAuth.php';
require_once dirname( __FILE__ ) . '/../include/MWOAuthSignatureMethod.php';
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

if ( empty( $secretKey ) ) {
	die( 'You do not seem to have the required RSA Private key in the configs folder, please alert your nearest developer and tell them to get their shit together' );
}

$originalapiurl = 'https://meta.wikimedia.org'.'/w/api.php';
$usertable = getUserData( $user );
$mwsecret = $usertable['mwsecret'];
$mwtoken = $usertable['mwtoken'];

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
				<h1>2015 Strategy Consultation (Pull)</h1>
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
							<td> <label for='page'> page title: </label>
							<td>
								<input id='page' name='page' size='30' type='td' value=''>
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
			<?php include dirname( __FILE__ ) . '/../include/lcapage.php'; ?>
	</div>
	<?php
flush();
if ( isset( $usertable['mwtoken'] ) && isset( $_POST['page'] ) ) {
	$page = $_POST['page'];
	$accessToken = new OAuthToken( $mwtoken, $mwsecret );
	$request = array(
		'action' => 'parse',
		'page' => $page,
		'prop' => 'sections',
		'format' => 'json',
	);

	$consumer = new OAuthConsumer( $consumerKey, $secretKey );
	$signer = new MWOAuthSignatureMethod_RSA_SHA1( new OAuthDataStore(), $secretKey );

	$api_req = OAuthRequest::from_consumer_and_token( $consumer, $accessToken, "POST", $originalapiurl, $request );
	$api_req->sign_request( $signer, $consumer, $accessToken );

	$ch = curl_init();

	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_URL, $originalapiurl );
	curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request ) );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $api_req->to_header() ) );
	$jsonresponse = curl_exec( $ch );
	if ( !$jsonresponse ) {
		echo json_encode( 'Curl error: ' . htmlspecialchars( curl_error( $ch ) ) );
	}

	$response = json_decode( $jsonresponse, true );
	$topull = array();


	foreach ( $response['parse']['sections'] as $section ) {
		if ( $section['level'] == '2' ) {
			$topull[] = $section['index'];
		}
	}

	echo '<script> $("#results").append("<tr><th>Section</th><th>User</th><th>Country</th><th>HomeWiki</th><th>Global edit count</th><th>Meta registration</th><th>Meta edit count</><th>Comment</th></tr>");</script>';

	//set up for the loop
	$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
	$mysql->set_charset( "utf8" );

	// hack to make sure I don't delete everything in the loop for now
	reset($topull);
	$first = key($topull);
	foreach ( $topull as $mainkey => $section ) {
		$homewiki = 'Unknown';
		$metaeditcount = 'Unknown';
		$metaregistration = 'Unknown';
		$globaleditcount = 'Unknown';

		$request = array(
			'action' => 'query',
			'titles' => $page,
			'prop' => 'revisions',
			'rvprop' => 'content',
			'continue' => '',
			'rvsection' => $section,
			'format' => 'json',
		);

		$api_req = OAuthRequest::from_consumer_and_token( $consumer, $accessToken, "POST", $originalapiurl, $request );
		$api_req->sign_request( $signer, $consumer, $accessToken );

		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_URL, $originalapiurl );
		curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request ) );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $api_req->to_header() ) );
		$jsonresponse = curl_exec( $ch );
		if ( !$jsonresponse ) {
			echo json_encode( 'Curl error: ' . htmlspecialchars( curl_error( $ch ) ) );
		}

		$response = json_decode( $jsonresponse, true );

		//grr this shit is far too buried in the response

		foreach ($response['query']['pages'] as $key => $value) {
			$data = $value;
		}

		$rawwikitext = $data['revisions']['0']['*'];
		//store in DB
		$pageid = $data['pageid'];

		$commentator = get_string_between( $rawwikitext, '<small> Response by [[Special:Contributions/', '|');

		if ( $commentator == '' ) {
			$commentator = 'Unknown';
		}

		if ( filter_var( $commentator, FILTER_VALIDATE_IP ) ) {
			$geodata = json_decode( @file_get_contents( $geolocateapi.$commentator ), true );

			if ( array_key_exists( 'country', $geodata ) ) {
				$country = $geodata['country'];
			} else {
				$country = $geodata['country_code'];
			}
		} else {
			$country = 'Logged In User';
		}

		if ( !filter_var( $commentator, FILTER_VALIDATE_IP ) && $commentator != 'Unknown' ) {
			$request = array(
				'action' => 'query',
				'list' => 'users|usercontribs',
				'ususers' => $commentator,
				'usprop' => 'editcount|groups|registration',
				'ucuser' => $commentator,
				'ucdir' => 'newer',
				'uclimit' => '1',
				'ucprop' => 'timestamp',
				'meta' => 'globaluserinfo',
				'guiuser' => $commentator,
				'guiprop' => 'editcount|groups|merged',
				'continue' => '',
				'format' => 'json',
			); 

			$api_req = OAuthRequest::from_consumer_and_token( $consumer, $accessToken, "POST", $originalapiurl, $request );
			$api_req->sign_request( $signer, $consumer, $accessToken );

			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_URL, $originalapiurl );
			curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request ) );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $api_req->to_header() ) );
			$jsonresponse = curl_exec( $ch );
			if ( !$jsonresponse ) {
				echo json_encode( 'Curl error: ' . htmlspecialchars( curl_error( $ch ) ) );
			}

			$userdata = json_decode( $jsonresponse, true )['query'];

			$localdata = $userdata['users'][0];
			$globaldata = $userdata['globaluserinfo'];

			if ( !array_key_exists( 'missing', $localdata ) && !array_key_exists( 'invalid', $localdata ) ) {
				$metaeditcount = $localdata['editcount'];
				$metaregistration = $localdata['registration'];
			}

			if ( array_key_exists( 'merged', $globaldata ) ) {
				$globaleditcount = $globaldata['editcount'];

				if ( array_key_exists( 'home', $globaldata ) ) {
					$homewiki = $globaldata['home'];
				}
			}

		}

		$insertemplate = 'INSERT INTO strategycomments (page,user,country,homewiki,globaledits,metaedits,metaregistration,comment,timestamp) VALUES (?,?,?,?,?,?,?,?,?)';
		$submittime = gmdate( "Y-m-d H:i:s", time() );

		//purge any data from this page already stored for now

		if ($mainkey === $first) {
			$purgesql = 'DELETE FROM strategycomments WHERE page='.$pageid;
			$mysql->query( $purgesql );
		}

		$insert = $mysql->prepare( $insertemplate );
		if ( $insert === false ) {
			echo "WARNING: DATA NOT SAVED";
			die();
		}
		flush();

		$insert->bind_param( 'issssssss', $pageid, $commentator, $country, $homewiki, $globaleditcount, $metaeditcount, $metaregistration, $rawwikitext, $submittime );
		$insert->execute();

		echo '<script> $("#results").append("<tr><td>'.$section.'</td><td>'.$commentator.'</td><td>'.$country.'</td><td>'.$homewiki.'</td><td>'.$globaleditcount.'</td><td>'.$metaregistration.'</td><td>'.$metaeditcount.'</td><td>'.htmlspecialchars( json_encode( $rawwikitext)  ).'</td> </tr>");</script>';
		flush();

	}
	$mysql->close();
}

?>
</body>
</html>