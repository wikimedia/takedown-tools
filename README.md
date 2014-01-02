lca-takedown
============

Repository for webapp used by Wikimedia Foundation LCA department when processing legal takedowns

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
centralLog.php - calls and displays log for submissions done on all lcatool forms.
logDetails.php - displays details of logged events on demand (by clicking title on centraLog.php).

multiuseFunctions.php - file with functions used (or which could be used) in multiple different forms. Currently contains:
	setupdataurl() - takes a file and converts into a well formed dataurl.
	curlAPIpost() - takes post data and headers and sends to a designated API.
	lcalog() - Logs data to the central log for lcatools

lcatools.sql - installation script for database required for tools.
LICENSE.txt (MIT License for all files not otherwise marked)  
README.md (this readme)
lcaToolsConfig.sample.ini - Sample configuration file for takedown processor
.gitignore - ignore real config files which have private keys/passwords and .htaccess file

In order to use the software remember to copy lcaToolsConfig.sample.init to lcaToolsConfig.ini and fill in the missing attributes.

include folder:
lcapage.php - template for surrounding pieces of lcatools pages (toolbar/logo/login header etc)

releaseDetail.php - Log detail template to show details about basic releases of confidential information.

images folder:  
monobook-bullet.png (Public Domain)  
roryshield.jpg (Ruby Wang CC-BY-SA-3.0 unported https://creativecommons.org/licenses/by-sa/3.0/deed.en)
favicon.ico (Copyright Wikimedia Foundation)
20px-Help.png (Public Domain help icon - http://tango.freedesktop.org/Tango_Desktop_Project)

CSS folder:  
main.css (mediawiki monobook styling Gabriel Wicke GPL)  
pikaday.css (BSD and MIT)  
lca.css - page specific css/overrides

Scripts folder:
lca.js - shared scripts for lcatools pages (currently logout script).
jquery-1.10.2.min.js  - Jquery (MIT)
moment.min.js  - date/time processing library (MIT)
pikaday.js (BSD and MIT)  
pikaday.jquery.js (BSD and MIT)
