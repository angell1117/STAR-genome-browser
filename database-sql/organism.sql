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
-- Dumping data for table `organism`
--

LOCK TABLES `organism` WRITE;
/*!40000 ALTER TABLE `organism` DISABLE KEYS */;
INSERT INTO `organism` VALUES ('Apis mellifera'),('Arabidopsis lyrata'),('Arabidopsis thaliana'),('Bombyx mori'),('Caenorhabditis elegans'),('Candida albicans'),('Candida glabrata'),('Chlamydomonas reinhardtii'),('Chlorella sp. NC64A'),('Ciona intestinalis'),('Coprinopsis cinerea'),('Debaryomyces hansenii'),('Dictyostelium discoideum'),('Drosophila melanogaster'),('Drosophila mojavensis'),('Drosophila pseudoobscura'),('Drosophila sechellia'),('Drosophila simulans'),('Drosophila yakuba'),('Escherichia coli'),('Gallus gallus'),('Geobacter sulfurreducens'),('Homo sapiens'),('Kluyveromyces lactis'),('Laccaria bicolor'),('Lachancea waltii'),('Macaca mulatta'),('Mus musculus'),('Mus spretus'),('Naumovia castellii'),('Nematostella vectensis'),('Neurospora crassa'),('Oryza sativa'),('Pan troglodytes'),('Phycomyces blakesleeanus'),('Physcomitrella patens'),('Plasmodium falciparum'),('Postia placenta'),('Rattus norvegicus'),('Saccharomyces bayanus'),('Saccharomyces cerevisiae'),('Saccharomyces kluyveri'),('Saccharomyces mikatae'),('Saccharomyces paradoxus'),('Schizosaccharomyces pombe'),('Selaginella moellendorffii'),('Tetraodon nigroviridis'),('Tribolium castaneum'),('Uncinocarpus reesii'),('Volvox carteri'),('Yarrowia lipolytica'),('Others'),('Zebrafish');
/*!40000 ALTER TABLE `organism` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-09-29 15:28:24
