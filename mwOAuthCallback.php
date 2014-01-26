<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2014-01-16

Mediawiki oAuth registration page. Callback page

Huge help from example cli file by Chris Steipp https://www.mediawiki.org/wiki/OAuth/For_Developers#PHP_demo_cli_client_with_RSA_keys

---------------------------------------------   */
session_start();
require_once 'include/multiuseFunctions.php';
require_once 'include/OAuth.php';
require_once 'include/MWOAuthSignatureMethod.php';
require_once 'include/JWT.php';
date_default_timezone_set( 'UTC' );

// cast config and log variables
$config = parse_ini_file( 'lcaToolsConfig.ini' );
$user = $_SERVER['PHP_AUTH_USER'];
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
$consumerKey = $config['mwconsumer_key'];
$secretKey = file_get_contents( 'lcatoolskey.pem' );

if ( empty( $secretKey ) ) {
	die( 'You do not seem to have the required RSA Private key in the main app folder, please alert your nearest developer and tell them to get their shit together' );
}

$oauthurl = makehttps( $config['mw_oauthserver'] ).'/wiki/Special:OAuth';
$access_url = $oauthurl . '/token?format=json';
$identify_url = $oauthurl . '/identify';
$apiurl = makehttps( $config['mw_oauthserver'] ).'/w/api.php';
$server = $config['mw_oauthserver'];

?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>DMCA Takedowns</title>
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
				<h1>Wikimedia OAuth Registration</h1>
				<br />
				<table border='1' id='mw-movepage-table' style='font-weight:bold;'>
					<tr>
						<td colspan='4'>
							Welcome to the LCA Tools Mediawiki OAuth setup page. Let's get you registered!
                    <tr>
                        <td >
                            <u>Step 1:</u> <br /> Temporary OAuth verification code and token received <br /> but not yet verified.
                        </td>
                        <td >
                            <img id='tempreceived' src='images/List-remove.svg' width='40px'/>
                        </td>
                        <td >
                            <u> Step 4:</u> <br /> Basic API request done as test.
                        </td>
                        <td >
                            <img id='basictest' src='images/List-remove.svg' width='40px'/>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <u>Step 2:</u> <br /> Cookie session set.
                        </td>
                        <td>
                            <img id='session' src='images/List-remove.svg' width='40px'/>
                        </td>
                        <td >
                            <u>Step 5:</u> <br /> Signed identity recieved, we now know who you are and where (on wiki) you live.
                        </td>
                        <td>
                            <img id='signedid' src='images/List-remove.svg' width='40px'/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <u>Step 3:</u> <br /> Permenant access token requested and received.
                        </td>
                        <td>
                            <img id='permrequest' src='images/List-remove.svg' width='40px'/>
                        </td>
                         <td >
                            <u>Step 6:</u> <br /> Information saved
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
                <fieldset>
                	<legend>Verification information</legend>
                	<textarea id='insecureinfo' wrap='virtual' rows='18' cols='90'></textarea>


                	<textarea id='secureinfo' wrap='virtual' rows='18' cols='90'></textarea>

                </fieldset>
                <fieldset>
                	<legend>debug info</legend>
                	<textarea id='tokenresponse'></textarea>

                </fieldset>
			</div>
		</div>
			<?php include 'include/lcapage.php'; ?>
	</div>
	<?php

if ( !empty( $_GET['oauth_verifier'] ) & !empty( $_GET['oauth_token'] ) ) {
	$temptoken = $_GET['oauth_token'];
	$verifier = $_GET['oauth_verifier'];
	echo "<script> $('#tempreceived').attr('src', 'images/Dialog-accept.svg'); </script>".PHP_EOL;
} else {
	echo "<script> $('#tempreceived').attr('src', 'images/Dialog-error-round.svg'); </script>".PHP_EOL;
	echo "<script> $('#result').html('<span style=\'color:red\'>There does not appear to be the required oauth attributes in the query string. Did you get here by accident?</span>'); </script>".PHP_EOL;
	die();
}
flush();

if ( isset( $_SESSION['TempTokenKey'] ) & isset( $_SESSION['TempTokenSecret'] ) ) {
	if ( $_SESSION['TempTokenKey'] === $temptoken ) {
		$tempsecret = $_SESSION['TempTokenSecret'];
		echo "<script> $('#session').attr('src', 'images/Dialog-accept.svg'); </script>".PHP_EOL;
	} else {
		echo "<script> $('#session').attr('src', 'images/Dialog-error-round.svg'); </script>".PHP_EOL;
		echo "<script> $('#result').html('<span style=\'color:red\'> The token passed back by OAuth was not the same as the token in your session, for your security the authorization process has stopped. Please try again by going [here] or contact James. </span>');</script>".PHP_EOL;
		die();
	}
} else {
	echo "<script> $('#session').attr('src', 'images/Dialog-error-round.svg'); </script>".PHP_EOL;
	echo "<script> $('#result').html('<span style=\'color:red\'> You do not appear to have a session set, which is required to verify that the person coming here is the same person who started the process (and logged in) and that this isn\'t someone trying to steal your access information. Please make sure you are accepting cookies and try again by going [here]. If you have any problems please contact James. </span>');</script>".PHP_EOL;
	die();
}
flush();

$consumer = new OAuthConsumer( $consumerKey, $secretKey );
$signer = new MWOAuthSignatureMethod_RSA_SHA1( new OAuthDataStore(), $secretKey );

$accessrequest = new OAuthToken( $temptoken, $tempsecret );
$parsed = parse_url( $access_url );
parse_str( $parsed['query'], $params );
$params['oauth_verifier'] = $verifier;
$params['title'] = 'Special:OAuth/token';

$acc_req = OAuthRequest::from_consumer_and_token( $consumer, $accessrequest, "GET", $access_url, $params );
$acc_req->sign_request( $signer, $consumer, $accessrequest );

$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, $access_url );
curl_setopt( $ch, CURLOPT_HEADER, 0 );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $acc_req->to_header() ) );
$data = curl_exec( $ch );
if ( !$data ) {
	echo 'Curl error: ' . curl_error( $ch );
	echo "<script> $('#permrequest').attr('src', 'images/Dialog-error-round.svg'); </script>".PHP_EOL;
	echo "<script> $('#result').html('<span style=\'color:red\'>We appear to have had an issue requesting your permenant authorization data, either because of a problem on our side or the wiki side. Please try again or contact James. </span>'); </script>".PHP_EOL;
	die();
}

echo "<script> $('#permrequest').attr('src', 'images/Dialog-accept.svg'); </script>".PHP_EOL;
echo "<script> $('#tokenresponse').val(".$data."); </script>".PHP_EOL;
flush();
$acc = json_decode( $data );
$accessToken = new OAuthToken( $acc->key, $acc->secret );
$apiParams = array(
	'action' => 'query',
	'meta' => 'userinfo',
	'uiprop' => 'rights',
	'format' => 'json',
);

$api_req = OAuthRequest::from_consumer_and_token( $consumer, $accessToken, "GET", $apiurl, $apiParams );
$api_req->sign_request( $signer, $consumer, $accessToken );

$basicid = mwOAuthAPIcall( $apiurl, $apiParams, $api_req );

if ( $basicid ) {
	echo "<script> $('#basictest').attr('src', 'images/Dialog-accept.svg'); </script>".PHP_EOL;
	echo "<script> $('#insecureinfo').val('".$basicid."'); </script>".PHP_EOL;
} else {
	echo "<script> $('#basictest').attr('src', 'images/Dialog-error-round.svg'); </script>".PHP_EOL;
	echo "<script> $('#result').html('<span style=\'color:red\'>We appear to have had an issue actually using your data to do a request to Meta, this may be because of an issue on our side or on the wiki side. Please try again or contact James. </span>'); </script>".PHP_EOL;
	die();
}
flush();

$consumer_secret = $config['mwconsumer_secret'];

$signedParams = array(
	'title' => 'Special:OAuth/identify'
);

$request = OAuthRequest::from_consumer_and_token( $consumer, $accessToken, "GET", $identify_url, $signedParams );
$request->sign_request( $signer, $consumer, $accessToken );

unset( $ch );
$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, $identify_url );
curl_setopt( $ch, CURLOPT_HEADER, 0 );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $request->to_header() ) );
$data = curl_exec( $ch );
if ( !$data ) {
	echo 'Curl error: ' . htmlspecialchars( curl_error( $ch ) );
}

$identity = JWT::decode( $data, $consumer_secret );

if ( !validateJWT( $identity, $consumerKey, $request->get_parameter( 'oauth_nonce' ), $server ) ) {
	echo "<script> $('#signedid').attr('src', 'images/Dialog-error-round.svg'); </script>".PHP_EOL;
	echo "<script> $('#secureinfo').val('The JWT did not validate'); </script>".PHP_EOL;
	echo "<script> $('#result').html('<span style=\'color:red\'>We appear to have had an issue actually using your data to do a request to Meta, this may be because of an issue on our side or on the wiki side. Please try again or contact James. </span>'); </script>".PHP_EOL;
	die();
} else {
	echo "<script> $('#signedid').attr('src', 'images/Dialog-accept.svg'); </script>".PHP_EOL;
	$announcement = json_encode( 'We got a valid JWT, describing the user as:'.PHP_EOL.'* Username: '.$identity->username.PHP_EOL.' * User\'s current groups: '. implode( ',', $identity->groups ) .PHP_EOL.' * User\'s current rights: ' . implode( ',', $identity->rights ) . PHP_EOL );
	echo "<script> $('#secureinfo').val(".$announcement."); </script>";
}
flush();

$insertemplate = 'INSERT INTO user (user,mwtoken,mwsecret,wiki_user,registration_time) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE mwtoken = VALUES(mwtoken), mwsecret = VALUES(mwsecret), wiki_user = VALUES(wiki_user), registration_time = VALUES(registration_time)';
$submittime = gmdate( "Y-m-d H:i:s", time() );

$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
$mysql->set_charset( "utf8" );
$wiki_user = $identity->username;
$mwtoken = $acc->key;
$mwsecret = $acc->secret;

$insert = $mysql->prepare( $insertemplate );
if ( $insert === false ) {
	echo "<script> $('#logged').attr('src', 'images/Dialog-error-round.svg'); </script>".PHP_EOL;
	echo 'Error while preparing: ' . $insertemplate . ' Error text: ' . $mysql->error, E_USER_ERROR;
	echo "<script> $('#result').html('<span style=\'color:red\'>We appear to have had an issue recording your data, please try again later or contact James. </span>'); </script>".PHP_EOL;
	die();

}
flush();

$insert->bind_param( 'sssss', $user, $mwtoken, $mwsecret, $wiki_user, $submittime );

$insert->execute();

// $log->insert_id;
$mysql->close();
echo "<script> $('#logged').attr('src', 'images/Dialog-accept.svg'); </script>".PHP_EOL;
echo "<script> $('#result').html('<span style=\'color:green\'>CONGRATS! You have successfully registered with the LCA Tools Wiki Editing Program. </span>'); </script>".PHP_EOL;
flush();



?>
</body>
</html>
