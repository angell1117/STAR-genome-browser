-- MySQL dump 10.13  Distrib 5.1.67, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: sdec
-- ------------------------------------------------------
-- Server version	5.1.67

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
-- Table structure for table `center`
--

DROP TABLE IF EXISTS `center`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `center` (
  `center` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `annoj`
--

DROP TABLE IF EXISTS `annoj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annoj` (
  `track_type` char(20) DEFAULT NULL,
  `aj_path` char(40) DEFAULT NULL,
  `aj_height` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuration` (
  `conf_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conf_name` varchar(40) DEFAULT NULL,
  `build_date` datetime NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `trk_ids` varchar(1500) DEFAULT NULL,
  `pubname` varchar(60) DEFAULT NULL,
  `puburl` varchar(100) DEFAULT NULL,
  `user_id` char(20) NOT NULL,
  `trk_hts` varchar(200) NOT NULL,
  `start_chr` char(2) DEFAULT '1',
  `start_pos` int(11) DEFAULT '1',
  `bases` int(4) DEFAULT '20',
  `pixels` int(2) DEFAULT '1',
  `lastview_date` datetime DEFAULT NULL,
  `view_count` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`conf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1621 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configuration1`
--

DROP TABLE IF EXISTS `configuration1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuration1` (
  `conf_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conf_name` varchar(40) DEFAULT NULL,
  `build_date` datetime NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `trk_ids` varchar(150) NOT NULL,
  `pubname` varchar(60) DEFAULT NULL,
  `puburl` varchar(100) DEFAULT NULL,
  `user_id` char(20) NOT NULL,
  `trk_hts` varchar(200) NOT NULL,
  `start_chr` char(2) DEFAULT '1',
  `start_pos` int(11) DEFAULT '1',
  `bases` int(4) DEFAULT '20',
  `pixels` int(2) DEFAULT '1',
  PRIMARY KEY (`conf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=878 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `data_type`
--

DROP TABLE IF EXISTS `data_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_type` (
  `data_type` char(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mark`
--

DROP TABLE IF EXISTS `mark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mark` (
  `mark` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organism`
--

DROP TABLE IF EXISTS `organism`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organism` (
  `organism` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdecgroup`
--

DROP TABLE IF EXISTS `sdecgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdecgroup` (
  `GROUP_NAME` varchar(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sdectrack`
--

DROP TABLE IF EXISTS `sdectrack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sdectrack` (
  `track_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `track`
--

DROP TABLE IF EXISTS `track`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `track` (
  `track_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `track_name` text,
  `upload_date` datetime NOT NULL,
  `track_url` text,
  `track_type` char(20) DEFAULT NULL,
  `user_id` char(20) NOT NULL,
  `access` enum('private','group','public') NOT NULL,
  `track_user` text,
  `organism` varchar(80) DEFAULT NULL,
  `url_self` varchar(120) DEFAULT NULL,
  `url_meta` varchar(120) DEFAULT NULL,
  `center` varchar(80) DEFAULT NULL,
  `mark` varchar(80) DEFAULT NULL,
  `type` varchar(80) DEFAULT NULL,
  `data_type` char(20) DEFAULT NULL,
  `data_format` char(20) DEFAULT NULL,
  `data_author` varchar(20) DEFAULT NULL,
  `info` text,
  `category` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`track_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10436 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `track2`
--

DROP TABLE IF EXISTS `track2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `track2` (
  `track_id` int(10) unsigned NOT NULL DEFAULT '0',
  `track_name` varchar(80) NOT NULL,
  `upload_date` datetime NOT NULL,
  `track_url` varchar(120) NOT NULL,
  `track_type` enum('ReadsTrack','ModelsTrack','MethTrack') NOT NULL,
  `user_id` char(20) NOT NULL,
  `access` enum('private','group','public') NOT NULL,
  `track_user` text,
  `organism` varchar(80) DEFAULT NULL,
  `url_self` varchar(120) DEFAULT NULL,
  `url_meta` varchar(120) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `type`
--

DROP TABLE IF EXISTS `type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type` (
  `type` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `USER_ID` varchar(40) NOT NULL DEFAULT '' COMMENT 'User account',
  `PASSWD` varchar(50) DEFAULT NULL COMMENT 'password',
  `USER_TNAME` varchar(45) DEFAULT NULL COMMENT 'name',
  `USER_TELEPHONE` varchar(45) DEFAULT NULL COMMENT 'telphone',
  `USER_INSTITUTE` varchar(100) DEFAULT NULL COMMENT 'institute',
  `USER_ADDRESS` varchar(100) DEFAULT NULL COMMENT 'address',
  `EMAIL` varchar(100) DEFAULT NULL COMMENT 'email address',
  `ACCOUNT_GROUP` varchar(40) DEFAULT NULL COMMENT 'account group',
  `USER_STATUS` tinyint(4) DEFAULT NULL COMMENT 'account status',
  `GROUP_ID` int(10) DEFAULT NULL COMMENT 'group id',
  `MEMO` varchar(100) DEFAULT NULL COMMENT 'memo',
  `REG_TIME` datetime DEFAULT NULL COMMENT 'register time',
  `MODIFY_TIME` datetime DEFAULT NULL COMMENT 'modify time',
  PRIMARY KEY (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userinfo`
--

DROP TABLE IF EXISTS `userinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userinfo` (
  `user_id` char(20) NOT NULL DEFAULT '',
  `email` char(40) NOT NULL,
  `passwd` char(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usertrack`
--

DROP TABLE IF EXISTS `usertrack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertrack` (
  `USER_ID` varchar(40) NOT NULL,
  `TRACK_LIST` text NOT NULL,
  PRIMARY KEY (`USER_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usrgroup`
--

DROP TABLE IF EXISTS `usrgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usrgroup` (
  `GROUP_ID` int(11) NOT NULL DEFAULT '0' COMMENT 'group id',
  `GROUP_NAME` varchar(50) DEFAULT '' COMMENT 'group_name',
  PRIMARY KEY (`GROUP_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dump completed on 2013-09-29 15:19:03
