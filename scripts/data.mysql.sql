-- MySQL dump 10.13  Distrib 5.1.63, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: authserver_zf
-- ------------------------------------------------------
-- Server version	5.1.63-0+squeeze1

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

USE `authserver_zf`;

/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` VALUES ('dichred_clientid1','7aba9979f96d797400ed5e6503f85972','web','https://localhost/client/index/process','Dichiarazione dei redditi client','L\'applicazione che permette di automatizzare (in parte) la compilazione della dichiarazione dei redditi 730.', DEFAULT/*,'2012-05-09 08:20:45'*/);
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `resource_server` WRITE;
/*!40000 ALTER TABLE `resource_server` DISABLE KEYS */;
INSERT INTO `resource_server` VALUES 
('comune_rs_id','fe57e2b903694aefc6b87c87190d533f','cf','Comune Resource Server','https://localhost/comune/server/data'),
('agenziaentrate_rs_id', '59af05a9f4a1de9c4a6a3933a235dee4', 'cf', 'Agenzia delle Entrate Server', 'https://localhost/agenziaentrate/server/data'),
('agterritorio_rs_id', '535985ed1e2d6075b640b09481bebe7e', 'cf', 'Agenzia del Territorio (e Catasto) Server', 'https://localhost/agenziaterritorio/server/data');
/*!40000 ALTER TABLE `resource_server` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `scope` WRITE;
/*!40000 ALTER TABLE `scope` DISABLE KEYS */;
INSERT INTO `scope` VALUES ('comune_rs_id','lettura_dati_anagrafici','Lettura dati anagrafici'), ('comune_rs_id','aggiornamento_dati_anagrafici','Aggiornamento dati anagrafici'), ('comune_rs_id','scrittura_dati_anagrafici','Scittura dati anagrafici'), ('comune_rs_id','cancellazione_dati_anagrafici','Cancellazione dati anagrafici'), ('agenziaentrate_rs_id', 'lettura_dati_sostituto_imposta', 'Lettura dati del sostituto d\'imposta'), ('agenziaentrate_rs_id', 'lettura_dati_contratti_locazione_fabbricati', 'Lettura dati dei contratti di locazione dei fabbricati'), ('agenziaentrate_rs_id', 'lettura_dati_cud', 'Lettura dati del CUD'), ('agenziaentrate_rs_id', 'lettura_dati_spese_mediche', 'Lettura dati delle spese mediche'), ('agterritorio_rs_id', 'lettura_dati_fabbricati', 'Lettura dati dei fabbricati in possesso');
/*!40000 ALTER TABLE `scope` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('mario.rossi@gmail.com','5f4dcc3b5aa765d61d8327deb882cf99', DEFAULT), ('artur.tolstenco@gmail.com', '5f4dcc3b5aa765d61d8327deb882cf99', DEFAULT);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `user_reference` WRITE;
/*!40000 ALTER TABLE `user_reference` DISABLE KEYS */;
INSERT INTO `user_reference` VALUES ('mario.rossi@gmail.com', 'comune_rs_id', 'RSSMRA85T10A562S'), ('mario.rossi@gmail.com', 'agenziaentrate_rs_id', 'RSSMRA85T10A562S'), ('mario.rossi@gmail.com', 'agterritorio_rs_id', 'RSSMRA85T10A562S'), ('artur.tolstenco@gmail.com', 'comune_rs_id', 'aaaaaaaaaaaaaaaaaaaaaa');
/*!40000 ALTER TABLE `user_reference` ENABLE KEYS */;
UNLOCK TABLES;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
