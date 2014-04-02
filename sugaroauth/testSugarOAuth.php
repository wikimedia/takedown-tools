<?php

require_once dirname( __FILE__ ) . '/../include/multiuseFunctions.php';
require_once dirname( __FILE__ ) . '/../include/OAuth.php';
require_once dirname( __FILE__ ) . '/../include/sugar.class.php';
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
$consumerkey = $config['sugarconsumer_key'];
$consumersecret = $config['sugarconsumer_secret'];

$usertable = getUserData( $user );
$secret = $usertable['sugarsecret'];
$token = $usertable['sugartoken'];

if ( $secret && $token ) {
	
	$sugar = new sugar( $consumerkey, $consumersecret, $sugarapiurl, $token, $secret );

	$login = $sugar->login();

	$userid = $sugar->getUserID();
	$sugaruname = $sugar->getUserName();
	$modulesraw = $sugar->getModules();

	foreach ( $modulesraw['modules'] as $array ) {
		foreach ( $array as $key => $value )
		if ( $key == 'module_key' ) {
			$modules[] = $value;
		}
	}

	$fieldresponse = $sugar->getAvailableFields( 'Cases' );
	$fields = $fieldresponse['module_fields'];



}

?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>Sugar OAuth Test</title>
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
	<!--<script>
	$(document).ready(function(){
		$("#editbutton").click( function() {
			$("#result").html("<img src='/images/progressbar.gif' alt='waiting for edit progressbar'>");
			var dpagetitle = "User_talk:Jalexander/sandbox";
			var dsectiontitle = "Test edit from LCA Tools";
			var dmwtoken = <?php/* echo '"'.$mwtoken.'"' /*?>;
			var dmwsecret = <?php/* echo '"'.$mwsecret.'"' /*?>;
			var dapiurl = <?php/* echo '"'.$apiurl.'"' */?>;
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
	</script>-->
</head>
<body class='mediawiki'>
	<div id='globalWrapper'>
		<div id='column-content'>
			<div id='content'>
				<h1>Sugar CRM Tests</h1>
				<br />
				<fieldset>
					<legend>Automated Tests</legend>
					<table border='1' style='font-weight:bold;'>
						<?php if ( $secret && $token ) { ?>
						<tr>
							<td colspan='2' style='color:green'>
								You appear to have connected your SugarCRM and LCA Tools accounts.
							</td>
						</tr>
						<?php } else { ?>
						<tr>
							<td colspan='2' style='color:red'>
								You do not appear to have connected your SugarCRM and LCA Tools accounts. <a href="sugarOAuthRegistration.php">Please connect first</a>.
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td> 
								Sugar User ID for logged in user:
							</td>
							<td>
								<?php echo $userid; ?>
							</td>
						</tr>
						<tr>
							<td>
								Sugar User Name for logged in user:
							</td>
							<td>
								<?php echo $sugaruname; ?>
							</td>
						</tr>
						<tr>
							<td>
								Modules available to logged in user:
							</td>
							<td>
								<textarea id='availablemodules' wrap='virtual' rows='18' cols='30' style="outline: black solid 2px;"><?php echo json_encode( $modules, JSON_PRETTY_PRINT );?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Available fields for the Case Module
							</td>
							<td>
								<textarea id='availablefields' wrap='virtual' rows='18' cols='90' style="outline: black solid 2px;"><?php echo json_encode( $fields, JSON_PRETTY_PRINT )?></textarea>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend> Create a case </legend>
					<form id='createcase'>
						<table border='1'>
							<tr>
								<td>
									<label for='name'> Case Subject/Title: </label>
								</td>
								<td>
									<input type='text' size='30' id='name' name='name' />
								</td>
							</tr>
							<tr>
								<td>
									<label for='description'> Description/Case Body: </label>
								</td>
								<td>
									<textarea id='description' name='description' wrap='virtual' rows='4' ></textarea>
								</td>
							</tr>
						</table>
					</form>

				</fieldset>
				<fieldset>
					<legend> Delete your case </legend>
				</fieldset>
				</div>
		</div>
			<?php include dirname( __FILE__ ) . '/../include/lcapage.php'; ?>
	</div>
	<?php
flush();
?>
</body>
</html>
