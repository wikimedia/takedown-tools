<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2015-04-06

Quick and Dirty cross wiki link search function for Wikimedia Wikis. Will make better later.

---------------------------------------------   */

require_once dirname( __FILE__ ) . '/../include/multiuseFunctions.php';
require_once dirname( __FILE__ ) . '/../include/OAuth.php';
require_once dirname( __FILE__ ) . '/../include/MWOAuthSignatureMethod.php';
date_default_timezone_set( 'UTC' );
ini_set('max_execution_time', 300);

// cast config and log variables
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$user = $_SERVER['PHP_AUTH_USER'];
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
$consumerKey = $config['mwconsumer_key'];
$secretKey = file_get_contents( dirname( __FILE__ ) . '/../configs/lcatoolskey.pem' );
$useragent = $config['useragent'];

if ( empty( $secretKey ) ) {
	die( 'You do not seem to have the required RSA Private key in the configs folder, please alert your nearest developer and tell them to get their shit together' );
}

$originalapiurl = makehttps( $config['mw_oauthserver'] ).'/w/api.php';
$usertable = getUserData( $user );
$mwsecret = $usertable['mwsecret'];
$mwtoken = $usertable['mwtoken'];

if ( isset( $_POST['searchfor'] ) ) {
	$searchfor = $_POST['searchfor'];
	$searchproto = $_POST['proto'];
} else {
	$searchfor = null;
	$searchproto = null;
}

$t = 0;

?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>Global Link Search (ALPHA)</title>
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
				<h1>Global Link Search (ALPHA)</h1>
				<br />
				<fieldset>
					<legend>What do you want to search for? Please note this will search ALL wikis and may take time.</legend>
					<p> This form will search for an external link on all WMF wikis.</p>
					<p><b> Enter the URL you want to find WITHOUT the 'protocol' (i.e. wikipedia.org not https://wikipedia.org.</b></p>
					<p> If you want you are able to use a wildcard (*) at the start of a search (*.wikipedia.org not wikipedia.*) </p>


					<form id='inputform' method='POST'>
					<table>
						<tr>
							<td><?php
if ( $usertable['mwtoken'] ) {
	//do nothing
} else {
	echo 'Did not find user OAuth information, please register using the link on the sidebar'.'<script> $("#searchfor").attr("readonly", true);</script>';
}?>
							</td>
						</tr>
						<tr>
							<td>
								<label for='searchfor'> Search for: </label>
							</td>
							<td>
								<select name='proto'>
									<option value='http'>http://</option>
									<option value='https'>https://</option>
									<option value='ftp'>ftp://</option>
									<option value='ftps'>ftps://</option>
									<option value='irc'>irc://</option>
									<option value='tel'>tel:</option>
									<option value='bitcoin'>bitcoin:</option>
									<option value='mailto'>mailto:</option>
								</select>
							</td>
							<td>
								<input id='searchfor' name='searchfor' size='30' type='td' value=''>
							</td>
						</tr>
						<tr>
							<td> <input type='submit' value='Search' />
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend> Results: </legend>
					<?php
if ( $usertable['mwtoken'] ) {
	//do nothing
} else {
	echo '<table><tr><td style="color:red;">Did not find user OAuth information, please register using the link on the sidebar</td></tr></table>'.'<script> $("#searchfor").attr("readonly", true);</script>';
}?>
					<table border='1' id='results'></table>
				</fieldset>

				</div>
		</div>
			<?php include dirname( __FILE__ ) . '/../include/lcapage.php'; ?>
	</div>
	<?php
	echo '<script> $(\'input#searchfor\').val(\''.$searchfor.'\'); $(\'select[name="proto"]\').val(\''.$searchproto.'\'); </script>';
flush();
if ( isset( $usertable['mwtoken'] ) && isset( $_POST['searchfor'] ) ) {
	$accessToken = new OAuthToken( $mwtoken, $mwsecret );
	$request = array(
		'action' => 'sitematrix',
		'format' => 'json',
	);

	$consumer = new OAuthConsumer( $consumerKey, $secretKey );
	$signer = new MWOAuthSignatureMethod_RSA_SHA1( new OAuthDataStore(), $secretKey );

	$api_req = OAuthRequest::from_consumer_and_token( $consumer, $accessToken, "POST", $originalapiurl, $request );
	$api_req->sign_request( $signer, $consumer, $accessToken );

	$ch = curl_init();

	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_URL, $originalapiurl );
	curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request ) );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $api_req->to_header() ) );
	$jsonresponse = curl_exec( $ch );
	if ( !$jsonresponse ) {
		echo json_encode( 'Curl error: ' . htmlspecialchars( curl_error( $ch ) ) );
	}

	$response = json_decode( $jsonresponse, true );
	$sites = array();

	foreach ( $response['sitematrix'] as $key => $langarray ) {
		if ( $key != 'count' && $key != 'specials' ) {
			foreach ( $langarray['site'] as $langkey => $sitearray ) {
					$sites[] = $sitearray;
				}
		}
	}

	foreach ( $response['sitematrix']['specials'] as $key => $sitearray) {
		$sites[] = $sitearray;
	}

	foreach ( $sites as $key => $sitearray ) {
		$s = 0;
		$apiurl = makehttps( $sitearray['url'] ).'/w/api.php';
		$siteurl = makehttps( $sitearray['url'] );
		$dbname = $sitearray['dbname'];
		echo '<script> $("#results").append("<tr><th colspan=\'3\'> <a href=\''.$siteurl.'\' target=\'_blank\'>'.$dbname.'</a></th></tr>");</script>';
		if ( array_key_exists('closed', $sitearray ) ) {
			echo '<script> $("#results").append("<tr><td style=\'font-weight:bold;\' colspan=\'3\'> Closed Wiki, Skipping </td</tr>");</script>';
			continue;
		} elseif ( array_key_exists( 'private', $sitearray ) ) {
			echo '<script> $("#results").append("<tr><td style=\'font-weight:bold;\' colspan=\'3\'> Private Wiki, Skipping </td</tr>");</script>';
			continue;
		} elseif ( array_key_exists( 'fishbowl' , $sitearray ) ) {
			echo '<script> $("#results").append("<tr><td style=\'font-weight:bold;\' colspan=\'3\'> Fishbowl Wiki, Skipping </td</tr>");</script>';
			continue;
		}

		$request = array(
		'action' => 'query',
		'format' => 'json',
		'list' => 'exturlusage',
		'euprotocol' => $searchproto,
		'euquery' => $searchfor,
		'eulimit' => 'max',
		'euexpandurl' => '',
		'continue' => '',
		);

		curl_close( $ch );
		$ch = curl_init();

		a:

		$querystring = '?'.http_build_query($request);
		$geturl = $apiurl.$querystring;
		curl_setopt( $ch, CURLOPT_HTTPGET, true );
		curl_setopt( $ch, CURLOPT_URL, $geturl );
		curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$jsonresponse = curl_exec( $ch );
		$response = json_decode( $jsonresponse, true );

		if ( $response ) {
		
			if ( $response['query']['exturlusage'][0] ) {
				if ( !isset( $request['euoffset'] ) ) {
					echo '<script>$("#results").append("<tr></tr>");</script>';
					echo '<script>$("#results tr:last").append("<table id=\''.$dbname.'\' border=\'1\'></table>");</script>';
					echo '<script> $("#'.$dbname.'").append("<tr><th>Namespace</th><th>Page</th><th>Specific url found</th></tr>");</script>';

				}
				$searchresults = $response['query']['exturlusage'];
				foreach ($searchresults as $key => $result) {
					$title = $result['title'];
					$titleurl = $siteurl.'/wiki/'.$title;
					$title = utf8_encode( $title );
					$urlfound = utf8_encode( $result['url'] );
					$namespacefound = $result['ns'];

					echo '<script> $("#'.$dbname.'").append("<tr><td>'.$namespacefound.'</td><td><a href=\''.$titleurl.'\' target=\'_blank\'>'.$title.'</a></td><td>'.$urlfound.'</td></tr>");</script>';
					$s++;
					$t++;
					flush();
				}

				if ( array_key_exists('continue', $response ) && array_key_exists( 'euoffset', $response['continue'] ) ) {
				$offset = $response['continue']['euoffset'];
				$request['euoffset'] = $offset;
				//FIXME DONT USE GOTO
				goto a;
				}

				echo '<script> $("#'.$dbname.'").append("<tr><th colspan=\'3\'> Total hits: '.$s.' </th></tr>");</script>';
			} else {
				echo '<script> $("#results").append("<tr><td colspan=\'3\'>No search results found</td></tr>");</script>';
			}
		} else {
			echo '<script> $("#results").append("<tr><td style=\'color:red;\' colspan=\'3\'>There was an error with this search. If the wiki exists this appears it appears that you don\'t have an account on it. <br /> You may want to do a manual search on the wiki (click above) or re run this search after you have visited the wiki with the link above and seen your username in the top right corner. </td></tr>");</script>';
		}

	}
	echo '<script> $("#results").append("<tr><th colspan=\'3\'>DONE! - Total hits: '.$t.' </th></tr>");</script>';

} 
?>
</body>
</html>
