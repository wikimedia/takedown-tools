
CREATE DATABASE /*!32312 IF NOT EXISTS*/ `lcatools` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `lcatools`;
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
