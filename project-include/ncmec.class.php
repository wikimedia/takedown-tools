<?php
/**
 * Classes for interacting with the National Center for Missing and Exploited Children API
 *  originally designed for Wikimedia Foundation LCATools system.
 *
 *
 *
 * @file
 * @author James Alexander
 * @copyright © 2015 James Ryan Alexander
 * @license MIT at http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder of this software
 * @version 1.0 - 2015-09-21
 */

date_default_timezone_set( 'UTC' );

class ncmecip {

	/**
	* Internal class to hold information about an IP "event" for NCMEC
	* 
	* @class ncmecip
	* For example each individual checkuser result sent to NCMEC would be initially placed into an instance of this class
	*
	*/

	/**
	 *
	 *
	 * @var string $ip
	 * Internet Protocol (IP) number of the event
	 *
	 */
	private $ip;

	/**
	 *
	 *
	 * @var string $type
	 * Type of event described (Login, Registration, Purchase, Upload, Other, Unknown)
	 *
	 */
	private $type;

	/**
	 *
	 *
	 * @var string $timestamp
	 * XML DateTime stamp for IP event. Format "YYYY-MM-DDThh:mm:ss"
	 *  for now timezone is handled seperatly.
	 *
	 */
	private $timestamp = null;


	/**
	*
	* @var boolean $isproxy
	* Do we have reason to believe this is a proxy? Defaults to false.
	*
	*/
	private $isproxy = 0;

	/**
	 * Construction function called when the class is created
	 *
	 * Construction class contains only 1 required parameter (the IP itself)
	 *  optional elements allow for the object to be created from whole cloth but not requiring it.
	 *  If an event type is not given it is automatically labeled as 'other'.
	 *
	 * @param string $ip the IP in question, can be ipv4 or 6
	 * @param string $type (optional) the action type (Login, Registration, Purchase, Upload, Other, Unknown)
	 * @param string $isproxy (optional) do we have reason to believe that the IP is a proxy? Boolean
	 * @param string $timestamp (optional) timestamp of event in fashion that PHP can understand
	 *
	 */
	function __construct( string $ip, string $type = null, datetime $timestamp = null, boolean $isproxy = null ) {
		$this->ip = $ip;

		if ( $type ) {
			$this->type = $type;
		} else {
			$this->type = 'Other';
		}

		if ( $timestamp ) {
			$this->timestamp = date( DATE_ATOM, $timestamp );
		}

		if ( $isproxy ) {
			$this->isproxy = 1;
		}
	}

	/**********************************************************************************************

	Public functions to retrieve or set private variables with appropriate fallback or processing. 

	*************************************************************************************************/

	/**
	 * Set ip event type
	 *
	 * @param string $type the action type (Login, Registration, Purchase, Upload, Other, Unknown)
	 *
	 */
	public function setType( $type ) {
		$this->type = $type;
	}

	/**
	 * Set timestamp for event
	 * 
	 * @param datetime $timestamp datetime in any format PHP will understand
	 *
	 */
	public function settime( datetime $timestamp ) {
		$this->timestamp = date( DATE_ATOM, $timestamp );
	}

	/**
	 * Set THAT we think this IP is a proxy
	 *
	 */
	public function setproxy() {
		$this->isproxy = 1;
	}

	/**
	 * Get IP which is identified in object
	 * 
	 * @return string
	 *
	 */
	public function getIP() {
		return $this->ip;
	}

	/**
	 * Get type
	 * 
	 * @return string
	 *
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Get timestamp
	 * 
	 * @return string
	 *
	 */
	public function gettime() {
		return $this->timestamp;
	}

	/**
	 * check if the IP is set as a proxy
	 * 
	 * @return boolean
	 *
	 */
	public function isproxy() {
		return $this->isproxy;
	}

}

class ncmec {
	/**
	* wrapper class for contacting and interacting with the API of the Natioal Center for Missing and Exploited Children
	* 
	* @class ncmeci
	*
	*/

	/**
	 * Report number
	 *
	 * @var int $reportnum
	 * The report number for the report currently being worked on
	 *
	 */
	public $reportnum = null;

	/**
	 *
	 *
	 * @var resource $ch
	 * cURL resource to be used for different api calls.
	 */
	private $ch = null;

	/**
	 *
	 *
	 * @var string $api_url
	 * Url to use when connecting to the API.
	 */
	private $api_url;

	/**
	 *
	 *
	 * @var string $api_url_test
	 * Url to use when connecting to the API test servers.
	 */
	private $api_url_test;

	/**
	 *
	 * @var string $ncmecuser
	 * username to use with NCMEC API
	 *
	 */
	private $ncmecuser;

	/**
	 *
	 * @var string $ncmecuser_test
	 * username to use with NCMEC API
	 *
	 */
	private $ncmecuser_test;

	/**
	 *
	 * @var string $ncmecpass
	 * password to use with NCMEC API
	 *
	 */
	private $ncmecpass;

	/**
	 *
	 * @var string $ncmecpass_test
	 * password to use with NCMEC API test system where applicable
	 *
	 */
	private $ncmecpass_test;

	/**
	 * Report object
	 *
	 * @var ncmecReport $report
	 * The main report object if sending a full report.
	 *
	 */
	private $report = null;

	/**
	 * Construction function called whent the class is created.
	 *
	 * Construction class requires, at the very least, a starting api_url. 
	 *  optionally takes a cURL resource that already exists
	 * @param mixed $logininfo 	Either an array of strings or a string that says "skip" to use singlar login info.
	 *							Array requires the following keys:
	 *								NCMEC_user_prod (or NCMEC_user) NCMEC username for production server
	 *								NCMEC_password_prod (or NCMEC_password) NCMEC password for production server
	 *								NCMEC_URL_Production (or NCMEC_URL) NCMEC API URL
	 *							Also accepts, for the test servers:
	 *								NCMEC_user_test
	 *								NCMEC_password_test
	 *								NCMEC_URL_Test
	 * @param resource $ch (optional)	Already existing cURL resource to use instead of creating one fresh.
	 *
	 */
	function __construct( $logininfo, resource $ch = null ) {

		if ( is_array( $logininfo) ) {

			$this->login( $logininfo );

		} elseif ( $logininfo === 'skip' ) {

			continue;

		} else {
			
			return 'error, no login info given and skip order not passed';
			exit();

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
	private function getcURL( resoruce $resource = null ) {
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
	 * Get username for NCMEC API, defaults to production
	 *
	 * @param string $type (optional) accepts 'test' to return test server username. Otherwise returns production.
	 *
	 * @return string Returns the username for the requested server
	 */
	private function getUser( $type = null ) {

		if ( $type === 'test' ) {

			return $this->ncmecuser_test;

		} else {

			return $this->ncmecuser;

		}
	}

	/**
	 * Get password for NCMEC API, defaults to production
	 *
	 * @param string $type (optional) accepts 'test' to return test server password. Otherwise returns production.
	 *
	 * @return string Returns the password for the requested server
	 */
	private function getPass( $type = null ) {

		if ( $type === 'test' ) {

			return $this->ncmecpass_test;

		} else {

			return $this->ncmecpass;

		}
	}

	/**
	 * Get url for NCMEC API, defaults to production
	 *
	 * @param string $type (optional) accepts 'test' to return test server url. Otherwise returns production.
	 *
	 * @return string Returns the url for the requested server
	 */
	private function getapiurl( $type = null ) {

		if ( $type === 'test' ) {

			return $this->api_url_test;

		} else {

			return $this->api_url;

		}
	}


	/*************************************************************************************

	Private internal functions to do common actions inside of the public functions

	**************************************************************************************/

	/**
	 * Genaric function to set up and call the NCMEC api for a basic response.
	 *  This is a successor of the basic function in multiuseFunctions.php.
	 *  NOT used for the more complicated report calls but used for things like opening the report or sending a retraction.
	 *
	 * @param array $data the post fields that need to be sent in the request.
	 *
	 * @return $response the response from the call itself.
	 *
	 */
	private function basicpost( array $data ) {

	$ch = $this->getcURL;
	curl_reset( $ch );
	curl_setopt( $ch, CURLOPT_URL, $this->api_url );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_USERPWD, $this->ncmecuser.":".$this->ncmecpass );
	curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );

	$result = curl_exec( $ch );
	return $result;
}


	/**
	 * Process login info via config variable (set up assuming LCATOOLS or similarly structured key names) or exlicit login info
	 *
	 * @param mixed $logininfo 	Either an array of strings or a string that says "skip" to use singlar login info.
	 *							Array requires the following keys:
	 *								NCMEC_user_prod (or NCMEC_user) NCMEC username for production server
	 *								NCMEC_password_prod (or NCMEC_password) NCMEC password for production server
	 *								NCMEC_URL_Production (or NCMEC_URL) NCMEC API URL
	 *							Also accepts, for the test servers:
	 *								NCMEC_user_test
	 *								NCMEC_password_test
	 *								NCMEC_URL_Test
	 * @param string $user 	(optional only processed if first variable says "skip") Username for NCMEC API
	 * @param string $pass 	(optional only processed if first variable says "skip") Password for NCMEC API
	 * @param string $url 	(optional only processed if first variable says "skip") API url for NCMEC API
	 * @param string $type 	(optional only processed if first variable says "skip" and even then not needed, will assume production)
	 *						Accepts 'test' to assign login data to test servers, anything else assigns to production/only servers
	 * @return boolean true for success (only cares about production or if you explictly set test test) false for error.
	 */
	private function login( $logininfo, $user = null, $pass = null, $url = null, $type = null ) {

		//init $result
		$result = false;

		if ( is_array( $logininfo ) ) {
			//Begin processing of config variable
			if ( array_key_exists( 'NCMEC_user_prod', $logininfo ) ) {

				$this->ncmecuser = $logininfo['NCMEC_user_prod'];

				if ( array_key_exists( 'NCMEC_password_prod', $logininfo ) ) {

					$this->ncmecpass = $logininfo['NCMEC_password_prod'];
					$result = true;

					if ( array_key_exists( 'NCMEC_URL_Production', $logininfo ) ) {

						$this->api_url = $logininfo['NCMEC_URL_Production'];
						$result = true;
					} elseif ( array_key_exists( 'NCMEC_URL', $logininfo ) ) {

						$this->api_url = $logininfo['NCMEC_URL'];
						$result = true;
					} else {

						$result = false;

					}

				} elseif ( array_key_exists( 'NCMEC_password', $logininfo ) ) {

					$this->ncmecpass = $logininfo['NCMEC_password'];
					$result = true;

					if ( array_key_exists( 'NCMEC_URL_Production', $logininfo ) ) {

						$this->api_url = $logininfo['NCMEC_URL_Production'];
						$result = true;
					} elseif ( array_key_exists( 'NCMEC_URL', $logininfo ) ) {

						$this->api_url = $logininfo['NCMEC_URL'];
						$result = true;
					} else {

						$result = false;
						
					}

				} else {

					$result = false;

				}

			} elseif ( array_key_exists( 'NCMEC_user', $logininfo ) ) {

				$this->ncmecuser = $logininfo['NCMEC_user'];

				if ( array_key_exists( 'NCMEC_password_prod', $logininfo ) ) {

					$this->ncmecpass = $logininfo['NCMEC_password_prod'];
					$result = true;

				} elseif ( array_key_exists( 'NCMEC_password', $logininfo ) ) {

					$this->ncmecpass = $logininfo['NCMEC_password'];
					$result = true;

				} else {

					$result = false;

				}
			} else {

				$result = false;

			}
		} elseif ( $logininfo === 'skip' && $user && $pass && $url ) {

			if ( $type === 'test' ) {

				$this->ncmecuser_test = $user;
				$this->ncmecpass_test = $pass;
				$this->api_url_test = $url;
				$result = true;

			} else {

				$this->ncmecuser = $user;
				$this->ncmecpass = $pass;
				$this->api_url = $url;
				$result = true;

			}
		} else {

			//something broke
			$result = false;

		}

		if ( array_key_exists( 'NCMEC_user_test', $logininfo) && array_key_exists( 'NCMEC_password_test', $logininfo ) && array_key_exists( 'NCMEC_URL_Test', $logininfo ) ) {

			$this->ncmecuser_test = $logininfo['NCMEC_user_test'];
			$this->ncmecpass_test = $logininfo['NCMEC_password_test'];
			$this->api_url_test = $logininfo['NCMEC_URL_Test'];

		}

		return $result;
	}


	/**********************************************************************************************

	Public functions to retrieve or set private variables with appropriate fallback or processing. 

	*************************************************************************************************/

	/**
	 * Function to change the API endpoint
	 * 
	 * @param string $url new api endpoint url
	 *
	 */
	public function setURL(string $url ) {
		$this->api_url = $url;
	}

	/**
	 * Function to change the API login info
	 *  This is mostly used as an option when switching between test/production servers (for example checking status)
	 *  instead of creating a new object.
	 *
	 * @param string $username The new username to login with.
	 * @param string $password The new password to login with.
	 *
	 */
	public function userpass( string $username, string $password ) {
		$this->ncmecuser = $username;
		$this->ncmecpass = $password;
	}



	/**************************************************************************

	Public functions to make requests to the SugarCRM API. 
	Generall relatively targeted in nature and in rough order of initial use.

	****************************************************************************/

	/**
	 * Public function to get the status of the NCMEC server
	 *
	 * @param string $type 	(optional) accepts 'test' to get status of test server using test credentials
	 *						Assumes production if nothing given (or if anyting other then 'test' is given)
	 *
	 * @return response code from server
	 * Assumes only one response code, if not we have enough other issues for now.
	 *
	 */
	public function serverstatus( $type = null ) {

	$ch = $this->getcURL();
	curl_reset( $ch );
	curl_setopt( $ch, CURLOPT_URL, $this->getapiurl( $type ).'status' );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_USERPWD, $this->getUser().":".$this->getPass() );
	curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );

	$result = curl_exec( $ch );
	$responseXML = new DOMDocument();
	$responseXML->loadXML( $result );
	$responseNodes = $responseXML->getElementsByTagName( 'responseCode' );

	if ( $responseNodes->length==0 ) {
		$responsecode = null;
	} else {
		foreach ( $responseNodes as $r ) {
			$responsecode = $r->nodeValue;
		}
	}
	return $responsecode;
}

	/**
	 * Public function to retract a previously submitted NCMEC report. Uses current report number if not given.
	 *
	 * @param int (optional) retract the number of the NCMEC report to retract
	 *
	 * @return string $result message to display. Can display more then one in theory (but not expected)
	 *
	 */
	function retractreport( int $retract = null ) {
		if ( $retract ) {
			$this->reportnum = $retract;
		}

		$senddata = $arrayName = array('id' => $this->reportnum );

		$result = $this->basicpost( $senddata );
		$responseXML = new DOMDocument();
		$responseXML->loadXML( $result );
		$responseNodes = $responseXML->getElementsByTagName( 'responseCode' );

		if ( $responseNodes->length==0 ) {
			$responsecode = null;
			return 'Error: No response code detected, see XML'.var_dump( $result );
		} else {
			foreach ( $responseNodes as $r ) {
				$responsecode = $r->nodeValue;

				if ( $responsecode === '1016' ) {
					return 'Report Retracted';
				} elseif ( $responsecode === '1019' ) {
					return 'NCMEC says the user is incorrect, check the report ID';
				} elseif ( $responsecode === '1021' ) {
					return 'This report is already closed, you are now unable to retract it';
				} elseif ( $responsecode === '1022' ) {
					return 'This report has already been redacted';
				} else {
					return 'Unexpected resposne code detected. Look into documentation for code: '.$responsecode;
				}
			}
		}

	}






}