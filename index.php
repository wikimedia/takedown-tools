<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>LCA Tools</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='/scripts/jquery-1.10.2.min.js'></script>
	<script src='/scripts/lca.js'></script>
	<style type='text/css'>
	<!--/* <![CDATA[ */
	@import '/css/main.css';
	@import '/css/lca.css';
	/* ]]> */-->
	td { vertical-align: top; }
	.external, .external:visited { color: #222222; }
	.autocomment{color:gray}
	</style>
</head>
<body class='mediawiki'>
	<div id='globalWrapper'>
		<div id='column-content'>
			<div id='content'>
				<h1> LCA Tools </h1>
				<div style="text-align:center">
					<table border='2' style='position:absolute; left:10spx; top:40px;'>
						<tr>
							<td colspan='2'>
								Server/Connection Status
							</td>
						</tr>
						<tr>
							<td>
								NCMEC Production
							</td>
							<td>
								<img id='ncmec-prod' src='/images/List-remove.svg' width='15px'/>
							</td>
						</tr>
						<tr>
							<td>
								NCMEC Test
							</td>
							<td>
								<img id='ncmec-test' src='/images/List-remove.svg' width='15px'/>
							</td>
						</tr>
				</table>
					<img style="display:block; margin:auto;" width="500px" src="/images/Roryshieldlogo.png" />
					<u> LCA Tools </u> <br />
					<a href="standalone/globalsearch.php"> Global search (ALPHA) </a> <br /> <br />
					<u> Reporting forms </u> <br />
					<a href="takedown/legalTakedown.php">DMCA Takedown Form</a> <br />
					<a href="childprotection/NCMECreporting.php"> Child Protection Takedown Form </a> <br />
					<a href="release/basicRelease.php"> Basic Release of Confidential Information </a> <br /> <br />

					<u> 'special' pages </u> <br />
					<a href="centralLog.php"> Central submission Log </a> <br />
					<a href="childprotection/NCMECretract.php"> NCMEC report retraction form </a> <br /> <br />

					<u> Mediawiki OAuth </u> <br />
					<a href="mwoauth/beginmwoauthregistration.php"> Connect your Wikimedia wiki account </a> <br />
					<a href="mwoauth/testmwOAuth.php"> Test your stored Wikimedia OAuth data </a> <br /> <br />

					<u> SugarCRM OAuth </u> <br />
					<a href="sugaroauth/sugarOAuthRegistration.php"> Connect your SugarCRM Account </a><br />
					<a href="sugaroauth/testSugarOAuth.php"> Test your stored SugarCRM OAuth data </a><br />
			</div>

			</div>
		</div>
			<?php include 'include/lcapage.php'; ?>
		</div>
		<?php
flush();
require_once 'include/multiuseFunctions.php';
$config = parse_ini_file( 'lcaToolsConfig.ini' );
$NCMEC_URL_Production = $config['NCMEC_URL_Production'].'status';
$NCMEC_URL_Test = $config['NCMEC_URL_Test'].'status';
$produsername = $config['NCMEC_user_prod'];
$prodpassword = $config['NCMEC_password_prod'];
$testusername = $config['NCMEC_user_test'];
$testpassword = $config['NCMEC_password_test'];

$prodresult = NCMECstatus( $produsername, $prodpassword, $NCMEC_URL_Production );
if ( $prodresult === '0' ) {
	echo "<script> $('#ncmec-prod').attr('src', '/images/Dialog-accept.svg');</script>".PHP_EOL;
} else {
	echo "<script> $('#ncmec-prod').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
}
flush();
$testresult = NCMECstatus( $testusername, $testpassword, $NCMEC_URL_Test );
if ( $testresult === '0' ) {
	echo "<script> $('#ncmec-test').attr('src', '/images/Dialog-accept.svg');</script>".PHP_EOL;
} else {
	echo "<script> $('#ncmec-test').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
}
flush();?>
	</body>
</html>
