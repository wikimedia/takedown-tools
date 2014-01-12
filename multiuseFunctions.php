<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)
			
Date of creation : 2013-12-22
Last modified : 2014-01-02

Plugin providing functions which could be used in multiple LCA tools and/or multiple instances of the same tool.
Stored here mostly to keep main files cleaner.
			
---------------------------------------------   */

$config = parse_ini_file('lcaToolsConfig.ini');
$dbaddress = $config['database_address'];
$dbuser = $config['database_user'];
$dbpw = $config['database_password'];
$db = $config['database'];
libxml_use_internal_errors(true);

$NCMECusername = $config['NCMEC_user'];
$NCMECpassword = $config['NCMEC_password'];

function setupdataurl($inputfile) {
	/* in case a real file is passed instead of _FILES (should not happen in current setup) 
	or in case something went wrong and file is not stored in system anymore. */
	if (!array_key_exists('tmp_name', $inputfile)) {
		if (array_key_exists('name', $inputfile)) {
			$inputfile['tmp_name'] = $inputfile['name'];
		} else {
			return 'No file appears to be present, please try again';
		}
	}

	$tempfile = array();
	$tempfile['kind'] = 'original';
	$tempfile['file_name'] = $inputfile['name'];
	$datatemp = file_get_contents($inputfile['tmp_name']);
	$datatemp = base64_encode($datatemp);
	$uri = 'data: '. $inputfile['type'].';base64,'.$datatemp;
	$tempfile['file'] = $uri;
	
	return $tempfile;
}

function curlAPIpost ($url,$data,$headers='') {

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_VERBOSE, true);

$result = curl_exec($ch);
curl_close($ch);
return $result;
}


function lcalog($user,$type,$title,$test) {

	global $dbaddress, $dbuser, $dbpw, $db;

	$mysql = new mysqli($dbaddress,$dbuser,$dbpw,$db);
	$mysql->set_charset("utf8");

	$template = 'INSERT INTO centrallog (user,timestamp,type,title,test) VALUES (?,?,?,?,?)';

	$submittime = gmdate("Y-m-d H:i:s", time());

	$log = $mysql->prepare($template);
	if ($log === false) {
		echo 'Error while preparing: ' . $template . ' Error text: ' . $mysql->error, E_USER_ERROR;
	}

	$log->bind_param('sssss',$user,$submittime,$type,$title,$test);

	$log->execute();

	return $log->insert_id;
	$mysql->close();
}

// Following 2 functions Copyright CC 3.0 attribution PHP Group from http://creativecommons.org/licenses/by/3.0/legalcode - from http://www.php.net/manual/en/domdocument.schemavalidate.php#62032
function libxml_display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

function libxml_display_errors() {
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}

function NCMECsimpleauthdcurlPost($url,$data) {
	global $NCMECusername, $NCMECpassword;

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_USERPWD, $NCMECusername.":".$NCMECpassword);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function curlauthdAPIpost ($url,$data,$headers='') {
	global $NCMECpassword, $NCMECusername;

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_USERPWD, $NCMECusername.":".$NCMECpassword);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

$result = curl_exec($ch);
curl_close($ch);
return $result;
}

function NCMECstatus($url) {
	global $NCMECusername, $NCMECpassword;

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, $NCMECusername.":".$NCMECpassword);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	$result = curl_exec($ch);
	curl_close($ch);
	$responseXML = new DOMDocument();
	$responseXML->loadXML($result);
	$responseNodes = $responseXML->getElementsByTagName('responseCode');
	
	if ($responseNodes->length==0) {
		$responsecode = null;
	} else {
		foreach ($responseNodes as $r) {
		$responsecode = $r->nodeValue;
		}
	}
	return $responsecode;
}


