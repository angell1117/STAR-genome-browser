-- MySQL dump 10.13  Distrib 5.1.67, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: sdec_tbl
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
-- Table structure for table `hg19_models`
--

DROP TABLE IF EXISTS `hg19_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hg19_models` (
  `id` varchar(11) NOT NULL DEFAULT '',
  `parent` varchar(11) DEFAULT NULL,
  `assembly` varchar(10) DEFAULT NULL,
  `contributor` tinytext,
  `class` tinytext,
  `start` bigint(20) DEFAULT NULL,
  `end` bigint(20) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `strand` char(2) DEFAULT NULL,
  `phase` char(2) DEFAULT NULL,
  `comments` tinytext,
  `temp` varchar(100) DEFAULT NULL,
  `description` text,
  KEY `assembly` (`assembly`,`start`,`end`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  FULLTEXT KEY `id_2` (`id`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lincRNAsTranscripts_UCSC`
--

DROP TABLE IF EXISTS `lincRNAsTranscripts_UCSC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lincRNAsTranscripts_UCSC` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `parent` varchar(20) DEFAULT NULL,
  `assembly` varchar(10) DEFAULT NULL,
  `contributor` tinytext,
  `class` tinytext,
  `start` bigint(20) DEFAULT NULL,
  `end` bigint(20) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `strand` char(2) DEFAULT NULL,
  `phase` char(2) DEFAULT NULL,
  `comments` tinytext,
  `temp` varchar(100) DEFAULT NULL,
  `description` text,
  KEY `assembly` (`assembly`,`start`,`end`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  FULLTEXT KEY `id_2` (`id`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_hg18`
--

DROP TABLE IF EXISTS `models_hg18`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `models_hg18` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `parent` varchar(20) DEFAULT NULL,
  `assembly` varchar(10) DEFAULT NULL,
  `contributor` tinytext,
  `class` tinytext,
  `start` bigint(20) DEFAULT NULL,
  `end` bigint(20) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `strand` char(2) DEFAULT NULL,
  `phase` char(2) DEFAULT NULL,
  `comments` tinytext,
  `temp` varchar(100) DEFAULT NULL,
  `description` text,
  KEY `assembly` (`assembly`,`start`,`end`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  FULLTEXT KEY `id_2` (`id`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_hg18_lishen`
--

DROP TABLE IF EXISTS `models_hg18_lishen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `models_hg18_lishen` (
  `id` varchar(11) NOT NULL DEFAULT '',
  `parent` varchar(11) DEFAULT NULL,
  `assembly` varchar(10) DEFAULT NULL,
  `contributor` tinytext,
  `class` tinytext,
  `start` bigint(20) DEFAULT NULL,
  `end` bigint(20) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `strand` char(2) DEFAULT NULL,
  `phase` char(2) DEFAULT NULL,
  `comments` tinytext,
  `temp` varchar(100) DEFAULT NULL,
  `description` text,
  KEY `assembly` (`assembly`,`start`,`end`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  FULLTEXT KEY `id_2` (`id`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_mm9`
--

DROP TABLE IF EXISTS `models_mm9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `models_mm9` (
  `id` varchar(11) NOT NULL DEFAULT '',
  `parent` varchar(11) DEFAULT NULL,
  `assembly` varchar(10) DEFAULT NULL,
  `contributor` tinytext,
  `class` tinytext,
  `start` bigint(20) DEFAULT NULL,
  `end` bigint(20) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `strand` char(2) DEFAULT NULL,
  `phase` char(2) DEFAULT NULL,
  `comments` tinytext,
  `temp` varchar(100) DEFAULT NULL,
  `description` text,
  KEY `assembly` (`assembly`,`start`,`end`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  FULLTEXT KEY `id_2` (`id`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_zv9`
--

DROP TABLE IF EXISTS `models_zv9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `models_zv9` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `parent` varchar(20) DEFAULT NULL,
  `assembly` varchar(10) DEFAULT NULL,
  `contributor` tinytext,
  `class` tinytext,
  `start` bigint(20) DEFAULT NULL,
  `end` bigint(20) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `strand` char(2) DEFAULT NULL,
  `phase` char(2) DEFAULT NULL,
  `comments` tinytext,
  `temp` varchar(100) DEFAULT NULL,
  `description` text,
  KEY `assembly` (`assembly`,`start`,`end`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  FULLTEXT KEY `id_2` (`id`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twist_wig`
--

DROP TABLE IF EXISTS `twist_wig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `twist_wig` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assembly` varchar(10) NOT NULL,
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `value` float(8,2) unsigned NOT NULL,
  `strand` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `start` (`start`,`assembly`)
) ENGINE=InnoDB AUTO_INCREMENT=1305212 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wgRna_UCSC`
--

DROP TABLE IF EXISTS `wgRna_UCSC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wgRna_UCSC` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `parent` varchar(20) DEFAULT NULL,
  `assembly` varchar(10) DEFAULT NULL,
  `contributor` tinytext,
  `class` tinytext,
  `start` bigint(20) DEFAULT NULL,
  `end` bigint(20) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `strand` char(2) DEFAULT NULL,
  `phase` char(2) DEFAULT NULL,
  `comments` tinytext,
  `temp` varchar(100) DEFAULT NULL,
  `description` text,
  KEY `assembly` (`assembly`,`start`,`end`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  FULLTEXT KEY `id_2` (`id`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-08-30  0:16:14
