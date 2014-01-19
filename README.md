lca-tools
============

Repository for webapp used by Wikimedia Foundation LCA department to help with time intensive projects such as legal takedowns and confidential information releases.

This is currently a placeholder and will be expanded as we go and I have time.

Basic Installation instructions:  
1. Create database based on lcatools.sql [ generally you can do from the command line with 'mysql -u <username> -p < lcatools.sql' username being a user with create database privileges ]  
2. cp lcaToolsConfig.sample.ini to lcaToolsConfig.ini [linux/mac machines 'cp lcaToolsConfig.sample.ini lcaToolsConfig.ini']   
3. Fill out missing variables, especially API key for Chilling Effects (if using) and database credentials.  

Root folder:
index.php - Central start home page for all tools within the repository.  

legalTakedown.php - form to fill out for Wikimedia Commons DMCA take downs.  
legalTakedownProcessor.php - file to process input from legalTakedown.php, takes data/logs/sends to Chilling Effects on demand/processes templates to post on wiki.  

basicRelease.php - starting page with form to log a release of confidential informations released by the Wikimedia Foundation LCA team.  
basicReleaseProcessor.php - file to process input from basicRelease.php - takesData/processes/logs  

NCMECreporting.php - form to fill out when carrying out child protection takedowns gathering log info and the information needed for reporting to the National Center for Missing and Exploited Children.  
NCMECprocessing.php - processor to take NCMECreporting.php information and files, package it up and send it to NCMEC as well as record information for central logging. Sends to either test or production NCEMC servers depending on setting on collection form.  
NCMECprocessingOldDebug.php - older processor for NCMECreporting which is kept to allow on demand switching. Outputs significantly more debug info then production processor. Also sends to either test or production servers.  
NCMECretract.php - form to allow retracting a report to NCMEC before the report has been closed. Reports auto close as part of the processor so only valuable if something errored out during the submission process.  

centralLog.php - calls and displays log for submissions done on all lcatool forms.  
logDetails.php - displays details of logged events on demand (by clicking title on centraLog.php).  

beginoauthregistration.php - page to start the authorization process with mediawiki oauth  
	Begins blank request process then sends user to meta to finish authorization  
mwOAuthCallback.php - callback script for after a user has authorized themselves for LCATools.  
	Takes verification script and token, verifies against session started on beginoauthregistration.php.  
	If everything matches does verification api call and JWT request, verifies JWT and then stores information in LCA Tools user table.  
testOAuth.php - tests current users stored OAuth credentials by doing an API user information check.

lcatools.sql - installation script for database required for tools.  
LICENSE.txt (MIT License for all files not otherwise marked)  
README.md (this readme)  
lcaToolsConfig.sample.ini - Sample configuration file for takedown processor  
.gitignore - ignore real config files which have private keys/passwords and .htaccess file  

include folder:  
lcapage.php - template for surrounding pieces of lcatools pages (toolbar/logo/login header etc)  

ncmecdetail.php - details to display when looking at a detailed log entry from a NCMEC submission.  
dmcadetails.php - details to display when looking at a detailed log entry from a DMCA takedown submission.  
releaseDetail.php - details to display when looking at a detailed log entry from a basic release of confidential information.  

countrySelect.php - seperate file with list of countries that can be used to easily create a select box to choose country with 2 letter ISO code as select value.  
espsubmittal.xsd - xsd to define required standard for XML submitals to and responses from NCMEC. Used to verify data going to and coming back from them.  

OAuth.php - OAuth related classes a libary from Mediawiki Extension:OAuth and elsewhere (MIT Andy Smith)  
JWT.php - JSON Web Token implementantion originally a library from Mediawiki Extension:OAuth and elsewhere (http://opensource.org/licenses/BSD-3-Clause 3-clause BSD Anant Narayanan anant@php.net and Neuman Vong neuman@twilio.com)  
MWOAuthSignatureMethod_RSA_SHA1 - extension of OAuth RSA signature method (always load OAuth.php first) for mediawiki specifically. From Mediawiki Extension:OAuth GNU General Public License 2.0

multiuseFunctions.php - file with functions used (or which could be used) in multiple different forms. Currently contains:  
	setupdataurl() - takes a file and converts into a well formed dataurl.  
		Accepts: input file  
		Returns: array (kind of file, file_name, data url) Kind of file currently hardcoded to original  
	curlAPIpost() - takes post data and headers and sends to a designated API.  
		Accepts: url to post to, data, headers (optional)  
		Returns: response  
	lcalog() - Logs data to the central log for lcatools  
		Accepts: user, log type, log title, test marker  
		Returns: log id  
	libxml_display_error() and libxml_display_errors() - functions from PHP documentation comments that assist in better formatting for XML verification errors (mostly used for verifying submission against xsd)  
		Accepts: Error  
		Returns: error information  
	NCMECsimpleauthdcurlPost() - function for simple authenticated posts to NCMEC containing only basic form field data such as a file or report id. Used for all file submissions, to close a report and to retract a report.  
		Accepts: username, password, post url, post data  
		Returns: response  
	curlauthdAPIpost() - function for more complex authenticated posts to NCMEC containing XML data, used to open reports and to submit file details after submitting a file.  
		Accepts: username, password, post url, post data, post headers (optional)  
		Returns: response  
	NCMECstatus() - function to check the status of a NCMEC server, used to verify authentication and connection working.  
		Accepts: username, password, post url  
		Returns: responsecode  
	noheaderstringget() - send a simple url get (using query string)  
		Accepts: request (as full url)  
		Returns: response  
	mwOAuthAPIcall() - regular api call to MW Oauth  
		Accepts: url, api parameters, oauth signed request (for headers)  
		Returns: response  
	validateJWT() - function from Chris Steipp to validate mediawiki Json Web Token  
		Accepts: identity (web token), consumer key, nonce, server (where you got the JWT from)  
		Response: boolean response  

examples folder:  
NCMECproofofconcept.php - proof of concept form with fake data to exhibit how the NCMEC form works without actual submission. Used for original testing purposes, kept for exhibit purposes.  

images folder:  
monobook-bullet.png (Public Domain)  
roryshield.jpg (Ruby Wang CC-BY-SA-3.0 unported https://creativecommons.org/licenses/by-sa/3.0/deed.en)  
favicon.ico (Copyright Wikimedia Foundation)  

All Public Domain icons beow - http://tango.freedesktop.org/Tango_Desktop_Project png's available for IE fall back reasons.  
20px-Help.png   
Dialog-error-round.png  
Dialog-error-round.svg  
Dialog-accept.png  
Dialog-accept.svg  
List-remove.png  
List-remove.svg  

CSS folder:  
main.css (mediawiki monobook styling Gabriel Wicke GPL)  
pikaday.css (BSD and MIT)  
lca.css - page specific css/overrides  

Scripts folder:  
lca.js - shared scripts for lcatools pages (currently logout script).  
jquery-1.10.2.min.js  - Jquery (MIT)  
jquery.validate.min.js - jquery form validation plugin ( Jörn Zaefferer Licensed under the MIT license. )  
moment.min.js  - date/time processing library (MIT)  
pikaday.js (BSD and MIT)  
pikaday.jquery.js (BSD and MIT)
