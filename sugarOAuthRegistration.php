<?php

require_once 'include/multiuseFunctions.php';
require_once 'include/classSugar.php';
date_default_timezone_set( 'UTC' );

// cast config and log variables
$config = parse_ini_file( 'lcaToolsConfig.ini' );
$user = $_SERVER['PHP_AUTH_USER'];

//$sugarapiurl = $config['sugar_apiurl'];

$sugarapiurl = 'http://localhost/~jamesur/sugar/service/v4_1/rest.php';
$sugarkey = $config['sugarconsumer_key'];
$sugarsecret = $config['sugarconsumer_secret'];
$callback_url = $config['sugar_callback'];

$request = new sugar( $sugarkey, $sugarsecret, $sugarapiurl );
$request->setCallback( $callback_url );
$redirectURL = $request->getRequestToken();

session_name('sugarregister');
session_start();
$_SESSION['TempTokenKey'] = $request->gettemptoken();
$_SESSION['TempTokenSecret'] = $request->gettempsecret();
session_write_close();

header( 'Location: '.$redirectURL );

?>