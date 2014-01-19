<?php

require_once('include/multiuseFunctions.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

// cast config and log variables
$config = parse_ini_file('lcaToolsConfig.ini');
$user = $_SERVER['PHP_AUTH_USER'];
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
require_once('include/OAuth.php');
require_once('include/MWOAuthSignatureMethod.php');
require_once('include/JWT.php');

$consumerKey = $config['mwconsumer_key'];

$secretKey = file_get_contents('lcatoolskey.pem');

if (empty($secretKey)) {
	die('You do not seem to have the required RSA Private key in the main app folder, please alert your nearest developer and tell them to get their shit together');
}

$apiurl = 'https://meta.wikimedia.org/w/api.php';
$server = 'http://meta.wikimedia.org';

?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>OAuth Test</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/jquery-1.10.2.min.js'></script>
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
				<h1>Test getting user data</h1>
				<br />
				<textarea id='insecureinfo' wrap='virtual' rows='18' cols='90'></textarea>

				<fieldset>
					<legend>Test edit to User talk:Jalexander/sandbox</legend>
					<textarea id='testedit' wrap='virtual' rows='18' cols='90'> This is a test of the LCA Tools OAuth application to allow automated editing.</textarea>

				</fieldset>

				</div>
		</div>
			<?php include('include/lcapage.php'); ?>
	</div>
	<?php

	$mysql = new mysqli($dbaddress,$dbuser,$dbpw,$db);
	$mysql->set_charset("utf8");

	if ($mysql->connect_error) {
	  echo 'Database connection fail: '  . $mysql->connect_error, E_USER_ERROR;
	}

	$sql = 'Select * FROM user';
	$sql .= ' WHERE user=\''.$user.'\'';

	$results = $mysql->query($sql);

	if($results === false) {
	  echo 'Bad SQL or no log: ' . $sql . ' Error: ' . $mysql->error, E_USER_ERROR;
	}

	$usertable = $results->fetch_assoc();

	$accessToken = new OAuthToken( $usertable['mwtoken'], $usertable['mwsecret'] );
	$apiParams = array(
		'action' => 'query',
		'meta' => 'userinfo',
		'uiprop' => 'rights',
		'format' => 'json',
	);

	$consumer = new OAuthConsumer( $consumerKey, $secretKey );
	$signer = new MWOAuthSignatureMethod_RSA_SHA1( new OAuthDataStore(), $secretKey );

	$api_req = OAuthRequest::from_consumer_and_token($consumer,$accessToken,"GET",$apiurl,$apiParams);
	$api_req->sign_request( $signer, $consumer, $accessToken );

	$basicid = mwOAuthAPIcall($apiurl,$apiParams, $api_req);


	if ($basicid) {
		echo "<script> $('#insecureinfo').val('".$basicid."'); </script>".PHP_EOL;
	} else {
		echo "<script> $('#insecureinfo').val('No data recieved it seems'); </script>".PHP_EOL;
		die();
	}

	?>
</body>
</html>
