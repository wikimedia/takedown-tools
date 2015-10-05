lca-tools
============

Repository for webapp used by Wikimedia Foundation Trust & Safety to help with time intensive projects such as legal takedowns and confidential information releases.

### Basic Installation instructions:  
1. Create database based on lcatools.sql [ generally you can do from the command line with `mysql -u <username> -p < lcatools.sql` username being a user with create database privileges ]  
2. copy lcaToolsConfig.sample.ini to lcaToolsConfig.ini [linux/mac machines `cp lcaToolsConfig.sample.ini lcaToolsConfig.ini`]   
3. Fill out missing variables, especially credentials such as API key's and login/database credentials.
4. In general the system expects a user to be logged in, this is generally done via some version of basic apache auth.  

### Requirements
* PHP5
* Apache2
* PHP5-Curl
* Some login/BasicAuth system (production currently uses LDAP)
* MYSQL

### Files:
* index.php - Central start home page for all tools within the repository including checks on server status for NCMEC API.  
* centralLog.php - calls and displays log for submissions done on all lcatool forms.  
* logDetails.php - displays details of logged events on demand (by clicking title on centraLog.php).  
* mwOAuthCallback.php - callback script for after a user has authorized themselves for LCATools.  
  * Takes verification script and token, verifies against session started on beginoauthregistration.php (in mwoauth folder).  
  * If everything matches does verification api call and JWT request, verifies JWT and then stores information in LCA Tools user table.  
* LICENSE.txt (MIT License for all files not otherwise marked)  
* README.md (this readme)  
* lcaToolsConfig.sample.ini - Sample configuration file for takedown processor  You should create a copy and a real 'lcaToolsConfig.ini' during tool setup.  
* .gitignore - ignore real config files which have private keys/passwords, key files and .htaccess file  

#### configs folder:  
* Configuration files
  * lcatools.sql - installation script for database required for tools.  
  * In this folder you should also place lcatools.pem and lcatools.pub the, created, private and public keys for your mwOauth tool if you are using one.  

#### CSS folder:  
* main.css (mediawiki monobook styling Gabriel Wicke GPL)  
* pikaday.css (BSD and MIT)  
* lca.css - page specific /css/overrides  

#### examples folder:  
NCMECproofofconcept.php - proof of concept form with fake data to exhibit how the NCMEC form works without actual submission. Used for original testing purposes, kept for exhibit purposes.  

#### images folder:  
* monobook-bullet.png (Public Domain)  
* progressbar.gif (Public Domain)
* roryshield.jpg (Ruby Wang CC-BY-SA-3.0 unported https://creativecommons.org/licenses/by-sa/3.0/deed.en)  
* favicon.ico (Copyright Wikimedia Foundation)  
* All Public Domain icons beow - http://tango.freedesktop.org/Tango_Desktop_Project png's available for IE fall back reasons.  
  * 20px-Help.png   
  * Dialog-error-round.png  
  * Dialog-error-round.svg  
  * Dialog-accept.png  
  * Dialog-accept.svg  
  * List-remove.png  
  * List-remove.svg  
  * List-add.svg  
  * Emblem-multiple.svg

#### mwoauth folder:  
* beginmwoauthregistration.php - page to start the authorization process with mediawiki oauth  
  * Begins blank request process then sends user to meta to finish authorization  
* testmwOAuth.php - tests current users stored OAuth credentials by doing an API user information check.  
* mwOAuthProcessor.php - ajax ready php script for other parts of the program to use when doing edits or other post actions on demand.  

#### project-include folder:
* Folder containing shared files specifically meant for the LCATools project.
  * page.php - template for surrounding pieces of lcatools pages (toolbar/logo/login header etc)  
  * ncmecdetail.php - details to display when looking at a detailed log entry from a NCMEC submission.  
  * dmcadetails.php - details to display when looking at a detailed log entry from a DMCA takedown submission.  
  * releaseDetail.php - details to display when looking at a detailed log entry from a basic release of confidential information.  
  * ncmec.class.php - Class for interacting with the API of the National Center for Missing and Exploited Children

#### release folder:  
* Tool to log a asic release of confidential information.
  * basicRelease.php - starting page with form to log a release of confidential informations released by the Wikimedia Foundation LCA team.  
  * basicReleaseProcessor.php - file to process input from basicRelease.php - takesData/processes/logs  

#### scripts folder:  
* lca.js - shared scripts for lcatools pages (currently logout script).  
* jquery-1.10.2.min.js  - Jquery (MIT)  
* jquery.validate.min.js - jquery form validation plugin ( Jörn Zaefferer Licensed under the MIT license. )  
* moment.min.js  - date/time processing library (MIT)  
* pikaday.js (BSD and MIT)
* pikaday.jquery.js (BSD and MIT)

#### sugaroauth folder:  
* Tools and scripts to use to register with the OAuth system of a SugarCRM installation and to interact with it.
  * sugarOAuthCallback.php - callback script for registering/connecting account with sugarCRM acccount. 
    * Checks for active registration session  
    * Takes verification code and requests permemnant credentials from sugarCRM  
    * Verifies that credentials work and then registers them in the database along with sugarCRM username  
  * sugarOAuthRegistration.php - Initial registration/account connection script for connecting lcatools and sugarCRM accounts.  
  * testSugarOAuth.php - tests current users stored OAuth credentials for sugarCRM by doing multiple API calls.
  * sugarOAuthProcessor.php - Ajax ready php script for other parts of the program to use when creating cases or other sugar actions on demand.  

### Included git submodules
* Seperate git repositories included as submodules in the LCATools system. Seperated out into different repositories for organization purposes and to, in theory, make splitting private vs public easier down the road. All are included as folders in the tool root.

#### 2015 Strategy folder:
* Seperate repository holding tools used to process and view comments made durign the 2015 Strategy Consultation.

#### childprotection folder:  
* Seperate repository holding tools for reports to the National Center for Missing and Exploited Children. See repository for more information.

#### core-include folder:
* Seperate repository that contains shared files that could be useful to both the private LCATools system and a, yet to be created, public tools repository.

#### standalone folder:  
* Seperate repository holding standalone tools that could exist in either the LCATools system or a, yet to be created, public tools system.

#### takedown folder:  
* Seperate repository holding  tools to process DMCA Takedowns sent to the Wikimedia Foundation and report them to Chilling Effects.


