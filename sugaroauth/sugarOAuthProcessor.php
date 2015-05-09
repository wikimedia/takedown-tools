<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2014-04-04

SugarCRM oAuth processor for LCA Tools [to be used for ajax queries]

---------------------------------------------   */

require_once dirname( __FILE__ ) . '/../core-include/multiuseFunctions.php';
require_once dirname( __FILE__ ) . '/../core-include/sugar.class.php';
date_default_timezone_set( 'UTC' );

$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$sugarapiurl = $config['sugar_apiurl'];
//$sugarapiurl = 'http://localhost/~jamesur/sugar/service/v4_1/rest.php';
$consumerkey = $config['sugarconsumer_key'];
$consumersecret = $config['sugarconsumer_secret'];

if ( isset( $_POST['action'] ) ) {
	$action = $_POST['action'];
} else {
	$action = null;
}

if ( $action ) {
	switch ( $action ) {
		default:
			break;

		case 'createcase':

			if ( isset( $_POST['user'] ) ){
				$user = $_POST['user'];
			} else {
				echo 'You have not set a user, no further processing possible';
				die();
			}

			if ( isset( $_POST['data'] ) ) {
				$data = $_POST['data'];
			} else {
				echo 'you have not sent any data to use, no futher processing possible';
				die();
			}
			
			$usertable = getUserData( $user );
			$secret = $usertable['sugarsecret'];
			$token = $usertable['sugartoken'];

			$sugar = new sugar( $consumerkey, $consumersecret, $sugarapiurl, $token, $secret );

			$sugar->login();

			$response = $sugar->create_case( $data );

			echo json_encode($response);
			break;

	}
}

