--
-- Host: Wills-Mac.attlocal.net    Database: metube
-- ------------------------------------------------------
-- Server version	5.5.52-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account` (
  `username` varchar(50) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(60) DEFAULT NULL,
  `type` varchar(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES ('chris','c36aa41cce1f6151e952f0a1c67a97d809ed685e','chris@example.com','1'),('evan','5e772b0040905d370dcfd60b77b38b22a30e927d','evan@example.com','1'),('metube','d8913df37b24c97f28f840114d05bd110dbb2e44','ldong@clemson.edu','1'),('palmer','0fa3ab2c8515cb241d18d5d9b0fd69faf4477415','pcone@clemson.edu','1'),('test','5e52fee47e6b070565f74372468cdc699de89107','','1'),('TestCase','e3431a8e0adbf96fd140103dc6f63a3f8fa343ab','TestCase@metube.com','1'),('TestCase1','cd9d379715cccc83fd8c8c2dc0730c6dd081bd35','Test@comcast.net','1'),('The boy','c1aac5ff1cbfcc2efaa1b2a19c2d887b8d44edfa','Iseeyou@will.com','1'),('will','e37a88681eaafdf47a5a86b4c3998693e7b313ab','wsherre@clemson.edu','1');
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `mediaid` int(11) DEFAULT NULL,
  `cat` varchar(50) DEFAULT NULL,
  KEY `mediaid` (`mediaid`),
  CONSTRAINT `category_ibfk_1` FOREIGN KEY (`mediaid`) REFERENCES `media` (`mediaid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (18,'new'),(18,'hey'),(16,'hey'),(16,'new'),(29,'test'),(35,'new'),(35,'yo'),(35,'Animals'),(36,'Animals'),(9,'Animals'),(37,'Animals'),(38,'Animals'),(39,'Animals'),(42,'cheeseball');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `comment` mediumtext,
  `filepath` varchar(100) DEFAULT NULL,
  `user` varchar(20) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  KEY `user` (`user`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user`) REFERENCES `account` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES ('This comment was removed by the owner of this video','uploads/will/soccer.mp4','test','2020-11-16 18:57:57'),('Futbol','uploads/test/soccer.mp4','palmer','2020-11-30 05:25:32'),('That is a cute puppy!','uploads/metube/nintendogs_wallcoo.com_6.jpg','TestCase1','2020-12-01 17:22:54'),('I love it!','uploads/metube/nintendogs_wallcoo.com_6.jpg','Palmer','2020-12-01 17:23:36'),('This comment was removed by the owner of this video','uploads/will/soccer.mp4','TestCase1','2020-12-01 17:26:32'),('Hello','uploads/palmer/temporalrecurrence.png','palmer','2020-12-02 07:17:03'),('theis is cool','uploads/will/cheeseball.png','will','2020-12-04 15:38:56'),('cute','uploads/metube/nintendogs_wallcoo.com_6.jpg','will','2020-12-04 15:44:20');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `user` varchar(20) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `apply` tinyint(1) DEFAULT NULL,
  KEY `user` (`user`),
  CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`user`) REFERENCES `account` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES ('test','palmer',0),('metube','palmer',0),('will','test',0),('evan','will',0),('palmer','will',0),('will','chris',0),('test','chris',1),('TestCase1','Palmer',0),('will','TestCase1',0),('chris','TestCase1',1),('evan','TestCase1',1),('metube','TestCase1',1),('test','TestCase1',1),('TestCase','TestCase1',1),('The boy','TestCase1',1),('TestCase','Palmer',1),('metube','will',1),('TestCase','will',1);
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `download`
--

DROP TABLE IF EXISTS `download`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `download` (
  `downloadid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `mediaid` int(11) NOT NULL,
  `downloadtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`downloadid`),
  KEY `username` (`username`),
  KEY `mediaid` (`mediaid`),
  CONSTRAINT `download_ibfk_2` FOREIGN KEY (`username`) REFERENCES `account` (`username`),
  CONSTRAINT `download_ibfk_1` FOREIGN KEY (`mediaid`) REFERENCES `media` (`mediaid`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `download`
--

LOCK TABLES `download` WRITE;
/*!40000 ALTER TABLE `download` DISABLE KEYS */;
INSERT INTO `download` VALUES (6,'metube',9,'2020-10-22 13:40:21'),(8,'will',16,'2020-11-09 16:19:49'),(9,'will',16,'2020-11-09 16:37:50'),(10,'will',16,'2020-11-09 22:16:54'),(11,'will',18,'2020-11-10 01:04:15'),(12,'will',18,'2020-11-10 01:11:12'),(15,'will',16,'2020-11-10 01:22:53'),(18,'test',21,'2020-11-10 08:20:29'),(19,'test',21,'2020-11-10 08:21:53'),(20,'test',21,'2020-11-10 08:22:51'),(21,'test',22,'2020-11-10 08:48:58'),(22,'test',22,'2020-11-10 08:49:10'),(23,'test',23,'2020-11-10 15:59:36');
/*!40000 ALTER TABLE `download` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fav`
--

DROP TABLE IF EXISTS `fav`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fav` (
  `mediaid` int(11) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  KEY `mediaid` (`mediaid`),
  CONSTRAINT `fav_ibfk_1` FOREIGN KEY (`mediaid`) REFERENCES `media` (`mediaid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fav`
--

LOCK TABLES `fav` WRITE;
/*!40000 ALTER TABLE `fav` DISABLE KEYS */;
INSERT INTO `fav` VALUES (22,'will'),(26,'will'),(18,'chris'),(29,'palmer'),(27,'TestCase1'),(35,'TestCase1'),(35,'will'),(42,'will'),(9,'will');
/*!40000 ALTER TABLE `fav` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keywords` (
  `mediaid` int(11) DEFAULT NULL,
  `word` varchar(20) DEFAULT NULL,
  KEY `mediaid` (`mediaid`),
  CONSTRAINT `keywords_ibfk_1` FOREIGN KEY (`mediaid`) REFERENCES `media` (`mediaid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `keywords`
--

LOCK TABLES `keywords` WRITE;
/*!40000 ALTER TABLE `keywords` DISABLE KEYS */;
INSERT INTO `keywords` VALUES (18,'new'),(16,'hey'),(35,'dogs'),(35,'puppies'),(35,'cute'),(35,'Animals'),(36,'dogs'),(37,'Safari'),(37,'Tiger'),(38,'Safari'),(38,'Giraffe'),(39,'Safari'),(39,'Elephant'),(9,'dogs');
/*!40000 ALTER TABLE `keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `mediaid` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(64) NOT NULL,
  `filepath` varchar(256) NOT NULL,
  `type` varchar(30) DEFAULT '0',
  `lastaccesstime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mediaid`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (9,'nintendogs_wallcoo.com_6.jpg','uploads/metube/','image/jpeg','2021-10-07 04:19:27'),(16,'test.jpeg','uploads/will/','image/jpeg','2021-10-07 04:19:27'),(17,'meep.jpg','uploads/evan/','image/jpeg','2021-10-07 04:19:27'),(18,'soccer.mp4','uploads/will/','video/mp4','2021-10-07 04:19:27'),(21,'schedule.png','uploads/test/','image/png','2021-10-07 04:19:27'),(22,'soccer.mp4','uploads/test/','video/mp4','2021-10-07 04:19:27'),(23,'test.jpeg','uploads/test/','image/jpeg','2021-10-07 04:19:27'),(26,'schedule.png','uploads/will/','image/png','2021-10-07 04:19:27'),(27,'ball.mp4','uploads/will/','video/mp4','2021-10-07 04:19:27'),(28,'cross.mp4','uploads/will/','video/mp4','2021-10-07 04:19:27'),(29,'vcity.mp4','uploads/will/','video/mp4','2021-10-07 04:19:27'),(30,'temporalrecurrence.png','uploads/palmer/','image/png','2021-10-07 04:19:27'),(31,'Spatial+distributions.png','uploads/palmer/','image/png','2021-10-07 04:19:27'),(32,'NRP.png','uploads/palmer/','image/png','2021-10-07 04:19:27'),(34,'cheeseball.png','uploads/will/','image/png','2021-10-07 04:19:27'),(35,'baby_dog13.mp4','uploads/TestCase1/','video/mp4','2021-10-07 04:19:27'),(36,'German_Shepherd.jpg','uploads/TestCase1/','image/jpeg','2021-10-07 04:19:27'),(37,'tiger.jpg','uploads/TestCase1/','image/jpeg','2021-10-07 04:19:27'),(38,'giraffe+sighting.jpg','uploads/TestCase1/','image/jpeg','2021-10-07 04:19:27'),(39,'elephant.jpeg','uploads/TestCase1/','image/jpeg','2021-10-07 04:19:27'),(40,'UserSignIn-result.png','uploads/palmer/','image/png','2021-10-07 04:19:27'),(41,'cheeseball.png','uploads/will/','image/png','2021-10-07 04:19:27'),(42,'cheeseball.png','uploads/will/','image/png','2021-10-07 04:19:27');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `target` varchar(20) DEFAULT NULL,
  `sender` varchar(20) DEFAULT NULL,
  `message` mediumtext,
  `time` datetime DEFAULT NULL,
  KEY `target` (`target`),
  KEY `sender` (`sender`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender`) REFERENCES `account` (`username`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`target`) REFERENCES `account` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES ('palmer','will','hey','2020-10-27 00:28:21'),('metube','will','test','2020-10-27 00:45:30'),('palmer','will','yo','2020-10-27 01:29:16'),('palmer','will','test','2020-10-27 01:38:49'),('metube','will','g','2020-10-27 01:39:50'),('palmer','will','hey','2020-10-27 01:39:57'),('will','palmer','yoooooooo','2020-10-27 01:40:12'),('will','palmer','haha it works wow','2020-10-27 01:40:21'),('will','palmer','yea im talking to myself idc its 2am ','2020-10-27 01:47:38'),('palmer','will','compose test','2020-10-27 10:37:37'),('test','will','first message','2020-10-27 10:38:15'),('test','will','hey its me again','2020-10-27 10:38:55'),('will','test','hey','2020-10-27 18:56:06'),('will','palmer','BRUH','2020-11-05 19:34:12'),('will','palmer','I think i got hacked lmao','2020-11-05 19:35:06'),('will','palmer','this is insane','2020-11-05 19:35:20'),('will','palmer','this is insane','2020-11-05 19:36:08'),('test','will','whatsup','2020-11-09 21:59:39'),('test','will','test','2020-11-10 14:05:59'),('chris','will','yo whatsup','2020-11-23 16:52:20'),('will','chris','hey','2020-11-23 16:52:42'),('will','evan','hey','2020-11-30 17:32:30'),('evan','will','yoooo whatsup','2020-11-30 17:32:49'),('palmer','TestCase1','Hello','2020-12-01 17:06:28'),('TestCase1','palmer','Hi how are you today','2020-12-01 17:16:24'),('palmer','TestCase1','Hi how are you today','2020-12-01 17:19:32'),('palmer','will','test','2020-12-04 14:43:40'),('palmer','will','hey','2020-12-04 15:33:52'),('test','will','send','2020-12-04 15:34:01');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playlist`
--

DROP TABLE IF EXISTS `playlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlist` (
  `owner` varchar(20) DEFAULT NULL,
  `title` varchar(20) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  KEY `id` (`id`),
  CONSTRAINT `playlist_ibfk_1` FOREIGN KEY (`id`) REFERENCES `media` (`mediaid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playlist`
--

LOCK TABLES `playlist` WRITE;
/*!40000 ALTER TABLE `playlist` DISABLE KEYS */;
INSERT INTO `playlist` VALUES ('will','hey',18),('will','yo',9),('palmer','MyPlaylist\\',22),('TestCase1','Test Playlist 1',35),('TestCase1','Test Playlist 1',28),('TestCase1','Test Playlist 2',23),('TestCase1','Test Playlist 2',27),('will','hey',9);
/*!40000 ALTER TABLE `playlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `title`
--

DROP TABLE IF EXISTS `title`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `title` (
  `title` varchar(100) DEFAULT NULL,
  `description` mediumtext,
  `mediaid` int(11) DEFAULT NULL,
  KEY `mediaid` (`mediaid`),
  CONSTRAINT `title_ibfk_1` FOREIGN KEY (`mediaid`) REFERENCES `media` (`mediaid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `title`
--

LOCK TABLES `title` WRITE;
/*!40000 ALTER TABLE `title` DISABLE KEYS */;
INSERT INTO `title` VALUES ('kid kicking a ball','this is a video about soccer',18),('soccer.mp4','No description',22),('ball.mp4','No description',27),('cross','No description',28),('vcity.mp4','No description',29),('nintendogs_wallcoo.com_6.jpg','No description',9),('test','Meep baby',16),('meep.jpg','No description',17),('schedule.png','No description',21),('test.jpeg','No description',23),('schedule.png','No description',26),('temporalrecurrence.png','No description',30),('Spatial+distributions.png','No description',31),('NRP.png','No description',32),('Pups\r\n','Dogs',35),('Big Dog','The goodest of boys',36),('tiger.jpg','No description',37),('giraffes','No description',38),('elephant','No description',39),('UserSignIn-result.png','No description',40),('cheeseball','Cheeseball',42);
/*!40000 ALTER TABLE `title` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload`
--

DROP TABLE IF EXISTS `upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `upload` (
  `uploadid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `mediaid` int(11) NOT NULL,
  `uploadtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uploadid`),
  KEY `username` (`username`),
  KEY `mediaid` (`mediaid`),
  CONSTRAINT `upload_ibfk_2` FOREIGN KEY (`username`) REFERENCES `account` (`username`),
  CONSTRAINT `upload_ibfk_1` FOREIGN KEY (`mediaid`) REFERENCES `media` (`mediaid`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upload`
--

LOCK TABLES `upload` WRITE;
/*!40000 ALTER TABLE `upload` DISABLE KEYS */;
INSERT INTO `upload` VALUES (9,'metube',9,'2008-09-05 20:28:36'),(13,'will',16,'2020-10-22 14:39:58'),(14,'evan',17,'2020-10-31 23:21:17'),(15,'will',18,'2020-11-09 14:42:30'),(18,'test',21,'2020-11-10 08:20:22'),(19,'test',22,'2020-11-10 08:48:46'),(20,'test',23,'2020-11-10 15:59:32'),(23,'will',26,'2020-11-12 06:33:24'),(24,'will',27,'2020-11-12 20:18:02'),(25,'will',28,'2020-11-12 20:18:16'),(26,'will',29,'2020-11-12 20:18:29'),(27,'palmer',30,'2020-11-30 10:22:14'),(28,'palmer',31,'2020-11-30 10:23:39'),(29,'palmer',32,'2020-11-30 10:24:56'),(32,'TestCase1',35,'2020-12-01 19:29:55'),(33,'TestCase1',36,'2020-12-01 21:50:32'),(34,'TestCase1',37,'2020-12-01 22:34:35'),(35,'TestCase1',38,'2020-12-01 22:35:21'),(36,'TestCase1',39,'2020-12-01 22:37:59'),(37,'palmer',40,'2020-12-02 12:12:12'),(39,'will',42,'2020-12-04 20:35:26');
/*!40000 ALTER TABLE `upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_playlists`
--

DROP TABLE IF EXISTS `user_playlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_playlists` (
  `owner` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  KEY `owner` (`owner`),
  CONSTRAINT `user_playlists_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `account` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_playlists`
--

LOCK TABLES `user_playlists` WRITE;
/*!40000 ALTER TABLE `user_playlists` DISABLE KEYS */;
INSERT INTO `user_playlists` VALUES ('will','hey'),('will','yo'),('palmer','MyPlaylist\\'),('TestCase1','Test Playlist 1'),('TestCase1','Test Playlist 2');
/*!40000 ALTER TABLE `user_playlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `view`
--

DROP TABLE IF EXISTS `view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `view` (
  `mediaid` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  KEY `mediaid` (`mediaid`),
  CONSTRAINT `view_ibfk_1` FOREIGN KEY (`mediaid`) REFERENCES `media` (`mediaid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `view`
--

LOCK TABLES `view` WRITE;
/*!40000 ALTER TABLE `view` DISABLE KEYS */;
INSERT INTO `view` VALUES (9,26),(16,20),(17,1),(18,71),(21,2),(22,38),(23,3),(26,1),(27,9),(28,7),(29,12),(30,1),(31,1),(32,1),(35,13),(36,1),(37,0),(38,0),(39,4),(40,0),(34,0),(34,0),(41,0),(42,1);
/*!40000 ALTER TABLE `view` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-08-20 23:43:08
