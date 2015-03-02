<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)

Date of creation : 2013-12-07
Last modified : 2014-01-02

Thanks to Quentinv57 (of the Wikimedia projects) for some of the inspiration for the start.

Universal form to assist in DMCA takedowns by LCA team.

Part 1. Simple form for all information and wiki code spit out- Complete 2013-12-09
Part 2. Submit data to Chilling Effects - in process 2013-12-18

---------------------------------------------   */

require_once dirname( __FILE__ ) . '/../include/multiuseFunctions.php';
require_once dirname( __FILE__ ) . '/../include/OAuth.php';
require_once dirname( __FILE__ ) . '/../include/sugar.class.php';
date_default_timezone_set( 'UTC' );

//null variables that may or may not be set later depending on how it goes
$locationURL = null;
$filessent = null;

// cast config and log variables
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$sendtoCE = $config['sendtoCE'];
$user = $_SERVER['PHP_AUTH_USER'];

// cast test and update variable
if ( isset( $_POST['is-test'] ) && $_POST['is-test'] === 'Yes' ) {
	$istest = 'Y';
} else {
	$istest = 'N';
}

if ( isset( $_POST['isUpdate'] ) && isset( $_POST['updateLogEntry'] ) ) {
	$isUpdate = true;
	$oldlogid = $_POST['updateLogEntry'];
} else {
	$isUpdate = false;
}

$involved_users = !isset( $_POST['involved-user'] ) ? null : $_POST['involved-user'];
$logging_metadata = !isset( $_POST['logging-metadata'] ) ? null : $_POST['logging-metadata'];
$strike_note = !isset( $_POST['strike-note'] ) ? null : $_POST['strike-note'];
if ( is_array( $strike_note ) && in_array( 'other', $strike_note ) ) {
	$strike_note[array_search( 'other', $strike_note )] = 'Other: ' . $_POST['strike-note-other'];
}
$sender_name = !isset( $_POST['sender-name'] ) ? null : $_POST['sender-name'];
$sender_person = !isset( $_POST['sender-person'] ) ? null : $_POST['sender-person'];
$sender_firm = !isset( $_POST['sender-firm']) ? null : $_POST['sender-firm'];
$sender_address1 = !isset( $_POST['sender-address1'] ) ? null : $_POST['sender-address1'];
$sender_address2 = !isset( $_POST['sender-address2'] ) ? null : $_POST['sender-address2'];
$sender_city = !isset( $_POST['sender-city'] ) ? null : $_POST['sender-city'];
$sender_zip = !isset( $_POST['sender-zip'] ) ? null : $_POST['sender-zip'];
$sender_state = !isset( $_POST['sender-state'] ) ? null : $_POST['sender-state'];
$sender_country = !isset( $_POST['sender-country'] ) ? null : $_POST['sender-country'];
$takedown_date = !isset( $_POST['takedown-date'] ) ? null : $_POST['takedown-date'];
$action_taken = !isset( $_POST['action-taken'] ) ? null : $_POST['action-taken'];
$takedown_title = !isset( $_POST['takedown-title'] ) ? null : $_POST['takedown-title'];
$takedown_method = !isset( $_POST['takedown-method'] ) ? null : $_POST['takedown-method'];
$takedown_subject = !isset( $_POST['takedown-subject'] ) ? null : $_POST['takedown-subject'];
$takedown_text = !isset( $_POST['takedown-body'] ) ? null : $_POST['takedown-body'];
$project_involved = !isset( $_POST['project'] ) ? null : $_POST['project'];

if ( $project_involved == 'enwiki' ) {
	$linkbase = 'https://en.wikipedia.org';
} else {
	$linkbase = 'https://commons.wikimedia.org';
}


// cast form ce-send variable.
if ( isset( $_POST['ce-send'] ) && $_POST['ce-send'] === 'Yes' ) {
	$formsendtoCE = true;
} else {
	$formsendtoCE = false;
}


if ( !empty( $_POST['files-affected'] ) ) {
	$filearray = $_POST['files-affected'];
	// Error check for file prefix
	foreach ($filearray as $key => $value) {
		if ( substr( $value, 0, 5 ) == 'File:' || substr( $value, 0, 5 ) == 'file:' ) {
			$filearray[$key] = substr( $value, 5 );
		}
	}
}

if ( !empty( $_POST['pages-affected'] ) ) {
	$pagesarray = $_POST['pages-affected'];
}

if ( !empty( $filearray ) ) {
	foreach ( $filearray as $value ) {
		$linksarray[] = $linkbase.'/wiki/File:'.$value;
	}
}

if ( !empty( $pagesarray ) ) {
	foreach ( $pagesarray as $value ) {
		$linksarray[] = $linkbase.'/wiki/'.$value;
	}
}



?>


<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>Legal Takedowns</title>
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
				<h1>Processed Takedown</h1>
				<fieldset>
					<legend> Process status </legend>
					<table>
						<tr>
							<td>
								<u> Step 1: </u> Send report to Chilling Effects
							</td>
							<td>
								<img id='senttoce' src='/images/List-remove.svg' width='40px'/>
							</td>
						</tr>
						<tr>
							<td colspan='2'>
								<div id='celink'></div>
							</td>
						</tr>
						<tr>
							<td>
								<u> Step 2: </u> Create sugarcase
							</td>
							<td>
								<img id='sugarcase' src='/images/List-remove.svg' width='40px'/>
							</td>
						</tr>
						<tr>
							<td colspan='2'>
								<div id='caselink'></div>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>Debugging and double checking information for James</legend>
					<table>
						<tr>
							<td>
								User table info:
							</td>
							<td>
								<textarea><?php echo print_r( $usertable ) ?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								CE info being sent?
							</td>
							<td>
								<textarea><?php echo $sendtoCE.$formsendtoCE?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Takedown users
							</td>
							<td>
								<textarea><?php echo !empty( $involved_users ) ? var_dump( $involved_users ) : ""?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Var dump of filearray
							</td>
							<td>
								<textarea><?php echo !empty( $filearray ) ? var_dump( $filearray ) : ""?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								print of php array for CE
							</td>
							<td>
								<textarea><?php echo var_dump( $CE_post_data );?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								print of json for CE
							</td>
							<td>
								<textarea><?php echo json_encode( $CE_post_data, JSON_UNESCAPED_SLASHES );?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Print of response from CE
							</td>
							<td>
								<textarea><?php echo print_r( $result );?></textarea>
					</table>
				</fieldset>
			</div>
		</div>
			<?php include dirname( __FILE__ ) . '/../include/lcapage.php'; ?>
		</div>
		<?php

		// Set up file uploads if they exist.
		if ( is_uploaded_file( $_FILES['takedown-files']['tmp_name'][0] ) ) {

			foreach ( $_FILES['takedown-files']['tmp_name'] as $key => $value ) {

				$tempfile = array();
				$tempfile['kind'] = 'original';
				$datatemp = file_get_contents( $_FILES['takedown-files']['tmp_name'][$key] );
				$datatemp = base64_encode( $datatemp );
				$uri = 'data:'.$_FILES['takedown-files']['type'][$key].';base64,'.$datatemp;
				$tempfile['file'] = $uri;
				$tempfile['file_name'] = $_FILES['takedown-files']['name'][$key];

				$CE_post_files[] = $tempfile;
				$filessent[] = $_FILES['takedown-files']['name'][$key];
			}

		}

		// Set up initial post data for Chilling Effects
		$CE_post_data = array (
			'authentication_token' => $config['CE_apikey'],
			'notice' => array (
				'title' => $takedown_title,
				'type' => $_POST['ce-report-type'],
				'subject' => $takedown_subject,
				'date_sent' => $takedown_date,
				'source' => $takedown_method,
				'action_taken' => $action_taken,
				'body' => $takedown_text,
				'tag_list' => 'wikipedia, wikimedia',
				'jurisdiction_list' => 'US, CA',
			),
		);

		$CE_post_entities = array (
			array (
				'name' => 'recipient',
				'entity_attributes' => $config['CE_recipient'],
			),
			array (
				'name' => 'sender',
				'entity_attributes' => array (
					'name' => $sender_name,
					'address_line_1' => $sender_address1,
					'address_line_2' => $sender_address2,
					'city' => $sender_city,
					'state' => $sender_state,
					'zip' => $sender_zip,
					'country_code' => $sender_country,
				),
			),
		);

		$CE_post_data['notice']['entity_notice_roles_attributes'] = $CE_post_entities;

		if ( !empty( $linksarray ) ) {

			foreach ( $linksarray as $key => $value ) {
				$urlarray[] = array ( 'url' => $value );
			}
			$CE_post_works[] = array (
				'infringing_urls_attributes' => $urlarray,
			);
		}

		$CE_post_data['notice']['works_attributes'] = $CE_post_works;

		if ( !empty( $CE_post_files ) ) {
			$CE_post_data['notice']['file_uploads_attributes'] = $CE_post_files;
		}

		$CE_post = json_encode( $CE_post_data );

		$apiurl = $linkbase.'/w/api.php';
		$usertable = getUserData( $user );
		$mwsecret = $usertable['mwsecret'];
		$mwtoken = $usertable['mwtoken'];

		// Set up headers for Chilling Effects submission
		$CE_post_headers = array (
			'Accept: application/json',
			'Content-Type: application/json',
			'Content-Length: ' . strlen( $CE_post ),
		);

		// send to Chilling Effects
		if ( $sendtoCE && $formsendtoCE ) {
			$result = curlAPIpost( $config['CE_apiurl'], $CE_post, $CE_post_headers );

			$headers = explode( "\n", $result );
			foreach ( $headers as $header ) {
				if ( stripos( $header, 'Location:' ) !== false ) {
					$locationURL = substr( $header, 10 );
					$locationURL = trim( $locationURL );
				}
			}
		}



			if ( $sendtoCE && $formsendtoCE ) {

			if ( isset( $locationURL ) ) {
				echo "<script> $('#celink').html('The DMCA Takedown was sent to Chilling Effects and you can find the submission at <a href=\'".$locationURL."\' target=\'_blank\'>".$locationURL."</a>');</script>".PHP_EOL;
				echo "<script> $('#senttoce').attr('src', '/images/Dialog-accept.svg'); </script>".PHP_EOL;
			} 
		} else {
				echo "<script> $('#celink').html('It does not appear that a report was sent to Chilling Effects <br /> If there is a problem please see James or look at the debug section at the button of the page for the response from CE');</script>".PHP_EOL;
				echo "<script> $('#senttoce').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
			}


		//log into central log
		if ( $isUpdate ) {
			$log_type = 'Log update';
			$log_title = 'Updated log entry '.$oldlogid;
		} else {
			$log_type = 'DMCA';
			$log_title = 'Non posted DMCA takedown notice, '.$sender_name;
		}
		$log_row = lcalog( $user, $log_type, $log_title, $istest );

		if ( $isUpdate ) {
			$logid = $oldlogid;
		} else {
			$logid = $log_row;
		}


		if ( !$isUpdate ) {
			//create sugar case if needed

			$casedata['name'] = $log_title;
			$logurl = $config['toolsurl'].'logDetails.php?logid='.$logid;
			$casedata['description'] = $logurl;
			$casedata['resolution'] = $logurl;
			$casedata['status'] = 'Closed';
			$casedata['type'] = 'ca_dmca';
			$notedata['name'] = 'takedown notes';
			$notedata['parent_type'] = 'Cases';
			$noteusers = '';
			foreach ( $involved_users as $nameid => $involvedname ) {
				$noteusers .= $involvedname.'
				';
			}
			if ( !isset( $locationURL ) ) {
				$locationURL = 'This takedown was not sent to Chilling Effects';
			}
			$notedata['description'] = '
			Foundation Wiki Takedown Post: https://www.wikimediafoundation.org/wiki/'.htmlentities( str_replace( ' ', '_', $wmfwiki_title ) ).'
			Link to Chilling Effects: '.$locationURL.'
			User(s) who added content: 
			'.$noteusers;

			if ( $istest == 'Y' ) {
				$casedata['description'] .= '
				This submission was marked as a test';
			}

			$sugarapiurl = $config['sugar_url'].'/service/v4_1/rest.php';
			$sugarbaseurl = $config['sugar_url'].'/index.php';
			$sugarconsumerkey = $config['sugarconsumer_key'];
			$sugarconsumersecret = $config['sugarconsumer_secret'];
			$sugarsecret = $usertable['sugarsecret'];
			$sugartoken = $usertable['sugartoken'];

			if ( isset( $sugarsecret ) && isset( $sugartoken ) ) {
				$sugar = new sugar( $sugarconsumerkey, $sugarconsumersecret, $sugarapiurl, $sugartoken, $sugarsecret );

				$login = $sugar->login();

				if ( $login ) {
					$caseid = $sugar->create_case( $casedata );

					if ( $caseid ) {
						$sugarurl = $sugarbaseurl.'?module=Cases&action=detailview&record='.$caseid;
						$notedata['parent_id'] = $caseid;
						$noteid = $sugar->create_note( $notedata );
						if ( $noteid ) {
							echo "<script> $('#caselink').html('A sugar case was created which you can find <u> <a href=\'".$sugarurl."\' target=\'_blank\'>HERE</a></u>');</script>".PHP_EOL;
							echo "<script> $('#sugarcase').attr('src', '/images/Dialog-accept.svg'); </script>".PHP_EOL;
						} 
					}
				}
			} else {
					echo "<script> $('#caselink').html('It does not appear that a sugar case was completed (you may not have connected your account) <br /> If there is a problem please see James.');</script>".PHP_EOL;
					echo "<script> $('#sugarcase').attr('src', '/images/Dialog-error-round.svg'.svg'); </script>".PHP_EOL;
				}
		} else {
			echo "<script> $('#caselink').html('It does not appear that a sugar case was completed because this is just an updated of an old log entry <br /> If there is a problem please see James.');</script>".PHP_EOL;
		}




		// Get ready to store full data in database

		$mysql = new mysqli( $dbaddress, $dbuser, $dbpw, $db );
		$mysql->set_charset( "utf8" );

		$submittime = gmdate( "Y-m-d H:i:s", time() );
		$insert_user = $user;
		$insert_sender_city = $sender_city;
		$insert_sender_zip = $sender_zip;
		$insert_sender_state = $sender_state;
		$insert_sender_country = $sender_country;
		$insert_takedown_date = $takedown_date;
		$insert_action_taken = $action_taken;
		$insert_takedown_title = $takedown_title;
		$insert_takedown_method = $takedown_method;
		$insert_takedown_subject = $takedown_subject;
		$insert_involved_users = serialize( $involved_users );
		$insert_logging_metadata = serialize( $logging_metadata );
		$insert_strike_note = serialize( $strike_note );
		$insert_ce_url = $locationURL;
		$insert_filessent = serialize( $filessent );
		$insert_files_affected = serialize( $linksarray );

		// do it


		$template = 'INSERT INTO dmcatakedowns (log_id,user,timestamp,sender_city,sender_zip,sender_state,sender_country,takedown_date,action_taken,takedown_title,takedown_method,takedown_subject,involved_user,logging_metadata,strike_note,ce_url,files_sent,files_affected,test) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE sender_city = VALUES(sender_city), sender_zip = VALUES(sender_zip), sender_state = VALUES(sender_state), sender_country = VALUES(sender_country), takedown_date = VALUES(takedown_date), action_taken = VALUES(action_taken), takedown_title = VALUES(takedown_title), takedown_method = VALUES(takedown_method), takedown_subject = VALUES(takedown_subject), involved_user = VALUES(involved_user), logging_metadata = VALUES(logging_metadata), strike_note = VALUES(strike_note), ce_url = VALUES(ce_url), files_sent = VALUES(files_sent), files_affected = VALUES(files_affected), test = VALUES(test)';

		$insert = $mysql->prepare( $template );
		if ( $insert === false ) {
			echo 'Error while preparing: ' . $template . ' Error text: ' . $mysql->error, E_USER_ERROR;
		}

		$insert->bind_param( 'issssssssssssssssss', $logid, $insert_user, $submittime, $insert_sender_city, $insert_sender_zip, $insert_sender_state, $insert_sender_country, $insert_takedown_date, $insert_action_taken, $insert_takedown_title, $insert_takedown_method, $insert_takedown_subject, $insert_involved_users, $insert_logging_metadata, $insert_strike_note, $insert_ce_url, $insert_files_sent, $insert_files_affected, $istest );

		$insert->execute();

		// add redirect marker if update log entry
		if ( $isUpdate ) {
			$template = 'INSERT INTO logupdates (log_id, old_log) VALUES (?,?)';
			$insert = $mysql->prepare( $template );
			if ( $insert === false ) {
				echo 'Error while preparing: ' . $template . ' Error text: ' . $mysql->error, E_USER_ERROR;
			}
			$insert->bind_param( 'ii', $log_row, $oldlogid );
			$insert->execute();
		}

		$insert->close();

		?>
	</body>
</html>
