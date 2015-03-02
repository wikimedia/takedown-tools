/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `lcatools`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `lcatools` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `lcatools`;

--
-- Table structure for table `basicrelease`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/ `basicrelease` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL,
  `who_received` text,
  `pre_approved` varchar(3) DEFAULT NULL,
  `why_released` text,
  `who_released` varchar(255) DEFAULT NULL,
  `who_released_to` varchar(255) DEFAULT NULL,
  `released_to_contact` text,
  `details` text,
  `test` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `log_id` (`log_id`),
  KEY `logid` (`log_id`),
  KEY `user_submitted` (`user`),
  KEY `test_submission` (`test`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `centrallog`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/ `centrallog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `test` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submit_user` (`user`),
  KEY `submit_type` (`type`),
  KEY `is_test` (`test`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dmcatakedowns`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/ `dmcatakedowns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL,
  `sender_city` text,
  `sender_zip` text,
  `sender_state` text,
  `sender_country` varchar(255) DEFAULT NULL,
  `takedown_date` date DEFAULT NULL,
  `action_taken` varchar(10) DEFAULT NULL,
  `takedown_title` text,
  `commons_title` text,
  `wmfwiki_title` text,
  `takedown_method` varchar(255) DEFAULT NULL,
  `takedown_subject` varchar(255) DEFAULT NULL,
  `involved_user` varchar(255) DEFAULT NULL,
  `logging_metadata` text,
  `strike_note` text,
  `ce_url` varchar(255) DEFAULT NULL,
  `files_sent` text,
  `files_affected` text,
  `test` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `log_id` (`log_id`),
  KEY `user_submiter` (`user`),
  KEY `action` (`action_taken`),
  KEY `is_test` (`test`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logupdates`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/ `logupdates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `old_log` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ncmecrelease`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/ `ncmecrelease` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `project` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `legalapproved` char(1) DEFAULT NULL,
  `whoapproved` varchar(255) DEFAULT NULL,
  `whynotapproved` varchar(255) DEFAULT NULL,
  `logging_metadata` text,
  `logging_details` text,
  `test` char(1) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `log_id` (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `strategycomments`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/ `strategycomments` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `page` int(8) unsigned NOT NULL,
  `user` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `homewiki` varchar(255) DEFAULT NULL,
  `globaledits` varchar(255) DEFAULT NULL,
  `metaedits` varchar(255) DEFAULT NULL,
  `metaregistration` varchar(255) DEFAULT NULL,
  `comment` text,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `country` (`country`),
  KEY `homewiki` (`homewiki`),
  KEY `page_from` (`page`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `submittedfilehashes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/ `submittedfilehashes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clog_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `tlog_id` int(11) NOT NULL,
  `hash` binary(16) NOT NULL,
  `test` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fhtype` (`type`),
  KEY `clog` (`clog_id`),
  KEY `tlog` (`tlog_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/ `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) DEFAULT NULL,
  `mwtoken` varchar(255) DEFAULT NULL,
  `mwsecret` varchar(255) DEFAULT NULL,
  `wiki_user` varchar(255) DEFAULT NULL,
  `registration_time` datetime DEFAULT NULL,
  `sugartoken` varchar(255) DEFAULT NULL,
  `sugarsecret` varchar(255) DEFAULT NULL,
  `sugaruser` varchar(255) DEFAULT NULL,
  `sugar_registration_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-03-02 11:52:51
