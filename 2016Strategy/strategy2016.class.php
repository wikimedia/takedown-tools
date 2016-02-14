<?php
/**
 * Classes for the 2016 Strategy project
 *
 *
 *
 * @file
 * @author James Alexander
 * @copyright © 2016 James Ryan Alexander
 * @license MIT at http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder of this software
 * @version 1.0 - 2016-02-07
 */

require_once dirname( __FILE__ ) . '/../core-include/multiuseFunctions.php';
require_once dirname( __FILE__ ) . '/../core-include/OAuth.php';
require_once dirname( __FILE__ ) . '/../core-include/MWOAuthSignatureMethod.php';
require_once dirname( __FILE__ ) . '/../core-include/unirest-php/src/Unirest.php';

class strategyuser2016 {
	/**
	* internal class for commenator on the 2016 strategy process
	* 
	* @class 2016strategyuser
	*
	*/

	/**
	* User name
	*
	* @var string $username
	* The username or IP given/discovred by the user on the comments page
	*/
	public $username = null;

	/**
	*
	*
	* @var boolean $isip
	* Is this user an IP? False/null for no true for yes.
	*/
	public $isip = null;

	/**
	*
	*
	* @var string $homewiki
	* homewiki for user
	*/
	public $homewiki = null;

	/**
	*
	*
	* @var string $country
	* country location for IPs
	*/
	public $country = null;

	/**
	*
	*
	* @var string $globaledits
	* Global user edits for user
	*/
	public $globaledits = null;

	/**
	*
	*
	* @var string $metaregistration
	* Meta registration time for user
	*/
	public $metaregistration = null;

	/**
	*
	*
	* @var string $homeregistration
	* registration time for at homewiki
	*/
	public $homeregistration = null;

	/**
	*
	*
	* @var string $metaedits
	* number of edits on MetaWiki for user
	*/
	public $metaedits = null;

	/**
	*
	*
	* @var array $approaches
	* array showing how a user voted on approaches in the form:
	* knowledge => 1|2|-4 with positive being for and negative being aginst that numbered approach.
	*/
	public $approaches = null;

	/**
	*
	*
	* @var string $commentsreach
	* stored comments from the reach section
	*/
	public $commentsreach = null;

	/**
	*
	*
	* @var string $commentscommunities
	* stored comments from the communities section
	*/
	public $commentscommunities = null;

	/**
	*
	*
	* @var string $commentsknowledge
	* stored comments from the knowledge section
	*/
	public $commentsknowledge = null;

	/**
	*
	*
	* @var string $commentsgeneral
	* stored comments from general talk pages
	*/
	public $commentsgeneral = null;

	/**
	*
	*
	* @var int $checked
	* Whether the data needs to be sorted. 1 is already checked
	*/
	public $checked = 0;

	public function markip() {

		$this->isip = true;

		return  true;
	}

	public function ischecked() {

		$this->checked = 1;

		return true;
	}
}

	class strategy2016 {

	/**
	*  Class for 2016 Strategy process to operate on and determine different bits of data about them for analysis
	* 
	* @class 2016strategy
	*
	*/

	/**
	*
	*
	* @var resource $mysql
	* mysql resource for database lookups/inserts
	*/
	private $mysql = null;

	/**
	*
	*
	* @var array $users
	* array of strategy commentators/users
	*/
	private $users = array();

	/**
	*
	*
	* @var string $geolocateapi
	* api for geolocation database
	*/
	private $geolocateapi = null;

	/**
	*
	*
	* @var string $geolocatekey
	* api key for geolocation database
	*/
	private $geolocatekey = null;

	/**
	*
	*
	* @var string $mwapiurl
	* url for mediawiki api
	*/
	private $mwapiurl = null;

	/**
	*
	*
	* @var string $accessToken
	* access token for mediaWiki api user
	*/
	private $accessToken = null;

	/**
	*
	*
	* @var string $consumer
	* mediaWiki OAuth consumer to reuse
	*/
	private $consumer = null;

	/**
	*
	*
	* @var string $signer
	* mediawiki OAuth signging object to reuse
	*/
	private $signer = null;

	/**
	 * Construction function called whent the class is created.
	 *
	 * Construction class requires database and OAuth credentials and optionally takes geolocation login info and 
	 *  a cURL resource that already exists
	 *
	 * @param string $dbaddress 		database address/location
	 * @param string $dbuser 			database username
	 * @param string $dbpass 			database password
	 * @param string $database 			database to use
	 * @param string $mwapiurl 			mediaWiki api url
	 * @param string $consumerkey 		public key for mw OAuth consumer
	 * @param string $consumersecretkey	secret key for mw OAuth consumer
	 * @param string $mwtoken			public token for mwOAuth user
	 * @param string $mwsecret			secret token for mwOAuth user
	 * @param string $geolocateapi 		(optional) api for geolcation database if using
	 * @param string $geolocatekey		(optional) api key for geolocation database if using
	 * @param resource $ch 				(optional) Already existing cURL resource to use instead of creating one fresh.
	 *
	 */
	function __construct( $dbaddress, $dbuser, $dbpass, $database, $mwapiurl, $consumerkey, $consumersecretkey, $mwtoken, $mwsecret, $geolocateapi = null, $geolocatekey = null, resource $ch = null ) {

		$this->mysql = new mysqli( $dbaddress, $dbuser, $dbpass, $database );
		$this->mysql->set_charset( "utf8" );

		$this->mwapiurl = $mwapiurl;

		$this->consumer = new OAuthConsumer( $consumerkey, $consumersecretkey );
		$this->signer = new MWOAuthSignatureMethod_RSA_SHA1( new OAuthDataStore(), $consumersecretkey );

		$this->accessToken = new OAuthToken( $mwtoken, $mwsecret );

		if ( $geolocateapi && $geolocatekey ) {
			$this->geolocateapi = $geolocateapi;
			$this->geolocatekey = $geolocatekey;
		}

		$this->getcURL( $ch );

	}

	/**
	* Destruction class to be called at script completion.
	*
	* Called when the class is no longer in use or the calling script is finishing.
	* Used mostly to make sure we release the session so that no one else could use it.
	*/
	function __destruct() {
		curl_close( $this->ch );

	}



	/*************************************************************************************

	Private internal functions to retrieve necessary variables with appropriate fallback

	**************************************************************************************/

	/**
	 * Get cURL object, create if doesn't exist.
	 *
	 * Should always be used when cURL is going to be used so that we can use one cURL object and not open up too many threads.
	 * If $ch already exists function will immediatly return it, if not it will create one and then return it.
	 *
	 * @param resource $resource (optional) already existing cURL resource to add into the system
	 *
	 * @return cURL resource
	*/
	private function getcURL( resource $resource = null ) {
		if ( $resource ) {

			$this->ch = $resource;

		} elseif ( $this->ch ) {

			return $this->ch;

		} else {

			$this->ch = curl_init();
			return $this->ch;

		}
	}

	/**
	* Get geolocation of IP as requested
	*
	* @param string $ip 	IP to lookup country
	*
	* @return string of country name
	*/
	public function geolocate( $ip ) {

		$response = Unirest\Request::get("https://telize-v1.p.mashape.com/geoip/".$ip,
  array(
    "X-Mashape-Key" => $this->geolocatekey,
    "Accept" => "application/json"
  )
);

		$geodata = json_decode( $response->raw_body, true);

		if ( array_key_exists( 'country', $geodata ) ) {
				$country = $geodata['country'];
			} else {
				$country = $geodata['country_code'];
			}

		return $country;
	}

	/**
	* is this string an IP
	*
	* @param string $user 	IP to test
	*
	* @return boolean true or false
	*/
	public function isip( $user ) {

		if ( filter_var( $user, FILTER_VALIDATE_IP ) ) {

			return true;

		} else {

			return false;

		}
	}




	/*************************************************************************************

	public functions to create and populate a user

	**************************************************************************************/

	/**
	* Create new user from only a username and, optionally, populate initial meta data
	*
	* @param string $username
	* @param boolean $getmeta
	*
	* @return strategyuser2016 user object
	*/
	public function newUserFromUsername( $username, $getmeta = null ) {

		$newuser = new strategyuser2016();

		$newuser->username = $username;

		if ( $getmeta ) {

			$newuser = $this->populateMetaData( $newuser );
		}

		return $newuser;
	}

	/**
	* Function to populate meta data of user via mediawiki api
	* 
	* @param strategyuser2016 $userObject
	* @param boolean $pushtousers
	*
	* @return strategyuser2016 object
	*/
	public function populateMetaData( strategyuser2016 $userObject, $pushtousers = null ) {

		$username = $userObject->username;
		$ch = $this->getcURL();

		if ( $this->isip( $username ) ) {

			$userObject->markip();
			$userObject->country = $this->geolocate( $username );
			$userObject->homewiki = 'Unknown';

			if ( $pushtousers ) {

				array_push( $this->users, $userObject );

			}

			return $userObject;
		} else {

			$userObject->country = 'Logged In User';

		}

		// set up meta request
		$request = array(
				'action' => 'query',
				'list' => 'users|usercontribs',
				'ususers' => $username,
				'usprop' => 'editcount|groups|registration',
				'ucuser' => $username,
				'ucdir' => 'newer',
				'uclimit' => '1',
				'ucprop' => 'timestamp',
				'meta' => 'globaluserinfo',
				'guiuser' => $username,
				'guiprop' => 'editcount|groups|merged',
				'continue' => '',
				'format' => 'json',
			); 

			$api_req = OAuthRequest::from_consumer_and_token( $this->consumer, $this->accessToken, "POST", $this->mwapiurl, $request );
			$api_req->sign_request( $this->signer, $this->consumer, $this->accessToken );

			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_URL, $this->mwapiurl );
			curl_setopt( $ch, CURLOPT_USERAGENT, 'LCA Tools 1.5 Wikimedia Trust & Safety tool system, contact jalexander@wikimedia.org' );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request ) );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $api_req->to_header() ) );

			$jsonresponse = curl_exec( $ch );

			if ( !$jsonresponse ) {
				echo json_encode( 'Curl error: ' . htmlspecialchars( curl_error( $ch ) ) );
			}

			$userdata = json_decode( $jsonresponse, true )['query'];

			// seperate out meta and global data
			$localdata = $userdata['users'][0];
			$globaldata = $userdata['globaluserinfo'];

			// if local (meta) data exists
			if ( !array_key_exists( 'missing', $localdata ) && !array_key_exists( 'invalid', $localdata ) ) {
				$userObject->metaedits = $localdata['editcount'];
				$userObject->metaregistration = $localdata['registration'];
			}

			// if global data exists
			if ( array_key_exists( 'merged', $globaldata ) ) {
				$userObject->globaledits = $globaldata['editcount'];

				if ( array_key_exists( 'home', $globaldata ) ) {
					$userObject->homewiki = $globaldata['home'];

					foreach ($globaldata["merged"] as $key => $value) {
						if ( $value["wiki"] == $userObject->homewiki ) {
							$userObject->homeregistration = $value["registration"];
						}
					}
				}
			}

			if ( $pushtousers ) {

				array_push( $this->users, $userObject );

			}

			return $userObject;

	}

	/**
	* Add comments to the right location
	*
	* @param string $comment the comment to add
	* @param strategyuser2016 $userObject
	* @param string $type type of comment
	*
	* @return strategyuser2016 $userObject
	*/
	public function setComments($comment, $userObject, $type) {

		if ( $type == 'reach' ) {
			$userObject->commentsreach = $comment;
			return $userObject;
		} elseif ( $type == 'communities' ) {
			$userObject->commentscommunities = $comment;
			return $userObject;
		} elseif ( $type == 'knowledge' ) {
			$userObject->commentsknowledge = $comment;
			return $userObject;
		} elseif ( $type == 'general' ) {
			$userObject->commentsgeneral = $comment;
			return $userObject;
		} else {
			return false;
		}
	}

	/**
	* add data from userObject to database
	*
	* @param strategyuser2016 $userObject
	*
	* @return boolean result
	*/
	public function insertMetaData( $userObject ) {

		$timestamp = gmdate( "Y-m-d H:i:s", time() );

		$metaInsertTemplate = 'INSERT INTO strategycomments_2016 ( user, country, homewiki, globaledits, metaedits, metaregistration, homeregistration, timestamp, checked) VALUES (?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE country = VALUES(country), homewiki = VALUES(homewiki), globaledits = VALUES(globaledits), metaedits = VALUES(metaedits), metaregistration = VALUES(metaregistration), homeregistration = VALUES(homeregistration), timestamp = VALUES(timestamp), checked = VALUES(checked)';

		$metainsert = $this->mysql->prepare( $metaInsertTemplate );
		if ( $metainsert === false ) {
			echo 'Error while preparing: ' . $template . ' Error text: ' . $this->mysql->error, E_USER_ERROR;
		}

		$metainsert->bind_param( 'ssssssssi', $userObject->username, $userObject->country, $userObject->homewiki, $userObject->globaledits, $userObject->metaedits, $userObject->metaregistration, $userObject->homeregistration, $timestamp, $userObject->checked);

		$metainsert->execute();

		return true;

	}

	/**
	*
	*
	* @param strategyuser2016 $userObject user object to operate on
	* @param string $type what type of comments or approaches to insert
	*  options - all, approaches, reach, communities, knowledge, general
	*
	* @return boolean result
	*/
	public function insertComments( $userObject, $type = null )  {

		$timestamp = gmdate( "Y-m-d H:i:s", time() );

		if ( $type == 'all' ) {

			$insertTemplate = 'INSERT INTO strategycomments_2016 ( user, approaches, commentsreach, commentscommunities, commentsknowledge, commentsgeneral, timestamp, checked) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE approaches = VALUES(approaches), commentsreach = VALUES(commentsreach), commentscommunities = VALUES(commentscommunities), commentsknowledge = VALUES(commentsknowledge), commentsgeneral = VALUES(commentsgeneral), timestamp = VALUES(timestamp), checked = VALUES(checked)';

			$insert = $this->mysql->prepare( $insertTemplate );
			if ( $insert === false ) {
				echo 'Error while preparing: ' . $template . ' Error text: ' . $this->mysql->error, E_USER_ERROR;
			}

			$insert->bind_param( 'sssssssi', $userObject->username, $userObject->approaches, $userObject->commentsreach, $userObject->commentscommunities, $userObject->commentsknowledge, $userObject->commentsgeneral, $timestamp, $userObject->checked );

			$insert->execute();

			return true;
		} elseif ( $type == 'approaches' ) {

			$insertTemplate = 'INSERT INTO strategycomments_2016 ( user, approaches, timestamp, checked) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE approaches = VALUES(approaches), timestamp = VALUES(timestamp), checked = VALUES(checked)';

			$insert = $this->mysql->prepare( $insertTemplate );
			if ( $insert === false ) {
				echo 'Error while preparing: ' . $template . ' Error text: ' . $this->mysql->error, E_USER_ERROR;
			}

			$insert->bind_param( 'sssi', $userObject->username, serialize( $userObject->approaches ), $timestamp, $userObject->checked);
			$insert->execute();

			return true;
		} elseif ( $type == 'reach' ) {

			$insertTemplate = 'INSERT INTO strategycomments_2016 ( user, commentsreach, timestamp, checked) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE commentsreach = VALUES(commentsreach), timestamp = VALUES(timestamp), checked = VALUES(checked)';

			$insert = $this->mysql->prepare( $insertTemplate );
			if ( $insert == false ) {
				echo 'Error while preparing: ' . $insertTemplate . ' Error text: ' . $this->mysql->error, E_USER_ERROR;
			}

			$insert->bind_param( 'sssi', $userObject->username, $userObject->commentsreach, $timestamp, $userObject->checked);
			$insert->execute();

			return true;
		} elseif ( $type == 'communities' ) {

			$insertTemplate = 'INSERT INTO strategycomments_2016 ( user, commentscommunities, timestamp, checked) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE commentscommunities = VALUES(commentscommunities), timestamp = VALUES(timestamp), checked = VALUES(checked)';

			$insert = $this->mysql->prepare( $insertTemplate );
			if ( $insert === false ) {
				echo 'Error while preparing: ' . $template . ' Error text: ' . $this->mysql->error, E_USER_ERROR;
			}

			$insert->bind_param( 'sssi', $userObject->username, $userObject->commentscommunities, $timestamp, $userObject->checked);
			$insert->execute();

			return true;
		} elseif ( $type == 'knowledge' ) {

			$insertTemplate = 'INSERT INTO strategycomments_2016 ( user, commentsknowledge, timestamp, checked) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE commentsknowledge = VALUES(commentsknowledge), timestamp = VALUES(timestamp), checked = VALUES(checked)';

			$insert = $this->mysql->prepare( $insertTemplate );
			if ( $insert === false ) {
				echo 'Error while preparing: ' . $template . ' Error text: ' . $this->mysql->error, E_USER_ERROR;
			}

			$insert->bind_param( 'sssi', $userObject->username, $userObject->commentsknowledge, $timestamp, $userObject->checked);
			$insert->execute();

			return true;
		} elseif ( mb_strtolower( $type ) === 'general' ) {

			$insertTemplate = 'INSERT INTO strategycomments_2016 ( user, commentsgeneral, timestamp, checked) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE commentsgeneral = VALUES(commentsgeneral), timestamp = VALUES(timestamp), checked = VALUES(checked)';

			$insert = $this->mysql->prepare( $insertTemplate );
			if ( $insert === false ) {
				echo 'Error while preparing: ' . $template . ' Error text: ' . $this->mysql->error, E_USER_ERROR;
			}

			$insert->bind_param( 'sssi', $userObject->username, $userObject->commentsgeneral, $timestamp, $userObject->checked);
			$insert->execute();

			return true;
		} else {

			return false;

		}
	}

	/**
	* Get all unchecked users and add to $users 
	*
	* @param string $unchecked allows to only get unchecked users if needed
	*
	* @return array $users
	*/
	public function getsavedUsers( $unchecked = null ) {

		$select = 'SELECT * FROM strategycomments_2016';

		if ( $unchecked ) {
			$select .= ' WHERE checked = 0';
		}
		$data = $this->mysql->query( $select );

		while ( $row = $data->fetch_assoc() ) {

			$userObject = newUserFromUsername( $row['user'] );

			$userObject['homewiki'] = $row['homewiki'];

			if ( isset( $userObject['country'] ) ) {

				$userObject['country'] = $row['country'];
				$userObject['isip'] = true;
			}

			$userObject['globaledits'] = $row['globaledits'];
			$userObject['metaedits'] = $row['metaedits'];
			$userObject['metaregistration'] = $row['metaregistration'];
			$userObject['homeregistration'] = $row['homeregistration'];
			$userObject['approaches'] = $row['approaches'];
			$userObject['commentsreach'] = $row['commentsreach'];
			$userObject['commentsknowledge'] = $row['commentsknowledge'];
			$userObject['commentsgeneral'] = $row['commentsgeneral'];
			$userObject['commentscommunities'] = $row['commentscommunities'];
			$userObject['checked'] = $row['checked'];

			array_push( $this->users, $userObject );
		
		}

		return $this->users;

	}

	/**
	* function to take a mediawiki page name (the site that is registered in the object) and return an array of the wikitext sections
	*  that are on that page.
	*
	* @param string $page pagename to look up
	* @param string $whichsections level of sections to look for, defaults to 2
	*
	* @return array of sections
	*/
	public function getPageSections( $page, $whichsections = "2" ) {

		$ch = $this->getcURL();

		$request = array(
		'action' => 'parse',
		'page' => $page,
		'prop' => 'sections',
		'format' => 'json',
		);

		$api_req = OAuthRequest::from_consumer_and_token( $this->consumer, $this->accessToken, "POST", $this->mwapiurl, $request );
		$api_req->sign_request( $this->signer, $this->consumer, $this->accessToken );

		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_URL, $this->mwapiurl );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'LCA Tools 1.5 Wikimedia Trust & Safety tool system, contact jalexander@wikimedia.org' );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request ) );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $api_req->to_header() ) );

		$jsonresponse = curl_exec( $ch );
		if ( !$jsonresponse ) {
			echo json_encode( 'Curl error: ' . htmlspecialchars( curl_error( $ch ) ) );
		}

		$response = json_decode( $jsonresponse, true );
		$result = array();


		foreach ( $response['parse']['sections'] as $section ) {
			if ( $section['level'] ==  $whichsections ) {
				$result[] = $section['index'];
			}
		}
		return $result;

	}

	/**
	* get the wikitext from a specific page and section
	*
	* @param string $page page name to look at
	* @param string $section index for the section to look at
	*
	* @return string of raw wikitext for the section
	*/
	function getSectionText( $page, $section ) {
		$ch = $this->getcURL();

		$request = array(
			'action' => 'query',
			'titles' => $page,
			'prop' => 'revisions',
			'rvprop' => 'content',
			'continue' => '',
			'rvsection' => $section,
			'format' => 'json',
		);

		$api_req = OAuthRequest::from_consumer_and_token( $this->consumer, $this->accessToken, "POST", $this->mwapiurl, $request );
		$api_req->sign_request( $this->signer, $this->consumer, $this->accessToken );

		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_URL, $this->mwapiurl );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'LCA Tools 1.5 Wikimedia Trust & Safety tool system, contact jalexander@wikimedia.org' );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request ) );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $api_req->to_header() ) );

		$jsonresponse = curl_exec( $ch );
		if ( !$jsonresponse ) {
			echo json_encode( 'Curl error: ' . htmlspecialchars( curl_error( $ch ) ) );
		}

		$response = json_decode( $jsonresponse, true );
		
		//grr x1000 this shit is far too buried in the response

		foreach ($response['query']['pages'] as $key => $value) {
			$data = $value;
		}

		$rawwikitext = $data['revisions']['0']['*'];

		return $rawwikitext;


	}



}