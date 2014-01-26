<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2014-01-16

Mediawiki oAuth functions for LCA Tools

---------------------------------------------   */

require_once 'include/multiuseFunctions.php';
require_once 'include/OAuth.php';
require_once 'include/MWOAuthSignatureMethod.php';
require_once 'include/JWT.php';

date_default_timezone_set( 'UTC' );

$config = parse_ini_file( 'lcaToolsConfig.ini' );
$consumerKey = $config['mwconsumer_key'];
$secretKey = file_get_contents( 'lcatoolskey.pem' );
$useragent = $config['useragent'];
if ( isset( $_POST['action'] ) ) {
	$action = $_POST['action'];
} else {
	$action = 'none';
}


function mwOAuthpost( $mwtoken, $mwsecret, $apiurl, $request, &$ch = null ) {
	global $consumerKey, $secretKey, $useragent;

	$consumer = new OAuthConsumer( $consumerKey, $secretKey );
	$signer = new MWOAuthSignatureMethod_RSA_SHA1( new OAuthDataStore(), $secretKey );
	$accessToken = new OAuthToken( $mwtoken, $mwsecret );

	$api_req = OAuthRequest::from_consumer_and_token( $consumer, $accessToken, "POST", $apiurl, $request );
	$api_req->sign_request( $signer, $consumer, $accessToken );

	if ( !$ch ) {
		$ch = curl_init();
	}

	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_URL, $apiurl );
	curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request ) );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $api_req->to_header() ) );
	$response = curl_exec( $ch );
	if ( !$response ) {
		echo json_encode( 'Curl error: ' . htmlspecialchars( curl_error( $ch ) ) );
	}

	return $response;


}


function getEditToken( $mwtoken, $mwsecret, $apiurl, &$ch = null ) {

	$requestParams = array(
		'action' => 'tokens',
		'format' => 'json',
	);

	if ( !$ch ) {
		$ch = null;
	}

	$jsonresult = mwOAuthpost( $mwtoken, $mwsecret, $apiurl, $requestParams, $ch );

	$result = json_decode( $jsonresult, true );

	if ( $result['tokens']['edittoken'] ) {
		$edittoken = $result['tokens']['edittoken'];
	} else $edittoken=null;

	return $edittoken;

}

/* FIXME's Assumptions: assuming no captcha */

switch ( $action ) {
case 'none':
	echo json_encode( 'you appear to not actually asked me to do anything ......' );
	break;

case 'newsection':
	$pagetitle = $_POST['pagetitle'];
	$sectiontitle = $_POST['sectiontitle'];
	$text = $_POST['text'];
	$mwtoken = $_POST['mwtoken'];
	$mwsecret = $_POST['mwsecret'];
	$apiurl = $_POST['apiurl'];
	$editsummary = $_POST['editsummary'];

	$edittoken = getEditToken( $mwtoken, $mwsecret, $apiurl );

	if ( $edittoken ) {
		$apiParams = array(
			'action' => 'edit',
			'format' => 'json',
			'title' => $pagetitle,
			'section' => 'new',
			'text' => $text,
			'summary' => $editsummary,
			'recreate' => 'true',
			'token' => $edittoken,
		);
		if ( $sectiontitle ) {
			$apiParams['sectiontitle']  = $sectiontitle;
		}

		$result = mwOAuthpost( $mwtoken, $mwsecret, $apiurl, $apiParams );
		echo $result;
	}
	break;

case 'appendtext':
	$pagetitle = $_POST['pagetitle'];
	$text = $_POST['text'];
	$mwtoken = $_POST['mwtoken'];
	$mwsecret = $_POST['mwsecret'];
	$apiurl = $_POST['apiurl'];
	$editsummary = $_POST['editsummary'];

	$edittoken = getEditToken( $mwtoken, $mwsecret, $apiurl );

	if ( $edittoken ) {
		$apiParams = array(
			'action' => 'edit',
			'format' => 'json',
			'title' => $pagetitle,
			'appendtext' => $text,
			'summary' => $editsummary,
			'recreate' => 'true',
			'token' => $edittoken,
		);

		$result = mwOAuthpost( $mwtoken, $mwsecret, $apiurl, $apiParams );
		echo $result;
		break;
	}

default:
	echo json_encode( 'you appear to have sent an unrecogized action option, please contact the developer or hit yourself if you are said developer' );
	break;
}
