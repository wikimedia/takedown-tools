<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2015-03-01

Quick and Dirty tool to show feedback from 2015 Strategy Consultation

---------------------------------------------   */

$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
mb_internal_encoding( 'UTF-8' );
header( 'Content-Type: text/html; charset=UTF-8' );

// get and/or set up variables

$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db ); // set up mysql connection
$mysql->set_charset( "utf8" );

if ( $mysql->connect_error ) {
	echo 'Database connection fail: '  . $mysql->connect_error, E_USER_ERROR;
}

$countryrequest = 'SELECT DISTINCT country FROM strategycomments';

$countryquery = $mysql->query( $countryrequest );

if ( $countryquery === false ) {
	echo 'Bad SQL or no log: ' . $countryrequest . ' Error: ' . $mysql->error, E_USER_ERROR;
}

while ( $crow = $countryquery->fetch_assoc() ) {
	$countries[] = $crow['country'];
}

$homerequest = 'SELECT DISTINCT homewiki FROM strategycomments';

$homequery = $mysql->query( $homerequest );

if ( $homequery === false ) {
	echo 'Bad SQL or no log: ' . $homerequest . ' Error: ' . $mysql->error, E_USER_ERROR;
}

while ( $hrow = $homequery->fetch_assoc() ) {
	$homewikis[] = $hrow['homewiki'];
}

$lastupdaterequest = 'SELECT timestamp FROM strategycomments ORDER BY timestamp DESC LIMIT 1';

$lastupdatequery = $mysql->query( $lastupdaterequest );
$lastupdatearray = $lastupdatequery->fetch_assoc();
$lastupdate = $lastupdatearray['timestamp'];


$sql = 'Select * FROM strategycomments'; // basic query

$offset = ( !empty( $_GET['offset'] ) ) ? intval( $_GET['offset'] ) : 0; // offset starts at 0

$sortby = ( !empty( $_GET['sort'] ) ) ? $_GET['sort'] : "id"; // grab sort options

$order = ( !empty( $_GET['order'] ) ) ? $_GET['order'] : ""; // grab order options

$displaycountry = ( !empty( $_GET['displaycountry'] ) ) ? $_GET['displaycountry'] : 'all'; // grab test options

$displaywiki = ( !empty( $_GET['displaywiki'] ) ) ? $_GET['displaywiki'] : 'all'; // grab test options

if ( in_array( $displaycountry, $countries ) || in_array( $displaywiki, $homewikis ) || $displaycountry === 'allip' || $displaywiki === 'noips' ) {
	$sql .= ' WHERE';
}

if ( in_array( $displaycountry, $countries ) ) {
	$sql .= ' country=\''.$displaycountry.'\'';
}

if ( $displaycountry === 'allip' ) {
	$sql .= ' country <> \'Logged In User\'';
}

if ( in_array( $displaycountry, $countries ) && in_array( $displaywiki, $homewikis ) || in_array( $displaycountry, $countries ) && $displaywiki === 'noips' || in_array( $displaywiki, $homewikis ) && $displaycountry === 'allip' || $displaywiki === 'noips' && $displaycountry === 'allip' ) {
	$sql .= ' AND';
}

if ( in_array( $displaywiki, $homewikis ) ) {
	$sql .= ' homewiki=\''.$displaywiki.'\'';
}

if ( $displaywiki === 'noips' ) {
	$sql.= ' homewiki <> \'Unknown\'';
}

if ( $sortby == 'id' ) {
	$sql .= ' ORDER BY id';
}
elseif ( $sortby == 'user' ) {
	$sql .= ' ORDER BY user';
}
elseif ( $sortby == 'homewiki' ) {
	$sql .= ' ORDER BY homewiki';
}
elseif ( $sortby == 'globaledits' && $order != 'DESC' ) {
	$sql .= ' ORDER BY globaledits * 1';
}
elseif ( $sortby == 'globaledits' && $order = 'DESC' ) {
	$sql .= ' ORDER BY globaledits * 1 DESC, globaledits DESC';
}
elseif ( $sortby == 'metaedits' && $order != 'DESC' ) {
	$sql .= ' ORDER BY metaedits * 1';
}
elseif ( $sortby == 'metaedits' && $order = 'DESC' ) {
	$sql .= ' ORDER BY metaedits * 1 DESC, metaedits DESC';
} else {
	$sql .= ' ORDER BY id'; // fall back is always ID but want to still allow for order if someone hasn't set sortby.
}

if ( $order == 'DESC' && $sortby != 'globaledits' && $sortby != 'metaedits' ) {
	$sql .=' DESC';
} else {
	//do nothing
}

$limit = ( !empty( $_GET['limit'] ) ) ? intval( $_GET['limit'] ) : '50'; // 50 is the limit by default currently not showing other options but hidden ability
if ( $limit>5000 ) $limit = 5000; // limit can't be over 5000 for now

$results = $mysql->query( $sql );

if ( $results === false ) {
	echo 'Bad SQL or no log: ' . $sql . ' Error: ' . $mysql->error, E_USER_ERROR;
} else {
	$rows_returned = $results->num_rows;
}

$results->data_seek( $offset );

$nextoffset = $offset + $limit;

if ( ( $offset - $limit ) < 0 ) {
	$backoffset = 0;
} else {
	$backoffset = $offset - $limit;
}

$nextquery = http_build_query( array_merge( $_GET, array( 'offset' => $nextoffset ) ) );
$nexturl = $_SERVER['PHP_SELF'].'?'.$nextquery;

$backquery = http_build_query( array_merge( $_GET, array( 'offset' => $backoffset ) ) );
$backurl = $_SERVER['PHP_SELF'].'?'.$backquery;

?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>LCA Tools - 2015 Strategy Comments</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='/scripts/jquery-1.10.2.min.js'></script>
	<script src='/scripts/lca.js'></script>
	<script>
	$(document).ready(function(){
		var sort = <?php echo "'".$sortby."'";?>;
		$('select#sort option').filter(function() {
			return $(this).val() == sort;
		}).prop('selected', true);

		var order = <?php echo "'".$order."'";?>;
		$('select#order option').filter(function() {
			return $(this).val() == order;
		}).prop('selected', true);

		var fcountry = <?php echo "'".$displaycountry."'";?>;
		$('select#displaycountry option').filter(function() {
			return $(this).val() == fcountry;
		}).prop('selected', true);

		var fwiki = <?php echo "'".$displaywiki."'";?>;
		$('select#displaywiki option').filter(function() {
			return $(this).val() == fwiki;
		}).prop('selected', true);

	});

	</script>
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
				<h1> 2015 Strategy Comments </h1>
				<fieldset>
					<legend>Options</legend>
					<p><b> <u>Note:</u> Only IPs have a country set, any filter that limits to country ( or the All (no logged in) setting) will eliminate logged in users. </b></p>
					<p> <b><u>Note:</u> Only logged in users have a home wiki set, any filter that limited to home wikis (or the All (no IPs) setting) will eliminate logged our users. </b></p>
					<p><b><u>Last update:</u></b> <? echo $lastupdate; ?> UTC (data does not include current UTC day)</p>
					<p> Any questions or weird data issues can be directed to jalexander@wikimedia.org </p>
					<form method='GET'>
						<table border='0'>
							<tr>
								<td>
									<fieldset>
									<legend> Page Options </legend>
										<table border='0'>
											<tr>
												<td>
													<label for='sort'>Sort by:</label>
												</td>
												<td>
													<select name='sort' id='sort'>
														<option value='id'> ID </option>
														<option value='user'> User submitting </option>
														<option value='homewiki'> Home wiki </option>
														<option value='globaledits'> Global edits </option>
														<option value='metaedits'> Meta edits </option>
													</select>
												</td>
											</tr>
											<tr>
												<td>
													<label for='order'> Order the results: </label>
												</td>
												<td>
													<select name='order' id='order'>
														<option value='forwards'> Forwards </option>
														<option value='DESC'> Reverse </option>
													</select>
												</td>
											<tr>
												<td>
													<label for='limit'> How many items should be shown at once? </label>
												</td>
												<td>
													<input name='limit' id='limit' type='text' size='10' value='<?php echo $limit; ?>' />
												</td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td>
									<fieldset>
									<legend> Log Filter options </legend>
										<table border='0'>
											<tr>
												<td>
													<label for='displaycountry'> Display IPs from: </label>
												</td>
												<td>
													<select name='displaycountry' id='displaycountry'>
														<option value='all'> All (and logged in)</option>
														<option value='allip'> All (no logged in)</option>
														<?php
foreach ( $countries as $key => $value ) {
	echo '<option value="'.$value.'"> '.$value.' </option>';
}
?>
													</select>
												</td>
											</tr>
											<tr>
												<td>
													<label for='displaywiki'>Display user accounts from: </label>
												</td>
												<td>
													<select name='displaywiki' id='displaywiki'>
														<option value='all'> All wikis (and logged out) </option>
														<option value='noips'> All wikis (no IPs) </option>
														<?php
foreach ( $homewikis as $key => $value ) {
	echo '<option value="'.$value.'"> '.$value.' </option>';
}
?>
													</select>
												</td>
											</tr>
										</table>
									</fieldset>
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
							<td colspan='8'>
								<span style='float:left;'><?php if ( $offset==0 ) {
	echo '<b> &#60;&#60; BACK </b>';
} else {
	echo '<a href="'.$backurl.'"> &#60;&#60; BACK </a>';
} ?></span>
								<span style='float:right'><?php if ( ( $offset + $limit ) > $rows_returned ) {
	echo '<b> NEXT &#62;&#62; </b>';
} else {
	echo '<a href="'.$nexturl.'"> NEXT &#62;&#62; </a>';
} ?></span>
							</td>
						</tr>
						<tr>
							<th> ID </th>
							<th> User </th>
							<th> Country </th>
							<th> Home Wiki </th>
							<th> Global Edits </th>
							<th> Meta Edits </th>
							<th> Meta Registration </th>
							<th> Comment </th>
						</tr>
						<?php
$i = 0;
while ( ( $row = $results->fetch_assoc() ) && ( $i < $limit ) ) {
	echo '<tr>';
	echo '<td> '. $row['id'] . '</td>';
	echo '<td> '. $row['user'] . '</td>';
	echo '<td> '. $row['country'] . '</td>';
	echo '<td> '. $row['homewiki'] . '</td>';
	echo '<td> '. $row['globaledits'] . '</td>';
	echo '<td> '. $row['metaedits'] . '</td>';
	echo '<td> '. $row['metaregistration'] . '</td>';
	echo '<td> '. $row['comment'] . '</td>';
	echo '</tr>';
	$i++;
}
?>
					</table>
				</fieldset>
			</div>
		</div>
			<?php include 'lcapage-strategy.php'; ?>
		</div>
	</body>
</html>
