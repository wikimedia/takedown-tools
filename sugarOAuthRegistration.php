<?php

require_once 'include/multiuseFunctions.php';
date_default_timezone_set( 'UTC' );

// cast config and log variables
$config = parse_ini_file( 'lcaToolsConfig.ini' );
$user = $_SERVER['PHP_AUTH_USER'];
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
require_once 'include/OAuth.php';

//$sugarapiurl = $config['sugar_apiurl'];
$sugarapiurl = 'http://localhost/~jamesur/sugar/service/v4_1/rest.php';
$sugarkey = $config['sugarconsumer_key'];
$sugarsecret = $config['sugarconsumer_secret'];


$consumer = new OAuthConsumer( $sugarkey, $sugarsecret );
$params['method'] = 'oauth_request_token';
$params['oauth_callback'] = 'https://lcatools.corp.wikimedia.org';
$request = OAuthRequest::from_consumer_and_token( $consumer, NULL, "GET", $sugarapiurl, $params );
$signer = new OAuthSignatureMethod_HMAC_SHA1();
$request->sign_request ( $signer, $consumer, NULL );
$signed_url = $request->to_url();
//$data = noheaderstringget( $signed_url );


	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, (string) $request->to_url() );
	curl_setopt( $ch, CURLOPT_HTTPGET, true );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $request->to_header() ) );
	curl_setopt( $ch, CURLOPT_VERBOSE, true );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$data = curl_exec( $ch );

	if ( !$data ) {
		die( 'Curl error: ' . curl_error( $ch ) );
	}

	unset( $ch );

	$response = array();
	parse_str($data, $response);
	$redirectparams['oauth_token'] = $response['oauth_token'];
	$redirectparams['oauth_token_secret'] = $response['oauth_token_secret'];

	echo print_r($response);
	echo $response['authorize_url'].'&'.http_build_query($redirectparams);