<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2014-07-10

Quick and Dirty tool to show the text of a given page name on all wikis.

---------------------------------------------   */

require_once dirname( __FILE__ ) . '/../include/multiuseFunctions.php';
require_once dirname( __FILE__ ) . '/../include/OAuth.php';
require_once dirname( __FILE__ ) . '/../include/MWOAuthSignatureMethod.php';
date_default_timezone_set( 'UTC' );
ini_set('max_execution_time', 300);

// cast config and log variables
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$user = $_SERVER['PHP_AUTH_USER'];
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
$consumerKey = $config['mwconsumer_key'];
$secretKey = file_get_contents( dirname( __FILE__ ) . '/../configs/lcatoolskey.pem' );
$useragent = $config['useragent'];

if ( empty( $secretKey ) ) {
	die( 'You do not seem to have the required RSA Private key in the configs folder, please alert your nearest developer and tell them to get their shit together' );
}

$originalapiurl = makehttps( $config['mw_oauthserver'] ).'/w/api.php';
$usertable = getUserData( $user );
$mwsecret = $usertable['mwsecret'];
$mwtoken = $usertable['mwtoken'];

?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>Global Page Text (ALPHA)</title>
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
				<h1>Global Page Text (ALPHA)</h1>
				<br />
			<?php if ( !isset( $_POST['page'] ) ) : ?>
				<fieldset>
					<legend>What page do you want to look at? Please note this will search ALL wikis and may take time.</legend>
					<b> Note: You should <a href='https://meta.wikimedia.org/wiki/User:Krinkle/Tools/Global_SUL' target='_blank'> globally create </a> your accounts before you use this tool to avoid some random occasional bugs.</b>
					This form is designed to look for and display the text for a given page name on all wikis. Please include the english name space in the title.
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
			<?php else: ?>
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
			<?php endif; ?>

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
		'action' => 'sitematrix',
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
	$sites = array();

	foreach ( $response['sitematrix'] as $key => $langarray ) {
		if ( $key != 'count' && $key != 'specials' ) {
			foreach ( $langarray['site'] as $langkey => $sitearray ) {
					$sites[] = $sitearray;
				}
		}
	}

	foreach ( $response['sitematrix']['specials'] as $key => $sitearray) {
		$sites[] = $sitearray;
	}

	foreach ( $sites as $key => $sitearray ) {
		$apiurl = makehttps( $sitearray['url'] ).'/w/api.php';
		$siteurl = makehttps( $sitearray['url'] );
		$dbname = $sitearray['dbname'];
		echo '<script> $("#results").append("<tr><th colspan=\'2\'> <a href=\''.$siteurl.'\' target=\'_blank\'>'.$dbname.'</a></th></tr>");</script>';
		if ( array_key_exists('closed', $sitearray ) ) {
			echo '<script> $("#results").append("<tr><td colspan=\'2\' style=\'font-weight:bold;\'> Closed Wiki, Skipping </td</tr>");</script>';
			continue;
		} elseif ( array_key_exists( 'private', $sitearray ) ) {
			echo '<script> $("#results").append("<tr><td colspan=\'2\' style=\'font-weight:bold;\'> Private Wiki, Skipping </td</tr>");</script>';
			continue;
		} elseif ( array_key_exists( 'fishbowl' , $sitearray ) ) {
			echo '<script> $("#results").append("<tr><td colspan=\'2\' style=\'font-weight:bold;\'> Fishbowl Wiki, Skipping </td</tr>");</script>';
			continue;
		}

		$request = array(
		'action'  => 'query',
		'format'  => 'json',
		'titles'  => $page,
		'prop'    => 'revisions',
		'rvprop'  => 'content',
		'rvlimit' => '1', 
		);

		$api_req = OAuthRequest::from_consumer_and_token( $consumer, $accessToken, "POST", $apiurl, $request );
		$api_req->sign_request( $signer, $consumer, $accessToken );

		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_URL, $apiurl );
		curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request ) );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $api_req->to_header() ) );
		$jsonresponse = curl_exec( $ch );
		$response = json_decode( $jsonresponse, true );

		if ( $response ) {
		
				$pageresults = $response['query']['pages'];

				if ( is_array( $pageresults ) ) {
					foreach ($pageresults as $key => $result) {
						if ( $key !='-1' ) {
							$location = $siteurl.'/wiki/'.$result['title'];
							echo '<script> $("#results").append("<tr><td><a href=\''.htmlentities( $location, ENT_QUOTES ).'\' target=\'_blank\'>'.$result['title'].'</a></td><td>'.htmlentities( $result['revisions'][0]['*'], ENT_QUOTES ).'</td></tr>");</script>';
						} else {
							$location = $siteurl.'/wiki/'.$result['title'];
							echo '<script> $("#results").append("<tr><td><a href=\''.htmlentities( $location, ENT_QUOTES ).'\' target=\'_blank\'>'.$result['title'].'</a></td><td>No page found or mediawiki page set as default</td></tr>");</script>';
						}
						flush();
					}
				} else {
					echo '<script> $("#results").append("<tr><td colspan=\'2\' style=\'color:red;\'>There was an error with this search. If the wiki exists this appears it appears that you don\'t have an account on it. <br /> You may want to do a manual search on the wiki (click above) or re run this search after you have visited the wiki with the link above and seen your username in the top right corner. </td></tr>");</script>';
				}
		} else {
			echo '<script> $("#results").append("<tr><td colspan=\'2\' style=\'color:red;\'>There was an error with this search. If the wiki exists this appears it appears that you don\'t have an account on it. <br /> You may want to do a manual search on the wiki (click above) or re run this search after you have visited the wiki with the link above and seen your username in the top right corner. </td></tr>");</script>';
		}

	}
	echo '<script> $("#results").append("<tr><th colspan=\'2\' >DONE!</th></tr>");</script>';

} 
?>
</body>
</html>
