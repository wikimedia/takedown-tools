
CREATE DATABASE /*!32312 IF NOT EXISTS*/ `lcatools` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `lcatools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/  `basicrelease` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE /*!32312 IF NOT EXISTS*/ `dmcatakedowns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL,
  `sender_name` text,
  `sender_person` text,
  `sender_firm` text,
  `sender_address1` text,
  `sender_address2` text,
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
  `takedown_text` mediumtext,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
