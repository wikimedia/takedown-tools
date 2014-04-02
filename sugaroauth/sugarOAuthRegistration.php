<?php

require_once dirname( __FILE__ ) . '/../include/multiuseFunctions.php';
require_once dirname( __FILE__ ) . '/../include/sugar.class.php';
date_default_timezone_set( 'UTC' );

// cast config and log variables
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$user = $_SERVER['PHP_AUTH_USER'];

$sugarapiurl = $config['sugar_apiurl'];
//$sugarapiurl = 'http://localhost/~jamesur/sugar/service/v4_1/rest.php';

$sugarkey = $config['sugarconsumer_key'];
$sugarsecret = $config['sugarconsumer_secret'];
$callback_url = $config['sugar_callback'];

$usertable['sugartoken'] = null;
if ( isset( $_GET['force'] ) ) {
	if ( $_GET['force'] === '1' ) {
		// do nothing
	} else {
		$usertable = getUserData( $user );
	}
} else {
	$usertable = getUserData( $user );
}

if ( $usertable['sugartoken'] ) {
	$username = $usertable['sugaruser'];
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>Sugar Registration</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='/scripts/jquery-1.10.2.min.js'></script>
	<script src='/scripts/jquery.validate.min.js'></script>
	<script src='/scripts/lca.js'></script>
	<script>
		$(document).ready(function(){

	    //validate
	    $("#forceregister").validate();
	}
	</script>
	<script src='/scripts/lca.js'></script>
	<style type='text/css'>
	<!--/* <![CDATA[ */
	@import '/css/main.css';
	@import '/css/lca.css';
	/* ]]> */-->
	.external, .external:visited { color: #222222; }
	.autocomment{color:gray}
	</style>
</head>
<body class='mediawiki'>
	<div id='globalWrapper'>
		<div id='column-content'>
			<div id='content'>
				<h1>OAuth Registration Warning</h1>
				<center><b><p> You appear to have a registration in the system already using the user name <u><?php echo $username; ?></u>. <br />
					You may be trying to register again to switch usernames, because something isn't working or to test the registration system. <br />
					Going through the process with the same username will not hurt anything but no reason to spend time or resources when you don't need to! </p></b></center><br />
					<form id='forceregister'>
						<table>
							<tr>
								<td>
									<label for='force'>Are you sure you want to continue?</label>
								</td>
								<td>
									<b>Yes:</b>&nbsp;&nbsp;<input type='checkbox' name='force' id='force' value='1' required='true'>
								</td>
								<td>
									<input type='submit' value='submit' >
								</td>
							</tr>
							<tr>
								<td colspan='3'>
									<a href='index.php'> Nope! I'm set take me away from this weird place </a>
								</td>
							</tr>
						</table>
					</form>
			</div>
	    </div>
	        <?php include dirname( __FILE__ ) . '/../include/lcapage.php'; ?>
	</div>
</body>
</html>
<?php
	die();
}

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

