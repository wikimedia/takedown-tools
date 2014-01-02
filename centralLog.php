<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)
			
Date of Creation: 2013-12-30
Last modified: 2014-01-02

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

if ($sortby == 'id')
{
	$sql .= ' ORDER BY id';
}
elseif ($sortby == 'user')
{
	$sql .= ' ORDER BY user';
}
elseif ($sortby == 'time')
{
	$sql .= ' ORDER BY timestamp';
}
elseif($sortby == 'type')
{
	$sql .= ' ORDER BY type';
}
elseif($sortby == 'title')
{
	$sql .= ' ORDER BY title';
}
elseif($sortby == 'test')
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

$nextoffset = $offset + $limit;

if (($offset - $limit) < 0) {
	$backoffset = 0;
} else {
	$backoffset = $offset - $limit;
}

$nextquery = http_build_query(array_merge($_GET,array('offset' => $nextoffset)));
$nexturl = $_SERVER['PHP_SELF'].'?'.$nextquery;

$backquery = http_build_query(array_merge($_GET,array('offset' => $backoffset)));
$backurl = $_SERVER['PHP_SELF'].'?'.$backquery;

?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>DMCA Takedowns</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/jquery-1.10.2.min.js'></script>
	<script src='scripts/lca.js'></script>
	<script>
	$(document).ready(function(){
		var selected = <?php echo "'".$sortby."'";?>;
		$("select#sort option").filter(function() {
			return $(this).val() == selected;
		}).prop('selected', true);
	});

	</script>
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
				<fieldset>
					<legend>Log manipulation options</legend>
					<form method='GET'>
						<table border='0' id='mw-movepage-table'> 
							<tr>
								<td>
									<label for='sort'>Sort by:</label>
								</td>
								<td>
									<select name='sort' id='sort'>
										<option value='id'> ID </option>
										<option value='user'> User submitting </option>
										<option value='time'> Time submitted </option>
										<option value='title'> Submittion title </option>
										<option value='test'> If test </option>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<label for='limit'> How many items should be shown at once? </label>
								</td>
								<td>
									<input name='limit' id='limit' type='text' size='10' value='<?php echo $limit; ?>' />
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<input type='submit' value='Submit'>
								</td>
							</tr>
						</table>
					</form>
				</fieldset>
				<fieldset>
					<legend>Log data </legend>
					<table border='1' id='mw-movepage-table'> 
						<tr>
							<td colspan='6'>
								<span style='float:left;'><?php if ($offset==0) {
									echo '<b> &#60;&#60; BACK </b>';
								} else {
									echo '<a href="'.$backurl.'"> &#60;&#60; BACK </a>';
								} ?></span>
								<span style='float:right'><?php if (($offset + $limit) > $rows_returned) {
									echo '<b> NEXT &#62;&#62; </b>';
								} else {
									echo '<a href="'.$nexturl.'"> NEXT &#62;&#62; </a>';
								} ?></span>
							</td>
						</tr>
						<tr>
							<th> ID </th>
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
							echo '<td> '. '<a href="logDetails.php?logid='.$row['id'].'">'. $row['title'] . '</a></td>';
							echo '<td> '. $row['test'] . '</td>';
							echo '</tr>';
							$i++;
						}
						?>
					</table>
				</fieldset>
			</div>
		</div>
			<?php include('include/lcapage.php'); ?>
		</div>
	</body>
</html>