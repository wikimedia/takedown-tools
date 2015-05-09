<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2014-01-02

Show details of submitted entries (accessed by clicking on log title)

---------------------------------------------   */

require_once 'core-include/multiuseFunctions.php';
date_default_timezone_set( 'UTC' );

$config = parse_ini_file( 'lcaToolsConfig.ini' );

//Details available variable set to false (no details available) by default
$detailsavailable = false;

// What log entry are we looking up?
$drillto = ( !empty( $_GET['logid'] ) ) ? intval( $_GET['logid'] ) : null;

if ( $drillto ) {
	$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
	$mysql->set_charset( "utf8" );

	$initialLookup = 'SELECT id,type,title FROM centrallog WHERE id=?';

	$logLookup = $mysql->prepare( $initialLookup );
	if ( $logLookup === false ) {
		echo 'Error while preparing: ' . $initialLookup . ' Error text: ' . $mysql->error, E_USER_ERROR;
	}

	$logLookup->bind_param( 'i', $drillto );
	$logLookup->execute();

	$logLookup->bind_result( $id, $type, $title );

	while ( $logLookup->fetch() ) {
		$logData[] = array ( 'id' => $id, 'type' => $type, 'title' => $title );
	}

	if ( $logData[0]['type'] ) {
		$logType = $logData[0]['type'];
	} else {$logType = null;}

	if ( $logType === 'Release' ) {
		$detailLookup = 'SELECT * FROM basicrelease WHERE log_id='.$drillto;
		$detailResults = $mysql->query( $detailLookup );
		if ( $detailResults === false ) {
			echo 'Error while querying: ' . $detailResults . ' Error text: ' . $mysql->error, E_USER_ERROR;
		}

		if ( $detailResults->num_rows > 0 ) {
			$logDetails = $detailResults->fetch_assoc();
			$detailsavailable = true;

			$who_received = unserialize( stripslashes( $logDetails['who_received'] ) );
			$pre_approved = $logDetails['pre_approved'];
			$why_released = unserialize( stripslashes( $logDetails['why_released'] ) );
			$who_released = $logDetails['who_released'];
			$who_released_to = $logDetails['who_released_to'];
			$released_to_contact = $logDetails['released_to_contact'];
			$details = $logDetails['details'];
			$user = $logDetails['user'];
			$timestamp = $logDetails['timestamp'];
			$istest = $logDetails['test'];
		}
	}

	if ( $logType === 'DMCA' ) {
		$detailLookup = 'SELECT * FROM dmcatakedowns WHERE log_id='.$drillto;
		$detailResults = $mysql->query( $detailLookup );
		if ( $detailResults === false ) {
			echo 'Error while querying: ' . $detailResults . ' Error text: ' . $mysql->error, E_USER_ERROR;
		}

		if ( $detailResults->num_rows > 0 ) {
			$logDetails = $detailResults->fetch_assoc();
			$detailsavailable = true;

			$user = $logDetails['user'];
			$timestamp = $logDetails['timestamp'];
			$ce_url = $logDetails['ce_url'];
			$takedown_date = $logDetails['takedown_date'];
			//hack at the moment since I switched to serialized when it didn't used to be
			if ( isset( $logDetails['involved_user'] ) ) {
				$userarray = unserialize( $logDetails['involved_user'] );
				if ( $userarray === false && $logDetails['involved_user'] !== 'b:0;' ) {
			    	$involved_user = $logDetails['involved_user'];
				} else {
					$involved_user = $userarray;
				}

			}

			$linksarray = unserialize( stripslashes( $logDetails['files_affected'] ) );
			$wmfwiki_title = $logDetails['wmfwiki_title'];
			$commons_title = $logDetails['commons_title'];
			$filessent = unserialize( stripslashes( $logDetails['files_sent'] ) );
			$logging_metadata = unserialize( stripslashes( $logDetails['logging_metadata'] ) );
			$strike_note = unserialize( stripslashes( $logDetails['strike_note'] ) );
			$sender_country = $logDetails['sender_country'];
			$sender_state = $logDetails['sender_state'];
			$sender_city = $logDetails['sender_city'];
			$sender_zip = $logDetails['sender_zip'];
			$action_taken = $logDetails['action_taken'];
			$takedown_method = $logDetails['takedown_method'];
			$istest = $logDetails['test'];

		}


	}

	if ( $logType === 'Child Protection' ) {
		$detailLookup = 'SELECT * FROM ncmecrelease WHERE log_id='.$drillto;
		$detailResults = $mysql->query( $detailLookup );
		if ( $detailResults === false ) {
			echo 'Error while querying: ' . $detailResults . ' Error text: ' . $mysql->error, E_USER_ERROR;
		}

		if ( $detailResults->num_rows > 0 ) {
			$logDetails = $detailResults->fetch_assoc();
			$detailsavailable = true;

			$user = $logDetails['user'];
			$submittime = $logDetails['timestamp'];
			$uploaderusername = $logDetails['username'];
			$project = $logDetails['project'];
			$incfilename = $logDetails['filename'];
			if ( $logDetails['legalapproved'] === 'Y' ) {
				$legalapproved = 'Yes';
			} elseif ( $logDetails['legalapproved'] === 'N' ) {
				$legalapproved = 'No';
			} else { $legalapproved = 'Huh? DB Confused';}
			$whoapproved = $logDetails['whoapproved'];
			$whynotapproved = $logDetails['whynotapproved'];
			$logdata = unserialize( stripcslashes( $logDetails['logging_metadata'] ) );
			$details = $logDetails['logging_details'];
			$istest = $logDetails['test'];
			$reportID = $logDetails['report_id'];

			$getfilehash = 'SELECT HEX(hash) FROM submittedfilehashes WHERE clog_id='.$drillto;
			$hashresults = $mysql->query( $getfilehash );
			if ( $hashresults === false ) {
				echo 'Error while querying: ' . $hashresults . ' Error text: ' . $mysql->error, E_USER_ERROR;
			}

			if ( $hashresults->num_rows > 0 ) {
				$hasharray = $hashresults->fetch_assoc();
				$hash = $hasharray['HEX(hash)'];
			}

		}
	}

	if ( $logType === 'Log update' ) {
		$detailLookup = 'SELECT * FROM logupdates WHERE log_id='.$drillto;
		$detailResults = $mysql->query( $detailLookup );

		if ( $detailResults === false ) {
			echo 'Error while querying: ' . $detailResults . ' Error text: ' . $mysql->error, E_USER_ERROR;
		}

		if ( $detailResults->num_rows > 0 ) {
			$logDetails = $detailResults->fetch_assoc();
			$detailsavailable = true;
			$redirectid=$logDetails['old_log'];
			$redirectURL = $config['toolsurl'].'logDetails.php?logid='.$redirectid;
			header( 'Location: '.$redirectURL );
		}
	}

	$mysql->close();
}




?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>LCA Tools Log Detail</title>
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
				<h1>Log Details</h1>
				<?php echo $logType;
if ( !$detailsavailable ) {
	echo 'The details for the log entry that you clicked are not currently available, this could be for many reasons including:
					<ul>
					<li> You clicked on a very early log entry before data was saved for that log type. </li>
					<li> You visisted this page without actually clicking on a log entry (do you see a ?log=# piece of the url? where # is a number?).</li>
					<li> Logging for this type is not yet implemented. </li>
					<li> There has been a mysql error and we were unable to get the log details (if this is the case you likely see another error on top of of the page).</li>
					<li> The programmer screwed something up, in which case you should go dock him over the head (after verifying he screwed up) and/or give him booze. </li>
					</ul>
					<p> You can get back to the log by clicking <a href="centralLog.php">HERE</a>';
} elseif ( $logType == 'Release' ) {
	include 'project-include/releaseDetail.php';
} elseif ( $logType == 'Child Protection' ) {
	include 'project-include/ncmecdetail.php';
} elseif ( $logType == 'DMCA' ) {
	include 'project-include/dmcadetails.php';
} else { echo var_dump( $logDetails ); }

?>
				</div>
			</div>
			<?php include 'project-include/page.php'; ?>
		</div>
	</body>
</html>
