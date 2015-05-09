<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2014-01-14

Mediawiki oAuth registration page. start of process.

Huge help from example cli file by Chris Steipp https://www.mediawiki.org/wiki/OAuth/For_Developers#PHP_demo_cli_client_with_RSA_keys

---------------------------------------------   */
require_once dirname( __FILE__ ) . '/../core-include/multiuseFunctions.php';
require_once dirname( __FILE__ ) . '/../core-include/OAuth.php';
require_once dirname( __FILE__ ) . '/../core-include/MWOAuthSignatureMethod.php';
date_default_timezone_set( 'UTC' );

// cast config and log variables
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$user = $_SERVER['PHP_AUTH_USER'];
$consumerKey = $config['mwconsumer_key'];
$secretKey = file_get_contents( dirname( __FILE__ ) . '/../configs/lcatoolskey.pem' );
$oauthurl = makehttps( $config['mw_oauthserver'] ).'/wiki/Special:OAuth';
$usertable['mwtoken'] = null;
if ( isset( $_GET['force'] ) ) {
	if ( $_GET['force'] === '1' ) {
		// do nothing
	} else {
		$usertable = getUserData( $user );
	}
} else {
	$usertable = getUserData( $user );
}

if ( $usertable['mwtoken'] ) {
	$username = $usertable['wiki_user'];
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>Mediawiki OAuth Registration</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='/scripts/jquery-1.10.2.min.js'></script>
	<script src='/scripts/jquery.validate.min.js'></script>
	<script src='/scripts/lca.js'></script>
	<script>
		$(document).ready(function(){

	    //validate
	    $("#forceregister").validate();
	}
	</script>
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
				<h1>OAuth Registration Warning</h1>
				<center><b><p> You appear to have a registration in the system already using <u>User:<?php echo $username; ?></u>. <br />
					You may be trying to register again to switch usernames, because something isn't working or to test the registration system. <br />
					Going through the process with the same username will not hurt anything but no reason to spend time or resources when you don't need to! </p></b></center><br />
					<form id='forceregister'>
						<table>
							<tr>
								<td>
									<label for='force'>Are you sure you want to continue?</label>
								</td>
								<td>
									<b>Yes:</b>&nbsp;&nbsp;<input type='checkbox' name='force' id='force' value='1' required='true'>
								</td>
								<td>
									<input type='submit' value='submit' >
								</td>
							</tr>
							<tr>
								<td colspan='3'>
									<a href='index.php'> Nope! I'm set take me away from this weird place </a>
								</td>
							</tr>
						</table>
					</form>
			</div>
	    </div>
	        <?php include dirname( __FILE__ ) . '/../project-include/page.php'; ?>
	</div>
</body>
</html>
<?php
	die();
}

if ( empty( $secretKey ) ) {
	die( 'You do not seem to have the required RSA Private key in the configs folder, please alert your nearest developer and tell them to get their shit together' );
}

$request_url = $oauthurl . '/initiate?format=json&oauth_callback=oob';

$consumer = new OAuthConsumer( $consumerKey, $secretKey );

$parsedurl = parse_url( $request_url );
$extraSignedParams = array();
parse_str( $parsedurl['query'], $extraParams );
$extraSignedParams['title'] = 'Special:OAuth/initiate';

$request = OAuthRequest::from_consumer_and_token( $consumer, NULL, "GET", $request_url, $extraSignedParams );

$signer = new MWOAuthSignatureMethod_RSA_SHA1( new OAuthDataStore(), $secretKey );
$request->sign_request( $signer, $consumer, NULL );

$data = noheaderstringget( $request );

$requestToken = json_decode( $data );


$redirectURL = $oauthurl . '/authorize';
$redirectquery = array(
	'oauth_token' => $requestToken->key,
	'oauth_consumer_key' => $consumerKey );
$redirectquery = http_build_query( $redirectquery );
$redirectURL .= '?'.$redirectquery;

// apparently I need this for later and since I'm using a different page... yay requiring cookies.
session_start();
$_SESSION['TempTokenKey'] = $requestToken->key;
$_SESSION['TempTokenSecret'] = $requestToken->secret;
session_write_close();

header( 'Location: '.$redirectURL );

?>
