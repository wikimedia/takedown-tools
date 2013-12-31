<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)
			
Date of Creation: 2013-12-30
Last modified: 2013-12-30

Central log for submissions to LCA Tools
			
---------------------------------------------   */

$config = parse_ini_file('lcaToolsConfig.ini');
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];

// get and/or set up variables

$mysql = new mysqli($dbaddress,$dbuser,$dbpw,$db); // set up mysql connection

if ($mysql->connect_error) {
  echo 'Database connection fail: '  . $mysql->connect_error, E_USER_ERROR;
}

$sql = 'Select * FROM centrallog'; // basic query

$offset = (!empty($_GET['offset'])) ? intval($_GET['offset']) : 0; // offset starts at 0

$sortby = (!empty($_GET['sort'])) ? $_GET['sort'] : "none"; // grab sort options

if ($_GET['sort'] == 'id')
{
	$sql .= ' ORDER BY id';
}
elseif ($_GET['sort'] == 'user')
{
	$sql .= ' ORDER BY user';
}
elseif ($_GET['sort'] == 'time')
{
	$sql .= ' ORDER BY timestamp';
}
elseif($_GET['sort'] == 'type')
{
	$sql .= ' ORDER BY type';
}
elseif($_GET['sort'] == 'title')
{
	$sql .= ' ORDER BY title';
}
elseif($_GET['sort'] == 'test')
{
	$sql .= ' ORDER BY test';
}

$limit = (!empty($_GET['limit'])) ? intval($_GET['limit']) : '50'; // 50 is the limit by default currently not showing other options but hidden ability
	if ($limit>1000) $limit = 1000; // limit can't be over 1000 for now

$results = $mysql->query($sql);

if($results === false) {
  echo 'Bad SQL or no log: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR;
} else {
  $rows_returned = $results->num_rows;
}

$results->data_seek($offset);

?>

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
				<h1> LCA Central Submission Log </h1>
				
				<table border='1' id='mw-movepage-table'> 
					<tr>
						<th> <a>ID </th>
						<th> User </th>
						<th> Time (UTC) </th>
						<th> Type </th>
						<th> Title </th>
						<th> Test? </th>
					</tr>
					<?php
					$i = 0;
					while (($row = $results->fetch_assoc()) && ($i < $limit)) {
						echo '<tr>';
						echo '<td> '. $row['id'] . '</td>';
						echo '<td> '. $row['user'] . '</td>';
						echo '<td> '. $row['timestamp'] . '</td>';
						echo '<td> '. $row['type'] . '</td>';
						echo '<td> '. $row['title'] . '</td>';
						echo '<td> '. $row['test'] . '</td>';
						echo '</tr>';
						$i++;
					}
					?>
				</table>
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