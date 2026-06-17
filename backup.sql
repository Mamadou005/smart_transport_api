-- MySQL dump 10.13  Distrib 8.2.0, for Win64 (x86_64)
--
-- Host: localhost    Database: smart_transport
-- ------------------------------------------------------
-- Server version	8.2.0

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
-- Table structure for table `bagages`
--

DROP TABLE IF EXISTS `bagages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bagages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poids` decimal(5,2) NOT NULL,
  `code_qr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('enregistre','en_transit','arrive','perdu','recupere') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enregistre',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bagages_code_qr_unique` (`code_qr`),
  KEY `bagages_reservation_id_foreign` (`reservation_id`),
  CONSTRAINT `bagages_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bagages`
--

LOCK TABLES `bagages` WRITE;
/*!40000 ALTER TABLE `bagages` DISABLE KEYS */;
INSERT INTO `bagages` VALUES (5,11,'Matay',24.00,'BAG-CVVKF5KM','recupere','2026-05-20 17:35:17','2026-05-20 17:44:36'),(6,12,'Test',10.00,'BAG-WWLMZZMP','enregistre','2026-05-22 21:37:18','2026-05-22 21:37:18'),(7,14,'Dem',122.00,'BAG-AZD8DWWB','enregistre','2026-06-05 18:44:30','2026-06-05 18:44:30'),(8,15,'Test',20.00,'BAG-W0NXXEWW','enregistre','2026-06-12 01:41:59','2026-06-12 01:41:59');
/*!40000 ALTER TABLE `bagages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `localisations`
--

DROP TABLE IF EXISTS `localisations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `localisations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bagage_id` bigint unsigned NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `horodatage` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `localisations_bagage_id_foreign` (`bagage_id`),
  CONSTRAINT `localisations_bagage_id_foreign` FOREIGN KEY (`bagage_id`) REFERENCES `bagages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `localisations`
--

LOCK TABLES `localisations` WRITE;
/*!40000 ALTER TABLE `localisations` DISABLE KEYS */;
/*!40000 ALTER TABLE `localisations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_06_233601_create_personal_access_tokens_table',1),(5,'2026_05_06_233734_create_voyages_table',1),(6,'2026_05_06_233737_create_reservations_table',1),(7,'2026_05_06_233738_create_bagages_table',1),(8,'2026_05_06_233739_create_localisations_table',1),(9,'2026_05_06_233740_create_signalements_table',1),(10,'2026_05_06_233741_create_notifications_table',1),(11,'2026_05_21_004653_add_prix_to_voyages_table',2),(12,'2026_05_21_004914_create_paiements_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications_transport`
--

DROP TABLE IF EXISTS `notifications_transport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications_transport` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('confirmation','arrivee','perte','alerte') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmation',
  `lue` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_transport_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_transport_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications_transport`
--

LOCK TABLES `notifications_transport` WRITE;
/*!40000 ALTER TABLE `notifications_transport` DISABLE KEYS */;
INSERT INTO `notifications_transport` VALUES (1,3,'✅ Réservation confirmée : RES-TNAYUFVC — Dakar → Ndar','confirmation',1,'2026-05-14 20:58:23','2026-05-15 01:44:30'),(2,3,'🚨 Signalement enregistré pour le bagage BAG-X747KRQG. Notre équipe est alertée.','perte',1,'2026-05-14 20:59:31','2026-05-15 01:44:30'),(3,3,'🔍 Votre signalement pour le bagage BAG-X747KRQG est pris en charge. Nous recherchons activement votre bagage.','alerte',1,'2026-05-15 20:25:21','2026-05-15 20:27:56'),(4,3,'❌ Après recherche, votre bagage BAG-X747KRQG est confirmé perdu. Contactez notre service client.','perte',1,'2026-05-20 17:04:43','2026-06-05 18:44:46'),(5,3,'✅ Réservation confirmée ! Code : RES-JZMY30MX | Senegal → Maroc le 21/05/2026 à 18:00','confirmation',1,'2026-05-20 17:34:40','2026-06-05 18:44:46'),(6,3,'🧳 Bagage enregistré ! Code : BAG-CVVKF5KM | Poids : 24 kg','confirmation',1,'2026-05-20 17:35:17','2026-06-05 18:44:46'),(7,3,'🚪 Embarquement validé ! Bon voyage de Senegal vers Maroc.','confirmation',1,'2026-05-20 17:41:19','2026-06-05 18:44:46'),(8,3,'🚨 Signalement enregistré pour le bagage BAG-CVVKF5KM. Notre équipe est alertée et recherche votre bagage.','perte',1,'2026-05-20 17:43:34','2026-06-05 18:44:46'),(9,3,'🎉 Bonne nouvelle ! Votre bagage BAG-X747KRQG a été retrouvé et est disponible à la récupération.','arrivee',1,'2026-05-20 17:44:22','2026-06-05 18:44:46'),(10,3,'🔍 Votre signalement pour le bagage BAG-CVVKF5KM est pris en charge. Nous recherchons activement votre bagage.','alerte',1,'2026-05-20 17:53:52','2026-06-05 18:44:46'),(11,3,'✅ Réservation confirmée ! Code : RES-LJXT1IA2 | Dakar → Touba le 21/05/2026 à 15:00','confirmation',1,'2026-05-21 09:19:33','2026-06-05 18:44:46'),(12,3,'💳 Paiement de 5000.00 XOF confirmé ! Référence : PAY-GIPHPZ43ZD','confirmation',1,'2026-05-21 09:20:22','2026-06-05 18:44:46'),(13,3,'✅ Réservation confirmée ! Code : RES-XPREXCC2 | Dakar → Touba le 21/05/2026 à 15:00','confirmation',1,'2026-05-21 10:03:07','2026-06-05 18:44:46'),(14,3,'🧳 Bagage enregistré ! Code : BAG-WWLMZZMP | Poids : 10 kg','confirmation',1,'2026-05-22 21:37:18','2026-06-05 18:44:46'),(15,3,'✅ Réservation confirmée ! Code : RES-NQ16HNTC | Mbour → Thiadiaye le 06/06/2026 à 15:00','confirmation',1,'2026-06-05 18:41:32','2026-06-05 18:44:46'),(16,3,'💳 Paiement de 3000.00 XOF confirmé ! Référence : PAY-YRLHOIVSKZ','confirmation',1,'2026-06-05 18:43:49','2026-06-05 18:44:46'),(17,3,'🧳 Bagage enregistré ! Code : BAG-AZD8DWWB | Poids : 122 kg','confirmation',1,'2026-06-05 18:44:30','2026-06-05 18:44:46'),(18,3,'🎉 Bonne nouvelle ! Votre bagage BAG-CVVKF5KM a été retrouvé et est disponible à la récupération.','arrivee',1,'2026-06-12 01:34:40','2026-06-12 02:55:34'),(19,3,'✅ Réservation confirmée ! Code : RES-BGD0FXE4 | Dakar → Canada le 13/06/2026 à 06:00','confirmation',1,'2026-06-12 01:39:06','2026-06-12 02:55:34'),(20,3,'💳 Paiement de 400000.00 XOF confirmé ! Référence : PAY-TWOUMPXCS0','confirmation',1,'2026-06-12 01:40:10','2026-06-12 02:55:34'),(21,3,'🧳 Bagage enregistré ! Code : BAG-W0NXXEWW | Poids : 20 kg','confirmation',1,'2026-06-12 01:41:59','2026-06-12 02:55:34'),(22,3,'🚪 Embarquement validé ! Bon voyage de Dakar vers Canada.','confirmation',1,'2026-06-12 02:23:29','2026-06-12 02:55:34');
/*!40000 ALTER TABLE `notifications_transport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paiements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `devise` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'XOF',
  `methode` enum('wave','orange_money','cash') COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('en_attente','confirme','echoue','rembourse') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_paiement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confirme_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paiements_reference_unique` (`reference`),
  KEY `paiements_reservation_id_foreign` (`reservation_id`),
  KEY `paiements_user_id_foreign` (`user_id`),
  CONSTRAINT `paiements_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `paiements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiements`
--

LOCK TABLES `paiements` WRITE;
/*!40000 ALTER TABLE `paiements` DISABLE KEYS */;
INSERT INTO `paiements` VALUES (1,12,3,5000.00,'XOF','wave','confirme','PAY-GIPHPZ43ZD','771400276','2026-05-21 09:20:22','2026-05-21 09:20:11','2026-05-21 09:20:22'),(2,14,3,3000.00,'XOF','orange_money','confirme','PAY-YRLHOIVSKZ','771400276','2026-06-05 18:43:49','2026-06-05 18:43:35','2026-06-05 18:43:49'),(3,15,3,400000.00,'XOF','orange_money','confirme','PAY-TWOUMPXCS0','77 100 10 10','2026-06-12 01:40:09','2026-06-12 01:39:53','2026-06-12 01:40:09');
/*!40000 ALTER TABLE `paiements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'auth_token','02cc019d6b074b6df8dc3fa81af0d47632a1016f9e724871dd2eb6a9d872107a','[\"*\"]','2026-05-08 21:51:24',NULL,'2026-05-08 21:34:33','2026-05-08 21:51:24'),(2,'App\\Models\\User',4,'auth_token','595eb27c40085390c01e40ff3bcc2414e899cbb96af04b044bb2413a674d2e44','[\"*\"]','2026-05-08 22:09:50',NULL,'2026-05-08 22:08:54','2026-05-08 22:09:50'),(3,'App\\Models\\User',3,'auth_token','8b391a45a10b5328c10937fda929e91da20a840704273a3a7d078d721d2796d6','[\"*\"]','2026-05-08 22:10:42',NULL,'2026-05-08 22:10:39','2026-05-08 22:10:42'),(4,'App\\Models\\User',3,'auth_token','b4c6072ea3b150c304547035f48a7904cbe6591ec1a650dcc64191f12f8c0004','[\"*\"]','2026-05-08 22:12:55',NULL,'2026-05-08 22:12:51','2026-05-08 22:12:55'),(5,'App\\Models\\User',3,'auth_token','c0f1c582145d0812df04b1dad9ef18bc8460a15039f7bb8f30963727c7c20024','[\"*\"]','2026-05-08 22:22:21',NULL,'2026-05-08 22:22:15','2026-05-08 22:22:21'),(6,'App\\Models\\User',3,'auth_token','33dd9819ab2aed45456511aa3275768cd6a77f48ac5cdec8e2706bb2e8cf1a70','[\"*\"]','2026-05-08 22:33:48',NULL,'2026-05-08 22:33:43','2026-05-08 22:33:48'),(7,'App\\Models\\User',3,'auth_token','c4fe47d2a92b130041628b0dfc2957e1f71b4455649b6af83bd45026bb54dc1e','[\"*\"]','2026-05-09 03:52:09',NULL,'2026-05-09 03:51:52','2026-05-09 03:52:09'),(8,'App\\Models\\User',3,'auth_token','eabc9ce41e4cbc0effa3268fa923186f94c287b6cd42f6c870454e9e3a78460b','[\"*\"]',NULL,NULL,'2026-05-09 04:03:32','2026-05-09 04:03:32'),(9,'App\\Models\\User',3,'auth_token','0b92f4dd7ce3b8bc1e5268a097787511000162bb8a3862e699b8f84651b156a2','[\"*\"]','2026-05-09 04:03:59',NULL,'2026-05-09 04:03:35','2026-05-09 04:03:59'),(10,'App\\Models\\User',3,'auth_token','f997a606fc1488fc538b0e257ea7bb181d8bb59e3ae9d0e2c5d48452a2b28729','[\"*\"]','2026-05-09 04:41:28',NULL,'2026-05-09 04:39:53','2026-05-09 04:41:28'),(11,'App\\Models\\User',1,'auth_token','d432eb3e6fd46478404a0cdc6a4dea12b9cf00f9dd00cc5efbd67a9caf4f5e51','[\"*\"]','2026-05-09 04:44:02',NULL,'2026-05-09 04:43:00','2026-05-09 04:44:02'),(12,'App\\Models\\User',1,'auth_token','acace86ec3b9b24dedd19480d0d7f8d8afc454698d43c41daaf0765ff9b751cf','[\"*\"]','2026-05-09 05:43:12',NULL,'2026-05-09 05:42:24','2026-05-09 05:43:12'),(13,'App\\Models\\User',1,'auth_token','76328f3c2a0eef2f1c8282f9d8b658965b19aa525559db0038be0fa0ae8d91e5','[\"*\"]','2026-05-09 11:04:15',NULL,'2026-05-09 11:03:11','2026-05-09 11:04:15'),(14,'App\\Models\\User',3,'auth_token','025547da480c9a6763d7e6990c57e151382fef10abcb15dbf4511ef5c86dc7d3','[\"*\"]','2026-05-09 11:05:31',NULL,'2026-05-09 11:05:28','2026-05-09 11:05:31'),(15,'App\\Models\\User',1,'auth_token','fe0d5a01e4240dc156b70f46b0921eb8a7ab561808d46c76c60c4bf55f6b2e2c','[\"*\"]','2026-05-09 11:19:09',NULL,'2026-05-09 11:18:31','2026-05-09 11:19:09'),(16,'App\\Models\\User',1,'auth_token','367f3178fe4e065d55c7db63418fe638e6188df3785e1496b9e6efcee1e4e1c5','[\"*\"]','2026-05-10 00:46:31',NULL,'2026-05-10 00:46:26','2026-05-10 00:46:31'),(17,'App\\Models\\User',1,'auth_token','d9c10737d670ad6878885f60f6b2465a9bd8eb2970da7d668640112ac64effa6','[\"*\"]','2026-05-10 00:59:55',NULL,'2026-05-10 00:59:33','2026-05-10 00:59:55'),(18,'App\\Models\\User',3,'auth_token','e72987a39712fcb134ebfc81155074ccaea1fbad86f5744d2c22f4e8ac51c2a6','[\"*\"]','2026-05-10 01:01:05',NULL,'2026-05-10 01:00:18','2026-05-10 01:01:05'),(19,'App\\Models\\User',1,'auth_token','304de0b620c65442f061c79c4b37b35a635519dd525282c3af408ce99dccf13a','[\"*\"]','2026-05-10 01:19:35',NULL,'2026-05-10 01:01:43','2026-05-10 01:19:35'),(20,'App\\Models\\User',2,'auth_token','2f79c53d815f3dad7654030f1d239ff388049d01be8d147127d44eaa77b74338','[\"*\"]',NULL,NULL,'2026-05-10 01:20:15','2026-05-10 01:20:15'),(21,'App\\Models\\User',3,'auth_token','44c1a05ec9cc95b5e4eb85499b28e38eb4af2ed4158bbcbec67d58a5274e80e4','[\"*\"]','2026-05-10 01:25:47',NULL,'2026-05-10 01:22:18','2026-05-10 01:25:47'),(22,'App\\Models\\User',1,'auth_token','ac29f57697d24130bcfac972f7269cf633ed7035c8638e5bc70448915d9b8e08','[\"*\"]','2026-05-10 01:27:14',NULL,'2026-05-10 01:26:13','2026-05-10 01:27:14'),(23,'App\\Models\\User',2,'auth_token','762ab76f1c1275d3659a7f6ba41233b41e3566f0a6f2f3778eee0dd1d209dfcf','[\"*\"]',NULL,NULL,'2026-05-10 01:27:39','2026-05-10 01:27:39'),(24,'App\\Models\\User',1,'auth_token','bc88ec36091fc401dd214429093a1ea22825a370eef9dadcd140e8bc240a90ff','[\"*\"]','2026-05-10 01:31:27',NULL,'2026-05-10 01:28:47','2026-05-10 01:31:27'),(25,'App\\Models\\User',3,'auth_token','7ea2583295978c2f0b097cd3887e034e6f84d07995969be2874ff3cb76024f94','[\"*\"]','2026-05-10 01:33:55',NULL,'2026-05-10 01:32:11','2026-05-10 01:33:55'),(26,'App\\Models\\User',2,'auth_token','e85b79117f0cfab752649cde267ecc25e221d7a5411f995f37e9e33a06e7bd7b','[\"*\"]',NULL,NULL,'2026-05-11 20:45:42','2026-05-11 20:45:42'),(27,'App\\Models\\User',3,'auth_token','454680818f48f77b383b357c9886cd7605bf02a957997b213e8c3bdf43186e83','[\"*\"]','2026-05-11 20:48:43',NULL,'2026-05-11 20:47:41','2026-05-11 20:48:43'),(28,'App\\Models\\User',2,'auth_token','be017b122029d5230e2285b399fd4313261b1577683594ffc716c5885f5a2315','[\"*\"]',NULL,NULL,'2026-05-11 20:48:58','2026-05-11 20:48:58'),(29,'App\\Models\\User',1,'auth_token','933e30ebd304ef0391a407811ab35574fc8ab132a54cf8f7120114cc0bf5cf1d','[\"*\"]','2026-05-11 20:54:24',NULL,'2026-05-11 20:53:26','2026-05-11 20:54:24'),(30,'App\\Models\\User',3,'auth_token','9238657783902ea294b1e749946339ca89e5670edd6437d5352238fe6d1031c9','[\"*\"]','2026-05-11 20:55:23',NULL,'2026-05-11 20:54:52','2026-05-11 20:55:23'),(31,'App\\Models\\User',2,'auth_token','0ff5814a5affcbe381899e79cb573f9eea0bf236794332ed3650439721fda8ae','[\"*\"]',NULL,NULL,'2026-05-11 20:55:44','2026-05-11 20:55:44'),(32,'App\\Models\\User',1,'auth_token','b0934748919f7394dab51d87510c1a8f45ce0e1ba9ef35f09fe403a07011973f','[\"*\"]','2026-05-11 21:06:06',NULL,'2026-05-11 21:03:21','2026-05-11 21:06:06'),(33,'App\\Models\\User',1,'auth_token','09125dc3370bbe68b581143725e88957f931c9a7157b7096e12ab75727a40ba8','[\"*\"]','2026-05-11 21:10:31',NULL,'2026-05-11 21:08:39','2026-05-11 21:10:31'),(34,'App\\Models\\User',3,'auth_token','929523bb5a98bb5ec35853eb0f98b1198ed12e29e91f5f5e444097c19d2d100c','[\"*\"]','2026-05-11 21:11:32',NULL,'2026-05-11 21:11:11','2026-05-11 21:11:32'),(35,'App\\Models\\User',2,'auth_token','3a866ed5661dcde641edca1982a0b44915567a98350b62b0ede208f1ec0e3ece','[\"*\"]',NULL,NULL,'2026-05-11 21:11:46','2026-05-11 21:11:46'),(36,'App\\Models\\User',3,'auth_token','83995ce4dbd02f5a17cf6d077b752aa4411e9b099f51279f242a828c1232fbe9','[\"*\"]','2026-05-11 21:33:06',NULL,'2026-05-11 21:30:05','2026-05-11 21:33:06'),(37,'App\\Models\\User',2,'auth_token','1385950e2b0db4d35eeb1d710b2cc375f33fbb3573730608a30034fc4b2bfcd7','[\"*\"]',NULL,NULL,'2026-05-11 21:33:36','2026-05-11 21:33:36'),(38,'App\\Models\\User',3,'auth_token','6dd049370eb861d2afd54974dc21e4e713e5984044cc48430a1614145726c5ca','[\"*\"]','2026-05-11 21:36:29',NULL,'2026-05-11 21:35:03','2026-05-11 21:36:29'),(39,'App\\Models\\User',2,'auth_token','fb6654a497f880b36fb7d79bad7799b7eb5398948d34bb5a9816da3499bd535d','[\"*\"]',NULL,NULL,'2026-05-11 21:36:51','2026-05-11 21:36:51'),(40,'App\\Models\\User',3,'auth_token','bcce3d4e2a2dd8f388592b01c90f2fa317ace02bb494778736b4e5ec71c2742e','[\"*\"]','2026-05-11 21:40:47',NULL,'2026-05-11 21:40:03','2026-05-11 21:40:47'),(41,'App\\Models\\User',2,'auth_token','a32bca6685601b03733cdef7ca0f055bfa6adde7e335966d8dbc05f0687de978','[\"*\"]',NULL,NULL,'2026-05-11 21:41:02','2026-05-11 21:41:02'),(42,'App\\Models\\User',2,'auth_token','2b596133afc86fdd3842436aea4b5907e2ce08306c44156719a2a8c5c7c63c9d','[\"*\"]',NULL,NULL,'2026-05-12 15:53:43','2026-05-12 15:53:43'),(43,'App\\Models\\User',3,'auth_token','dea4917e41e1c6dd4b10a8f407b38b645824ecef02a0818de2de6df3258eecc3','[\"*\"]','2026-05-12 16:31:20',NULL,'2026-05-12 15:55:56','2026-05-12 16:31:20'),(44,'App\\Models\\User',2,'auth_token','9f81173e18c1cc254859997779b62fe3886381b77add51848e91debd314bf4e9','[\"*\"]',NULL,NULL,'2026-05-12 16:33:13','2026-05-12 16:33:13'),(45,'App\\Models\\User',2,'auth_token','7c8f5440d606a8822c6b982890e8c3c8a7f12b7f7fd983737a106cb439982a03','[\"*\"]',NULL,NULL,'2026-05-12 21:41:16','2026-05-12 21:41:16'),(46,'App\\Models\\User',2,'auth_token','385b3746547aca69d0670119f6f7a830fadba5bd00211549192064ebebfcd74d','[\"*\"]',NULL,NULL,'2026-05-12 21:43:52','2026-05-12 21:43:52'),(47,'App\\Models\\User',3,'auth_token','8c95b0bedb81167f3c36942eec9d0236950ed76552050fe1b11dfa6474eabad7','[\"*\"]','2026-05-12 22:02:35',NULL,'2026-05-12 22:01:29','2026-05-12 22:02:35'),(48,'App\\Models\\User',2,'auth_token','ba5688e2d2db8ac7d4df83dcab1eb4fc53201d2127c7d51382aec1918d94b31b','[\"*\"]','2026-05-12 23:16:57',NULL,'2026-05-12 22:02:58','2026-05-12 23:16:57'),(49,'App\\Models\\User',2,'auth_token','0c6a75c757953a882fd74be00bb4d3eb9419c0a67e7a7dc17bd2ef374979a185','[\"*\"]','2026-05-12 23:23:40',NULL,'2026-05-12 23:23:39','2026-05-12 23:23:40'),(50,'App\\Models\\User',3,'auth_token','f2336425f522cccce1b895b06f37e24ba7e423420ef6260e49b02f8dd5962879','[\"*\"]','2026-05-12 23:28:16',NULL,'2026-05-12 23:25:53','2026-05-12 23:28:16'),(51,'App\\Models\\User',1,'auth_token','d499240121b56d6789aca8ae289a4318dd936c675d10669bfeab9b724596de9e','[\"*\"]','2026-05-12 23:35:09',NULL,'2026-05-12 23:29:05','2026-05-12 23:35:09'),(52,'App\\Models\\User',3,'auth_token','61ba35d60ce0d187df490699ac695f3e68e1501c403124e462dd4c166e8a4ab9','[\"*\"]','2026-05-12 23:35:57',NULL,'2026-05-12 23:35:38','2026-05-12 23:35:57'),(53,'App\\Models\\User',2,'auth_token','f02cadce3215aaadcb435a38c6907775bf4212782e7df6982ae12915d486c534','[\"*\"]','2026-05-13 00:47:30',NULL,'2026-05-12 23:36:11','2026-05-13 00:47:30'),(54,'App\\Models\\User',2,'auth_token','dd3d646111fc8d17b2df02f8975da57e019f80834d6d4786352cd16d9b8b0fd2','[\"*\"]','2026-05-13 01:05:28',NULL,'2026-05-13 00:56:14','2026-05-13 01:05:28'),(55,'App\\Models\\User',2,'auth_token','4a986f855b3b997a0e75ac38705286288154bb8c842c8b0df1e1fbf80ed53ccb','[\"*\"]','2026-05-13 01:06:43',NULL,'2026-05-13 01:06:39','2026-05-13 01:06:43'),(56,'App\\Models\\User',2,'auth_token','446231fb936701b3fb0d6aef61612b4b4403360585bf1d38c81cc94683a463ed','[\"*\"]','2026-05-13 01:10:09',NULL,'2026-05-13 01:08:44','2026-05-13 01:10:09'),(57,'App\\Models\\User',1,'auth_token','4720286d7fea82b18b43f2f3aebe4897cf0dc608e2077fa919c5436cb6b61c48','[\"*\"]','2026-05-13 01:35:04',NULL,'2026-05-13 01:34:57','2026-05-13 01:35:04'),(58,'App\\Models\\User',1,'auth_token','e2da0966b58559e5fb84307c8b6f62459f2b3f86da0665decd61781e248696ee','[\"*\"]','2026-05-13 04:04:48',NULL,'2026-05-13 03:35:53','2026-05-13 04:04:48'),(59,'App\\Models\\User',3,'auth_token','3a2fb32bd5f780b58508256d1b16e2acc4f92f4bec3a698590874175007a2d56','[\"*\"]','2026-05-13 04:08:15',NULL,'2026-05-13 04:05:38','2026-05-13 04:08:15'),(60,'App\\Models\\User',1,'auth_token','e144ece7ca9b155015139f71c1df866f9af5fea3b378d8d28ef81b09b4d861e6','[\"*\"]','2026-05-13 04:08:58',NULL,'2026-05-13 04:08:27','2026-05-13 04:08:58'),(61,'App\\Models\\User',3,'auth_token','d4117fb866c9de50c4985da128f35e52b87ecc7ed0277ffe48513b518bf189c9','[\"*\"]','2026-05-13 04:09:31',NULL,'2026-05-13 04:09:12','2026-05-13 04:09:31'),(62,'App\\Models\\User',1,'auth_token','e361bf34efaa9f921044e3abf673ea3351a572694f56cb1fb43aa399279ccffa','[\"*\"]','2026-05-13 04:10:20',NULL,'2026-05-13 04:09:42','2026-05-13 04:10:20'),(63,'App\\Models\\User',3,'auth_token','fd646fdfe511536d620624e440339ca4748fedd69da24d863c917c300c6e8fb3','[\"*\"]','2026-05-13 04:11:38',NULL,'2026-05-13 04:10:37','2026-05-13 04:11:38'),(64,'App\\Models\\User',1,'auth_token','566b858f7fc6a1f54ea17e7c37da020a079586c5fd3aaf71b5d53888df2b7024','[\"*\"]','2026-05-13 04:12:29',NULL,'2026-05-13 04:11:50','2026-05-13 04:12:29'),(65,'App\\Models\\User',3,'auth_token','c4ee9e712d0a5b3b437363660e1837e1ca3e3c40eb6db012b2c6a7cd1e8231cb','[\"*\"]','2026-05-13 04:12:58',NULL,'2026-05-13 04:12:51','2026-05-13 04:12:58'),(66,'App\\Models\\User',3,'auth_token','9276542fc79af6ecd3cf773d0c38a1a183afcca4699e81f5c7025b53bf2bb1a6','[\"*\"]','2026-05-13 20:05:33',NULL,'2026-05-13 20:04:11','2026-05-13 20:05:33'),(67,'App\\Models\\User',1,'auth_token','75646fe5ae8fe5235fce94c35cac6df4dfffb716ac16587e0f28ba5a4b84c105','[\"*\"]','2026-05-13 20:06:20',NULL,'2026-05-13 20:06:09','2026-05-13 20:06:20'),(68,'App\\Models\\User',3,'auth_token','484426d055133d851abcc78f002d7130cca9f412d8fa5243a4d917718bd2bb78','[\"*\"]','2026-05-13 20:07:47',NULL,'2026-05-13 20:06:43','2026-05-13 20:07:47'),(69,'App\\Models\\User',1,'auth_token','b3a64f4cbd70cc390190bb861e9ca8f66795644a24f5f533ded9bf344eaa574e','[\"*\"]','2026-05-13 20:08:33',NULL,'2026-05-13 20:08:18','2026-05-13 20:08:33'),(70,'App\\Models\\User',3,'auth_token','8806a0d982af7a27bb11ef20cda51029307bd577174af9d08658444abb1ea2c3','[\"*\"]','2026-05-13 20:10:42',NULL,'2026-05-13 20:09:44','2026-05-13 20:10:42'),(71,'App\\Models\\User',1,'auth_token','fcdcac162e4edb8a7b08b1ff4fb724d277a69cd393971cfc27369442581d54f9','[\"*\"]','2026-05-13 22:24:46',NULL,'2026-05-13 20:11:36','2026-05-13 22:24:46'),(72,'App\\Models\\User',1,'auth_token','768200bda2d4337c97b619df7d9dde7dda4c87c3309f3d3f40eea6fa3b385066','[\"*\"]','2026-05-13 22:27:38',NULL,'2026-05-13 22:25:06','2026-05-13 22:27:38'),(73,'App\\Models\\User',1,'auth_token','e6949b31075ff3485e552a1b4a4ec685df86928ad87a4c24de507278387c963f','[\"*\"]','2026-05-13 22:28:24',NULL,'2026-05-13 22:27:58','2026-05-13 22:28:24'),(74,'App\\Models\\User',3,'auth_token','383dcfe3abf2f9968e0e0afb5668412124eb433e55ea36b7a95a6eb8b11b683a','[\"*\"]','2026-05-14 03:42:05',NULL,'2026-05-14 03:41:16','2026-05-14 03:42:05'),(75,'App\\Models\\User',3,'auth_token','f24e275a54cd0dc34dd5ed7a142344eeff570eb8e9a3bdb409906e6ab1e607d4','[\"*\"]','2026-05-14 20:56:11',NULL,'2026-05-14 20:55:02','2026-05-14 20:56:11'),(76,'App\\Models\\User',1,'auth_token','155695639424ebb49bd840c8ab513b1613f5c0dbaf81d7362e5d15af71749e83','[\"*\"]','2026-05-14 20:57:42',NULL,'2026-05-14 20:56:22','2026-05-14 20:57:42'),(77,'App\\Models\\User',3,'auth_token','c373c05f582e2f42961f40a1352033a9fa163bf1a09a78149b1c32a7cf996dab','[\"*\"]','2026-05-14 20:59:50',NULL,'2026-05-14 20:58:10','2026-05-14 20:59:50'),(78,'App\\Models\\User',3,'auth_token','2cfe1f29d293f136f734e42ec8ba991e4e4854e4556e7af82a088d44590135af','[\"*\"]','2026-05-15 01:46:06',NULL,'2026-05-15 01:44:15','2026-05-15 01:46:06'),(79,'App\\Models\\User',1,'auth_token','8cd482c2e795d3197810ad8901247a6d05933d067dfc5c5a3ca9cfb1e23d220c','[\"*\"]','2026-05-15 01:46:44',NULL,'2026-05-15 01:46:32','2026-05-15 01:46:44'),(80,'App\\Models\\User',3,'auth_token','e394de0b27b331a0802860255e2153f332ada7699b7734bcae8bc1ed95941a94','[\"*\"]','2026-05-15 04:18:35',NULL,'2026-05-15 04:17:40','2026-05-15 04:18:35'),(81,'App\\Models\\User',2,'auth_token','e302ba0e18ddaa8c62adabd0fa54eb26fc7a203fa315a3938c55f45a78affd17','[\"*\"]','2026-05-15 04:19:46',NULL,'2026-05-15 04:19:07','2026-05-15 04:19:46'),(82,'App\\Models\\User',3,'auth_token','7865bf75515d3931a5458dc882e4f0ad1c0f6d22e53445e96c49f1b1f5402726','[\"*\"]','2026-05-15 20:22:44',NULL,'2026-05-15 20:21:56','2026-05-15 20:22:44'),(83,'App\\Models\\User',2,'auth_token','1d1ac1c2d00aee900df31be864436b62a3a3305422ea777a14e4b92441bd036a','[\"*\"]','2026-05-15 20:23:42',NULL,'2026-05-15 20:23:15','2026-05-15 20:23:42'),(84,'App\\Models\\User',1,'auth_token','75ebbca6df8220ecce7e53e6ed7c6b10b4c0f139de3a5ac87f40ebb6922576ca','[\"*\"]','2026-05-15 20:27:11',NULL,'2026-05-15 20:24:19','2026-05-15 20:27:11'),(85,'App\\Models\\User',3,'auth_token','d84772e514d48ba6364125a653c6cce62dc087bd8c2686d55bfb36a06773059f','[\"*\"]','2026-05-15 20:28:52',NULL,'2026-05-15 20:27:39','2026-05-15 20:28:52'),(86,'App\\Models\\User',2,'auth_token','db8d67a6ea1f0c0d1e51369abb3ea3997be19d1849e2026f33f0e2b1dfc36766','[\"*\"]','2026-05-15 20:29:07',NULL,'2026-05-15 20:29:06','2026-05-15 20:29:07'),(87,'App\\Models\\User',1,'auth_token','6ce109a572322c7135d7bf6cbab780a8131bed5a6f38ab2923ed95a38597479f','[\"*\"]','2026-05-15 20:29:58',NULL,'2026-05-15 20:29:32','2026-05-15 20:29:58'),(88,'App\\Models\\User',2,'auth_token','c3ea9ffefed6965de50a46d0cc3cd89a7ac7ad61bcd0c4f2cc3b4e7ee9dc627f','[\"*\"]','2026-05-18 11:01:22',NULL,'2026-05-18 11:00:47','2026-05-18 11:01:22'),(89,'App\\Models\\User',1,'auth_token','a15613fdecac6e3f2268039098fa3cfec6a3e0d2b1ff993fe4027f1a5229d267','[\"*\"]','2026-05-18 11:02:25',NULL,'2026-05-18 11:01:58','2026-05-18 11:02:25'),(90,'App\\Models\\User',3,'auth_token','5e853123612643f71553e4057a35662e4da8322a9a61938acf6ff7e9a046da62','[\"*\"]','2026-05-18 11:02:58',NULL,'2026-05-18 11:02:40','2026-05-18 11:02:58'),(91,'App\\Models\\User',2,'auth_token','bbdbd801483a1c8d8a1a592ec4dd334d1d3c54716d84fc3449c714e8ae6b825d','[\"*\"]','2026-05-18 23:58:28',NULL,'2026-05-18 23:58:25','2026-05-18 23:58:28'),(92,'App\\Models\\User',1,'auth_token','ca43f310109ea84b560bef91dc58469756adeb55ff1d0930b21b2b367dfd376e','[\"*\"]','2026-05-19 00:02:28',NULL,'2026-05-19 00:01:18','2026-05-19 00:02:28'),(93,'App\\Models\\User',3,'auth_token','604b5a1d60ff780607b123b961d9f45f7ad8d90cd87e2e71ae2a8697ec7cd6dc','[\"*\"]','2026-05-19 00:05:24',NULL,'2026-05-19 00:04:27','2026-05-19 00:05:24'),(94,'App\\Models\\User',1,'auth_token','3d7e8dceb4b61182a2012438c83eeb79319315558d349711a4e67f8643faf708','[\"*\"]','2026-05-19 00:06:59',NULL,'2026-05-19 00:06:43','2026-05-19 00:06:59'),(95,'App\\Models\\User',3,'auth_token','3732bedc57867d7c2fae28e6a9c8a83aee42ff4960fab712d07a43a30b61c57f','[\"*\"]','2026-05-19 00:07:54',NULL,'2026-05-19 00:07:47','2026-05-19 00:07:54'),(96,'App\\Models\\User',3,'auth_token','64894b4a5476dfa86ae3e1df69b19eb4566ff2dae691bbf377f95296f397c0d1','[\"*\"]','2026-05-19 00:09:38',NULL,'2026-05-19 00:09:35','2026-05-19 00:09:38'),(97,'App\\Models\\User',3,'auth_token','b0dc61bff02cbefa8216bce2b9acaf375d92c3d2044482dddca748fe7379e970','[\"*\"]','2026-05-19 00:22:00',NULL,'2026-05-19 00:21:49','2026-05-19 00:22:00'),(98,'App\\Models\\User',3,'auth_token','6a311ac8fbc4c50fab66c25d9356dde82bf6b7beb89b12a73ea4d0b808e390e9','[\"*\"]','2026-05-19 00:26:43',NULL,'2026-05-19 00:26:13','2026-05-19 00:26:43'),(99,'App\\Models\\User',3,'auth_token','ad69e80e3b23775884211037ce3b50e2abf4fb3a50e84606b5bea4b55ac59cfc','[\"*\"]','2026-05-20 17:02:49',NULL,'2026-05-20 17:01:13','2026-05-20 17:02:49'),(100,'App\\Models\\User',1,'auth_token','1f6501e93304f7c8b6163059e80ffd8bc8990dc6acfdbb3fa4b6d7f57f6820d7','[\"*\"]','2026-05-20 17:04:52',NULL,'2026-05-20 17:03:13','2026-05-20 17:04:52'),(101,'App\\Models\\User',1,'auth_token','ad9d2dc21f6bced8d5d4cae7e47dedd1c8b41286044e1d0b50f3f6c7892d430e','[\"*\"]','2026-05-20 17:33:43',NULL,'2026-05-20 17:29:43','2026-05-20 17:33:43'),(102,'App\\Models\\User',3,'auth_token','b4603c39342f39001e6a5c72c70ad0c6617781bde715fa8644abe450fb5d9c00','[\"*\"]','2026-05-20 17:36:38',NULL,'2026-05-20 17:34:25','2026-05-20 17:36:38'),(103,'App\\Models\\User',2,'auth_token','38a6d81a7868afe75e8eb265cf6f918cb9f860f9243641964c6e7b33a6d410ae','[\"*\"]','2026-05-20 17:36:56',NULL,'2026-05-20 17:36:55','2026-05-20 17:36:56'),(104,'App\\Models\\User',3,'auth_token','31cbc54868c5d9744869a81a719224fdf95e23a86335c08dae4d9d25e0bf68f3','[\"*\"]','2026-05-20 17:39:07',NULL,'2026-05-20 17:37:57','2026-05-20 17:39:07'),(105,'App\\Models\\User',1,'auth_token','a0cbdbd4666eb74199611dd10e8b570370e74f4a4442a005db22d929fe94fc78','[\"*\"]','2026-05-20 17:39:40',NULL,'2026-05-20 17:39:31','2026-05-20 17:39:40'),(106,'App\\Models\\User',2,'auth_token','cd27b51a4f536576997ffa58ff9ac82c9aa1d09fee4f7e963417e76f36e6a60d','[\"*\"]','2026-05-20 17:41:40',NULL,'2026-05-20 17:40:03','2026-05-20 17:41:40'),(107,'App\\Models\\User',3,'auth_token','0a57111653f1e60d56e8a3d0b7988e99104aa12464d1fc07295cc21bdfd81b70','[\"*\"]','2026-05-20 17:43:44',NULL,'2026-05-20 17:42:45','2026-05-20 17:43:44'),(108,'App\\Models\\User',1,'auth_token','d7f0798a187bdfe32000e1623ab5cdc00cd2edd52c56a4089d6352bab5721822','[\"*\"]','2026-05-20 17:44:37',NULL,'2026-05-20 17:44:05','2026-05-20 17:44:37'),(109,'App\\Models\\User',3,'auth_token','c4a6aa0a736bb90d7283ba8026aacdf8a791e2bf841918e4689683f1c7e6f987','[\"*\"]','2026-05-20 17:46:04',NULL,'2026-05-20 17:45:12','2026-05-20 17:46:04'),(110,'App\\Models\\User',3,'auth_token','e1cb0d3a418976403cd26931898383fee756eaf482e00e87a69b0247236b84ff','[\"*\"]','2026-05-20 17:52:02',NULL,'2026-05-20 17:51:31','2026-05-20 17:52:02'),(111,'App\\Models\\User',1,'auth_token','7731862737e1220eef330a3ff5820a38680b12fa7d00fb270ba0681891b4379f','[\"*\"]','2026-05-20 17:54:07',NULL,'2026-05-20 17:52:37','2026-05-20 17:54:07'),(112,'App\\Models\\User',3,'auth_token','0b37e2eab8fdf557759c4b7d307c484d07c1d9470e67ace6bbe8327398961865','[\"*\"]','2026-05-21 09:16:51',NULL,'2026-05-21 09:16:23','2026-05-21 09:16:51'),(113,'App\\Models\\User',1,'auth_token','8f8cd2a40c2fa0e858085176c3297083b2ea522a0a97b0632bdd263d068a9125','[\"*\"]','2026-05-21 09:18:41',NULL,'2026-05-21 09:17:14','2026-05-21 09:18:41'),(114,'App\\Models\\User',3,'auth_token','216245487ffb9498512b15dea48eca0481f4bce83888a7c6c99c7919ad739374','[\"*\"]','2026-05-21 09:21:00',NULL,'2026-05-21 09:19:13','2026-05-21 09:21:00'),(115,'App\\Models\\User',1,'auth_token','29230b56398ca55a5c2dbcbcfb79b8d887a82b4c1c836420a3e7d3f3764274f0','[\"*\"]','2026-05-21 10:01:55',NULL,'2026-05-21 10:00:41','2026-05-21 10:01:55'),(116,'App\\Models\\User',3,'auth_token','35d2de4884f2c56d959844498a024d6c119ed49af934ad1a1eef325519218090','[\"*\"]','2026-05-21 10:04:19',NULL,'2026-05-21 10:02:27','2026-05-21 10:04:19'),(117,'App\\Models\\User',3,'auth_token','044f002b6d18697037d2eac650dcf80586dca4b14edfa542c01dde892d65729f','[\"*\"]','2026-05-22 21:38:42',NULL,'2026-05-22 21:36:35','2026-05-22 21:38:42'),(118,'App\\Models\\User',3,'auth_token','919e530dbbd45d70e5922bc990b2593312c3aaf2b91647bda4c793dc8b8860db','[\"*\"]','2026-05-22 21:39:18',NULL,'2026-05-22 21:39:16','2026-05-22 21:39:18'),(119,'App\\Models\\User',3,'auth_token','dcbd7a893729ad5888c639106ccc37568273a1db37598086031b56d0bd3bead5','[\"*\"]','2026-06-05 18:38:18',NULL,'2026-06-05 18:37:38','2026-06-05 18:38:18'),(120,'App\\Models\\User',1,'auth_token','e76ecc00656705f483d5eb60422595e1cfbd8e0d25c55b5ba4d829edf4fbfec8','[\"*\"]','2026-06-05 18:40:26',NULL,'2026-06-05 18:38:50','2026-06-05 18:40:26'),(121,'App\\Models\\User',3,'auth_token','2ca1049d80faef77a616d7b8861e2b0c919ac55b741228b16fd9f2adb5008fd5','[\"*\"]','2026-06-05 18:46:35',NULL,'2026-06-05 18:41:15','2026-06-05 18:46:35'),(122,'App\\Models\\User',2,'auth_token','93d77eb23b4f84343c64bbb769d7b4715b6f5127e3319e553a0049e8a6c1dddc','[\"*\"]','2026-06-05 18:47:13',NULL,'2026-06-05 18:47:02','2026-06-05 18:47:13'),(123,'App\\Models\\User',1,'auth_token','25950fb000504c27915e27c0ad03eb4487962515e7ad6a3092bac4fc8906677a','[\"*\"]','2026-06-12 01:37:39',NULL,'2026-06-12 01:33:50','2026-06-12 01:37:39'),(124,'App\\Models\\User',3,'auth_token','12f08b432f90c70f4d3ce3929f10727158950c272ccaa66847c56e522982c631','[\"*\"]','2026-06-12 01:43:38',NULL,'2026-06-12 01:38:30','2026-06-12 01:43:38'),(125,'App\\Models\\User',2,'auth_token','b5cc77444f193432727ef777fc5a496fdcc1aea9ba56a219bb2d5a3df58f8aa5','[\"*\"]','2026-06-12 02:20:45',NULL,'2026-06-12 02:19:55','2026-06-12 02:20:45'),(126,'App\\Models\\User',3,'auth_token','112dfb01faf016525e29699e94e2708ff1120759a55125b828ad954d3dd3d38f','[\"*\"]','2026-06-12 02:22:25',NULL,'2026-06-12 02:21:38','2026-06-12 02:22:25'),(127,'App\\Models\\User',2,'auth_token','31ae029c6254fe67ccfbe2e67cc865095d84aa59d8495358c4ce965cb0f385bf','[\"*\"]','2026-06-12 02:24:24',NULL,'2026-06-12 02:22:54','2026-06-12 02:24:24'),(128,'App\\Models\\User',1,'auth_token','c8e34e252b57461eb548dcb9601461959adc0dfbc70e4c5e0d1b4980c86db3ee','[\"*\"]','2026-06-12 02:29:37',NULL,'2026-06-12 02:25:33','2026-06-12 02:29:37'),(129,'App\\Models\\User',1,'auth_token','c535116959189b049ba490adbc99a7f4341dd1c00098c4de5bc9b6628e5e2e40','[\"*\"]','2026-06-12 02:30:31',NULL,'2026-06-12 02:30:21','2026-06-12 02:30:31'),(130,'App\\Models\\User',3,'auth_token','c852af3347e0ceb4e18de57bed179e0dbb33dedb88e6de1ebc78661771f636a8','[\"*\"]','2026-06-12 02:39:58',NULL,'2026-06-12 02:39:52','2026-06-12 02:39:58'),(131,'App\\Models\\User',3,'auth_token','bff1f13000e7392ea1e3d02b972e43893f4ba601d5bf12a2e4b58f5c810afe54','[\"*\"]','2026-06-12 03:01:13',NULL,'2026-06-12 02:55:18','2026-06-12 03:01:13');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `voyage_id` bigint unsigned NOT NULL,
  `code_qr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('en_attente','confirmee','embarquee','annulee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservations_code_qr_unique` (`code_qr`),
  KEY `reservations_user_id_foreign` (`user_id`),
  KEY `reservations_voyage_id_foreign` (`voyage_id`),
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_voyage_id_foreign` FOREIGN KEY (`voyage_id`) REFERENCES `voyages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (11,3,12,'RES-JZMY30MX','embarquee','2026-05-20 17:34:40','2026-05-20 17:41:19'),(12,3,13,'RES-LJXT1IA2','annulee','2026-05-21 09:19:33','2026-06-05 18:45:38'),(13,3,13,'RES-XPREXCC2','annulee','2026-05-21 10:03:07','2026-06-05 18:45:31'),(14,3,15,'RES-NQ16HNTC','annulee','2026-06-05 18:41:32','2026-06-05 18:45:21'),(15,3,16,'RES-BGD0FXE4','embarquee','2026-06-12 01:39:06','2026-06-12 02:23:29');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signalements`
--

DROP TABLE IF EXISTS `signalements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `signalements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bagage_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lieu_dernier_vu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('ouvert','en_cours','resolu') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ouvert',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `signalements_bagage_id_foreign` (`bagage_id`),
  KEY `signalements_user_id_foreign` (`user_id`),
  CONSTRAINT `signalements_bagage_id_foreign` FOREIGN KEY (`bagage_id`) REFERENCES `bagages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `signalements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signalements`
--

LOCK TABLES `signalements` WRITE;
/*!40000 ALTER TABLE `signalements` DISABLE KEYS */;
INSERT INTO `signalements` VALUES (5,5,3,'Test','Aeroport','resolu','2026-05-20 17:43:34','2026-06-12 01:34:40');
/*!40000 ALTER TABLE `signalements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('passager','agent','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'passager',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','Super','admin@smarttransport.com','771000000','$2y$12$RO/xjYohSu7.6V7.6UcN6.AdMLhpmwwLyUzm0OREBxvB9fHtDNzj.','admin','2026-05-08 21:25:05','2026-05-08 21:25:05'),(2,'Diallo','Ibrahima','agent@smarttransport.com','772000000','$2y$12$8WsR9FQg2yh.9.RlgcfFN.7diOLJgjHWrhG70xHEG45pvMw5IJDs.','agent','2026-05-08 21:25:25','2026-05-08 21:25:25'),(3,'Ndiaye','Ndiaga','passager@smarttransport.com','773000000','$2y$12$2ATZ2Up2bALZQvnjX74cwemXi/H2O9OXO6C8B4iJzczUIpyY1ZFoi','passager','2026-05-08 21:25:43','2026-05-15 01:45:56'),(4,'SOW','Busquito','busq@gmail.com','77 120 20 20','$2y$12$bUqJnmAkmyNzHHCUIL6oEeyMPbBwpgkOOzgqZClB35HDkT95RnEJq','passager','2026-05-08 22:08:54','2026-05-08 22:08:54');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voyages`
--

DROP TABLE IF EXISTS `voyages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voyages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `origine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_depart` datetime NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `type_transport` enum('routier','ferroviaire','aerien') COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('planifie','embarquement','en_cours','arrive','annule') COLLATE utf8mb4_unicode_ci DEFAULT 'planifie',
  `capacite` int NOT NULL DEFAULT '50',
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  `devise` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'XOF',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voyages`
--

LOCK TABLES `voyages` WRITE;
/*!40000 ALTER TABLE `voyages` DISABLE KEYS */;
INSERT INTO `voyages` VALUES (12,'Senegal','Maroc','2026-05-21 18:00:00','2026-05-21 00:00:00','aerien','arrive',250,0.00,'XOF','2026-05-20 17:33:42','2026-05-21 09:18:40'),(13,'Dakar','Touba','2026-05-21 15:00:00','2026-05-21 00:00:00','routier','arrive',50,5000.00,'XOF','2026-05-21 09:18:32','2026-06-05 18:39:15'),(14,'St Louis','Tamba','2026-05-21 18:00:00','2026-05-22 00:00:00','routier','arrive',50,15000.00,'XOF','2026-05-21 10:01:49','2026-06-05 18:39:04'),(15,'Mbour','Thiadiaye','2026-06-06 15:00:00','2026-06-06 00:00:00','routier','arrive',50,3000.00,'XOF','2026-06-05 18:40:25','2026-06-12 02:25:59'),(16,'Dakar','Canada','2026-06-13 06:00:00','2026-06-14 00:00:00','aerien','embarquement',50,400000.00,'XOF','2026-06-12 01:37:38','2026-06-12 02:26:02');
/*!40000 ALTER TABLE `voyages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-15 12:15:29
