<?php
# Test file to test geolocation set up without actually running data. Should be removed at some point soonish from structure.

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

$strategy = new strategy2016( $dbaddress, $dbuser, $dbpw, $db, $mwapiurl, $consumerKey, $secretKey, $mwtoken, $mwsecret, $geolocateapi, $geolocatekey);


$answer = $strategy->geolocate( '198.35.26.96' );

echo $answer;

?>