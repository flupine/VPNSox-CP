-- MySQL dump 10.16  Distrib 10.1.26-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: openvpn
-- ------------------------------------------------------
-- Server version	10.1.26-MariaDB-0+deb9u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `log_trusted_ip` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_trusted_port` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_remote_ip` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_remote_port` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `log_received` float NOT NULL DEFAULT '0',
  `log_send` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `user_pass` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1234',
  `user_mail` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_phone` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_online` tinyint(1) NOT NULL DEFAULT '0',
  `user_enable` tinyint(1) NOT NULL DEFAULT '1',
  `user_start_date` date NOT NULL,
  `user_end_date` date NOT NULL,
  `premium` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_pass` (`user_pass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-02-10 15:50:22
