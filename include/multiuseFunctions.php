<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2013-12-22
Last modified : 2014-01-02

Plugin providing functions which could be used in multiple LCA tools and/or multiple instances of the same tool.
Stored here mostly to keep main files cleaner.

---------------------------------------------   */

$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
libxml_use_internal_errors( true );

function setupdataurl( $inputfile ) {
	/* in case a real file is passed instead of _FILES (should not happen in current setup)
	or in case something went wrong and file is not stored in system anymore. */
	if ( !array_key_exists( 'tmp_name', $inputfile ) ) {
		if ( array_key_exists( 'name', $inputfile ) ) {
			$inputfile['tmp_name'] = $inputfile['name'];
		} else {
			return 'No file appears to be present, please try again';
		}
	}

	$tempfile = array();
	$tempfile['kind'] = 'original';
	$tempfile['file_name'] = $inputfile['name'];
	$datatemp = file_get_contents( $inputfile['tmp_name'] );
	$datatemp = base64_encode( $datatemp );
	$uri = 'data:'.$inputfile['type'].';base64,'.$datatemp;
	$tempfile['file'] = $uri;

	return $tempfile;
}

function curlAPIpost( $url, $data, $headers='' ) {

	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HEADER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch, CURLOPT_VERBOSE, true );

	$result = curl_exec( $ch );
	curl_close( $ch );
	return $result;
}


function lcalog( $user, $type, $title, $test ) {

	global $dbaddress, $dbuser, $dbpw, $db;

	$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
	$mysql->set_charset( "utf8" );

	$template = 'INSERT INTO centrallog (user,timestamp,type,title,test) VALUES (?,?,?,?,?)';

	$submittime = gmdate( "Y-m-d H:i:s", time() );

	$log = $mysql->prepare( $template );
	if ( $log === false ) {
		echo 'Error while preparing: ' . $template . ' Error text: ' . $mysql->error, E_USER_ERROR;
	}

	$log->bind_param( 'sssss', $user, $submittime, $type, $title, $test );

	$log->execute();

	return $log->insert_id;
	$mysql->close();
}

// Following 2 functions Copyright CC 3.0 attribution PHP Group from http://creativecommons.org/licenses/by/3.0/legalcode - from http://www.php.net/manual/en/domdocument.schemavalidate.php#62032
function libxml_display_error( $error ) {
	$return = "<br/>\n";
	switch ( $error->level ) {
	case LIBXML_ERR_WARNING:
		$return .= "<b>Warning $error->code</b>: ";
		break;
	case LIBXML_ERR_ERROR:
		$return .= "<b>Error $error->code</b>: ";
		break;
	case LIBXML_ERR_FATAL:
		$return .= "<b>Fatal Error $error->code</b>: ";
		break;
	}
	$return .= trim( $error->message );
	if ( $error->file ) {
		$return .=    " in <b>$error->file</b>";
	}
	$return .= " on line <b>$error->line</b>\n";

	return $return;
}

function libxml_display_errors() {
	$errors = libxml_get_errors();
	foreach ( $errors as $error ) {
		print libxml_display_error( $error );
	}
	libxml_clear_errors();
}

function NCMECsimpleauthdcurlPost( $username, $password, $url, $data ) {

	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	//curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt( $ch, CURLOPT_USERPWD, $username.":".$password );
	curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );

	$result = curl_exec( $ch );
	curl_close( $ch );
	return $result;
}

function curlauthdAPIpost( $username, $password, $url, $data, $headers='' ) {

	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
	//curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt( $ch, CURLOPT_USERPWD, $username.":".$password );
	curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );

	$result = curl_exec( $ch );
	curl_close( $ch );
	return $result;
}

function NCMECstatus( $username, $password, $url ) {

	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_USERPWD, $username.":".$password );
	curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );

	$result = curl_exec( $ch );
	curl_close( $ch );
	$responseXML = new DOMDocument();
	$responseXML->loadXML( $result );
	$responseNodes = $responseXML->getElementsByTagName( 'responseCode' );

	if ( $responseNodes->length==0 ) {
		$responsecode = null;
	} else {
		foreach ( $responseNodes as $r ) {
			$responsecode = $r->nodeValue;
		}
	}
	return $responsecode;
}

// mostly for oauth requests
function noheaderstringget( $request ) {
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, (string) $request );
	curl_setopt( $ch, CURLOPT_HTTPGET, true );
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$data = curl_exec( $ch );

	if ( !$data ) {
		die( 'Curl error: ' . curl_error( $ch ) );
	}

	unset( $ch );

	return $data;
}

function mwOAuthAPIcall( $url, $params, $signedrequest ) {
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url . "?" . http_build_query( $params ) );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $signedrequest->to_header() ) ); // Authorization header required for api
	$data = curl_exec( $ch );
	if ( !$data ) {
		echo 'Curl error: ' . htmlspecialchars( curl_error( $ch ) );
	}

	return $data;
}

// From Chris Steipp https://www.mediawiki.org/wiki/OAuth/For_Developers#PHP_demo_cli_client_with_RSA_keys with slight modifications.
function validateJWT( $identity, $consumerKey, $nonce, $server ) {

	$expectedCanonicalServer = $server;

	// Verify the issuer is who we expect (server sends $wgCanonicalServer)
	if ( $identity->iss !== $expectedCanonicalServer ) {
		print "Invalid Issuer";
		return false;
	}

	// Verify we are the intended audience
	if ( $identity->aud !== $consumerKey ) {
		print "Invalid Audience";
		return false;
	}

	// Verify we are within the time limits of the token. Issued at (iat) should be
	// in the past, Expiration (exp) should be in the future.
	$now = time();
	if ( $identity->iat > $now || $identity->exp < $now ) {
		print "Invalid Time";
		return false;
	}

	// Verify we haven't seen this nonce before, which would indicate a replay attack
	if ( $identity->nonce !== $nonce ) {
		print "Invalid Nonce";
		return false;
	}

	return true;
}

function getUserData( $user ) {
	global $dbaddress, $dbuser, $dbpw, $db;

	$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
	$mysql->set_charset( "utf8" );

	if ( $mysql->connect_error ) {
		echo json_encode( 'Database connection fail: '  . $mysql->connect_error, E_USER_ERROR );
	}

	$sql = 'Select * FROM user';
	$sql .= ' WHERE user=\''.$user.'\'';

	$results = $mysql->query( $sql );

	if ( $results === false ) {
		echo json_encode( 'Bad SQL or no log: ' . $sql . ' Error: ' . $mysql->error, E_USER_ERROR );
	}

	$usertable = $results->fetch_assoc();
	$mysql->close();
	return $usertable;
}

function makehttps( $url ) {
	$url = preg_replace("/^http:/", "https:", $url, 1);	
	return $url;
}

// From http://www.justin-cook.com/wp/2006/03/31/php-parse-a-string-between-two-strings/

function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

