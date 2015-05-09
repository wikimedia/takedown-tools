<?php
session_name('sugarregister');
session_start();
require_once dirname( __FILE__ ) . '/../core-include/multiuseFunctions.php'; 
require_once dirname( __FILE__ ) . '/../core-include/sugar.class.php';
date_default_timezone_set( 'UTC' );

// cast config and log variables
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$user = $_SERVER['PHP_AUTH_USER'];
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];

$sugarapiurl = $config['sugar_apiurl'];
//$sugarapiurl = 'http://localhost/~jamesur/sugar/service/v4_1/rest.php';
$sugarkey = $config['sugarconsumer_key'];
$sugarsecret = $config['sugarconsumer_secret'];

?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>SugarCRM OAuth Registration</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='/scripts/jquery-1.10.2.min.js'></script>
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
				<h1>Wikimedia OAuth Registration</h1>
				<br />
				<table border='1' id='mw-movepage-table' style='font-weight:bold;'>
					<tr>
						<td colspan='4'>
							Welcome to the LCA Tools Sugar Registration Page
                    <tr>
                        <td >
                            <u>Step 1:</u> <br /> Temporary OAuth verification code and token received <br /> but not yet verified.
                        </td>
                        <td >
                            <img id='tempreceived' src='/images/List-remove.svg' width='40px'/>
                        </td>
                        <td >
                            <u> Step 4:</u> <br /> Test login done to verify credentials.
                        </td>
                        <td >
                            <img id='basictest' src='/images/List-remove.svg' width='40px'/>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <u>Step 2:</u> <br /> Cookie session set and matches verification code.
                        </td>
                        <td>
                            <img id='session' src='/images/List-remove.svg' width='40px'/>
                        </td>
                        <td >
                            <u>Step 5:</u> <br /> Your Username is: <span id='username'></span>
                        </td>
                        <td>
                            <img id='usernamecheck' src='/images/List-remove.svg' width='40px'/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <u>Step 3:</u> <br /> Permanent access token requested and received.
                        </td>
                        <td>
                            <img id='permrequest' src='/images/List-remove.svg' width='40px'/>
                        </td>
                         <td >
                            <u>Step 5:</u> <br /> Information saved.
                        </td>
                        <td>
                            <img id='logged' src='/images/List-remove.svg' width='40px'/>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan='4'>
                    		<div id='result'></div>
                    	</td>
                    </tr>
                </table>
                <!--<fieldset>
                	<legend> Debug info </legend>
                	<textarea style='outline: black solid 2px;' id='tokeninfo'></textarea>
                	<textarea style='outline: black solid 2px;' id='tokenresponse'></textarea>
                	<textarea style='outline: black solid 2px;' id='loginsession'></textarea>

                </fieldset>-->
			</div>
		</div>
			<?php include dirname( __FILE__ ) . '/../project-include/page.php'; ?>
	</div>
	<?php

	// Setup sugar class
	$sugar = new sugar( $sugarkey, $sugarsecret, $sugarapiurl );

	// STEP 1
	if ( !empty( $_GET['oauth_verifier'] ) && !empty( $_GET['oauth_token'] ) ) {
		$temptoken = $_GET['oauth_token'];
		$verifier = $_GET['oauth_verifier'];
		echo "<script> $('#tempreceived').attr('src', '/images/Dialog-accept.svg'); </script>".PHP_EOL; 
	} else {
	echo "<script> $('#tempreceived').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
	echo "<script> $('#result').html('<span style=\'color:red\'>There does not appear to be the required oauth attributes in the query string. Did you get here by accident?</span>'); </script>".PHP_EOL;
	die();
	}
	flush();

	// STEP 2
	if ( isset( $_SESSION['TempTokenKey'] ) && isset( $_SESSION['TempTokenSecret'] ) ) {
		if ( $_SESSION['TempTokenKey'] === $temptoken ) {
			$tempsecret = $_SESSION['TempTokenSecret'];
			echo "<script> $('#session').attr('src', '/images/Dialog-accept.svg'); </script>".PHP_EOL; 
		} else {
		echo "<script> $('#session').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
		echo "<script> $('#result').html('<span style=\'color:red\'> The token passed back by OAuth was not the same as the token in your session, for your security the authorization process has stopped. Please try again by going <a href='sugarOAuthRegistration.php'>Here</a> or contact James. </span>');</script>".PHP_EOL;
		die();
		}
	} else {
	echo "<script> $('#session').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
	echo "<script> $('#result').html('<span style=\'color:red\'> You do not appear to have a session set, which is required to verify that the person coming here is the same person who started the process (and logged in) and that this isn\'t someone trying to steal your access information. Please make sure you are accepting cookies and try again by going <a href=\'sugarOAuthRegistration.php\'>Here</a>. If you have any problems please contact James. </span>');</script>".PHP_EOL;
	die();
	}
	flush();

	$token = $sugar->getpermtoken( $verifier, $temptoken, $tempsecret );
	if ( $token ) {
		echo "<script> $('#permrequest').attr('src', '/images/Dialog-accept.svg'); </script>".PHP_EOL; 
	} else {
		echo "<script> $('#permrequest').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
		echo "<script> $('#result').html('<span style=\'color:red\'> There appears to have been a problem retriving your permanent credentials. Please try again by going <a href=\'sugarOAuthRegistration.php\''>Here</a> or contact James. </span>');</script>".PHP_EOL;
		die();
	}
	flush();

	$session = $sugar->login();

	if ( $session ) {
		echo "<script> $('#basictest').attr('src', '/images/Dialog-accept.svg'); </script>".PHP_EOL; 
	} else {
		echo "<script> $('#basictest').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
		echo "<script> $('#result').html('<span style=\'color:red\'> There appears to have been a problem verifying your permanent credentials. Please try again by going <a href=\'sugarOAuthRegistration.php\'>Here</a> or contact James. </span>');</script>".PHP_EOL;
		die();
	}

	$insertemplate = 'INSERT INTO user (user,sugartoken,sugarsecret,sugaruser,sugar_registration_time) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE sugartoken = VALUES(sugartoken), sugarsecret = VALUES(sugarsecret), sugaruser = VALUES(sugaruser), sugar_registration_time = VALUES(sugar_registration_time)';
	$submittime = gmdate( "Y-m-d H:i:s", time() );

	$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
	$mysql->set_charset( "utf8" );
	$sugartoken = $sugar->getToken()->key;
	$sugarsecret = $sugar->getToken()->secret;
	$sugarusername = $sugar->getUserName();

	if ( $sugarusername ) {
		echo "<script> $('#username').html('".$sugarusername."'); </script>".PHP_EOL;
		echo "<script> $('#usernamecheck').attr('src', '/images/Dialog-accept.svg'); </script>".PHP_EOL;
	} else {
		echo "<script> $('#username').html('<span style=\'color:red\'>clasusername check failed</span>'); </script>".PHP_EOL;
		echo "<script> $('#usernamecheck').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
	}

	$insert = $mysql->prepare( $insertemplate );
	if ( $insert === false ) {
		echo "<script> $('#logged').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
		echo 'Error while preparing: ' . $insertemplate . ' Error text: ' . $mysql->error, E_USER_ERROR;
		echo "<script> $('#result').html('<span style=\'color:red\'>We appear to have had an issue recording your data, please try again later or contact James. </span>'); </script>".PHP_EOL;
		die();

	}
	flush();

	$insert->bind_param( 'sssss', $user, $sugartoken, $sugarsecret, $sugarusername, $submittime );

	$insert->execute();

	$mysql->close();
	echo "<script> $('#logged').attr('src', '/images/Dialog-accept.svg'); </script>".PHP_EOL;
	echo "<script> $('#result').html('<span style=\'color:green\'>You have successfully registered LCA Tools with SugarCRM. </span>'); </script>".PHP_EOL;
	flush();


	?>
</body>
</html>

