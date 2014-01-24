<?php

require_once('include/multiuseFunctions.php');
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
$usertable = getUserData($user);
$mwsecret = $usertable['mwsecret'];
$mwtoken = $usertable['mwtoken'];

?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>OAuth Test</title>
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
	<script>
	$(document).ready(function(){
		$("#editbutton").click( function() {
			$("#result").html("<img src='images/progressbar.gif' alt='waiting for edit progressbar'>");
			var dpagetitle = "User_talk:Jalexander/sandbox";
			var dsectiontitle = "Test edit from LCA Tools";
			var dmwtoken = <?php echo '"'.$mwtoken.'"' ?>;
			var dmwsecret = <?php echo '"'.$mwsecret.'"' ?>;
			var dapiurl = <?php echo '"'.$apiurl.'"' ?>;
			var deditsummary = "Test edit from LCA Tools system using mediawiki OAuth";
			var dtext = $("#testedit").val();
			var daction = "newsection";

			postdata = { action: daction, pagetitle: dpagetitle, sectiontitle: dsectiontitle, mwtoken: dmwtoken, mwsecret: dmwsecret, apiurl: dapiurl, editsummary: deditsummary, text: dtext };

			$.post( "mwOAuthProcessor.php", postdata, function(data) {
			if ( data && data.edit && data.edit.result == 'Success' ) {
				$('#testedit').val(JSON.stringify(data));
				$('#result').html(data.edit.result + 'you can see the results at <a href="https://meta.wikimedia.org/wiki/User_talk:Jalexander/sandbox" target="_blank"> User talk:Jalexander/sandbox</a>'); } 
			else if ( data && data.error ) {
				$('#testedit').val(JSON.stringify(data));
				$('#result').html(data.edit.error); } 
			else {
				$('#testedit').val(JSON.stringify(data));
				$('#result').html('hmmm something weird happened');
					} },"json"); 
		});

		
	});
	</script>

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
					<textarea id='testedit' wrap='virtual' rows='18' cols='90'> Let&#39;s make a test edit! You can type anything you want here to send a test edit to User talk:Jalexander/sandbox on meta.</textarea>
					<table>
						<tr>
							<td><?php
									if ($usertable['mwtoken']) {
										echo 'Found user OAuth Information! Want to try an edit? Click the button to the right!';
									} else {
										echo 'Did not find user OAuth information, please register using the link on the sidebar'.'<script> $("#testedit").attr("readonly", true);</script>'; 
								}?>
							</td>
							<td>
								<input id='editbutton' type='button' value='Start test edit'>
							</td>
						</tr>
						<tr>
							<td id='result' colspan='2'></td>
						</tr>
					</table>
				</fieldset>

				</div>
		</div>
			<?php include('include/lcapage.php'); ?>
	</div>
	<?php
	flush();
if ($usertable['mwtoken']) {
	$accessToken = new OAuthToken( $mwtoken, $mwsecret );
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
} else { echo "<script> $('#insecureinfo').val('Did not find user OAuth information, please register.'); </script>".PHP_EOL;}
flush();
	?>
</body>
</html>
