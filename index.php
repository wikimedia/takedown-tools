<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>DMCA Takedowns</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/lca.js'></script>
	<style type='text/css'>
	<!--/* <![CDATA[ */
	@import 'css/main.css'; 
	@import 'css/lca.css';
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
					<img style="display:block; margin:auto;" width="500px" src="images/roryshield.jpg" />
					<u> Reporting forms </u> <br />
					<a href="legalTakedown.php">DMCA Takedown Form</a> <br />
					<a href="NCMECreporting.php"> Child Protection Takedown Form </a> <br />
					<a href="basicRelease.php"> Basic Release of Confidential Information <a> <br /> <br />

					<u> 'special' pages </u> <br />
					<a href="CentralLog.php"> Central submission Log </a>
			</div>

			</div>
		</div>
			<div id='column-one'>
				<div role="navigation" id="p-personal" class="portlet">
					<div class="pBody">
						<ul>
							<li id="pt-login">You are logged in as <b><u><?php echo $_SERVER['PHP_AUTH_USER']; ?></u></b> if you are done or not you <b><a href="#" onclick="javascript: logout();">log out </a> </b></li>
						</ul>
					</div>
				</div>
				<div class='portlet' id='p-logo' role='banner'>
					<a href='index.php' title='Back home'></a>
				</div>
				<div class='no-text-transform portlet' id='p-navigation' role='navigation'>
					<h3>LCA links</h3>
					<div class='pBody'>
						<ul>
							<li id='lca-central-link'>
								<a href='https://sites.google.com/a/wikimedia.org/lca-central/'>LCA Central</a>
							</li>
							<li id='officewiki-link'>
								<a href='https://office.wikimedia.org/wiki/Main_Page'>Office Wiki</a>
							</li>
							<li id='collabwiki-link'>
								<a href='https://collab.wikimedia.org/wiki/Main_Page'>Collab Wiki</a>
							</li>
						</ul>
					</div>
				</div>
				<div class='no-text-transform portlet' id='p-navigation' role='navigation'>
					<h3>LCATools Forms</h3>
					<div class='pBody'>
						<ul>
							<li id='dmca-takedown-form'>
								<a href="legalTakedown.php">DMCA Takedown Form</a>
							</li>
							<li id='ncmec-form'>
								<a href="NCMECreporting.php"> Child Protection Takedown Form </a>
							</li>
							<li id='basic-release'>
								<a href="basicRelease.php"> Basic Release of Confidential Information </a>
							</li>
						</ul>
					</div>
				</div>
				<div class='no-text-transform portlet' id='p-navigation' role='navigation'>
					<h3>LCATools Special Pages</h3>
					<div class='pBody'>
						<ul>
							<li id='dmca-takedown-form'>
								<a href="CentralLog.php"> Central submission Log </a>
							</li>
						</ul>
					</div>
				</div>
				<div class='no-text-transform portlet' id='p-dmcalinks' role='navigation'>
					<h3>DMCA related links</h3>
					<div class='pBody'>
						<ul>
							<li id='comdmca-link'>
								<a href='https://commons.wikimedia.org/wiki/COM:DMCA'>Commons DMCA Page</a>
							</li>
							<li id='comvp-link'>
								<a href='https://commons.wikimedia.org/wiki/Commons:Village_pump'>Commons Village Pump</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>