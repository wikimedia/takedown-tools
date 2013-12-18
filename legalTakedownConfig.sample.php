<?php

/*   ---------------------------------------------

Author : James Alexander

License: MIT (see http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder with this file)
			
Date of creation : 2013-12-18
Last modified : 2013-12-18

Sample config file for Wikimedia LCA DMCA Takedown webapp 
			
---------------------------------------------   */

$takedown_config = array (
	'CE_apikey' => '',
	'CE_apiurl' => 'http://api-beta.chillingeffects.org/',
	'CE_recipient' =>  array (
		name => 'Wikimedia Foundation',
		kind => 'organization',
		address_line_1 => '149 New Montgomery St. 6th FL',
		city => 'San Francisco',
		state => 'CA',
		country_code => 'US',
		phone => '4158396885',
		url => 'https://wikimediafoundation.org',
		),
	);