-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: latsar
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_ip` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_device` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=496 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'admin','$2y$10$Lk06jPB9QlB80wdDjuI8G.30duKU6B6u1TBY3EcUrpwkY5y6CKUnO','Subbag PPK','profile/XLthFu6dDaHhQEdXOPyr1G8yzA3VUrdFxQCYAw91.png','administrator',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-02-02 21:03:16','2026-03-30 02:50:25'),(132,'kiki2','$2y$12$KtaCVaLNIx0u0ATqXlu6JO/d067wSul4K3AO3YTpkv3oq9H3NP0Zu','KIKI 2',NULL,'Koordinator',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-01-21 04:52:09','2026-03-06 01:40:51'),(488,'randy','$2y$12$UjnECztw2G8zkEnyO2np4unP2C3t8GZdbOuKoGEDj.gyTKlBr3I2y','Randy Pratama',NULL,'auditor',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-23 23:41:00','2026-03-01 20:07:46'),(489,'jose','$2y$12$f.dLAmU0/TC4g1EMmH6Vv.Uuke3KghKJ758Qtsh.fPSfFLMCCGC7e','Jose Keli',NULL,'auditor',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-01 20:56:16','2026-03-01 21:58:45'),(490,'dela','$2y$12$F6lYnL9OkxP21WTPM302LeFgIEPj3adSMWAJVczDXFEqbWVxw.286','Nyayu Dela',NULL,'koordinator',1,'10.200.17.129','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-01 20:57:40','2026-03-04 02:21:23'),(491,'nadine','$2y$12$jKiLD5kevr5wdDY1cWVrIea/wZeUmrUFPoz6VVO42UJDUnYnkH1.K','Nadine Kevanie',NULL,'koordinator',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-01 21:00:02','2026-03-03 02:41:22'),(492,'thoni','$2y$12$YaYyeKDXvXEYxpi/.E1dA.vrU2VtBuxzyccG2WVasmQVTWGJpwLqK','Sulthoni Salman',NULL,'auditor',1,NULL,NULL,'2026-03-01 21:00:27','2026-03-08 20:37:29'),(493,'irfan','$2y$12$qORWBYtLA0OdiN3Nixov/unMnFVRTAc585ufiqymxwLPd3fryPe/q','Irfan Nur',NULL,'auditor',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-01 21:09:53','2026-03-27 02:29:26'),(494,'bayu','$2y$12$YkPEDpPNzfM1FtkUKZMPg.j35gqY2w5E2UpfNeMD9nTjTvpUEhPDe','Bayu Mega',NULL,'auditor',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-01 21:10:15','2026-03-04 02:21:54'),(495,'testqa','$2y$12$yE2y1Uto4Fgb0BstPFgUmuzznp8kSlr9Xy90Xy85IeJw.aLn/5/l2','Quality Assurance',NULL,'qa',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 00:04:14','2026-03-27 00:04:53');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
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
-- Table structure for table `dms_documents`
--

DROP TABLE IF EXISTS `dms_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dms_documents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `year` int NOT NULL,
  `type` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `doc_no` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `tag` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Aktif','Arsip') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Aktif',
  `uploader` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_by` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_doc_no` (`doc_no`),
  KEY `idx_year` (`year`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dms_documents`
--

LOCK TABLES `dms_documents` WRITE;
/*!40000 ALTER TABLE `dms_documents` DISABLE KEYS */;
INSERT INTO `dms_documents` VALUES (1,2026,'Manajemen Pengawasan','e1-0021.PA.01','ST XXX',NULL,'Surat Tugas','Aktif','Admin',NULL,'2026-01-22 07:44:44','2026-02-22 19:50:35',NULL),(2,2026,'Sumber Daya Manusia','e1-0021.PA.02','AMSdk','lkasdl','Dokumen SDM','Aktif','Admin',NULL,'2026-01-22 07:45:23','2026-02-22 19:50:35',NULL),(3,2026,'Keuangan','e1-1231.PA.01','apmqwd','pmwqdw','Dokumen Keuangan','Aktif','Admin',NULL,'2026-01-22 07:46:42','2026-02-22 19:50:35',NULL),(4,2026,'Pemanfaatan Sistem Informasi (SI)','e1-2132.PA.01','asda;ksk','mdlkqlkdw','Dokumen Sistem Informasi (SI)','Aktif','Admin',NULL,'2026-01-22 07:47:17','2026-02-22 19:50:35',NULL),(5,2026,'Manajemen Pengawasan','e-2281/PA.01.01','ASKDL','asdmasd','Surat Tugas','Aktif','Admin','Admin SIKAP','2026-01-22 09:24:45','2026-02-13 03:00:07',NULL),(6,2026,'Manajemen Pengawasan','e-0001/PA.01.01','LKIP',NULL,'Surat Tugas','Aktif','Admin','Subbag PPK','2026-01-23 08:17:33','2026-03-02 21:52:13',NULL),(7,2026,'Manajemen Pengawasan','e-9928/PA.01.01','LKPD',NULL,'Surat Tugas','Aktif','Admin','Admin','2026-01-23 08:17:33','2026-02-22 19:50:35',NULL),(8,2026,'Manajemen Pengawasan','e-0019/PA.01.02','nasdklad',NULL,'Surat Tugas','Aktif','Admin',NULL,'2026-01-23 08:17:33','2026-02-22 19:50:35',NULL),(9,2026,'Manajemen Pengawasan','e-0001/PA.01.03','ST 1','TEST','Surat Tugas','Aktif','Admin','Admin','2026-01-23 08:33:58','2026-02-22 19:50:35',NULL),(10,2026,'Manajemen Pengawasan','e-0001/PA.01.10','SADS',NULL,'Surat Tugas','Aktif','Admin SIKAP','Admin SIKAP','2026-02-09 00:43:03','2026-02-09 00:43:03',NULL),(11,2026,'Manajemen Pengawasan','e-0001/PA.01.02','ST X 1',NULL,'Surat Tugas','Aktif','Admin SIKAP','Admin SIKAP','2026-02-09 02:25:03','2026-02-09 02:29:43',NULL),(12,2026,'Manajemen Pengawasan','e-0001/PA.01.04','ST X 2',NULL,'Surat Tugas','Aktif','Admin SIKAP','Admin SIKAP','2026-02-09 02:25:03','2026-02-09 02:25:03',NULL),(13,2026,'Manajemen Pengawasan','e-0001/PA.01.05','ST X 3',NULL,'Surat Tugas','Aktif','Admin SIKAP','Admin SIKAP','2026-02-09 02:25:03','2026-02-09 02:25:03',NULL),(14,2026,'Manajemen Pengawasan','e-0001/PA.01.20','Surat Tugas Kiki',NULL,'Surat Tugas','Aktif','Admin SIKAP','Admin SIKAP','2026-02-09 02:39:32','2026-02-13 02:56:47',NULL),(15,2026,'Manajemen Pengawasan','e-0001/PA.02.03','LHP Fraud 1',NULL,'Laporan Hasil Pengawasan (LHP)','Aktif','Admin SIKAP','Admin SIKAP','2026-02-22 19:52:43','2026-03-03 02:39:52',NULL);
/*!40000 ALTER TABLE `dms_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dms_files`
--

DROP TABLE IF EXISTS `dms_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dms_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `document_id` int unsigned NOT NULL,
  `doc_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `doc_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `storage_driver` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'public',
  `file_size` int unsigned NOT NULL,
  `size_bytes` bigint unsigned DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mime_type` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_document_id` (`document_id`),
  CONSTRAINT `fk_dms_files_document` FOREIGN KEY (`document_id`) REFERENCES `dms_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dms_files`
--

LOCK TABLES `dms_files` WRITE;
/*!40000 ALTER TABLE `dms_files` DISABLE KEYS */;
INSERT INTO `dms_files` VALUES (1,1,NULL,NULL,'18 PA.02.01 III.2026 Und Rapat Tim Reviu LKPD TA 2025.pdf','/latsar/uploads/dms/e1-0021-pa-01_20260122084444_1_0e78c85c.pdf','public',177610,NULL,'2026-02-05 09:37:08','2026-02-05 09:37:08','application/pdf','2026-01-22 07:44:44'),(2,2,NULL,NULL,'TW IV   KOMUNITAS PEMBELAJAR TW IV (1).pdf','/latsar/uploads/dms/e1-0021-pa-02_20260122084523_1_c775270c.pdf','public',3585808,NULL,'2026-02-05 09:37:08','2026-02-05 09:37:08','application/pdf','2026-01-22 07:45:23'),(3,3,NULL,NULL,'18 PA.02.01 III.2026 Und Rapat Tim Reviu LKPD TA 2025.pdf','/latsar/uploads/dms/e1-1231-pa-01_20260122084642_1_3f35667d.pdf','public',177610,NULL,'2026-02-05 09:37:08','2026-02-05 09:37:08','application/pdf','2026-01-22 07:46:42'),(4,4,NULL,NULL,'18 PA.02.01 III.2026 Und Rapat Tim Reviu LKPD TA 2025.pdf','/latsar/uploads/dms/e1-2132-pa-01_20260122084717_1_5476ae4f.pdf','public',177610,NULL,'2026-02-05 09:37:08','2026-02-05 09:37:08','application/pdf','2026-01-22 07:47:17'),(6,5,'e-2281/PA.01.01','ASKDL','18 PA.02.01 III.2026 Und Rapat Tim Reviu LKPD TA 2025.pdf','/latsar/uploads/dms/e1-2281-pa-19_20260122102445_1_4bd39800.pdf','public',177610,NULL,'2026-02-05 09:37:08','2026-02-05 09:37:08','application/pdf','2026-01-22 09:24:45'),(7,6,'e-0001/PA.01.01','Laporan Keuangan Inspektorat','18 PA.02.01 III.2026 Und Rapat Tim Reviu LKPD TA 2025.pdf','/latsar/uploads/dms/e-2901-pa-01-02_20260123091733_1_f67ac40e.pdf','public',177610,NULL,'2026-02-05 09:37:08','2026-03-02 21:52:13','application/pdf','2026-01-23 08:17:33'),(8,7,'e-9928/PA.01.01','LKPD','18 PA.02.01 III.2026 Und Rapat Tim Reviu LKPD TA 2025.pdf','/latsar/uploads/dms/e-9928-pa-01-01_20260123091733_2_8d551bb2.pdf','public',177610,NULL,'2026-02-05 09:37:08','2026-02-05 09:37:08','application/pdf','2026-01-23 08:17:33'),(9,8,NULL,NULL,'18 PA.02.01 III.2026 Und Rapat Tim Reviu LKPD TA 2025.pdf','/latsar/uploads/dms/e-0019-pa-01-02_20260123091733_3_f3dd67f2.pdf','public',177610,NULL,'2026-02-05 09:37:08','2026-02-05 09:37:08','application/pdf','2026-01-23 08:17:33'),(10,9,'e-0001/PA.01.03','ST 1','18 PA.02.01 III.2026 Und Rapat Tim Reviu LKPD TA 2025.pdf','/latsar/uploads/dms/e-0001-pa-01-02_20260123093358_1_e64dbee0.pdf','public',177610,NULL,'2026-02-05 09:37:08','2026-02-08 23:28:11','application/pdf','2026-01-23 08:33:58'),(13,9,'e-0001/PA.01.03','ST 1','Agenda Harian Sementara Inspektorat DKI Jakarta 20 Jan 26.pdf','/latsar/uploads/dms/e-0001-pa-01-04_20260130091718_2_17084acb.pdf','public',434339,NULL,'2026-02-05 09:37:08','2026-02-08 23:28:11','application/pdf','2026-01-30 08:17:18'),(14,10,'e-0001/PA.01.10','SADS','2026 - 0209 DL.01.02 - UNDANGAN RAPAT KOORDINASI PERSIAPAN PELATIHAN DASAR CPNS GOL. III AKT. 186-208 & GOL. II AKT. 41-80 TAHUN 2026 (1) (1).pdf','dms/prF9dsJIrQN0lHVz7JAAXaRENJ1wKBbd2gAMWE1j.pdf','public',1186469,1186469,'2026-02-09 00:43:03','2026-02-09 00:43:03','application/pdf','2026-02-09 00:43:03'),(15,10,'e-0001/PA.01.10','SADS','2026 - 0209 DL.01.02 - UNDANGAN RAPAT KOORDINASI PERSIAPAN PELATIHAN DASAR CPNS GOL. III AKT. 186-208 & GOL. II AKT. 41-80 TAHUN 2026 (1) (1).pdf','dms/nzwxtVa5ta1QR5qUfjyTZhZinHemizabvr5BekLK.pdf','public',1186469,1186469,'2026-02-09 00:43:03','2026-02-09 00:43:03','application/pdf','2026-02-09 00:43:03'),(16,10,'e-0001/PA.01.10','SADS','2026 - 0209 DL.01.02 - UNDANGAN RAPAT KOORDINASI PERSIAPAN PELATIHAN DASAR CPNS GOL. III AKT. 186-208 & GOL. II AKT. 41-80 TAHUN 2026 (1) (1).pdf','dms/SQCy7JsGfHfHb4BrCVBt3SpmMIi9icJY7R51j3Kc.pdf','public',1186469,1186469,'2026-02-09 00:43:03','2026-02-09 00:43:03','application/pdf','2026-02-09 00:43:03'),(17,11,'e-0001/PA.01.02','ST X 1','2026 - 0209 DL.01.02 - UNDANGAN RAPAT KOORDINASI PERSIAPAN PELATIHAN DASAR CPNS GOL. III AKT. 186-208 & GOL. II AKT. 41-80 TAHUN 2026 (1) (1).pdf','dms/Yos4rG3IM3S0YVZHW3Sy0dECYTjzZKIJw92YDq4I.pdf','public',1186469,1186469,'2026-02-09 02:25:03','2026-02-09 02:25:03','application/pdf','2026-02-09 02:25:03'),(18,12,'e-0001/PA.01.04','ST X 2','2026 - 0209 DL.01.02 - UNDANGAN RAPAT KOORDINASI PERSIAPAN PELATIHAN DASAR CPNS GOL. III AKT. 186-208 & GOL. II AKT. 41-80 TAHUN 2026 (1) (1).pdf','dms/jsk37Yigm8nEj3poJFDkxJHbTfpA4QPjJKMzMQ59.pdf','public',1186469,1186469,'2026-02-09 02:25:03','2026-02-09 02:25:03','application/pdf','2026-02-09 02:25:03'),(19,13,'e-0001/PA.01.05','ST X 3','2026 - 0209 DL.01.02 - UNDANGAN RAPAT KOORDINASI PERSIAPAN PELATIHAN DASAR CPNS GOL. III AKT. 186-208 & GOL. II AKT. 41-80 TAHUN 2026 (1) (1).pdf','dms/XQuhCRJig5I8sVXqr7Yt2HjJ1XBr2DMsA5y4Nlwl.pdf','public',1186469,1186469,'2026-02-09 02:25:03','2026-02-09 02:25:03','application/pdf','2026-02-09 02:25:03'),(23,14,'e-0001/PA.01.20','Test 1','e-0053 PA 02 02 SPT melaksanakan tugas sebagai bendahara pengeluaran,bendahara pengeluaran pembantu serta pengurus barang ,pengurus barang pembantu.s.pdf','dms/suZhbzFdWlJ7hwAHvPDCj7fOqlxaPIH3xokfLkwS.pdf','public',1116519,1116519,'2026-02-13 02:22:29','2026-02-13 02:46:29','application/pdf','2026-02-13 02:22:29'),(24,14,'e-0001/PA.01.21','Test 2','01 BPK Perwakilan, Pemberitahuan dan Permintaan data dan Dokumen Pemeriksaan atas LKPD 2025_Inspektorat.pdf','dms/RC5e5dcplFsZDiFcFEyp5XaYAZd3dMdmmFomkFIq.pdf','public',755721,755721,'2026-02-13 02:22:29','2026-02-13 02:36:21','application/pdf','2026-02-13 02:22:29'),(26,14,'e-0001/PA.01.23','Test 3','SURAT PANGGILAN LATSAR GOL  II TAHUN 2026.pdf','dms/vATakpO6dehgLjoUhYNla2CXCICosqwTx5j0Ulvc.pdf','public',1983319,1983319,'2026-02-13 02:50:11','2026-02-13 02:50:11','application/pdf','2026-02-13 02:50:11'),(27,15,'e-0001/PA.02.03','Fraud 1','Und. Rapat Pembahasan Capaian Indikator Kinerja Kunci dalam Laporan Penyelenggaraan Pemerintahan Daerah Provinsi DKI Jakarta Tahun 2025.pdf','dms/akOIzPa3Zsrvi4JqIj8V52IQskTfMqaRrlphNINB.pdf','public',522947,522947,'2026-02-22 19:52:44','2026-02-22 19:52:44','application/pdf','2026-02-22 19:52:44'),(28,15,'e-0001/PA.01.08','Fraud 2','Und. Rapat Pembahasan Capaian Indikator Kinerja Kunci dalam Laporan Penyelenggaraan Pemerintahan Daerah Provinsi DKI Jakarta Tahun 2025.pdf','dms/Q53K5qCZlkkM4KxFxmMmaCi8YOppIDJvhrcjIMo3.pdf','public',522947,522947,'2026-02-22 19:52:44','2026-02-22 19:52:44','application/pdf','2026-02-22 19:52:44');
/*!40000 ALTER TABLE `dms_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element1_jasa_konsultansi`
--

DROP TABLE IF EXISTS `element1_jasa_konsultansi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element1_jasa_konsultansi` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element1_jasa_konsultansi`
--

LOCK TABLES `element1_jasa_konsultansi` WRITE;
/*!40000 ALTER TABLE `element1_jasa_konsultansi` DISABLE KEYS */;
INSERT INTO `element1_jasa_konsultansi` VALUES (1,'Tujuan dan Ruang Lingkup','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Peran, tanggung jawab dan ekspektasi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Proses Konsultansi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(4,'Kualitas Hasil Konsultansi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element1_jasa_konsultansi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element1_jasa_konsultansi_edit_logs`
--

DROP TABLE IF EXISTS `element1_jasa_konsultansi_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element1_jasa_konsultansi_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element1_jasa_konsultansi_edit_logs_row_id_index` (`row_id`),
  KEY `element1_jasa_konsultansi_edit_logs_username_index` (`username`),
  KEY `element1_jasa_konsultansi_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element1_jasa_konsultansi_edit_logs`
--

LOCK TABLES `element1_jasa_konsultansi_edit_logs` WRITE;
/*!40000 ALTER TABLE `element1_jasa_konsultansi_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element1_jasa_konsultansi_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element1_kegiatan_asurans`
--

DROP TABLE IF EXISTS `element1_kegiatan_asurans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element1_kegiatan_asurans` (
  `id` int NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_general_ci,
  `analisis_nilai` text COLLATE utf8mb4_general_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_general_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_general_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_general_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_general_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_general_ci,
  `evidence` text COLLATE utf8mb4_general_ci,
  `verified` tinyint(1) DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_general_ci,
  `level_validation_state` longtext COLLATE utf8mb4_general_ci,
  `verify_note` text COLLATE utf8mb4_general_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_general_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_general_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element1_kegiatan_asurans`
--

LOCK TABLES `element1_kegiatan_asurans` WRITE;
/*!40000 ALTER TABLE `element1_kegiatan_asurans` DISABLE KEYS */;
INSERT INTO `element1_kegiatan_asurans` VALUES (1,'Ruang Lingkup dan Fokus','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Analisis dan Atribut Temuan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Kualitas Opini/Simpulan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(4,'Kualitas Rekomendasi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element1_kegiatan_asurans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element1_kegiatan_asurans_edit_logs`
--

DROP TABLE IF EXISTS `element1_kegiatan_asurans_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element1_kegiatan_asurans_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element1_kegiatan_asurans_edit_logs_row_id_index` (`row_id`),
  KEY `element1_kegiatan_asurans_edit_logs_username_index` (`username`),
  KEY `element1_kegiatan_asurans_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element1_kegiatan_asurans_edit_logs`
--

LOCK TABLES `element1_kegiatan_asurans_edit_logs` WRITE;
/*!40000 ALTER TABLE `element1_kegiatan_asurans_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element1_kegiatan_asurans_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_komunikasi_hasil`
--

DROP TABLE IF EXISTS `element2_komunikasi_hasil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_komunikasi_hasil` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_komunikasi_hasil`
--

LOCK TABLES `element2_komunikasi_hasil` WRITE;
/*!40000 ALTER TABLE `element2_komunikasi_hasil` DISABLE KEYS */;
INSERT INTO `element2_komunikasi_hasil` VALUES (1,'Kelengkapan Atribut Laporan dan Ketepatan Waktu Penyampaian','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element2_komunikasi_hasil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_komunikasi_hasil_edit_logs`
--

DROP TABLE IF EXISTS `element2_komunikasi_hasil_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_komunikasi_hasil_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element2_komunikasi_hasil_edit_logs_row_id_index` (`row_id`),
  KEY `element2_komunikasi_hasil_edit_logs_username_index` (`username`),
  KEY `element2_komunikasi_hasil_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_komunikasi_hasil_edit_logs`
--

LOCK TABLES `element2_komunikasi_hasil_edit_logs` WRITE;
/*!40000 ALTER TABLE `element2_komunikasi_hasil_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element2_komunikasi_hasil_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_pelaksanaan_penugasan`
--

DROP TABLE IF EXISTS `element2_pelaksanaan_penugasan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_pelaksanaan_penugasan` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_pelaksanaan_penugasan`
--

LOCK TABLES `element2_pelaksanaan_penugasan` WRITE;
/*!40000 ALTER TABLE `element2_pelaksanaan_penugasan` DISABLE KEYS */;
INSERT INTO `element2_pelaksanaan_penugasan` VALUES (1,'Identifikasi dan Pengumpulan data/informasi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Pelaksanaan Pedoman/Program Kerja','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Penyusunan Opini, Simpulan, dan Rekomendasi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element2_pelaksanaan_penugasan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_pelaksanaan_penugasan_edit_logs`
--

DROP TABLE IF EXISTS `element2_pelaksanaan_penugasan_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_pelaksanaan_penugasan_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element2_pelaksanaan_penugasan_edit_logs_row_id_index` (`row_id`),
  KEY `element2_pelaksanaan_penugasan_edit_logs_username_index` (`username`),
  KEY `element2_pelaksanaan_penugasan_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_pelaksanaan_penugasan_edit_logs`
--

LOCK TABLES `element2_pelaksanaan_penugasan_edit_logs` WRITE;
/*!40000 ALTER TABLE `element2_pelaksanaan_penugasan_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element2_pelaksanaan_penugasan_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_pemantauan_tindak_lanjut`
--

DROP TABLE IF EXISTS `element2_pemantauan_tindak_lanjut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_pemantauan_tindak_lanjut` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_pemantauan_tindak_lanjut`
--

LOCK TABLES `element2_pemantauan_tindak_lanjut` WRITE;
/*!40000 ALTER TABLE `element2_pemantauan_tindak_lanjut` DISABLE KEYS */;
INSERT INTO `element2_pemantauan_tindak_lanjut` VALUES (1,'Pelaksanaan Pemantauan Tindak Lanjut Rekomendasi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Evaluasi Efektivitas Tindak Lanjut Rekomendasi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Konfirmasi kepada Manajemen Klien dan/atau Entitas Mitra','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element2_pemantauan_tindak_lanjut` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_pemantauan_tindak_lanjut_edit_logs`
--

DROP TABLE IF EXISTS `element2_pemantauan_tindak_lanjut_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_pemantauan_tindak_lanjut_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element2_pemantauan_tindak_lanjut_edit_logs_row_id_index` (`row_id`),
  KEY `element2_pemantauan_tindak_lanjut_edit_logs_username_index` (`username`),
  KEY `element2_pemantauan_tindak_lanjut_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_pemantauan_tindak_lanjut_edit_logs`
--

LOCK TABLES `element2_pemantauan_tindak_lanjut_edit_logs` WRITE;
/*!40000 ALTER TABLE `element2_pemantauan_tindak_lanjut_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element2_pemantauan_tindak_lanjut_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_pengembangan_informasi`
--

DROP TABLE IF EXISTS `element2_pengembangan_informasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_pengembangan_informasi` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_pengembangan_informasi`
--

LOCK TABLES `element2_pengembangan_informasi` WRITE;
/*!40000 ALTER TABLE `element2_pengembangan_informasi` DISABLE KEYS */;
INSERT INTO `element2_pengembangan_informasi` VALUES (1,'Pelaksanaan Pengembangan Informasi Awal (Pra-Perencanaan)','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Desain Penugasan Pengawasan (DPP)','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element2_pengembangan_informasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_pengembangan_informasi_edit_logs`
--

DROP TABLE IF EXISTS `element2_pengembangan_informasi_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_pengembangan_informasi_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element2_pengembangan_informasi_edit_logs_row_id_index` (`row_id`),
  KEY `element2_pengembangan_informasi_edit_logs_username_index` (`username`),
  KEY `element2_pengembangan_informasi_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_pengembangan_informasi_edit_logs`
--

LOCK TABLES `element2_pengembangan_informasi_edit_logs` WRITE;
/*!40000 ALTER TABLE `element2_pengembangan_informasi_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element2_pengembangan_informasi_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_pengendalian_kualitas`
--

DROP TABLE IF EXISTS `element2_pengendalian_kualitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_pengendalian_kualitas` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_pengendalian_kualitas`
--

LOCK TABLES `element2_pengendalian_kualitas` WRITE;
/*!40000 ALTER TABLE `element2_pengendalian_kualitas` DISABLE KEYS */;
INSERT INTO `element2_pengendalian_kualitas` VALUES (1,'Melaksanakan Reviu Berjenjang pada Setiap Tahapan Penugasan Pengawasan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Melaksanakan Penjaminan Kualitas Internal','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Melaksanakan Telaah Sejawat Ekstern','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element2_pengendalian_kualitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_pengendalian_kualitas_edit_logs`
--

DROP TABLE IF EXISTS `element2_pengendalian_kualitas_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_pengendalian_kualitas_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element2_pengendalian_kualitas_edit_logs_row_id_index` (`row_id`),
  KEY `element2_pengendalian_kualitas_edit_logs_username_index` (`username`),
  KEY `element2_pengendalian_kualitas_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_pengendalian_kualitas_edit_logs`
--

LOCK TABLES `element2_pengendalian_kualitas_edit_logs` WRITE;
/*!40000 ALTER TABLE `element2_pengendalian_kualitas_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element2_pengendalian_kualitas_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_perencanaan_penugasan`
--

DROP TABLE IF EXISTS `element2_perencanaan_penugasan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_perencanaan_penugasan` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_perencanaan_penugasan`
--

LOCK TABLES `element2_perencanaan_penugasan` WRITE;
/*!40000 ALTER TABLE `element2_perencanaan_penugasan` DISABLE KEYS */;
INSERT INTO `element2_perencanaan_penugasan` VALUES (1,'Penyusunan Dokumen Perencanaan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Penyusunan Program Kerja','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element2_perencanaan_penugasan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element2_perencanaan_penugasan_edit_logs`
--

DROP TABLE IF EXISTS `element2_perencanaan_penugasan_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element2_perencanaan_penugasan_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element2_perencanaan_penugasan_edit_logs_row_id_index` (`row_id`),
  KEY `element2_perencanaan_penugasan_edit_logs_username_index` (`username`),
  KEY `element2_perencanaan_penugasan_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element2_perencanaan_penugasan_edit_logs`
--

LOCK TABLES `element2_perencanaan_penugasan_edit_logs` WRITE;
/*!40000 ALTER TABLE `element2_perencanaan_penugasan_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element2_perencanaan_penugasan_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element3_pelaporan_manajemen_kld`
--

DROP TABLE IF EXISTS `element3_pelaporan_manajemen_kld`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element3_pelaporan_manajemen_kld` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element3_pelaporan_manajemen_kld`
--

LOCK TABLES `element3_pelaporan_manajemen_kld` WRITE;
/*!40000 ALTER TABLE `element3_pelaporan_manajemen_kld` DISABLE KEYS */;
INSERT INTO `element3_pelaporan_manajemen_kld` VALUES (1,'Kualitas Penyajian Laporan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Kualitas Rekomendasi dan Nilai Tambah Strategis','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Pemanfaatan oleh Manajemen','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element3_pelaporan_manajemen_kld` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element3_pelaporan_manajemen_kld_edit_logs`
--

DROP TABLE IF EXISTS `element3_pelaporan_manajemen_kld_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element3_pelaporan_manajemen_kld_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element3_pelaporan_manajemen_kld_edit_logs_row_id_index` (`row_id`),
  KEY `element3_pelaporan_manajemen_kld_edit_logs_username_index` (`username`),
  KEY `element3_pelaporan_manajemen_kld_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element3_pelaporan_manajemen_kld_edit_logs`
--

LOCK TABLES `element3_pelaporan_manajemen_kld_edit_logs` WRITE;
/*!40000 ALTER TABLE `element3_pelaporan_manajemen_kld_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element3_pelaporan_manajemen_kld_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element3_perencanaan_pengawasan`
--

DROP TABLE IF EXISTS `element3_perencanaan_pengawasan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element3_perencanaan_pengawasan` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element3_perencanaan_pengawasan`
--

LOCK TABLES `element3_perencanaan_pengawasan` WRITE;
/*!40000 ALTER TABLE `element3_perencanaan_pengawasan` DISABLE KEYS */;
INSERT INTO `element3_perencanaan_pengawasan` VALUES (1,'Struktur Perencanaan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Fokus dan Sasaran Pengawasan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Adaptif','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(4,'Keterlibatan Manajemen','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element3_perencanaan_pengawasan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element3_perencanaan_pengawasan_edit_logs`
--

DROP TABLE IF EXISTS `element3_perencanaan_pengawasan_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element3_perencanaan_pengawasan_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element3_perencanaan_pengawasan_edit_logs_row_id_index` (`row_id`),
  KEY `element3_perencanaan_pengawasan_edit_logs_username_index` (`username`),
  KEY `element3_perencanaan_pengawasan_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element3_perencanaan_pengawasan_edit_logs`
--

LOCK TABLES `element3_perencanaan_pengawasan_edit_logs` WRITE;
/*!40000 ALTER TABLE `element3_perencanaan_pengawasan_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element3_perencanaan_pengawasan_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_dukungan_tik`
--

DROP TABLE IF EXISTS `element4_dukungan_tik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_dukungan_tik` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_dukungan_tik`
--

LOCK TABLES `element4_dukungan_tik` WRITE;
/*!40000 ALTER TABLE `element4_dukungan_tik` DISABLE KEYS */;
INSERT INTO `element4_dukungan_tik` VALUES (1,'Integrasi TI untuk Pengawasan Intern','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Pelatihan Pengguna','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Pengembangan dan Pengadaan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(4,'Pemanfaatan TI untuk Fungsi Manajerial Pengawasan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element4_dukungan_tik` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_dukungan_tik_edit_logs`
--

DROP TABLE IF EXISTS `element4_dukungan_tik_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_dukungan_tik_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element4_dukungan_tik_edit_logs_row_id_index` (`row_id`),
  KEY `element4_dukungan_tik_edit_logs_username_index` (`username`),
  KEY `element4_dukungan_tik_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_dukungan_tik_edit_logs`
--

LOCK TABLES `element4_dukungan_tik_edit_logs` WRITE;
/*!40000 ALTER TABLE `element4_dukungan_tik_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element4_dukungan_tik_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_manajemen_kinerja`
--

DROP TABLE IF EXISTS `element4_manajemen_kinerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_manajemen_kinerja` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_manajemen_kinerja`
--

LOCK TABLES `element4_manajemen_kinerja` WRITE;
/*!40000 ALTER TABLE `element4_manajemen_kinerja` DISABLE KEYS */;
INSERT INTO `element4_manajemen_kinerja` VALUES (1,'Perencanaan Kinerja','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Pengorganisasian Kinerja','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Pengendalian Kinerja','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element4_manajemen_kinerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_manajemen_kinerja_edit_logs`
--

DROP TABLE IF EXISTS `element4_manajemen_kinerja_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_manajemen_kinerja_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element4_manajemen_kinerja_edit_logs_row_id_index` (`row_id`),
  KEY `element4_manajemen_kinerja_edit_logs_username_index` (`username`),
  KEY `element4_manajemen_kinerja_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_manajemen_kinerja_edit_logs`
--

LOCK TABLES `element4_manajemen_kinerja_edit_logs` WRITE;
/*!40000 ALTER TABLE `element4_manajemen_kinerja_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element4_manajemen_kinerja_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_mekanisme_pendanaan`
--

DROP TABLE IF EXISTS `element4_mekanisme_pendanaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_mekanisme_pendanaan` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_mekanisme_pendanaan`
--

LOCK TABLES `element4_mekanisme_pendanaan` WRITE;
/*!40000 ALTER TABLE `element4_mekanisme_pendanaan` DISABLE KEYS */;
INSERT INTO `element4_mekanisme_pendanaan` VALUES (1,'Perencanaan dan Kecukupan Anggaran','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Penggunaan dan Fleksibilitas Anggaran','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element4_mekanisme_pendanaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_mekanisme_pendanaan_edit_logs`
--

DROP TABLE IF EXISTS `element4_mekanisme_pendanaan_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_mekanisme_pendanaan_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element4_mekanisme_pendanaan_edit_logs_row_id_index` (`row_id`),
  KEY `element4_mekanisme_pendanaan_edit_logs_username_index` (`username`),
  KEY `element4_mekanisme_pendanaan_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_mekanisme_pendanaan_edit_logs`
--

LOCK TABLES `element4_mekanisme_pendanaan_edit_logs` WRITE;
/*!40000 ALTER TABLE `element4_mekanisme_pendanaan_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element4_mekanisme_pendanaan_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_pengembangan_sdm_profesional_apip`
--

DROP TABLE IF EXISTS `element4_pengembangan_sdm_profesional_apip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_pengembangan_sdm_profesional_apip` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_pengembangan_sdm_profesional_apip`
--

LOCK TABLES `element4_pengembangan_sdm_profesional_apip` WRITE;
/*!40000 ALTER TABLE `element4_pengembangan_sdm_profesional_apip` DISABLE KEYS */;
INSERT INTO `element4_pengembangan_sdm_profesional_apip` VALUES (1,'Rencana Pengembangan Kompetensi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Pelaksanaan Pengembangan Kompetensi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element4_pengembangan_sdm_profesional_apip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_pengembangan_sdm_profesional_apip_edit_logs`
--

DROP TABLE IF EXISTS `element4_pengembangan_sdm_profesional_apip_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_pengembangan_sdm_profesional_apip_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `e4_psdpa_el_row_idx` (`row_id`),
  KEY `e4_psdpa_el_user_idx` (`username`),
  KEY `e4_psdpa_el_created_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_pengembangan_sdm_profesional_apip_edit_logs`
--

LOCK TABLES `element4_pengembangan_sdm_profesional_apip_edit_logs` WRITE;
/*!40000 ALTER TABLE `element4_pengembangan_sdm_profesional_apip_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element4_pengembangan_sdm_profesional_apip_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_perencanaan_sdm_apip`
--

DROP TABLE IF EXISTS `element4_perencanaan_sdm_apip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_perencanaan_sdm_apip` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_perencanaan_sdm_apip`
--

LOCK TABLES `element4_perencanaan_sdm_apip` WRITE;
/*!40000 ALTER TABLE `element4_perencanaan_sdm_apip` DISABLE KEYS */;
INSERT INTO `element4_perencanaan_sdm_apip` VALUES (1,'Perencanaan Kebutuhan SDM','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Rekrutmen dan Distribusi SDM','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element4_perencanaan_sdm_apip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element4_perencanaan_sdm_apip_edit_logs`
--

DROP TABLE IF EXISTS `element4_perencanaan_sdm_apip_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element4_perencanaan_sdm_apip_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element4_perencanaan_sdm_apip_edit_logs_row_id_index` (`row_id`),
  KEY `element4_perencanaan_sdm_apip_edit_logs_username_index` (`username`),
  KEY `element4_perencanaan_sdm_apip_edit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element4_perencanaan_sdm_apip_edit_logs`
--

LOCK TABLES `element4_perencanaan_sdm_apip_edit_logs` WRITE;
/*!40000 ALTER TABLE `element4_perencanaan_sdm_apip_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element4_perencanaan_sdm_apip_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element5_akses_informasi_sumberdaya`
--

DROP TABLE IF EXISTS `element5_akses_informasi_sumberdaya`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element5_akses_informasi_sumberdaya` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element5_akses_informasi_sumberdaya`
--

LOCK TABLES `element5_akses_informasi_sumberdaya` WRITE;
/*!40000 ALTER TABLE `element5_akses_informasi_sumberdaya` DISABLE KEYS */;
INSERT INTO `element5_akses_informasi_sumberdaya` VALUES (1,'Akses Informasi dan Dukungan Pimpinan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Nilai Tambah terhadap Pengawasan Intern','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element5_akses_informasi_sumberdaya` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element5_akses_informasi_sumberdaya_edit_logs`
--

DROP TABLE IF EXISTS `element5_akses_informasi_sumberdaya_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element5_akses_informasi_sumberdaya_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `e5_ais_logs_row_id_idx` (`row_id`),
  KEY `e5_ais_logs_username_idx` (`username`),
  KEY `e5_ais_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element5_akses_informasi_sumberdaya_edit_logs`
--

LOCK TABLES `element5_akses_informasi_sumberdaya_edit_logs` WRITE;
/*!40000 ALTER TABLE `element5_akses_informasi_sumberdaya_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element5_akses_informasi_sumberdaya_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element5_hubungan_apip_manajemen`
--

DROP TABLE IF EXISTS `element5_hubungan_apip_manajemen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element5_hubungan_apip_manajemen` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element5_hubungan_apip_manajemen`
--

LOCK TABLES `element5_hubungan_apip_manajemen` WRITE;
/*!40000 ALTER TABLE `element5_hubungan_apip_manajemen` DISABLE KEYS */;
INSERT INTO `element5_hubungan_apip_manajemen` VALUES (1,'Pemantauan dan Pemberian Arahan atas Peningkatan Kapabilitas APIP','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Kualitas Komunikasi Internal','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Kualitas Komunikasi APIP dengan Manajemen','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element5_hubungan_apip_manajemen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element5_hubungan_apip_manajemen_edit_logs`
--

DROP TABLE IF EXISTS `element5_hubungan_apip_manajemen_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element5_hubungan_apip_manajemen_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `e5_ham_logs_row_id_idx` (`row_id`),
  KEY `e5_ham_logs_username_idx` (`username`),
  KEY `e5_ham_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element5_hubungan_apip_manajemen_edit_logs`
--

LOCK TABLES `element5_hubungan_apip_manajemen_edit_logs` WRITE;
/*!40000 ALTER TABLE `element5_hubungan_apip_manajemen_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element5_hubungan_apip_manajemen_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element5_koordinasi_pengawasan`
--

DROP TABLE IF EXISTS `element5_koordinasi_pengawasan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element5_koordinasi_pengawasan` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element5_koordinasi_pengawasan`
--

LOCK TABLES `element5_koordinasi_pengawasan` WRITE;
/*!40000 ALTER TABLE `element5_koordinasi_pengawasan` DISABLE KEYS */;
INSERT INTO `element5_koordinasi_pengawasan` VALUES (1,'Intensitas Koordinasi dan Pertukaran Data Informasi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Sinergi dalam Perencanaan','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element5_koordinasi_pengawasan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element5_koordinasi_pengawasan_edit_logs`
--

DROP TABLE IF EXISTS `element5_koordinasi_pengawasan_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element5_koordinasi_pengawasan_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `e5_kp_logs_row_id_idx` (`row_id`),
  KEY `e5_kp_logs_username_idx` (`username`),
  KEY `e5_kp_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element5_koordinasi_pengawasan_edit_logs`
--

LOCK TABLES `element5_koordinasi_pengawasan_edit_logs` WRITE;
/*!40000 ALTER TABLE `element5_koordinasi_pengawasan_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element5_koordinasi_pengawasan_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element5_pembangunan_budaya_integritas`
--

DROP TABLE IF EXISTS `element5_pembangunan_budaya_integritas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element5_pembangunan_budaya_integritas` (
  `id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `skor` decimal(10,2) DEFAULT NULL,
  `analisis_bukti` text COLLATE utf8mb4_unicode_ci,
  `analisis_nilai` text COLLATE utf8mb4_unicode_ci,
  `grad_l1_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l2_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l3_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l4_catatan` text COLLATE utf8mb4_unicode_ci,
  `grad_l5_catatan` text COLLATE utf8mb4_unicode_ci,
  `evidence` text COLLATE utf8mb4_unicode_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `dokumen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_file_ids` longtext COLLATE utf8mb4_unicode_ci,
  `level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  `verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_verified` tinyint(1) NOT NULL DEFAULT '0',
  `qa_verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_verified_at` timestamp NULL DEFAULT NULL,
  `qa_verify_note` text COLLATE utf8mb4_unicode_ci,
  `qa_follow_up_recommendation` text COLLATE utf8mb4_unicode_ci,
  `qa_level_validation_state` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element5_pembangunan_budaya_integritas`
--

LOCK TABLES `element5_pembangunan_budaya_integritas` WRITE;
/*!40000 ALTER TABLE `element5_pembangunan_budaya_integritas` DISABLE KEYS */;
INSERT INTO `element5_pembangunan_budaya_integritas` VALUES (1,'Internalisasi Nilai Integritas dan Penerapan Etika Organisasi','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(2,'Mekanisme Pengaduan, Pemantauan, dan Tindak Lanjut Pelanggaran Integritas','-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `element5_pembangunan_budaya_integritas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element5_pembangunan_budaya_integritas_edit_logs`
--

DROP TABLE IF EXISTS `element5_pembangunan_budaya_integritas_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element5_pembangunan_budaya_integritas_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `row_id` int unsigned NOT NULL,
  `pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'save',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `e5_pbi_logs_row_id_idx` (`row_id`),
  KEY `e5_pbi_logs_username_idx` (`username`),
  KEY `e5_pbi_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element5_pembangunan_budaya_integritas_edit_logs`
--

LOCK TABLES `element5_pembangunan_budaya_integritas_edit_logs` WRITE;
/*!40000 ALTER TABLE `element5_pembangunan_budaya_integritas_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `element5_pembangunan_budaya_integritas_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element_assessments`
--

DROP TABLE IF EXISTS `element_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element_assessments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subtopic_slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtopic_title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `weighted_total` decimal(4,2) NOT NULL,
  `level` tinyint unsigned NOT NULL,
  `predikat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element_assessments_subtopic_slug_created_at_index` (`subtopic_slug`,`created_at`),
  CONSTRAINT `element_assessments_chk_1` CHECK (json_valid(`scores`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element_assessments`
--

LOCK TABLES `element_assessments` WRITE;
/*!40000 ALTER TABLE `element_assessments` DISABLE KEYS */;
/*!40000 ALTER TABLE `element_assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element_preference_legal_bases`
--

DROP TABLE IF EXISTS `element_preference_legal_bases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element_preference_legal_bases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `action_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `note` text COLLATE utf8mb4_unicode_ci,
  `uploaded_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `storage_driver` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pref_legal_action_created_idx` (`action_type`,`created_at`),
  KEY `pref_legal_uploaded_by_idx` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element_preference_legal_bases`
--

LOCK TABLES `element_preference_legal_bases` WRITE;
/*!40000 ALTER TABLE `element_preference_legal_bases` DISABLE KEYS */;
/*!40000 ALTER TABLE `element_preference_legal_bases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element_preferences`
--

DROP TABLE IF EXISTS `element_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payload` json NOT NULL,
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element_preferences`
--

LOCK TABLES `element_preferences` WRITE;
/*!40000 ALTER TABLE `element_preferences` DISABLE KEYS */;
/*!40000 ALTER TABLE `element_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element_progress_archives`
--

DROP TABLE IF EXISTS `element_progress_archives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element_progress_archives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `budget_year` smallint unsigned NOT NULL,
  `snapshot` json NOT NULL,
  `total_rows` int unsigned NOT NULL DEFAULT '0',
  `archived_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loaded_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_loaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `element_progress_archives_budget_year_unique` (`budget_year`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element_progress_archives`
--

LOCK TABLES `element_progress_archives` WRITE;
/*!40000 ALTER TABLE `element_progress_archives` DISABLE KEYS */;
INSERT INTO `element_progress_archives` VALUES (1,2026,'{\"tables\": {\"notifications\": {\"rows\": [{\"id\": 1, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-01-21 15:40:08\", \"subtopic_title\": \"Element 1 - Pengawasan Ketaatan\", \"coordinator_name\": \"KIKI 2\", \"coordinator_username\": \"kiki2\"}, {\"id\": 2, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-01-23 17:27:07\", \"subtopic_title\": \"Element 1 - Pengawasan Ketaatan\", \"coordinator_name\": \"Admin\", \"coordinator_username\": \"admin\"}, {\"id\": 3, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-01-30 14:35:55\", \"subtopic_title\": \"Element 1 - Pengawasan Ketaatan\", \"coordinator_name\": \"Admin\", \"coordinator_username\": \"admin\"}, {\"id\": 4, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-23 07:17:24\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 5, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-23 07:29:05\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 6, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-23 07:54:39\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 7, \"row_id\": 2, \"statement\": \"Analisis dan Atribut Temuan\", \"created_at\": \"2026-02-23 07:54:48\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 8, \"row_id\": 3, \"statement\": \"Kualitas Opini/Simpulan\", \"created_at\": \"2026-02-23 07:54:58\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 9, \"row_id\": 4, \"statement\": \"Kualitas Rekomendasi\", \"created_at\": \"2026-02-23 07:55:13\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 10, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-23 08:05:41\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 11, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-23 08:41:33\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 12, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-23 08:48:13\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 13, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-24 01:57:27\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 14, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-24 02:10:03\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 15, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-24 02:51:58\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 16, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-24 03:50:51\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 17, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-24 04:04:38\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 18, \"row_id\": 1, \"statement\": \"Tujuan dan Ruang Lingkup\", \"created_at\": \"2026-02-24 04:50:10\", \"subtopic_title\": \"Element 1 - Kegiatan Konsultansi\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 19, \"row_id\": 2, \"statement\": \"Peran, tanggung jawab dan ekspektasi\", \"created_at\": \"2026-02-24 04:51:09\", \"subtopic_title\": \"Element 1 - Kegiatan Konsultansi\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 20, \"row_id\": 3, \"statement\": \"Proses Konsultansi\", \"created_at\": \"2026-02-24 04:52:06\", \"subtopic_title\": \"Element 1 - Kegiatan Konsultansi\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 21, \"row_id\": 4, \"statement\": \"Kualitas Hasil Konsultansi\", \"created_at\": \"2026-02-24 04:52:49\", \"subtopic_title\": \"Element 1 - Kegiatan Konsultansi\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 22, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-24 07:10:54\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 23, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-26 01:51:14\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 24, \"row_id\": 2, \"statement\": \"Analisis dan Atribut Temuan\", \"created_at\": \"2026-02-26 01:51:42\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 25, \"row_id\": 3, \"statement\": \"Kualitas Opini/Simpulan\", \"created_at\": \"2026-02-26 01:52:08\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 26, \"row_id\": 4, \"statement\": \"Kualitas Rekomendasi\", \"created_at\": \"2026-02-26 01:52:36\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 27, \"row_id\": 1, \"statement\": \"Tujuan dan Ruang Lingkup\", \"created_at\": \"2026-02-26 01:54:24\", \"subtopic_title\": \"Element 1 - Kegiatan Konsultansi\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 28, \"row_id\": 2, \"statement\": \"Peran, tanggung jawab dan ekspektasi\", \"created_at\": \"2026-02-26 01:54:53\", \"subtopic_title\": \"Element 1 - Kegiatan Konsultansi\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 29, \"row_id\": 3, \"statement\": \"Proses Konsultansi\", \"created_at\": \"2026-02-26 01:55:20\", \"subtopic_title\": \"Element 1 - Kegiatan Konsultansi\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 30, \"row_id\": 4, \"statement\": \"Kualitas Hasil Konsultansi\", \"created_at\": \"2026-02-26 01:57:59\", \"subtopic_title\": \"Element 1 - Kegiatan Konsultansi\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 31, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-02-26 08:59:49\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Admin SIKAP\", \"coordinator_username\": \"admin\"}, {\"id\": 32, \"row_id\": 1, \"statement\": \"Pelaksanaan Pengembangan Informasi Awal (Pra-Perencanaan)\", \"created_at\": \"2026-03-02 08:53:06\", \"subtopic_title\": \"Element 2 - Pengembangan Informasi Awal\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 33, \"row_id\": 2, \"statement\": \"Desain Penugasan Pengawasan (DPP)\", \"created_at\": \"2026-03-02 08:54:07\", \"subtopic_title\": \"Element 2 - Pengembangan Informasi Awal\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 34, \"row_id\": 1, \"statement\": \"Kelengkapan Atribut Laporan dan Ketepatan Waktu Penyampaian\", \"created_at\": \"2026-03-02 09:02:25\", \"subtopic_title\": \"Element 2 - Komunikasi Hasil Penugasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 35, \"row_id\": 1, \"statement\": \"Penyusunan Dokumen Perencanaan\", \"created_at\": \"2026-03-03 04:53:43\", \"subtopic_title\": \"Element 2 - Perencanaan Penugasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 36, \"row_id\": 2, \"statement\": \"Penyusunan Program Kerja\", \"created_at\": \"2026-03-03 04:53:53\", \"subtopic_title\": \"Element 2 - Perencanaan Penugasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 37, \"row_id\": 1, \"statement\": \"Identifikasi dan Pengumpulan data/informasi\", \"created_at\": \"2026-03-03 04:55:20\", \"subtopic_title\": \"Element 2 - Pelaksanaan Penugasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 38, \"row_id\": 2, \"statement\": \"Pelaksanaan Pedoman/Program Kerja\", \"created_at\": \"2026-03-03 04:55:31\", \"subtopic_title\": \"Element 2 - Pelaksanaan Penugasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 39, \"row_id\": 3, \"statement\": \"Penyusunan Opini, Simpulan, dan Rekomendasi\", \"created_at\": \"2026-03-03 04:55:43\", \"subtopic_title\": \"Element 2 - Pelaksanaan Penugasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 40, \"row_id\": 1, \"statement\": \"Pelaksanaan Pemantauan Tindak Lanjut Rekomendasi\", \"created_at\": \"2026-03-03 06:35:23\", \"subtopic_title\": \"Element 2 - Pemantauan Tindak Lanjut\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 41, \"row_id\": 2, \"statement\": \"Evaluasi Efektivitas Tindak Lanjut Rekomendasi\", \"created_at\": \"2026-03-03 06:35:33\", \"subtopic_title\": \"Element 2 - Pemantauan Tindak Lanjut\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 42, \"row_id\": 3, \"statement\": \"Konfirmasi kepada Manajemen Klien dan/atau Entitas Mitra\", \"created_at\": \"2026-03-03 06:35:44\", \"subtopic_title\": \"Element 2 - Pemantauan Tindak Lanjut\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 43, \"row_id\": 1, \"statement\": \"Melaksanakan Reviu Berjenjang pada Setiap Tahapan Penugasan Pengawasan\", \"created_at\": \"2026-03-03 06:37:27\", \"subtopic_title\": \"Element 2 - Pengendalian Kualitas Penugasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 44, \"row_id\": 2, \"statement\": \"Melaksanakan Penjaminan Kualitas Internal\", \"created_at\": \"2026-03-03 06:37:37\", \"subtopic_title\": \"Element 2 - Pengendalian Kualitas Penugasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 45, \"row_id\": 3, \"statement\": \"Melaksanakan Telaah Sejawat Ekstern\", \"created_at\": \"2026-03-03 06:37:49\", \"subtopic_title\": \"Element 2 - Pengendalian Kualitas Penugasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 46, \"row_id\": 1, \"statement\": \"Tujuan dan Ruang Lingkup\", \"created_at\": \"2026-03-04 01:34:54\", \"subtopic_title\": \"Element 1 - Kegiatan Konsultansi\", \"coordinator_name\": \"Nyayu Dela\", \"coordinator_username\": \"dela\"}, {\"id\": 47, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-03-04 03:59:28\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 48, \"row_id\": 1, \"statement\": \"Struktur Perencanaan\", \"created_at\": \"2026-03-04 08:38:45\", \"subtopic_title\": \"Element 3 - Perencanaan Pengawasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 49, \"row_id\": 2, \"statement\": \"Fokus dan Sasaran Pengawasan\", \"created_at\": \"2026-03-04 08:38:55\", \"subtopic_title\": \"Element 3 - Perencanaan Pengawasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 50, \"row_id\": 3, \"statement\": \"Adaptif\", \"created_at\": \"2026-03-04 08:39:07\", \"subtopic_title\": \"Element 3 - Perencanaan Pengawasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 51, \"row_id\": 4, \"statement\": \"Keterlibatan Manajemen\", \"created_at\": \"2026-03-04 08:39:16\", \"subtopic_title\": \"Element 3 - Perencanaan Pengawasan\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 52, \"row_id\": 1, \"statement\": \"Kualitas Penyajian Laporan\", \"created_at\": \"2026-03-04 08:40:36\", \"subtopic_title\": \"Element 3 - Pelaporan kepada Manajemen K/L/D\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 53, \"row_id\": 2, \"statement\": \"Kualitas Rekomendasi dan Nilai Tambah Strategis\", \"created_at\": \"2026-03-04 08:40:48\", \"subtopic_title\": \"Element 3 - Pelaporan kepada Manajemen K/L/D\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 54, \"row_id\": 3, \"statement\": \"Pemanfaatan oleh Manajemen\", \"created_at\": \"2026-03-04 08:40:58\", \"subtopic_title\": \"Element 3 - Pelaporan kepada Manajemen K/L/D\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}, {\"id\": 55, \"row_id\": 1, \"statement\": \"Ruang Lingkup dan Fokus\", \"created_at\": \"2026-03-09 04:14:53\", \"subtopic_title\": \"Element 1 - Kegiatan Asurans\", \"coordinator_name\": \"Subbag PPK\", \"coordinator_username\": \"admin\"}], \"count\": 55, \"columns\": [\"id\", \"subtopic_title\", \"statement\", \"row_id\", \"coordinator_name\", \"coordinator_username\", \"created_at\"]}, \"element_assessments\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"subtopic_slug\", \"subtopic_title\", \"scores\", \"weighted_total\", \"level\", \"predikat\", \"notes\", \"submitted_by\", \"verified_by\", \"verified_at\", \"created_at\", \"updated_at\"]}, \"element_preferences\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"payload\", \"updated_by\", \"created_at\", \"updated_at\"]}, \"element4_dukungan_tik\": {\"rows\": [{\"id\": 1, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Integrasi TI untuk Pengawasan Intern\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Pelatihan Pengguna\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Pengembangan dan Pengadaan\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 4, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Pemanfaatan TI untuk Fungsi Manajerial Pengawasan\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 4, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element1_jasa_konsultansi\": {\"rows\": [{\"id\": 1, \"skor\": \"0.60\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[26]\", \"dokumen_path\": \"/uploads/dms/vATakpO6dehgLjoUhYNla2CXCICosqwTx5j0Ulvc.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": \"0.60\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[24]\", \"dokumen_path\": \"/uploads/dms/RC5e5dcplFsZDiFcFEyp5XaYAZd3dMdmmFomkFIq.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Proses Konsultansi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[17]\", \"dokumen_path\": \"/uploads/dms/Yos4rG3IM3S0YVZHW3Sy0dECYTjzZKIJw92YDq4I.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 4, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[27]\", \"dokumen_path\": \"/uploads/dms/akOIzPa3Zsrvi4JqIj8V52IQskTfMqaRrlphNINB.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 4, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element1_kegiatan_asurans\": {\"rows\": [{\"id\": 1, \"skor\": \"0.60\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"qa_verified\": 1, \"verify_note\": null, \"doc_file_ids\": \"[23]\", \"dokumen_path\": \"/uploads/dms/suZhbzFdWlJ7hwAHvPDCj7fOqlxaPIH3xokfLkwS.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": \"2026-03-27 06:57:02\", \"qa_verified_by\": \"admin\", \"qa_verify_note\": \"Test\", \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_follow_up_recommendation\": \"Test\"}, {\"id\": 2, \"skor\": \"0.75\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Analisis dan Atribut Temuan\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[15]\", \"dokumen_path\": \"/uploads/dms/nzwxtVa5ta1QR5qUfjyTZhZinHemizabvr5BekLK.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": \"0.75\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Kualitas Opini/Simpulan\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[28]\", \"dokumen_path\": \"/uploads/dms/Q53K5qCZlkkM4KxFxmMmaCi8YOppIDJvhrcjIMo3.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 4, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Kualitas Rekomendasi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[17]\", \"dokumen_path\": \"/uploads/dms/Yos4rG3IM3S0YVZHW3Sy0dECYTjzZKIJw92YDq4I.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 4, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element2_komunikasi_hasil\": {\"rows\": [{\"id\": 1, \"skor\": \"3.00\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Kelengkapan Atribut Laporan dan Ketepatan Waktu Penyampaian\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[17]\", \"dokumen_path\": \"/uploads/dms/Yos4rG3IM3S0YVZHW3Sy0dECYTjzZKIJw92YDq4I.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 1, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element4_manajemen_kinerja\": {\"rows\": [{\"id\": 1, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Perencanaan Kinerja\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Pengorganisasian Kinerja\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Pengendalian Kinerja\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 3, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element4_mekanisme_pendanaan\": {\"rows\": [{\"id\": 1, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Perencanaan dan Kecukupan Anggaran\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Penggunaan dan Fleksibilitas Anggaran\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 2, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element4_perencanaan_sdm_apip\": {\"rows\": [{\"id\": 1, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Perencanaan Kebutuhan SDM\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Rekrutmen dan Distribusi SDM\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 2, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element2_pelaksanaan_penugasan\": {\"rows\": [{\"id\": 1, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Identifikasi dan Pengumpulan data/informasi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[7]\", \"dokumen_path\": \"/uploads/latsar/uploads/dms/e-2901-pa-01-02_20260123091733_1_f67ac40e.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": \"1.20\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Pelaksanaan Pedoman/Program Kerja\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[1]\", \"dokumen_path\": \"/uploads/latsar/uploads/dms/e1-0021-pa-01_20260122084444_1_0e78c85c.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Penyusunan Opini, Simpulan, dan Rekomendasi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[8]\", \"dokumen_path\": \"/uploads/latsar/uploads/dms/e-9928-pa-01-01_20260123091733_2_8d551bb2.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 3, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element2_pengendalian_kualitas\": {\"rows\": [{\"id\": 1, \"skor\": \"1.20\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Melaksanakan Reviu Berjenjang pada Setiap Tahapan Penugasan Pengawasan\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[10]\", \"dokumen_path\": \"/uploads/latsar/uploads/dms/e-0001-pa-01-02_20260123093358_1_e64dbee0.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Melaksanakan Penjaminan Kualitas Internal\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[18]\", \"dokumen_path\": \"/uploads/dms/jsk37Yigm8nEj3poJFDkxJHbTfpA4QPjJKMzMQ59.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Melaksanakan Telaah Sejawat Ekstern\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[8]\", \"dokumen_path\": \"/uploads/latsar/uploads/dms/e-9928-pa-01-01_20260123091733_2_8d551bb2.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 3, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element2_perencanaan_penugasan\": {\"rows\": [{\"id\": 1, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Penyusunan Dokumen Perencanaan\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[7]\", \"dokumen_path\": \"/uploads/latsar/uploads/dms/e-2901-pa-01-02_20260123091733_1_f67ac40e.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": \"2.10\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Penyusunan Program Kerja\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[3]\", \"dokumen_path\": \"/uploads/latsar/uploads/dms/e1-1231-pa-01_20260122084642_1_3f35667d.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 2, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element5_koordinasi_pengawasan\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element2_pengembangan_informasi\": {\"rows\": [{\"id\": 1, \"skor\": \"1.20\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Pelaksanaan Pengembangan Informasi Awal (Pra-Perencanaan)\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[26]\", \"dokumen_path\": \"/uploads/dms/vATakpO6dehgLjoUhYNla2CXCICosqwTx5j0Ulvc.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": \"Test\", \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": \"1.80\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Desain Penugasan Pengawasan (DPP)\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[19]\", \"dokumen_path\": \"/uploads/dms/XQuhCRJig5I8sVXqr7Yt2HjJ1XBr2DMsA5y4Nlwl.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": \"Test\", \"grad_l5_catatan\": \"Test\", \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 2, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element3_perencanaan_pengawasan\": {\"rows\": [{\"id\": 1, \"skor\": \"0.45\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Struktur Perencanaan\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[27]\", \"dokumen_path\": \"/uploads/dms/akOIzPa3Zsrvi4JqIj8V52IQskTfMqaRrlphNINB.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": \"1.20\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Fokus dan Sasaran Pengawasan\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[24]\", \"dokumen_path\": \"/uploads/dms/RC5e5dcplFsZDiFcFEyp5XaYAZd3dMdmmFomkFIq.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": \"0.60\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Adaptif\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[24]\", \"dokumen_path\": \"/uploads/dms/RC5e5dcplFsZDiFcFEyp5XaYAZd3dMdmmFomkFIq.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 4, \"skor\": \"0.75\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Keterlibatan Manajemen\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[17]\", \"dokumen_path\": \"/uploads/dms/Yos4rG3IM3S0YVZHW3Sy0dECYTjzZKIJw92YDq4I.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 4, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element4_dukungan_tik_edit_logs\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element3_pelaporan_manajemen_kld\": {\"rows\": [{\"id\": 1, \"skor\": \"0.45\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Kualitas Penyajian Laporan\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[27]\", \"dokumen_path\": \"/uploads/dms/akOIzPa3Zsrvi4JqIj8V52IQskTfMqaRrlphNINB.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": \"1.80\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Kualitas Rekomendasi dan Nilai Tambah Strategis\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[7]\", \"dokumen_path\": \"/uploads/latsar/uploads/dms/e-2901-pa-01-02_20260123091733_1_f67ac40e.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": \"0.75\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Pemanfaatan oleh Manajemen\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[23]\", \"dokumen_path\": \"/uploads/dms/suZhbzFdWlJ7hwAHvPDCj7fOqlxaPIH3xokfLkwS.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 3, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element5_hubungan_apip_manajemen\": {\"rows\": [{\"id\": 1, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Pemantauan dan Pemberian Arahan atas Peningkatan Kapabilitas APIP\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Kualitas Komunikasi Internal\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Kualitas Komunikasi APIP dengan Manajemen\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 3, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element2_pemantauan_tindak_lanjut\": {\"rows\": [{\"id\": 1, \"skor\": \"1.20\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Pelaksanaan Pemantauan Tindak Lanjut Rekomendasi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[1]\", \"dokumen_path\": \"/uploads/latsar/uploads/dms/e1-0021-pa-01_20260122084444_1_0e78c85c.pdf\", \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Evaluasi Efektivitas Tindak Lanjut Rekomendasi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[23]\", \"dokumen_path\": \"/uploads/dms/suZhbzFdWlJ7hwAHvPDCj7fOqlxaPIH3xokfLkwS.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 3, \"skor\": \"0.90\", \"level\": \"3\", \"evidence\": null, \"verified\": 1, \"pernyataan\": \"Konfirmasi kepada Manajemen Klien dan/atau Entitas Mitra\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": \"[24]\", \"dokumen_path\": \"/uploads/dms/RC5e5dcplFsZDiFcFEyp5XaYAZd3dMdmmFomkFIq.pdf\", \"analisis_bukti\": \"Test\", \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": \"Test\", \"grad_l2_catatan\": \"Test\", \"grad_l3_catatan\": \"Test\", \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": \"{\\\"1\\\":1,\\\"2\\\":1,\\\"3\\\":1,\\\"4\\\":0,\\\"5\\\":0}\", \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 3, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element1_jasa_konsultansi_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:49:52\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-02-24 04:49:52\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 2, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:50:10\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-02-24 04:50:10\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 3, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:50:57\", \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"updated_at\": \"2026-02-24 04:50:57\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 4, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:51:09\", \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"updated_at\": \"2026-02-24 04:51:09\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 5, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:51:50\", \"pernyataan\": \"Proses Konsultansi\", \"updated_at\": \"2026-02-24 04:51:50\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 6, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:52:06\", \"pernyataan\": \"Proses Konsultansi\", \"updated_at\": \"2026-02-24 04:52:06\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 7, \"action\": \"save\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:52:35\", \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"updated_at\": \"2026-02-24 04:52:35\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 8, \"action\": \"verify\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:52:49\", \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"updated_at\": \"2026-02-24 04:52:49\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 9, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:34:36\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-02-25 09:34:36\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 10, \"action\": \"clear\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:34:42\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-02-25 09:34:42\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 11, \"action\": \"verify_reset\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:34:49\", \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"updated_at\": \"2026-02-25 09:34:49\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 12, \"action\": \"clear\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:35:03\", \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"updated_at\": \"2026-02-25 09:35:03\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 13, \"action\": \"verify_reset\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:35:13\", \"pernyataan\": \"Proses Konsultansi\", \"updated_at\": \"2026-02-25 09:35:13\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 14, \"action\": \"clear\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:35:17\", \"pernyataan\": \"Proses Konsultansi\", \"updated_at\": \"2026-02-25 09:35:17\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 15, \"action\": \"verify_reset\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:35:24\", \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"updated_at\": \"2026-02-25 09:35:24\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 16, \"action\": \"clear\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:35:29\", \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"updated_at\": \"2026-02-25 09:35:29\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 17, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:54:14\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-02-26 01:54:14\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 18, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:54:24\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-02-26 01:54:24\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 19, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:54:41\", \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"updated_at\": \"2026-02-26 01:54:41\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 20, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:54:53\", \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"updated_at\": \"2026-02-26 01:54:53\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 21, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:55:10\", \"pernyataan\": \"Proses Konsultansi\", \"updated_at\": \"2026-02-26 01:55:10\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 22, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:55:20\", \"pernyataan\": \"Proses Konsultansi\", \"updated_at\": \"2026-02-26 01:55:20\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 23, \"action\": \"save\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:55:35\", \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"updated_at\": \"2026-02-26 01:55:35\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 24, \"action\": \"verify\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:57:59\", \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"updated_at\": \"2026-02-26 01:57:59\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 25, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"dela\", \"created_at\": \"2026-03-04 01:33:16\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-03-04 01:33:16\", \"display_name\": \"Nyayu Dela\"}, {\"id\": 26, \"action\": \"verify\", \"row_id\": 1, \"username\": \"dela\", \"created_at\": \"2026-03-04 01:34:54\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-03-04 01:34:54\", \"display_name\": \"Nyayu Dela\"}, {\"id\": 27, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:42:22\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-03-04 01:42:22\", \"display_name\": \"Subbag PPK\"}, {\"id\": 28, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:42:30\", \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"updated_at\": \"2026-03-04 01:42:30\", \"display_name\": \"Subbag PPK\"}, {\"id\": 29, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:42:39\", \"pernyataan\": \"Proses Konsultansi\", \"updated_at\": \"2026-03-04 01:42:39\", \"display_name\": \"Subbag PPK\"}, {\"id\": 30, \"action\": \"save\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:42:48\", \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"updated_at\": \"2026-03-04 01:42:48\", \"display_name\": \"Subbag PPK\"}, {\"id\": 31, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:42:55\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-03-04 01:42:55\", \"display_name\": \"Subbag PPK\"}, {\"id\": 32, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:43:02\", \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"updated_at\": \"2026-03-04 01:43:02\", \"display_name\": \"Subbag PPK\"}, {\"id\": 33, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:43:10\", \"pernyataan\": \"Proses Konsultansi\", \"updated_at\": \"2026-03-04 01:43:10\", \"display_name\": \"Subbag PPK\"}, {\"id\": 34, \"action\": \"verify\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:43:17\", \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"updated_at\": \"2026-03-04 01:43:17\", \"display_name\": \"Subbag PPK\"}, {\"id\": 35, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:45:01\", \"pernyataan\": \"Tujuan dan Ruang Lingkup\", \"updated_at\": \"2026-03-04 03:45:01\", \"display_name\": \"Subbag PPK\"}, {\"id\": 36, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:45:08\", \"pernyataan\": \"Peran, tanggung jawab dan ekspektasi\", \"updated_at\": \"2026-03-04 03:45:08\", \"display_name\": \"Subbag PPK\"}, {\"id\": 37, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:45:18\", \"pernyataan\": \"Proses Konsultansi\", \"updated_at\": \"2026-03-04 03:45:18\", \"display_name\": \"Subbag PPK\"}, {\"id\": 38, \"action\": \"verify\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:45:30\", \"pernyataan\": \"Kualitas Hasil Konsultansi\", \"updated_at\": \"2026-03-04 03:45:30\", \"display_name\": \"Subbag PPK\"}], \"count\": 38, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element1_kegiatan_asurans_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 05:05:10\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 05:05:10\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 2, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 06:20:25\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 06:20:25\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 3, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 06:33:55\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 06:33:55\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 4, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 06:38:11\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 06:38:11\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 5, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-23 07:51:23\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-23 07:51:23\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 6, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-23 07:53:36\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-23 07:53:36\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 7, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-23 07:53:37\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-23 07:53:37\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 8, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-23 07:54:01\", \"pernyataan\": \"Kualitas Opini/Simpulan\", \"updated_at\": \"2026-02-23 07:54:01\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 9, \"action\": \"save\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-23 07:54:21\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-02-23 07:54:21\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 10, \"action\": \"save\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:04:13\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-02-23 08:04:13\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 11, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:09:39\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 08:09:39\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 12, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:10:26\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 08:10:26\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 13, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:11:35\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 08:11:35\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 14, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:12:26\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-23 08:12:26\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 15, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:12:33\", \"pernyataan\": \"Kualitas Opini/Simpulan\", \"updated_at\": \"2026-02-23 08:12:33\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 16, \"action\": \"verify\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:12:40\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-02-23 08:12:40\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 17, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:41:20\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 08:41:20\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 18, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:41:33\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 08:41:33\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 19, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:48:02\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 08:48:02\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 20, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-23 08:48:13\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-23 08:48:13\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 21, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 01:02:08\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 01:02:08\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 22, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 01:05:12\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 01:05:12\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 23, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 01:05:35\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 01:05:35\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 24, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 01:06:16\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 01:06:16\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 25, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 01:49:49\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 01:49:49\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 26, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 01:51:16\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 01:51:16\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 27, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 01:57:27\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 01:57:27\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 28, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 02:05:28\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 02:05:28\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 29, \"action\": \"clear\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 02:08:45\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 02:08:45\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 30, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 02:09:50\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 02:09:50\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 31, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 02:10:03\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 02:10:03\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 32, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 02:10:52\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 02:10:52\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 33, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 02:51:58\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 02:51:58\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 34, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 02:57:11\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 02:57:11\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 35, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 03:50:51\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 03:50:51\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 36, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 03:53:10\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 03:53:10\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 37, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 03:54:56\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 03:54:56\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 38, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 03:55:02\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 03:55:02\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 39, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 03:55:29\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 03:55:29\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 40, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 03:58:24\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 03:58:24\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 41, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 03:59:10\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 03:59:10\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 42, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:00:54\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:00:54\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 43, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:01:07\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:01:07\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 44, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:01:18\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:01:18\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 45, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:01:30\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:01:30\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 46, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:03:23\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:03:23\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 47, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:03:33\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:03:33\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 48, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:04:23\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:04:23\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 49, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:04:38\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:04:38\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 50, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:05:59\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:05:59\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 51, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 04:22:22\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 04:22:22\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 52, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 07:05:35\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 07:05:35\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 53, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 07:10:54\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 07:10:54\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 54, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 07:26:50\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 07:26:50\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 55, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 07:27:22\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 07:27:22\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 56, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 07:37:26\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 07:37:26\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 57, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 07:38:07\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 07:38:07\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 58, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-24 07:38:48\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-24 07:38:48\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 59, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-24 07:39:14\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 07:39:14\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 60, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-24 07:41:58\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-24 07:41:58\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 61, \"action\": \"verify\", \"row_id\": 1, \"username\": \"kiki2\", \"created_at\": \"2026-02-24 07:56:28\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-24 07:56:28\", \"display_name\": \"KIKI 2\"}, {\"id\": 62, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:33:29\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-25 09:33:29\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 63, \"action\": \"clear\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:33:34\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-25 09:33:34\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 64, \"action\": \"verify_reset\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:33:43\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-25 09:33:43\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 65, \"action\": \"clear\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:33:47\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-25 09:33:47\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 66, \"action\": \"verify_reset\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:33:59\", \"pernyataan\": \"Kualitas Opini/Simpulan\", \"updated_at\": \"2026-02-25 09:33:59\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 67, \"action\": \"clear\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:34:05\", \"pernyataan\": \"Kualitas Opini/Simpulan\", \"updated_at\": \"2026-02-25 09:34:05\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 68, \"action\": \"verify_reset\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:34:14\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-02-25 09:34:14\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 69, \"action\": \"clear\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-25 09:34:19\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-02-25 09:34:19\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 70, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 00:57:14\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-26 00:57:14\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 71, \"action\": \"clear\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:03:36\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-26 01:03:36\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 72, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:51:00\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-26 01:51:00\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 73, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:51:14\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-26 01:51:14\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 74, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:51:31\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-26 01:51:31\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 75, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:51:42\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-02-26 01:51:42\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 76, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:51:58\", \"pernyataan\": \"Kualitas Opini/Simpulan\", \"updated_at\": \"2026-02-26 01:51:58\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 77, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:52:08\", \"pernyataan\": \"Kualitas Opini/Simpulan\", \"updated_at\": \"2026-02-26 01:52:08\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 78, \"action\": \"save\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:52:26\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-02-26 01:52:26\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 79, \"action\": \"verify\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-02-26 01:52:36\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-02-26 01:52:36\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 80, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 08:13:36\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-26 08:13:36\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 81, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 08:59:49\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-26 08:59:49\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 82, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 09:06:41\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-26 09:06:41\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 83, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-02-26 09:06:58\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-02-26 09:06:58\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 84, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 02:46:33\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-02 02:46:33\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 85, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 02:48:20\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-02 02:48:20\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 86, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 02:51:32\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-02 02:51:32\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 87, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 02:57:19\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-02 02:57:19\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 88, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 02:57:33\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-02 02:57:33\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 89, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 02:57:45\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-02 02:57:45\", \"display_name\": \"Admin SIKAP\"}, {\"id\": 90, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:40:36\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-04 01:40:36\", \"display_name\": \"Subbag PPK\"}, {\"id\": 91, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:40:46\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-03-04 01:40:46\", \"display_name\": \"Subbag PPK\"}, {\"id\": 92, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:41:02\", \"pernyataan\": \"Kualitas Opini/Simpulan\", \"updated_at\": \"2026-03-04 01:41:02\", \"display_name\": \"Subbag PPK\"}, {\"id\": 93, \"action\": \"save\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:41:10\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-03-04 01:41:10\", \"display_name\": \"Subbag PPK\"}, {\"id\": 94, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:41:21\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-04 01:41:21\", \"display_name\": \"Subbag PPK\"}, {\"id\": 95, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:41:29\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-03-04 01:41:29\", \"display_name\": \"Subbag PPK\"}, {\"id\": 96, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:41:37\", \"pernyataan\": \"Kualitas Opini/Simpulan\", \"updated_at\": \"2026-03-04 01:41:37\", \"display_name\": \"Subbag PPK\"}, {\"id\": 97, \"action\": \"verify\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-03-04 01:41:45\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-03-04 01:41:45\", \"display_name\": \"Subbag PPK\"}, {\"id\": 98, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 02:35:34\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-04 02:35:34\", \"display_name\": \"Subbag PPK\"}, {\"id\": 99, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:44:19\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-04 03:44:19\", \"display_name\": \"Subbag PPK\"}, {\"id\": 100, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:44:29\", \"pernyataan\": \"Analisis dan Atribut Temuan\", \"updated_at\": \"2026-03-04 03:44:29\", \"display_name\": \"Subbag PPK\"}, {\"id\": 101, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:44:39\", \"pernyataan\": \"Kualitas Opini/Simpulan\", \"updated_at\": \"2026-03-04 03:44:39\", \"display_name\": \"Subbag PPK\"}, {\"id\": 102, \"action\": \"verify\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:44:47\", \"pernyataan\": \"Kualitas Rekomendasi\", \"updated_at\": \"2026-03-04 03:44:47\", \"display_name\": \"Subbag PPK\"}, {\"id\": 103, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:46:38\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-04 03:46:38\", \"display_name\": \"Subbag PPK\"}, {\"id\": 104, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:50:42\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-04 03:50:42\", \"display_name\": \"Subbag PPK\"}, {\"id\": 105, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:59:11\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-04 03:59:11\", \"display_name\": \"Subbag PPK\"}, {\"id\": 106, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 03:59:28\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-04 03:59:28\", \"display_name\": \"Subbag PPK\"}, {\"id\": 107, \"action\": \"verify_reset\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-09 04:14:40\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-09 04:14:40\", \"display_name\": \"Subbag PPK\"}, {\"id\": 108, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-09 04:14:53\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-09 04:14:53\", \"display_name\": \"Subbag PPK\"}, {\"id\": 109, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-09 04:15:04\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-09 04:15:04\", \"display_name\": \"Subbag PPK\"}, {\"id\": 110, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-09 04:15:16\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-09 04:15:16\", \"display_name\": \"Subbag PPK\"}, {\"id\": 111, \"action\": \"qa_verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-27 06:57:02\", \"pernyataan\": \"Ruang Lingkup dan Fokus\", \"updated_at\": \"2026-03-27 06:57:02\", \"display_name\": \"Subbag PPK\"}], \"count\": 111, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element2_komunikasi_hasil_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 09:02:13\", \"pernyataan\": \"Kelengkapan Atribut Laporan dan Ketepatan Waktu Penyampaian\", \"updated_at\": \"2026-03-02 09:02:13\", \"display_name\": \"Subbag PPK\"}, {\"id\": 2, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 09:02:25\", \"pernyataan\": \"Kelengkapan Atribut Laporan dan Ketepatan Waktu Penyampaian\", \"updated_at\": \"2026-03-02 09:02:25\", \"display_name\": \"Subbag PPK\"}], \"count\": 2, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element5_akses_informasi_sumberdaya\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element4_manajemen_kinerja_edit_logs\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element4_mekanisme_pendanaan_edit_logs\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element5_pembangunan_budaya_integritas\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element4_perencanaan_sdm_apip_edit_logs\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element2_pelaksanaan_penugasan_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:54:20\", \"pernyataan\": \"Identifikasi dan Pengumpulan data/informasi\", \"updated_at\": \"2026-03-03 04:54:20\", \"display_name\": \"Subbag PPK\"}, {\"id\": 2, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:54:44\", \"pernyataan\": \"Pelaksanaan Pedoman/Program Kerja\", \"updated_at\": \"2026-03-03 04:54:44\", \"display_name\": \"Subbag PPK\"}, {\"id\": 3, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:55:10\", \"pernyataan\": \"Penyusunan Opini, Simpulan, dan Rekomendasi\", \"updated_at\": \"2026-03-03 04:55:10\", \"display_name\": \"Subbag PPK\"}, {\"id\": 4, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:55:20\", \"pernyataan\": \"Identifikasi dan Pengumpulan data/informasi\", \"updated_at\": \"2026-03-03 04:55:20\", \"display_name\": \"Subbag PPK\"}, {\"id\": 5, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:55:31\", \"pernyataan\": \"Pelaksanaan Pedoman/Program Kerja\", \"updated_at\": \"2026-03-03 04:55:31\", \"display_name\": \"Subbag PPK\"}, {\"id\": 6, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:55:43\", \"pernyataan\": \"Penyusunan Opini, Simpulan, dan Rekomendasi\", \"updated_at\": \"2026-03-03 04:55:43\", \"display_name\": \"Subbag PPK\"}], \"count\": 6, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element2_pengendalian_kualitas_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:36:23\", \"pernyataan\": \"Melaksanakan Reviu Berjenjang pada Setiap Tahapan Penugasan Pengawasan\", \"updated_at\": \"2026-03-03 06:36:23\", \"display_name\": \"Subbag PPK\"}, {\"id\": 2, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:36:53\", \"pernyataan\": \"Melaksanakan Penjaminan Kualitas Internal\", \"updated_at\": \"2026-03-03 06:36:53\", \"display_name\": \"Subbag PPK\"}, {\"id\": 3, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:37:16\", \"pernyataan\": \"Melaksanakan Telaah Sejawat Ekstern\", \"updated_at\": \"2026-03-03 06:37:16\", \"display_name\": \"Subbag PPK\"}, {\"id\": 4, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:37:27\", \"pernyataan\": \"Melaksanakan Reviu Berjenjang pada Setiap Tahapan Penugasan Pengawasan\", \"updated_at\": \"2026-03-03 06:37:27\", \"display_name\": \"Subbag PPK\"}, {\"id\": 5, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:37:37\", \"pernyataan\": \"Melaksanakan Penjaminan Kualitas Internal\", \"updated_at\": \"2026-03-03 06:37:37\", \"display_name\": \"Subbag PPK\"}, {\"id\": 6, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:37:49\", \"pernyataan\": \"Melaksanakan Telaah Sejawat Ekstern\", \"updated_at\": \"2026-03-03 06:37:49\", \"display_name\": \"Subbag PPK\"}], \"count\": 6, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element2_perencanaan_penugasan_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:52:54\", \"pernyataan\": \"Penyusunan Dokumen Perencanaan\", \"updated_at\": \"2026-03-03 04:52:54\", \"display_name\": \"Subbag PPK\"}, {\"id\": 2, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:53:28\", \"pernyataan\": \"Penyusunan Program Kerja\", \"updated_at\": \"2026-03-03 04:53:28\", \"display_name\": \"Subbag PPK\"}, {\"id\": 3, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:53:43\", \"pernyataan\": \"Penyusunan Dokumen Perencanaan\", \"updated_at\": \"2026-03-03 04:53:43\", \"display_name\": \"Subbag PPK\"}, {\"id\": 4, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-03 04:53:53\", \"pernyataan\": \"Penyusunan Program Kerja\", \"updated_at\": \"2026-03-03 04:53:53\", \"display_name\": \"Subbag PPK\"}], \"count\": 4, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element5_koordinasi_pengawasan_edit_logs\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element2_pengembangan_informasi_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 08:52:51\", \"pernyataan\": \"Pelaksanaan Pengembangan Informasi Awal (Pra-Perencanaan)\", \"updated_at\": \"2026-03-02 08:52:51\", \"display_name\": \"Subbag PPK\"}, {\"id\": 2, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 08:53:06\", \"pernyataan\": \"Pelaksanaan Pengembangan Informasi Awal (Pra-Perencanaan)\", \"updated_at\": \"2026-03-02 08:53:06\", \"display_name\": \"Subbag PPK\"}, {\"id\": 3, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-02 08:53:51\", \"pernyataan\": \"Desain Penugasan Pengawasan (DPP)\", \"updated_at\": \"2026-03-02 08:53:51\", \"display_name\": \"Subbag PPK\"}, {\"id\": 4, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-02 08:54:07\", \"pernyataan\": \"Desain Penugasan Pengawasan (DPP)\", \"updated_at\": \"2026-03-02 08:54:07\", \"display_name\": \"Subbag PPK\"}, {\"id\": 5, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 08:59:47\", \"pernyataan\": \"Pelaksanaan Pengembangan Informasi Awal (Pra-Perencanaan)\", \"updated_at\": \"2026-03-02 08:59:47\", \"display_name\": \"Subbag PPK\"}, {\"id\": 6, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-02 09:00:04\", \"pernyataan\": \"Pelaksanaan Pengembangan Informasi Awal (Pra-Perencanaan)\", \"updated_at\": \"2026-03-02 09:00:04\", \"display_name\": \"Subbag PPK\"}], \"count\": 6, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element3_perencanaan_pengawasan_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:36:21\", \"pernyataan\": \"Struktur Perencanaan\", \"updated_at\": \"2026-03-04 08:36:21\", \"display_name\": \"Subbag PPK\"}, {\"id\": 2, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:36:42\", \"pernyataan\": \"Fokus dan Sasaran Pengawasan\", \"updated_at\": \"2026-03-04 08:36:42\", \"display_name\": \"Subbag PPK\"}, {\"id\": 3, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:37:03\", \"pernyataan\": \"Adaptif\", \"updated_at\": \"2026-03-04 08:37:03\", \"display_name\": \"Subbag PPK\"}, {\"id\": 4, \"action\": \"save\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:37:23\", \"pernyataan\": \"Keterlibatan Manajemen\", \"updated_at\": \"2026-03-04 08:37:23\", \"display_name\": \"Subbag PPK\"}, {\"id\": 5, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:38:45\", \"pernyataan\": \"Struktur Perencanaan\", \"updated_at\": \"2026-03-04 08:38:45\", \"display_name\": \"Subbag PPK\"}, {\"id\": 6, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:38:55\", \"pernyataan\": \"Fokus dan Sasaran Pengawasan\", \"updated_at\": \"2026-03-04 08:38:55\", \"display_name\": \"Subbag PPK\"}, {\"id\": 7, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:39:07\", \"pernyataan\": \"Adaptif\", \"updated_at\": \"2026-03-04 08:39:07\", \"display_name\": \"Subbag PPK\"}, {\"id\": 8, \"action\": \"verify\", \"row_id\": 4, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:39:16\", \"pernyataan\": \"Keterlibatan Manajemen\", \"updated_at\": \"2026-03-04 08:39:16\", \"display_name\": \"Subbag PPK\"}], \"count\": 8, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element3_pelaporan_manajemen_kld_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:39:42\", \"pernyataan\": \"Kualitas Penyajian Laporan\", \"updated_at\": \"2026-03-04 08:39:42\", \"display_name\": \"Subbag PPK\"}, {\"id\": 2, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:40:05\", \"pernyataan\": \"Kualitas Rekomendasi dan Nilai Tambah Strategis\", \"updated_at\": \"2026-03-04 08:40:05\", \"display_name\": \"Subbag PPK\"}, {\"id\": 3, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:40:24\", \"pernyataan\": \"Pemanfaatan oleh Manajemen\", \"updated_at\": \"2026-03-04 08:40:24\", \"display_name\": \"Subbag PPK\"}, {\"id\": 4, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:40:36\", \"pernyataan\": \"Kualitas Penyajian Laporan\", \"updated_at\": \"2026-03-04 08:40:36\", \"display_name\": \"Subbag PPK\"}, {\"id\": 5, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:40:48\", \"pernyataan\": \"Kualitas Rekomendasi dan Nilai Tambah Strategis\", \"updated_at\": \"2026-03-04 08:40:48\", \"display_name\": \"Subbag PPK\"}, {\"id\": 6, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-04 08:40:58\", \"pernyataan\": \"Pemanfaatan oleh Manajemen\", \"updated_at\": \"2026-03-04 08:40:58\", \"display_name\": \"Subbag PPK\"}], \"count\": 6, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element4_pengembangan_sdm_profesional_apip\": {\"rows\": [{\"id\": 1, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Rencana Pengembangan Kompetensi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}, {\"id\": 2, \"skor\": null, \"level\": \"-\", \"evidence\": null, \"verified\": 0, \"pernyataan\": \"Pelaksanaan Pengembangan Kompetensi\", \"qa_verified\": 0, \"verify_note\": null, \"doc_file_ids\": null, \"dokumen_path\": null, \"analisis_bukti\": null, \"analisis_nilai\": null, \"qa_verified_at\": null, \"qa_verified_by\": null, \"qa_verify_note\": null, \"grad_l1_catatan\": null, \"grad_l2_catatan\": null, \"grad_l3_catatan\": null, \"grad_l4_catatan\": null, \"grad_l5_catatan\": null, \"level_validation_state\": null, \"qa_level_validation_state\": null, \"qa_follow_up_recommendation\": null}], \"count\": 2, \"columns\": [\"id\", \"pernyataan\", \"level\", \"skor\", \"analisis_bukti\", \"analisis_nilai\", \"grad_l1_catatan\", \"grad_l2_catatan\", \"grad_l3_catatan\", \"grad_l4_catatan\", \"grad_l5_catatan\", \"evidence\", \"verified\", \"dokumen_path\", \"doc_file_ids\", \"level_validation_state\", \"verify_note\", \"qa_verified\", \"qa_verified_by\", \"qa_verified_at\", \"qa_verify_note\", \"qa_follow_up_recommendation\", \"qa_level_validation_state\"]}, \"element5_hubungan_apip_manajemen_edit_logs\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element2_pemantauan_tindak_lanjut_edit_logs\": {\"rows\": [{\"id\": 1, \"action\": \"save\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:34:23\", \"pernyataan\": \"Pelaksanaan Pemantauan Tindak Lanjut Rekomendasi\", \"updated_at\": \"2026-03-03 06:34:23\", \"display_name\": \"Subbag PPK\"}, {\"id\": 2, \"action\": \"save\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:34:44\", \"pernyataan\": \"Evaluasi Efektivitas Tindak Lanjut Rekomendasi\", \"updated_at\": \"2026-03-03 06:34:44\", \"display_name\": \"Subbag PPK\"}, {\"id\": 3, \"action\": \"save\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:35:09\", \"pernyataan\": \"Konfirmasi kepada Manajemen Klien dan/atau Entitas Mitra\", \"updated_at\": \"2026-03-03 06:35:09\", \"display_name\": \"Subbag PPK\"}, {\"id\": 4, \"action\": \"verify\", \"row_id\": 1, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:35:23\", \"pernyataan\": \"Pelaksanaan Pemantauan Tindak Lanjut Rekomendasi\", \"updated_at\": \"2026-03-03 06:35:23\", \"display_name\": \"Subbag PPK\"}, {\"id\": 5, \"action\": \"verify\", \"row_id\": 2, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:35:33\", \"pernyataan\": \"Evaluasi Efektivitas Tindak Lanjut Rekomendasi\", \"updated_at\": \"2026-03-03 06:35:33\", \"display_name\": \"Subbag PPK\"}, {\"id\": 6, \"action\": \"verify\", \"row_id\": 3, \"username\": \"admin\", \"created_at\": \"2026-03-03 06:35:44\", \"pernyataan\": \"Konfirmasi kepada Manajemen Klien dan/atau Entitas Mitra\", \"updated_at\": \"2026-03-03 06:35:44\", \"display_name\": \"Subbag PPK\"}], \"count\": 6, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element5_akses_informasi_sumberdaya_edit_logs\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element5_pembangunan_budaya_integritas_edit_logs\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}, \"element4_pengembangan_sdm_profesional_apip_edit_logs\": {\"rows\": [], \"count\": 0, \"columns\": [\"id\", \"row_id\", \"pernyataan\", \"username\", \"display_name\", \"action\", \"created_at\", \"updated_at\"]}}, \"version\": 1, \"total_rows\": 293, \"captured_at\": \"2026-03-27T09:26:34+00:00\"}',293,'admin','admin','2026-03-31 05:45:39','2026-03-27 02:26:34','2026-03-31 05:45:39');
/*!40000 ALTER TABLE `element_progress_archives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element_team_assignments`
--

DROP TABLE IF EXISTS `element_team_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `element_team_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `element_slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coordinator_username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `member_usernames` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `element_team_assignments_element_slug_unique` (`element_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element_team_assignments`
--

LOCK TABLES `element_team_assignments` WRITE;
/*!40000 ALTER TABLE `element_team_assignments` DISABLE KEYS */;
INSERT INTO `element_team_assignments` VALUES (5,'element1','dela','[\"bayu\", \"irfan\", \"jose\"]','2026-03-01 21:34:27','2026-03-27 02:30:42'),(6,'element2',NULL,'[\"randy\"]','2026-03-02 01:38:15','2026-03-03 18:30:38');
/*!40000 ALTER TABLE `element_team_assignments` ENABLE KEYS */;
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
-- Table structure for table `general_information_profiles`
--

DROP TABLE IF EXISTS `general_information_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_information_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dasar_hukum_penilaian` text COLLATE utf8mb4_unicode_ci,
  `pemerintah_daerah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_skpd` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bidang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kepala_pemerintah_daerah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `undang_undang_pendirian` text COLLATE utf8mb4_unicode_ci,
  `visi` text COLLATE utf8mb4_unicode_ci,
  `misi` text COLLATE utf8mb4_unicode_ci,
  `inspektur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_kantor` text COLLATE utf8mb4_unicode_ci,
  `jumlah_kantor_wilayah` text COLLATE utf8mb4_unicode_ci,
  `kontak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_information_profiles`
--

LOCK TABLES `general_information_profiles` WRITE;
/*!40000 ALTER TABLE `general_information_profiles` DISABLE KEYS */;
INSERT INTO `general_information_profiles` VALUES (1,'Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintah Daerah','Pemerintah Provinsi DKI Jakarta','Inspektorat Provinsi DKI Jakarta','Pengawasan Internal Pemerintah Daerah','Dr. Ir. Pramono Anung Wibowo, M.M.','Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintah Daerah','Menjadi Lembaga Pengawas Internal Terdepan Di Lingkungan Pemerintah Daerah','- Meningkatkan Sumber Daya Manusia Yang Unggul dan Terpercaya\n- Mengembangkan Sistem Pengawasan Untuk Menjamin Mutu Tata Kelola Pemerintahan Yang Baik\n- Penguatan Instrumen Pengawasan Terkait Tugas dan Fungsi Inspektorat\n- Mewujudkan Lingkungan Kerja Yang Solid dan Kondusif\n- Meningkatkan Pembinaan Terhadap Instansi dan Koordinasi Dengan Stakeholder','Dhany Sukma, S.Sos., M.A.P.','Grha Ali Sadikin Blok G Lt. 17-18\nJl. Medan Merdeka Selatan No. 8-9 Jakarta Pusat\n10110','5 Inspektorat Pembantu Wilayah Kota Administratif dan 1 Inspektorat Pembantu Wilayah Kabupaten Administratif','(021) 3822263 - 3813523','https://inspektorat.jakarta.go.id',NULL,'2026-03-26 20:07:09','2026-03-26 20:07:09');
/*!40000 ALTER TABLE `general_information_profiles` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_02_03_035129_create_accounts_table',1),(5,'2026_02_03_035133_create_notifications_table',1),(6,'2026_02_03_035138_create_dms_documents_table',1),(7,'2026_02_03_035144_create_dms_files_table',1),(8,'2026_02_03_040613_create_sessions_table',2),(9,'2026_02_03_061636_create_element_assessments_table',3),(10,'2026_02_04_000001_create_element1_kegiatan_asurans_table',4),(11,'2026_02_04_000002_add_file_size_to_dms_files_table',4),(12,'2026_02_05_120000_add_missing_columns_to_dms_files_table',4),(13,'2026_02_05_130500_add_login_meta_to_accounts_table',5),(14,'2026_02_10_140000_drop_element_and_subtopic_from_dms_documents',6),(15,'2026_02_13_000001_rename_name_to_title_in_dms_documents_table',6),(16,'2026_02_23_000001_create_element1_kegiatan_asurans_doc_selections_table',7),(17,'2026_02_23_000002_normalize_dms_document_types_and_tags',8),(18,'2026_02_23_000002_create_element1_kegiatan_asurans_row_doc_selections_table',9),(19,'2026_02_24_000001_add_doc_file_ids_to_element1_kegiatan_asurans_table',10),(20,'2026_02_24_000002_drop_element1_kegiatan_asurans_row_doc_selections_table',11),(21,'2026_02_24_000003_create_element1_kegiatan_asurans_edit_logs_table',12),(22,'2026_02_24_000004_drop_element1_kegiatan_asurans_doc_selections_table',13),(23,'2026_02_24_000005_add_level_validation_state_to_element1_kegiatan_asurans_table',14),(24,'2026_02_24_000006_create_element1_jasa_konsultansi_table',15),(25,'2026_02_24_000007_create_element1_jasa_konsultansi_edit_logs_table',15),(26,'2026_03_02_000001_create_element_team_assignments_table',16),(27,'2026_03_02_000002_create_element2_pengembangan_informasi_table',17),(28,'2026_03_02_000003_create_element2_pengembangan_informasi_edit_logs_table',17),(29,'2026_03_02_000004_create_element2_perencanaan_penugasan_table',18),(30,'2026_03_02_000005_create_element2_perencanaan_penugasan_edit_logs_table',18),(31,'2026_03_02_000006_create_element2_pelaksanaan_penugasan_table',19),(32,'2026_03_02_000007_create_element2_pelaksanaan_penugasan_edit_logs_table',19),(33,'2026_03_02_000008_create_element2_komunikasi_hasil_table',20),(34,'2026_03_02_000009_create_element2_komunikasi_hasil_edit_logs_table',20),(35,'2026_03_02_000010_create_element2_pemantauan_tindak_lanjut_table',21),(36,'2026_03_02_000011_create_element2_pemantauan_tindak_lanjut_edit_logs_table',21),(37,'2026_03_02_000012_create_element2_pengendalian_kualitas_table',22),(38,'2026_03_02_000013_create_element2_pengendalian_kualitas_edit_logs_table',22),(39,'2026_03_04_000001_create_element3_perencanaan_pengawasan_table',23),(40,'2026_03_04_000002_create_element3_perencanaan_pengawasan_edit_logs_table',23),(41,'2026_03_04_000003_create_element3_pelaporan_manajemen_kld_table',23),(42,'2026_03_04_000004_create_element3_pelaporan_manajemen_kld_edit_logs_table',23),(43,'2026_03_12_000001_create_element4_manajemen_kinerja_table',24),(44,'2026_03_12_000002_create_element4_manajemen_kinerja_edit_logs_table',24),(45,'2026_03_12_000003_create_element4_mekanisme_pendanaan_table',25),(46,'2026_03_12_000004_create_element4_mekanisme_pendanaan_edit_logs_table',25),(47,'2026_03_12_000005_create_element4_perencanaan_sdm_apip_table',26),(48,'2026_03_12_000006_create_element4_perencanaan_sdm_apip_edit_logs_table',26),(49,'2026_03_12_000007_create_element4_pengembangan_sdm_profesional_apip_table',27),(50,'2026_03_12_000008_create_element4_pengembangan_sdm_profesional_apip_edit_logs_table',28),(51,'2026_03_12_000009_create_element4_dukungan_tik_table',29),(52,'2026_03_12_000010_create_element4_dukungan_tik_edit_logs_table',29),(53,'2026_03_13_000011_add_missing_indexes_to_element4_pengembangan_sdm_profesional_apip_edit_logs_table',30),(54,'2026_03_13_000012_create_element5_pembangunan_budaya_integritas_table',31),(55,'2026_03_13_000013_create_element5_pembangunan_budaya_integritas_edit_logs_table',31),(56,'2026_03_13_000014_create_element5_hubungan_apip_manajemen_table',31),(57,'2026_03_13_000015_create_element5_hubungan_apip_manajemen_edit_logs_table',31),(58,'2026_03_13_000016_create_element5_koordinasi_pengawasan_table',31),(59,'2026_03_13_000017_create_element5_koordinasi_pengawasan_edit_logs_table',31),(60,'2026_03_13_000018_create_element5_akses_informasi_sumberdaya_table',31),(61,'2026_03_13_000019_create_element5_akses_informasi_sumberdaya_edit_logs_table',31),(62,'2026_03_16_000001_create_element_preferences_table',31),(63,'2026_03_25_000001_add_qa_final_verification_to_subtopic_tables',31),(64,'2026_03_25_000002_add_qa_follow_up_recommendation_to_subtopic_tables',31),(65,'2026_03_25_000002_add_qa_level_validation_state_to_subtopic_tables',31),(66,'2026_03_25_000003_create_element_preference_legal_bases_table',31),(67,'2026_03_26_000001_create_general_information_profiles_table',31),(68,'2026_03_27_000001_create_element_progress_archives_table',32),(69,'2026_03_30_120000_add_scope_columns_to_notifications_table',33),(70,'2026_03_30_130000_create_notification_reads_table',34),(71,'2026_03_30_130100_add_performance_indexes_to_notifications_table',34);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_reads`
--

DROP TABLE IF EXISTS `notification_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_reads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_id` bigint unsigned NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_reads_notification_user_unique` (`notification_id`,`username`),
  KEY `notification_reads_username_read_at_index` (`username`,`read_at`),
  KEY `notification_reads_notification_read_at_index` (`notification_id`,`read_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_reads`
--

LOCK TABLES `notification_reads` WRITE;
/*!40000 ALTER TABLE `notification_reads` DISABLE KEYS */;
INSERT INTO `notification_reads` VALUES (1,63,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(2,62,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(3,61,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(4,60,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(5,59,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(6,58,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(7,57,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(8,56,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(9,55,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(10,54,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(11,53,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(12,52,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(13,51,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(14,50,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(15,49,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(16,48,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(17,47,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(18,46,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(19,45,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(20,44,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(21,43,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(22,42,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(23,41,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(24,40,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(25,39,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(26,38,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(27,37,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(28,36,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(29,35,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(30,34,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(31,33,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(32,32,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(33,31,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(34,30,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(35,29,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(36,28,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(37,27,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(38,26,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(39,25,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(40,24,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(41,23,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(42,22,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(43,21,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(44,20,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(45,19,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(46,18,'admin','2026-03-30 02:37:17','2026-03-30 00:23:51','2026-03-30 02:37:17'),(47,17,'admin','2026-03-30 02:34:51','2026-03-30 00:23:51','2026-03-30 02:34:51'),(48,16,'admin','2026-03-30 02:34:07','2026-03-30 00:23:51','2026-03-30 02:34:07'),(49,15,'admin','2026-03-30 01:39:02','2026-03-30 00:23:51','2026-03-30 01:39:02'),(50,14,'admin','2026-03-30 01:39:02','2026-03-30 00:23:51','2026-03-30 01:39:02'),(151,13,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(152,12,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(153,11,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(154,10,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(155,9,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(156,8,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(157,7,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(158,6,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(159,5,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(160,4,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(161,3,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(162,2,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(163,1,'admin','2026-03-30 01:39:02','2026-03-30 01:21:28','2026-03-30 01:39:02'),(193,64,'admin','2026-03-30 02:37:17','2026-03-30 01:21:46','2026-03-30 02:37:17'),(795,65,'admin','2026-03-30 02:37:17','2026-03-30 01:38:33','2026-03-30 02:37:17'),(1083,66,'admin','2026-03-30 02:37:17','2026-03-30 02:34:51','2026-03-30 02:37:17'),(1133,67,'admin','2026-03-30 02:37:17','2026-03-30 02:37:17','2026-03-30 02:37:17');
/*!40000 ALTER TABLE `notification_reads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `subtopic_title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `statement` text COLLATE utf8mb4_general_ci,
  `row_id` int DEFAULT NULL,
  `element_slug` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subtopic_slug` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `coordinator_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `coordinator_username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notifications_element_slug_index` (`element_slug`),
  KEY `notifications_subtopic_slug_index` (`subtopic_slug`),
  KEY `notifications_element_created_at_index` (`element_slug`,`created_at`),
  KEY `notifications_subtopic_created_at_index` (`subtopic_slug`,`created_at`),
  KEY `notifications_actor_created_at_index` (`coordinator_username`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('5PGJz70BpwVsOZE28NELUDjdhiIAoqFc1otgcyaX',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSVFmWXc3MmxtOWR6NFBGSFMzc3c4am5UVng1bnpZZ3JqdTZ5ejN5bCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3QvaW5mb3JtYXNpLXVtdW0iO3M6NToicm91dGUiO3M6MjA6ImluZm9ybWFzaS11bXVtLmluZGV4Ijt9czo0OiJ1c2VyIjthOjY6e3M6MjoiaWQiO2k6MTtzOjg6InVzZXJuYW1lIjtzOjU6ImFkbWluIjtzOjEyOiJkaXNwbGF5X25hbWUiO3M6MTA6IlN1YmJhZyBQUEsiO3M6NDoicm9sZSI7czoxMzoiYWRtaW5pc3RyYXRvciI7czoxMDoicm9sZV9sYWJlbCI7czoxMzoiQWRtaW5pc3RyYXRvciI7czoxMzoicHJvZmlsZV9waG90byI7czo1MjoicHJvZmlsZS9YTHRoRnU2ZERhSGhRRWRYT1B5cjFHOHl6QTNWVXJkRnhRQ1lBdzkxLnBuZyI7fXM6MTY6Imxhc3RfYWN0aXZpdHlfYXQiO2k6MTc3NTAxODc0Mjt9',1775018742);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'latsar'
--

--
-- Dumping routines for database 'latsar'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-01 11:45:46
