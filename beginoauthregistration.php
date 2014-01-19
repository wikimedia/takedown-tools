<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)
			
Date of creation : 2014-01-14

Mediawiki oAuth registration page. start of process.

Huge help from example cli file by Chris Steipp https://www.mediawiki.org/wiki/OAuth/For_Developers#PHP_demo_cli_client_with_RSA_keys
			
---------------------------------------------   */
require_once('include/multiuseFunctions.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

// cast config and log variables
$config = parse_ini_file('lcaToolsConfig.ini');
$user = $_SERVER['PHP_AUTH_USER'];
require_once('include/OAuth.php');
require_once('include/MWOAuthSignatureMethod.php');

$consumerKey = $config['mwconsumer_key'];

$secretKey = file_get_contents('lcatoolskey.pem');

if (empty($secretKey)) {
	die('You do not seem to have the required RSA Private key in the main app folder, please alert your nearest developer and tell them to get their shit together');
}

$oauthurl = 'https://meta.wikimedia.org/wiki/Special:OAuth';
$request_url = $oauthurl . '/initiate?format=json&oauth_callback=oob';
 
$consumer = new OAuthConsumer( $consumerKey, $secretKey );

$parsedurl = parse_url( $request_url );
$extraSignedParams = array();
parse_str($parsedurl['query'], $extraParams);
$extraSignedParams['title'] = 'Special:OAuth/initiate';

$request = OAuthRequest::from_consumer_and_token($consumer,NULL,"GET",$request_url,$extraSignedParams);

$signer = new MWOAuthSignatureMethod_RSA_SHA1( new OAuthDataStore(), $secretKey );
$request->sign_request($signer,$consumer,NULL);

$data = noheaderstringget($request);

$requestToken = json_decode($data);


$redirectURL = $oauthurl . '/authorize';
$redirectquery = array(
	'oauth_token' => $requestToken->key,
	'oauth_consumer_key' => $consumerKey );
$redirectquery = http_build_query($redirectquery);
$redirectURL .= '?'.$redirectquery;

// apparently I need this for later and since I'm using a different page... yay requiring cookies.
session_start();
	$_SESSION['TempTokenKey'] = $requestToken->key;
	$_SESSION['TempTokenSecret'] = $requestToken->secret;
session_write_close();

header('Location: '.$redirectURL);

?>


