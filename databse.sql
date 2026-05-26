-- MySQL dump 10.13  Distrib 8.0.32, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: pinterest
-- ------------------------------------------------------
-- Server version	8.0.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(100) DEFAULT NULL,
  `admin_email` varchar(150) DEFAULT NULL,
  `admin_password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `admin_email` (`admin_email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'Admin','ayushisomya1611@gmail.com','0192023a7bbd73250516f069df18b500','2026-05-10 20:07:41');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Admin','ayushisomya1611@gmail.com','Aayu@123','2026-05-11 04:01:09');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `board_pins`
--

DROP TABLE IF EXISTS `board_pins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `board_pins` (
  `board_pin_id` int NOT NULL AUTO_INCREMENT,
  `board_id` int NOT NULL,
  `pin_id` int NOT NULL,
  PRIMARY KEY (`board_pin_id`),
  KEY `board_id` (`board_id`),
  KEY `pin_id` (`pin_id`),
  CONSTRAINT `board_pins_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `boards` (`board_id`) ON DELETE CASCADE,
  CONSTRAINT `board_pins_ibfk_2` FOREIGN KEY (`pin_id`) REFERENCES `pins` (`pin_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board_pins`
--

LOCK TABLES `board_pins` WRITE;
/*!40000 ALTER TABLE `board_pins` DISABLE KEYS */;
INSERT INTO `board_pins` VALUES (14,15,77),(15,17,78),(16,17,79);
/*!40000 ALTER TABLE `board_pins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boards`
--

DROP TABLE IF EXISTS `boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boards` (
  `board_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `board_name` varchar(255) NOT NULL,
  `description` text,
  `privacy` enum('public','private') DEFAULT 'public',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`board_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `boards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boards`
--

LOCK TABLES `boards` WRITE;
/*!40000 ALTER TABLE `boards` DISABLE KEYS */;
INSERT INTO `boards` VALUES (1,12,'Fashion Inspo','Outfits and style ideas I love','public','2026-04-02 04:30:00'),(2,12,'Home Decor Dreams','Interior design ideas for my future home','public','2026-04-02 05:00:00'),(3,13,'Tech Gadgets','Latest gadgets and electronics','public','2026-04-04 03:30:00'),(4,13,'Startup Tips','Entrepreneurship and business growth','public','2026-04-04 04:00:00'),(5,14,'Food & Recipes','Delicious recipes from around the world','public','2026-04-06 06:30:00'),(6,14,'Travel Bucket List','Places I want to visit','public','2026-04-06 07:00:00'),(7,15,'Brand Designs','Logo and branding inspiration','public','2026-04-08 03:00:00'),(8,15,'Photography','Best shots and photo composition tips','private','2026-04-08 03:30:00'),(9,16,'Art & Illustration','Digital and traditional art I admire','public','2026-04-11 08:30:00'),(10,16,'Fitness Goals','Workouts, yoga and health tips','public','2026-04-11 09:00:00'),(11,17,'Marketing Ideas','Creative marketing campaigns','public','2026-04-13 04:30:00'),(15,32,'Mountain','','public','2026-05-21 10:03:25'),(17,33,'Ayush','','public','2026-05-21 12:08:38');
/*!40000 ALTER TABLE `boards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_requests`
--

DROP TABLE IF EXISTS `business_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `business_requests` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `request_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `business_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_requests`
--

LOCK TABLES `business_requests` WRITE;
/*!40000 ALTER TABLE `business_requests` DISABLE KEYS */;
INSERT INTO `business_requests` VALUES (1,13,'approved','2026-04-04 04:30:00'),(2,15,'pending','2026-04-08 03:30:00'),(3,17,'approved','2026-04-13 05:30:00'),(4,20,'rejected','2026-04-21 04:30:00'),(5,13,'approved','2026-04-04 04:30:00'),(6,15,'pending','2026-04-08 03:30:00'),(7,17,'approved','2026-04-13 05:30:00'),(8,20,'rejected','2026-04-21 04:30:00');
/*!40000 ALTER TABLE `business_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int unsigned NOT NULL,
  `receiver_id` int unsigned NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_messages`
--

LOCK TABLES `chat_messages` WRITE;
/*!40000 ALTER TABLE `chat_messages` DISABLE KEYS */;
INSERT INTO `chat_messages` VALUES (1,26,29,'Hii',0,'2026-05-21 12:28:21'),(2,26,30,'hii',1,'2026-05-21 12:29:17'),(3,30,26,'hello',1,'2026-05-21 12:38:56'),(4,30,26,'hii abcd',1,'2026-05-21 12:50:30'),(5,26,30,'hello',1,'2026-05-21 12:50:36'),(6,30,26,'nice',1,'2026-05-21 12:56:33'),(7,32,26,'hii',1,'2026-05-21 15:35:56'),(8,31,32,'hrtyu',0,'2026-05-21 16:10:26'),(9,33,26,'hii',1,'2026-05-21 17:42:49'),(10,26,33,'hello',1,'2026-05-21 17:43:54'),(11,33,26,'123 123 check',1,'2026-05-21 17:44:08');
/*!40000 ALTER TABLE `chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_online`
--

DROP TABLE IF EXISTS `chat_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_online` (
  `user_id` int unsigned NOT NULL,
  `last_seen` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `socket_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_online`
--

LOCK TABLES `chat_online` WRITE;
/*!40000 ALTER TABLE `chat_online` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_online` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `comment_id` int NOT NULL AUTO_INCREMENT,
  `pin_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`),
  KEY `pin_id` (`pin_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`pin_id`) REFERENCES `pins` (`pin_id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `followers`
--

DROP TABLE IF EXISTS `followers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `followers` (
  `follower_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `following_user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`follower_id`),
  UNIQUE KEY `unique_follow` (`user_id`,`following_user_id`),
  KEY `following_user_id` (`following_user_id`),
  CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`following_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `followers`
--

LOCK TABLES `followers` WRITE;
/*!40000 ALTER TABLE `followers` DISABLE KEYS */;
INSERT INTO `followers` VALUES (1,12,14,'2026-04-05 04:30:00'),(2,12,16,'2026-04-06 05:30:00'),(3,13,17,'2026-04-06 03:30:00'),(4,14,12,'2026-04-07 06:30:00'),(5,16,15,'2026-04-12 04:30:00'),(6,19,13,'2026-04-19 03:30:00'),(7,21,12,'2026-04-23 05:30:00'),(16,11,12,'2026-05-19 10:07:52'),(17,21,13,'2026-05-19 10:10:56'),(22,31,11,'2026-05-20 08:52:53'),(26,26,32,'2026-05-21 10:07:00'),(27,26,33,'2026-05-21 12:15:03');
/*!40000 ALTER TABLE `followers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `like_id` int NOT NULL AUTO_INCREMENT,
  `pin_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`like_id`),
  KEY `pin_id` (`pin_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`pin_id`) REFERENCES `pins` (`pin_id`) ON DELETE CASCADE,
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (1,4,12,'2026-04-06 05:00:00'),(2,1,13,'2026-04-05 04:00:00'),(3,13,14,'2026-04-15 06:30:00'),(4,5,16,'2026-04-10 08:30:00'),(5,11,17,'2026-04-15 03:30:00'),(6,7,19,'2026-04-20 04:30:00'),(7,10,18,'2026-04-16 05:30:00'),(8,3,21,'2026-04-23 06:30:00');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `notification_type` varchar(100) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (9,11,'Kumari Ayushi saved your pin \"Sourdough Bread Guide\"','save',0,'2026-05-21 09:22:01'),(10,11,'Kumari Ayushi commented on your pin \"Sourdough Bread Guide\": \"hii\"','comment',0,'2026-05-21 09:33:39'),(11,11,'Kumari Ayushi commented on your pin \"Santorini Sunsets\": \"hii\"','comment',0,'2026-05-21 09:37:16'),(12,11,'Kumari Ayushi commented on your pin \"Navy Dress Style\": \"hii\"','comment',0,'2026-05-21 09:41:10'),(13,11,'Kumari Ayushi commented on your pin \"Santorini Sunsets\": \"hello\"','comment',0,'2026-05-21 09:44:39'),(14,11,'Kumari Ayushi commented on your pin \"Oil Pastel Landscape\": \"hi\"','comment',0,'2026-05-21 09:46:06'),(15,32,'ABCD started following you','follow',1,'2026-05-21 10:04:27'),(16,32,'ABCD liked your pin \"Mountain\"','like',1,'2026-05-21 10:04:34'),(17,32,'ABCD commented on your pin \"Mountain\": \"hi\"','comment',1,'2026-05-21 10:04:38'),(18,32,'ABCD commented on your pin \"Mountain\": \"teri\"','comment',0,'2026-05-21 10:07:08'),(19,11,'Ayush saved your pin \"Watercolour Florals\"','save',0,'2026-05-21 12:10:45'),(20,11,'Ayush liked your pin \"Watercolour Florals\"','like',0,'2026-05-21 12:10:47'),(21,11,'Ayush saved your pin \"Watercolour Florals\"','save',0,'2026-05-21 12:11:14'),(22,11,'Ayush commented on your pin \"Watercolour Florals\": \"hi\"','comment',0,'2026-05-21 12:11:23'),(23,33,'ABCD liked your pin \"Ayush\"','like',1,'2026-05-21 12:14:59'),(24,33,'ABCD started following you','follow',1,'2026-05-21 12:15:03'),(25,33,'ABCD liked your pin \"Hello\"','like',1,'2026-05-22 04:27:06'),(26,11,'ABCD liked your pin \"Navy Dress Style\"','like',0,'2026-05-22 06:07:47'),(27,11,'ABCD commented on your pin \"Navy Dress Style\": \"HELLO\"','comment',0,'2026-05-22 06:09:09'),(28,11,'ABCD liked your pin \"Minimal Desk Setup\"','like',0,'2026-05-22 10:57:51');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pin_comments`
--

DROP TABLE IF EXISTS `pin_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pin_comments` (
  `comment_id` int NOT NULL AUTO_INCREMENT,
  `pin_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`),
  KEY `pin_id` (`pin_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `pin_comments_ibfk_1` FOREIGN KEY (`pin_id`) REFERENCES `pins` (`pin_id`) ON DELETE CASCADE,
  CONSTRAINT `pin_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pin_comments`
--

LOCK TABLES `pin_comments` WRITE;
/*!40000 ALTER TABLE `pin_comments` DISABLE KEYS */;
INSERT INTO `pin_comments` VALUES (1,58,27,'Nice','2026-05-19 16:58:22'),(2,76,31,'nice','2026-05-20 12:39:33'),(3,76,31,'jhy','2026-05-20 12:39:36'),(4,76,31,'de','2026-05-20 12:39:37'),(5,76,31,'frwe','2026-05-20 12:39:38'),(6,76,31,'de','2026-05-20 12:39:39'),(7,76,31,'dewr','2026-05-20 12:39:40'),(8,76,31,'dfewr','2026-05-20 12:39:41'),(9,76,31,'few','2026-05-20 12:39:49'),(10,76,31,'red','2026-05-20 12:39:50'),(11,76,31,'ewr','2026-05-20 12:39:52'),(12,58,31,'hii','2026-05-20 14:23:59'),(13,76,31,'hii','2026-05-20 14:28:55'),(17,60,32,'hii','2026-05-21 15:11:10'),(18,43,32,'hello','2026-05-21 15:14:39'),(19,52,32,'hi','2026-05-21 15:16:06'),(20,77,26,'hi','2026-05-21 15:34:38'),(21,77,26,'teri','2026-05-21 15:37:08'),(22,44,33,'hi','2026-05-21 17:41:23'),(23,60,26,'HELLO','2026-05-22 11:39:09');
/*!40000 ALTER TABLE `pin_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pin_likes`
--

DROP TABLE IF EXISTS `pin_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pin_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pin_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`pin_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pin_likes`
--

LOCK TABLES `pin_likes` WRITE;
/*!40000 ALTER TABLE `pin_likes` DISABLE KEYS */;
INSERT INTO `pin_likes` VALUES (7,13,27),(8,15,27),(5,44,26),(15,44,33),(18,60,26),(13,76,26),(12,76,31),(14,77,26),(16,78,26),(17,79,26);
/*!40000 ALTER TABLE `pin_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pins`
--

DROP TABLE IF EXISTS `pins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pins` (
  `pin_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `image` varchar(255) NOT NULL,
  `destination_link` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pin_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `pins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pins`
--

LOCK TABLES `pins` WRITE;
/*!40000 ALTER TABLE `pins` DISABLE KEYS */;
INSERT INTO `pins` VALUES (1,12,'Boho Summer Dress','Perfect flowy dress for summer days at the beach','https://picsum.photos/seed/pin1/400/600','https://pinterest.com','Fashion','2026-04-03 04:30:00'),(2,12,'Minimalist Outfit','Clean lines, neutral tones — effortless everyday style','https://picsum.photos/seed/pin2/400/600','https://pinterest.com','Fashion','2026-04-04 05:30:00'),(3,12,'Cozy Living Room Setup','Warm tones, soft lighting and comfy furniture','https://picsum.photos/seed/pin3/400/600','https://pinterest.com','Home Decor','2026-04-05 04:00:00'),(4,13,'Wireless Earbuds 2026','Best noise-cancelling earbuds for daily commute','https://picsum.photos/seed/pin4/400/600','https://pinterest.com','Technology','2026-04-05 08:30:00'),(5,13,'Minimal Desk Setup','Clean and productive workspace for developers','https://picsum.photos/seed/pin5/400/600','https://pinterest.com','Technology','2026-04-06 04:30:00'),(6,13,'10 Rules for Startups','Key principles every founder should follow','https://picsum.photos/seed/pin6/400/600','https://pinterest.com','Business','2026-04-07 02:30:00'),(7,14,'Homemade Butter Chicken','Rich, creamy and full of flavour — best recipe ever','https://picsum.photos/seed/pin7/400/600','https://pinterest.com','Food','2026-04-07 06:30:00'),(8,14,'Mango Cheesecake','No-bake mango cheesecake that melts in your mouth','https://picsum.photos/seed/pin8/400/600','https://pinterest.com','Food','2026-04-08 07:30:00'),(9,14,'Santorini Greece','White buildings, blue domes and endless ocean views','https://picsum.photos/seed/pin9/400/600','https://pinterest.com','Travel','2026-04-09 09:30:00'),(10,16,'Watercolour Florals','Delicate watercolour flowers in pastel shades','https://picsum.photos/seed/pin10/400/600','https://pinterest.com','Art','2026-04-12 03:30:00'),(11,16,'Portrait Sketch Tutorial','Step-by-step guide to drawing realistic portraits','https://picsum.photos/seed/pin11/400/600','https://pinterest.com','Art','2026-04-12 05:00:00'),(13,17,'Instagram Ad Design Tips','How to design ads that actually convert','https://picsum.photos/seed/pin13/400/600','https://pinterest.com','Marketing','2026-04-14 05:30:00'),(14,17,'Modern Villa Design','Open plan living with floor-to-ceiling glass walls','https://picsum.photos/seed/pin14/400/600','https://pinterest.com','Architecture','2026-04-15 08:30:00'),(15,15,'Golden Hour Photography','Capturing the magic of sunset light','https://picsum.photos/seed/pin15/400/600','https://pinterest.com','Photography','2026-04-16 12:30:00'),(40,11,'Pink Saree Look','Elegant pink saree with embroidered border - perfect for festive occasions.','https://picsum.photos/seed/saree1/400/560','#','fashion','2026-05-19 03:57:03'),(41,11,'Avocado Toast Recipe','The perfect brunch - creamy avocado on sourdough with poached eggs.','https://picsum.photos/seed/food2/400/300','#','food','2026-05-19 03:57:03'),(42,11,'Wide Leg Jeans Outfit','Baggy wide-leg jeans with minimal sneakers - the effortless look.','https://picsum.photos/seed/jeans3/400/500','#','fashion','2026-05-19 03:57:03'),(43,11,'Santorini Sunsets','Chasing golden hour over the Aegean Sea. Every evening is a masterpiece.','https://picsum.photos/seed/santorini/400/350','#','travel','2026-05-19 03:57:03'),(44,11,'Watercolour Florals','Step-by-step guide to loose watercolour flowers for beginners.','https://picsum.photos/seed/art5/400/480','#','art','2026-05-19 03:57:03'),(45,11,'Morning Yoga Flow','10-minute energising yoga routine to start your day with calm energy.','https://picsum.photos/seed/yoga6/400/420','#','fitness','2026-05-19 03:57:03'),(46,11,'Minimal Desk Setup','Clean, productive home-office setup under Rs 20,000. Less is more.','https://picsum.photos/seed/desk7/400/320','#','tech','2026-05-19 03:57:03'),(47,11,'Forest Bathing','How spending time in forests boosts mental health and reduces stress.','https://picsum.photos/seed/forest8/400/540','#','nature','2026-05-19 03:57:03'),(48,11,'DIY Macrame Wall Art','Create beautiful macrame art with just rope and a dowel rod.','https://picsum.photos/seed/mac9/400/400','#','diy','2026-05-19 03:57:03'),(49,11,'Tokyo Neon Nights','A night photography walk through Shinjuku. Stunning city lights.','https://picsum.photos/seed/tokyo10/400/460','#','travel','2026-05-19 03:57:03'),(50,11,'Sourdough Bread Guide','Everything you need to know about fermenting your first sourdough loaf.','https://picsum.photos/seed/bread11/400/380','#','food','2026-05-19 03:57:03'),(51,11,'Boho Living Room','Earthy tones and texture-rich textiles for a relaxed bohemian vibe.','https://picsum.photos/seed/boho12/400/500','#','home','2026-05-19 03:57:03'),(52,11,'Oil Pastel Landscape','Beginner-friendly oil pastel sunset tutorial - no experience needed.','https://picsum.photos/seed/pastel13/400/440','#','art','2026-05-19 03:57:03'),(53,11,'Core Workout at Home','30-day core challenge - no gym, no equipment needed. Start today!','https://picsum.photos/seed/core14/400/360','#','fitness','2026-05-19 03:57:03'),(54,11,'Linen Summer Dress','Breathable, chic linen looks for the hottest summer months.','https://picsum.photos/seed/linen15/400/520','#','fashion','2026-05-19 03:57:03'),(55,11,'Raspberry Smoothie Bowl','Topped with granola, chia seeds, and fresh berries. Healthy and pretty.','https://picsum.photos/seed/smooth16/400/310','#','food','2026-05-19 03:57:03'),(56,11,'Northern Lights Iceland','Aurora hunting tips and the best spots to camp under the lights.','https://picsum.photos/seed/aurora17/400/470','#','travel','2026-05-19 03:57:03'),(57,11,'DIY Terrarium Garden','Build a gorgeous mini ecosystem in a glass jar. Perfect weekend project.','https://picsum.photos/seed/terra18/400/420','#','diy','2026-05-19 03:57:03'),(58,11,'Cute Golden Retriever','This fluffy pup just wants to be your best friend!','https://picsum.photos/seed/puppy19/400/440','#','animals','2026-05-19 03:57:03'),(59,11,'Bold Lip Makeup','Deep red and berry lip looks for every skin tone. Statement beauty.','https://picsum.photos/seed/makeup20/400/380','#','beauty','2026-05-19 03:57:03'),(60,11,'Navy Dress Style','Off-shoulder navy dress perfect for evening outings and parties.','https://picsum.photos/seed/dress21/400/500','#','fashion','2026-05-19 03:57:03'),(61,11,'Terracotta Bedroom','Warm earthy tones with linen textures - cosy bedroom goals.','https://picsum.photos/seed/bedroom22/400/440','#','home','2026-05-19 03:57:03'),(62,11,'Ink Sketch Art','Penguin illustration in ink and pencil. Simple and adorable.','https://picsum.photos/seed/sketch23/400/400','#','art','2026-05-19 03:57:03'),(63,11,'Web Dev Portfolio','Dark-themed web developer portfolio with orange accents. Clean and modern.','https://picsum.photos/seed/webdev24/400/500','#','tech','2026-05-19 03:57:03'),(64,27,'Aayu Pic','Aayu pic in the college uniform','https://picsum.photos/seed/aayu/400/500','','Technology','2026-05-19 09:43:22'),(76,27,'helo','','uploads/pins/pin_27_1779253413.jpeg','','fashion','2026-05-20 05:03:33'),(77,32,'Mountain','Mountain habihwir wh','uploads/pins/pin_32_1779357844.jpeg','','nature','2026-05-21 10:04:04'),(78,33,'Ayush','','uploads/pins/pin_33_1779365351.png','','tech','2026-05-21 12:09:11'),(79,33,'Hello','','uploads/pins/pin_33_1779365416.png','','fashion','2026-05-21 12:10:16');
/*!40000 ALTER TABLE `pins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `pin_id` int DEFAULT NULL,
  `report_reason` text,
  `report_status` enum('pending','resolved') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  KEY `user_id` (`user_id`),
  KEY `pin_id` (`pin_id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`pin_id`) REFERENCES `pins` (`pin_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saved_pins`
--

DROP TABLE IF EXISTS `saved_pins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saved_pins` (
  `saved_pin_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `pin_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`saved_pin_id`),
  KEY `user_id` (`user_id`),
  KEY `pin_id` (`pin_id`),
  CONSTRAINT `saved_pins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `saved_pins_ibfk_2` FOREIGN KEY (`pin_id`) REFERENCES `pins` (`pin_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saved_pins`
--

LOCK TABLES `saved_pins` WRITE;
/*!40000 ALTER TABLE `saved_pins` DISABLE KEYS */;
INSERT INTO `saved_pins` VALUES (1,12,4,'2026-04-06 04:30:00'),(2,12,7,'2026-04-08 05:30:00'),(3,13,1,'2026-04-05 03:30:00'),(4,14,5,'2026-04-07 08:30:00'),(5,16,6,'2026-04-09 09:30:00'),(6,17,10,'2026-04-14 04:30:00'),(7,19,2,'2026-04-20 03:30:00'),(8,21,9,'2026-04-23 05:30:00'),(11,26,43,'2026-05-19 06:55:06'),(12,26,45,'2026-05-19 07:47:01'),(21,31,76,'2026-05-20 08:58:48'),(22,26,76,'2026-05-20 09:00:17'),(23,32,50,'2026-05-21 09:22:01'),(25,33,44,'2026-05-21 12:11:14');
/*!40000 ALTER TABLE `saved_pins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text,
  `website` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT '',
  `gender` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `account_type` enum('personal','business') DEFAULT 'personal',
  `business_status` enum('none','pending','approved','rejected','suspended') DEFAULT 'none',
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  `deactivated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (11,'Ayushi','ayushi210','somasaumya12@gmail.com','$2y$10$ZI1S0hogmaZ/7H7g3FYiGufScL1aVQvHUp88bvfJYbD3Ig1suc9fS','default.png',NULL,NULL,'','Female','2004-11-16','personal','none',0,'2026-05-12 04:17:43',1,0,NULL,NULL),(12,'Priya Sharma','priyasharma','priya.sharma@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Female','1998-03-15','personal','none',0,'2026-04-01 03:42:00',1,0,NULL,NULL),(13,'Rahul Verma','rahulverma','rahul.verma@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Male','1995-07-22','business','approved',0,'2026-04-03 06:15:00',1,0,NULL,NULL),(14,'Sneha Patel','snehapatel','sneha.patel@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Female','2000-01-10','personal','none',0,'2026-04-05 09:00:00',1,0,NULL,NULL),(15,'Arjun Mehta','arjunmehta','arjun.mehta@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Male','1997-11-05','business','pending',0,'2026-04-07 02:30:00',1,0,NULL,NULL),(16,'Kavya Nair','kavyanair','kavya.nair@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Female','1999-06-18','personal','suspended',0,'2026-04-10 10:50:00',1,0,NULL,NULL),(17,'Vikram Singh','vikramsingh','vikram.singh@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Male','1993-09-30','business','approved',0,'2026-04-12 04:30:00',1,0,NULL,NULL),(18,'Anjali Gupta','anjali_gupta','anjali.gupta@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Female','2001-04-25','personal','none',0,'2026-04-15 07:15:00',1,0,NULL,NULL),(19,'Rohan Das','rohandas','rohan.das@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Male','1996-12-12','personal','none',0,'2026-04-18 04:00:00',1,0,NULL,NULL),(20,'Meera Joshi','meerajoshi','meera.joshi@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Female','1994-08-08','business','rejected',0,'2026-04-20 09:30:00',1,0,NULL,NULL),(21,'Aditya Kumar','adityakumar','aditya.kumar@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','default.png',NULL,NULL,'','Male','2002-02-14','personal','none',0,'2026-04-22 05:40:00',1,0,NULL,NULL),(26,'ABCD','abcd226','abcd12@gmail.com','$2y$10$O/mUeKhsI3WmXcldt9UUJe.tRrt2Vxa2kUoSYORbdkn7iqjrJlZPO','uploads/profiles/user_26_1779358141.jpeg','','','','Male','2000-12-11','personal','none',0,'2026-05-15 09:45:26',1,0,NULL,NULL),(27,'Deleted User','abhay496','deleted_27@pinboard.invalid','',NULL,NULL,NULL,NULL,'Male','2004-11-28','personal','none',0,'2026-05-18 03:55:26',0,1,NULL,'2026-05-20 16:19:06'),(28,'nishchay','nishchay766','nagpaln201@gmail.com','$2y$10$u.UYyz5HcLLm33.2gc5iEuIAmguJ9GENfN/yilK/6f0wEgFw034ki',NULL,NULL,NULL,'','Male','2026-11-06','personal','none',0,'2026-05-18 09:38:24',1,0,NULL,NULL),(29,'Aayu','aayu523','aayu12@gmail.com','$2y$10$s3towXhDHCnr9CgRBzi5l.A5MwOt7zNNxviWroYFpQkmjJJD7e4ra',NULL,NULL,NULL,'','Female','2000-11-12','personal','none',0,'2026-05-18 11:25:10',1,0,NULL,NULL),(30,'Aditi','aditi949','aditi@gmail.com','$2y$10$IfHtxvFjQRRdxei8SMhGQ.9ihUlmb6zxl/4hZxwLHzhrO7Ux9qsvS',NULL,NULL,NULL,'','Female','1999-10-27','personal','suspended',0,'2026-05-18 11:26:08',1,0,NULL,NULL),(31,'gunjan','gunjan142','gunjan@gmail.com','$2y$10$.wQ6nibibuMrWGbMXnAv3.2m5fHHCJa7HnzYNBI6p.S4yOr9hv4jm',NULL,'','','','','2003-03-29','personal','none',0,'2026-05-20 07:09:02',1,0,NULL,NULL),(32,'Kumari Ayushi','kumariayushi984','abcd@gmail.com','$2y$10$3uJCw080A7O604HbUYuONe.a4cXXv0.sZcXiT.FEdVstqgkVKAHJy',NULL,NULL,NULL,'','Female','2000-11-16','personal','none',0,'2026-05-21 09:08:14',1,0,NULL,NULL),(33,'Ayush','ayush405','bayush0001@gmail.com','$2y$10$qlFijWi9hJ349M8oBAvIJeVDebxUyDqvqIeYFirSGFqleG92Ews8O',NULL,NULL,NULL,'','Male','2000-11-11','personal','none',0,'2026-05-21 12:04:20',1,0,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-22 16:31:07
