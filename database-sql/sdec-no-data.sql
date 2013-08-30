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
-- Table structure for table `MetaData_Encode`
--

DROP TABLE IF EXISTS `MetaData_Encode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MetaData_Encode` (
  `idmEncode` int(11) NOT NULL AUTO_INCREMENT COMMENT 'internal Id for a sample',
  `ID` varchar(45) NOT NULL COMMENT 'external ID , provided by RenLab',
  `Marker` varchar(45) NOT NULL,
  `Stage` varchar(45) NOT NULL,
  `RepID` int(11) NOT NULL COMMENT 'replicate id',
  `Tissue` varchar(45) NOT NULL,
  `Info` varchar(45) DEFAULT NULL COMMENT 'memo',
  `FileName` varchar(45) DEFAULT NULL COMMENT 'file name same as ID string',
  `FK_track` int(11) DEFAULT NULL,
  `UploadDate` date DEFAULT NULL,
  `DataAuthor` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idmEncode`)
) ENGINE=MyISAM AUTO_INCREMENT=195 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Plan_Encode`
--

DROP TABLE IF EXISTS `Plan_Encode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Plan_Encode` (
  `Markers` varchar(45) DEFAULT NULL,
  `Stage` varchar(45) DEFAULT NULL,
  `Tissue` varchar(45) DEFAULT NULL,
  `RepNum` int(11) DEFAULT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
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
  PRIMARY KEY (`conf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1598 DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=10302 DEFAULT CHARSET=latin1;
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

--
-- Temporary table structure for view `view_Stat_encode`
--

DROP TABLE IF EXISTS `view_Stat_encode`;
/*!50001 DROP VIEW IF EXISTS `view_Stat_encode`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Stat_encode` (
  `Amount` bigint(21),
  `Markers` longtext,
  `Input` int(1),
  `Tissue` varchar(45),
  `Stage` varchar(45),
  `RepID` int(11)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Stat_no_rep`
--

DROP TABLE IF EXISTS `view_Stat_no_rep`;
/*!50001 DROP VIEW IF EXISTS `view_Stat_no_rep`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Stat_no_rep` (
  `Amount` bigint(21),
  `Markers` longtext,
  `Input` int(1),
  `Tissue` varchar(45),
  `Stage` varchar(45)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `view_Stat_encode`
--

/*!50001 DROP TABLE IF EXISTS `view_Stat_encode`*/;
/*!50001 DROP VIEW IF EXISTS `view_Stat_encode`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sdepig`@`tabit.ucsd.edu` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Stat_encode` AS select count(`MetaData_Encode`.`Marker`) AS `Amount`,group_concat(`MetaData_Encode`.`Marker` order by `MetaData_Encode`.`Marker` ASC separator ',') AS `Markers`,(find_in_set('input',group_concat(`MetaData_Encode`.`Marker` separator ',')) > 0) AS `Input`,`MetaData_Encode`.`Tissue` AS `Tissue`,`MetaData_Encode`.`Stage` AS `Stage`,`MetaData_Encode`.`RepID` AS `RepID` from `MetaData_Encode` group by lcase(concat(`MetaData_Encode`.`Tissue`,`MetaData_Encode`.`Stage`,`MetaData_Encode`.`RepID`)) order by count(`MetaData_Encode`.`Marker`) desc,`MetaData_Encode`.`Tissue`,`MetaData_Encode`.`Stage`,`MetaData_Encode`.`RepID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Stat_no_rep`
--

/*!50001 DROP TABLE IF EXISTS `view_Stat_no_rep`*/;
/*!50001 DROP VIEW IF EXISTS `view_Stat_no_rep`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sdepig`@`tabit.ucsd.edu` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Stat_no_rep` AS select count(distinct `MetaData_Encode`.`Marker`) AS `Amount`,group_concat(distinct `MetaData_Encode`.`Marker` order by `MetaData_Encode`.`Marker` ASC separator ',') AS `Markers`,(find_in_set('input',group_concat(`MetaData_Encode`.`Marker` separator ',')) > 0) AS `Input`,`MetaData_Encode`.`Tissue` AS `Tissue`,`MetaData_Encode`.`Stage` AS `Stage` from `MetaData_Encode` group by lcase(concat(`MetaData_Encode`.`Tissue`,`MetaData_Encode`.`Stage`)) order by count(distinct `MetaData_Encode`.`Marker`) desc,`MetaData_Encode`.`Tissue`,`MetaData_Encode`.`Stage`,`MetaData_Encode`.`RepID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-08-30  0:14:16
