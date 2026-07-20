/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.5.29-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: db_berita
-- ------------------------------------------------------
-- Server version	10.5.29-MariaDB-0+deb11u1

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
-- Table structure for table `admin_sessions`
--

DROP TABLE IF EXISTS `admin_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `idx_session_token` (`session_token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `admin_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `penulis` (`id_penulis`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_sessions`
--

LOCK TABLES `admin_sessions` WRITE;
/*!40000 ALTER TABLE `admin_sessions` DISABLE KEYS */;
INSERT INTO `admin_sessions` VALUES (1,1,'sess_b8cc0af40bd7983589cd3a190cacf2b5b616374ba61ed7ecd99d9c474ebe146f_1769746698','2026-01-30 13:18:18','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 04:18:18','2026-01-30 04:18:19'),(2,1,'sess_2ff9fe439f4d1e44f26a664c00a9493073609b4641afafe86afeb5aa9eba9f79_1770003925','2026-02-02 12:45:25','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 03:45:25','2026-02-02 03:45:25'),(3,1,'sess_1761e2078f94ac12f4a81532cae65339b32ab0b02d2f00daf88b7bce34a775bb_1770004825','2026-02-02 13:00:25','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 04:00:25','2026-02-02 05:55:27'),(4,1,'sess_b16a971ea4dcef0087e048fe6288a74d4516759d2e0b5bd9cd8852b9eda96a1b_1770012652','2026-02-02 15:10:52','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 06:10:52','2026-02-02 06:30:54'),(5,1,'sess_4722af63cab45b480d06965c69036afc481eb38668e274c41e8e41a9bd603319_1770013887','2026-02-02 15:31:27','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 06:31:27','2026-02-02 06:31:27'),(6,1,'sess_8443b7d2c164c7b2bbc2e230a7c23a6264c1ab275469c0090885a534625116aa_1770013894','2026-02-02 15:31:34','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 06:31:34','2026-02-02 06:36:36'),(7,1,'sess_77f73748f79792639dc7be7e4e0025dfa54561256af516f857e426d0b9d4ff93_1770015276','2026-02-02 15:54:36','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 06:54:36','2026-02-02 07:04:39'),(8,1,'sess_3efcb24b554b133ff051d8a299d66d816f8f1dbc707fd4f03d90a861bbe878c6_1770016868','2026-02-02 16:21:08','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 07:21:08','2026-02-02 07:21:08'),(9,1,'sess_966ee12ed8835d424aae1eec5450401af107cb8a955b63c069b01189ff634241_1770016873','2026-02-02 16:21:13','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 07:21:13','2026-02-02 07:21:13'),(10,1,'sess_6cd1ef8b5f369b84e34a687e06ecf7e1108bf07e3abd9994e20aa6eb4d034266_1770016879','2026-02-02 16:21:19','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 07:21:19','2026-02-02 08:57:57'),(11,1,'sess_996324772be6cd917c8597bd2873c4a828a432d8a311c0b9feb972e6c0cd8c6c_1770078561','2026-02-03 09:29:21','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-03 00:29:21','2026-02-03 00:29:21'),(12,1,'sess_0ab9ed503a27cc1b8315cdfd307d0502ec94c9f8782a471ddfcafdf9b2726e89_1770078565','2026-02-03 09:29:25','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-03 00:29:25','2026-02-03 00:29:25'),(13,1,'sess_e4d529335910a66d99b70ff0bfdfd7f802214a3d655ff802ebbda2bd93bd3ddc_1770078570','2026-02-03 09:29:30','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-03 00:29:30','2026-02-03 00:44:32'),(14,1,'sess_d249a46df9d2755d16bbac88cd51da7b3eddd8540836ed5574152e58a45035de_1770181912','2026-02-04 14:11:52','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 05:11:52','2026-02-04 05:16:25'),(15,1,'sess_d9e9faf3c26fb470bc4679dd07e28b02e8cc8a477ac7db9adfef2a41828fdc75_1770247740','2026-02-05 08:29:00','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 23:29:00','2026-02-04 23:29:00'),(16,1,'sess_77d35a8e49fea68ebf962f12ffbb4614130190681a61c2975d11ec2d59b41c8f_1770247746','2026-02-05 08:29:06','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 23:29:06','2026-02-04 23:47:41'),(17,1,'sess_3e364f2e14573b573ae8e1a08730b6ce0c96dc0eaf7c035202de57aa32eff05f_1770248876','2026-02-05 08:47:56','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 23:47:56','2026-02-04 23:47:56'),(20,1,'sess_e030ea5461d198ff8177645c5df814a5894cdf33bbb23482a5042bfb940dd59f_1770256617','2026-02-05 10:56:57','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 01:56:57','2026-02-05 02:40:26'),(21,1,'sess_d8d5277d5aa88012f9dfa62174ae942c05c0e9f18bc8663af4b1c7e57acf264e_1770259254','2026-02-05 11:40:54','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 02:40:54','2026-02-05 02:40:54'),(23,1,'sess_8aa6fe8cdb9e78691b7ee40c4aaa8364fca6f7dc532409425977e726cd877e6d_1770263813','2026-02-05 12:56:53','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 03:56:53','2026-02-05 03:56:53'),(24,10,'sess_a3c8280c9c162257d7e2403bf3c3e5efbc299236db7cfecee4a81b0f60aaa25b_1770276062','2026-02-05 16:21:02','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 07:21:02','2026-02-05 07:21:02'),(25,1,'sess_3b101aaa86566330943be04f71c7f1a85451722acfa67e6bbdad82b8cb44e9e0_1770350043','2026-02-06 12:54:03','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 03:54:03','2026-02-06 03:54:03'),(26,1,'sess_d8df0ce88c0ebd194e0a7f2854050145aa558ea65ad206cd4e3b644398b668b1_1770350554','2026-02-06 13:02:34','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 04:02:34','2026-02-06 04:02:34'),(27,5,'sess_4b398c55473a4d814edf936d5352589795160e9ea83eb2fcfa1654257cee079d_1770350658','2026-02-06 13:04:18','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 04:04:18','2026-02-06 04:04:18'),(28,1,'sess_0706da1a089e400e9add8ada66d7aae8a5662e869c7546b2367480217d844d65_1770351049','2026-02-06 13:10:49','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 04:10:49','2026-02-06 04:10:49'),(29,1,'sess_e4cb821f857fad8e392e434de8e7594e65b90f98b29481c0c063e4bb302a8a50_1770434121','2026-02-07 12:15:21','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-07 03:15:21','2026-02-07 03:15:21'),(30,1,'sess_634caef04cd015d7afa5c8c3eaf0d53c8e285a9256d4a3ca126ad38dc93108ac_1770439228','2026-02-07 13:40:28','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-07 04:40:28','2026-02-07 04:40:28');
/*!40000 ALTER TABLE `admin_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berita`
--

DROP TABLE IF EXISTS `berita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `berita` (
  `id_berita` int(11) NOT NULL AUTO_INCREMENT,
  `id_kategori` int(11) DEFAULT NULL,
  `id_penulis` int(11) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `ringkasan` text DEFAULT NULL,
  `isi_berita` longtext NOT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL,
  `alt_gambar` varchar(255) DEFAULT NULL,
  `meta_description` varchar(160) DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `tanggal_publish` datetime DEFAULT NULL,
  `status` enum('draft','publish','archived') DEFAULT 'draft',
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_berita`),
  UNIQUE KEY `slug` (`slug`),
  KEY `id_penulis` (`id_penulis`),
  KEY `idx_status` (`status`),
  KEY `idx_tanggal_publish` (`tanggal_publish`),
  KEY `idx_kategori` (`id_kategori`),
  KEY `idx_featured` (`featured`),
  KEY `idx_status_publish` (`status`,`tanggal_publish`),
  KEY `idx_featured_status` (`featured`,`status`),
  KEY `idx_slug` (`slug`),
  CONSTRAINT `berita_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL,
  CONSTRAINT `berita_ibfk_2` FOREIGN KEY (`id_penulis`) REFERENCES `penulis` (`id_penulis`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berita`
--

LOCK TABLES `berita` WRITE;
/*!40000 ALTER TABLE `berita` DISABLE KEYS */;
INSERT INTO `berita` VALUES (1,1,1,'Gempa Bumi Magnitudo 5.2 Guncang Jawa Barat','gempa-bumi-magnitudo-52-guncang-jawa-barat','Gempa bumi dengan magnitudo 5.2 mengguncang wilayah Jawa Barat pada pagi hari ini.','<p>Badan Meteorologi, Klimatologi, dan Geofisika (BMKG) melaporkan terjadinya gempa bumi dengan magnitudo 5.2 yang mengguncang wilayah Jawa Barat pada pukul 08:30 WIB.</p><p>Pusat gempa berada di koordinat 6.85 LS dan 107.12 BT dengan kedalaman 10 km. Gempa ini tidak berpotensi tsunami.</p>','news_6987e2751b5ee.jpg',NULL,'Ilustrasi gempa bumi di Jawa Barat','gempa bumi, jawa barat, bmkg, magnitudo 5.2',31,'2026-01-29 15:39:26','publish',0,'2026-01-29 08:39:26','2026-02-11 06:21:14'),(7,7,1,'Hilal Awal Ramadhan 1447 Hijriah Tahun 2026','hilal-awal-ramadhan-1447-hijriah-tahun-2026',NULL,'Hilal Awal Ramadhan 1447 Hijriah sebagai penentu awal bulan Hijriyah ini sangat penting bagi umat Islam karena berhubungan dengan waktu ibadah, terutama bulan Ramadan, Syawal, dan Zulhijah.\nBadan Meteorologi Klimatologi dan Geofisika (BMKG) sebagai institusi pemerintah yang salah satu tugas pokok dan fungsinya adalah memberikan pelayanan tanda waktu dan posisi bulan dan matahari. BMKG memberikan pertimbangan secara ilmiah kepada stake holder (Kementerian Agama, dll) dalam penentuan awal bulan hijriyah. Disamping memberikan informasi data-data Hilal hasil hisab (perhitungan), BMKG juga melaksanakan rukyat (observasi) hilal di 37 lokasi di Indonesia yang dapat disaksikan secara online (Live Streaming) di kanal https://hilal.bmkg.go.id/ setiap bulan.\nPada Gambar ditampilkan peta ketinggian Hilal untuk pengamat di 60o LU sampai dengan 60o LS saat Matahari terbenam di masing-masing lokasi pengamat di permukaan Bumi pada tanggal 17 dan 18 Februari 2026. Pada peta tersebut, tinggi Hilal adalah besar sudut yang dinyatakan dari posisi proyeksi Bulan di Horizon-teramati hingga ke posisi pusat piringan Bulan berada. Tinggi Hilal positif berarti Hilal berada di atas horizon pada saat Matahari terbenam. Adapun tinggi Hilal negatif berarti Hilal berada di bawah horizon pada saat Matahari terbenam. Pada Gambar ditampilkan peta ketinggian Hilal saat matahari terbenam untuk pengamat di Indonesia pada tanggal 17 dan 18 Februari 2026.','news_6987e24d1d80c.png',NULL,NULL,NULL,42,'2026-02-05 07:55:45','publish',0,'2026-02-05 00:55:45','2026-02-11 12:11:25'),(8,1,1,'Sekolah Lapang Gempabumi dan Tsunami (SLG) Desa Awang Tahun 2025','sekolah-lapang-gempabumi-dan-tsunami-slg-desa-awang-tahun-2025',NULL,'Sekolah Lapang Gempabumi dan Tsunami (SLG) Desa Awang, Kecamatan Pujut, Kabupaten\nLombok Tengah, dilaksanakan pada 14 Juni 2025 oleh Stasiun Geofisika Mataram BMKG\nsebagai upaya mitigasi bencana dan peningkatan kesiapsiagaan masyarakat terhadap ancaman\ngempabumi dan tsunami. Kegiatan ini juga menjadi bagian dari persiapan Desa Awang menuju\nTsunami Ready Community yang diakui oleh UNESCO-IOC.\nKegiatan SLG diikuti oleh 51 peserta yang terdiri dari unsur BPBD, pemerintah desa,\nTNI/Polri, sekolah, media, dan masyarakat. Rangkaian kegiatan meliputi pembukaan, pre-test,\npemaparan materi, gladi ruang (Table Top Exercise), post-test, susur jalur evakuasi tsunami,\npenyusunan rekomendasi, dan penutupan.\nSLG menghasilkan rekomendasi Masyarakat Siaga Tsunami Desa Awang yang mencakup\npenguatan pemahaman peta bahaya tsunami, pembaruan data penduduk dan peta evakuasi,\npenguatan sistem peringatan dini, peningkatan sarana prasarana evakuasi, edukasi\nberkelanjutan, serta penguatan kearifan lokal dalam mitigasi bencana.\nSecara keseluruhan, kegiatan SLG Desa Awang tahun 2025 berhasil meningkatkan kapasitas\ndan kesiapsiagaan masyarakat serta memperkuat koordinasi antar pemangku kepentingan\ndalam rantai peringatan dini gempabumi dan tsunami','news_698bc8dc7b54d.jpg',NULL,NULL,NULL,25,'2026-02-06 11:17:01','publish',1,'2026-02-06 04:17:01','2026-02-11 12:04:17');
/*!40000 ALTER TABLE `berita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `slug_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `slug_kategori` (`slug_kategori`),
  KEY `idx_slug_kategori` (`slug_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori`
--

LOCK TABLES `kategori` WRITE;
/*!40000 ALTER TABLE `kategori` DISABLE KEYS */;
INSERT INTO `kategori` VALUES (1,'Gempa Bumi','gempa-bumi','Berita terkait aktivitas gempa bumi di Indonesia','2026-01-29 08:39:26','2026-01-29 08:39:26'),(2,'Cuaca','cuaca','Informasi prakiraan cuaca dan iklim','2026-01-29 08:39:26','2026-01-29 08:39:26'),(3,'Tsunami','tsunami','Peringatan dan informasi tsunami','2026-01-29 08:39:26','2026-01-29 08:39:26'),(4,'Teknologi','teknologi','Berita teknologi meteorologi dan geofisika','2026-01-29 08:39:26','2026-01-29 08:39:26'),(5,'Edukasi','edukasi','Artikel edukatif tentang fenomena alam','2026-01-29 08:39:26','2026-01-29 08:39:26'),(7,'Tanda Waktu','tanda-waktu','Informasi Tanda waktu BMKG','2026-02-02 06:55:09','2026-02-02 06:55:09'),(8,'Mitigasi','mitigasi','Informasi Mitigasi Bencana dari BMKG','2026-02-02 06:55:42','2026-02-02 06:55:42'),(9,'Magnet Bumi','magnet-bumi','Informasi magnet bumi BMKG','2026-02-02 06:56:23','2026-02-02 06:56:23'),(10,'Petir','petir','Informasi Petir BMKG','2026-02-02 06:56:47','2026-02-02 06:56:47');
/*!40000 ALTER TABLE `kategori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `komentar`
--

DROP TABLE IF EXISTS `komentar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `komentar` (
  `id_komentar` int(11) NOT NULL AUTO_INCREMENT,
  `id_berita` int(11) NOT NULL,
  `nama_pengunjung` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `isi_komentar` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `tanggal_komentar` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id_komentar`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `komentar`
--

LOCK TABLES `komentar` WRITE;
/*!40000 ALTER TABLE `komentar` DISABLE KEYS */;
INSERT INTO `komentar` VALUES (1,1,'Ahmad Wijaya','ahmad@email.com','Terima kasih atas informasi gempa bumi yang sangat berguna.','approved','2026-02-05 04:09:07',NULL,NULL),(2,1,'Siti Nurhaliza','siti@email.com','Apakah ada kemungkinan gempa susulan?','pending','2026-02-05 04:09:07',NULL,NULL),(3,2,'Budi Santoso','budi@email.com','Prakiraan cuaca sangat membantu.','approved','2026-02-05 04:09:07',NULL,NULL);
/*!40000 ALTER TABLE `komentar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_token` (`token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `penulis` (`id_penulis`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penulis`
--

DROP TABLE IF EXISTS `penulis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `penulis` (
  `id_penulis` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `role_id` int(11) DEFAULT 2,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_penulis`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `penulis_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penulis`
--

LOCK TABLES `penulis` WRITE;
/*!40000 ALTER TABLE `penulis` DISABLE KEYS */;
INSERT INTO `penulis` VALUES (1,'Super Admin','admin','admin@bmkg.go.id','$2y$10$WThaR80bZs9KMvA9ennv6.ZEToEaw2QYCddbQYL4AY7H4e4Qnh7pm',NULL,'Super Administrator sistem berita BMKG',1,'aktif','2026-01-29 08:39:26','2026-02-07 04:40:28'),(2,'Admin BMKG','bmkg_admin','bmkg_admin@bmkg.go.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,'Administrator sistem berita BMKG',2,'aktif','2026-01-29 08:39:26','2026-01-29 08:39:26'),(3,'Editor BMKG','editor','editor@bmkg.go.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,'Editor berita BMKG',3,'aktif','2026-01-29 08:39:26','2026-01-29 08:39:26'),(4,'Tim Redaksi','redaksi','redaksi@bmkg.go.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,'Tim redaksi berita BMKG',3,'aktif','2026-01-29 08:39:26','2026-01-29 08:39:26'),(5,'Super Administrator BMKG','superadmin','superadmin@bmkg.go.id','$argon2id$v=19$m=19456,t=2,p=1$YzZmYzgxZDUwOWZhMzQ2NTk0ZGJjMDQ3Njg2YzdjMjg$Nj6EhiphFmTEoHvd5o/S5zNyxbl8o4Cx7XmaNVRQl54',NULL,'Super Administrator dengan akses penuh ke seluruh sistem',1,'aktif','2026-02-05 07:07:47','2026-02-06 04:04:18'),(7,'Nama Lengkap User','username','email@bmkg.go.id','PASSWORD_HASH',NULL,'Bio user',2,'aktif','2026-02-05 07:08:31','2026-02-05 07:08:31'),(10,'User BMKG','user123','useradmin@bmkg.go.id','$argon2id$v=19$m=19456,t=2,p=1$YzZmYzgxZDUwOWZhMzQ2NTk0ZGJjMDQ3Njg2YzdjMjg$Nj6EhiphFmTEoHvd5o/S5zNyxbl8o4Cx7XmaNVRQl54',NULL,'user',4,'aktif','2026-02-05 07:20:19','2026-02-05 07:21:02');
/*!40000 ALTER TABLE `penulis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_logs`
--

DROP TABLE IF EXISTS `security_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(50) NOT NULL,
  `event_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_logs`
--

LOCK TABLES `security_logs` WRITE;
/*!40000 ALTER TABLE `security_logs` DISABLE KEYS */;
INSERT INTO `security_logs` VALUES (1,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":1}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 00:17:40'),(2,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":2}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 00:17:42'),(3,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":3}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 00:17:43'),(4,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":4}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 00:30:42'),(5,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":5}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 00:30:48'),(6,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":1}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 00:48:44'),(7,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":1}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 03:25:21'),(8,'login_failed','{\"username\":\"bmkg_admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":1}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 03:25:27'),(9,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":2}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 03:31:59'),(10,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":1}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 04:03:08'),(11,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":2}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 04:11:31'),(12,'login_failed','{\"username\":\"admin\",\"ip_address\":\"10.21.223.43\",\"attempts\":3}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 04:14:55'),(13,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-30 04:18:18'),(14,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 03:45:25'),(15,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 04:00:25'),(16,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 06:10:52'),(17,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 06:31:27'),(18,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 06:31:34'),(19,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 06:54:36'),(20,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 07:21:08'),(21,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 07:21:13'),(22,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-02 07:21:19'),(23,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-03 00:29:21'),(24,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-03 00:29:25'),(25,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-03 00:29:30'),(26,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 05:11:52'),(27,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 23:29:00'),(28,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 23:29:06'),(29,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 23:47:56'),(30,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 23:48:01'),(31,'logout','{\"user_id\":\"1\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 00:50:41'),(32,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 00:54:58'),(33,'logout','{\"user_id\":\"1\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 01:55:02'),(34,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 01:56:57'),(35,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 02:40:54'),(36,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 02:40:59'),(37,'logout','{\"user_id\":\"1\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 03:41:41'),(38,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 03:56:53'),(39,'login_failed','{\"username\":\"moderator\",\"ip_address\":\"10.21.223.43\",\"attempts\":1}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 07:12:51'),(40,'login_success','{\"user_id\":\"10\",\"username\":\"user123\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 07:21:02'),(41,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 03:54:03'),(42,'login_failed','{\"username\":\"bmkg_user\",\"ip_address\":\"10.21.223.43\",\"attempts\":1}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 03:54:36'),(43,'login_failed','{\"username\":\"superadmin\",\"ip_address\":\"10.21.223.43\",\"attempts\":1}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 04:02:26'),(44,'login_failed','{\"username\":\"superadmin\",\"ip_address\":\"10.21.223.43\",\"attempts\":2}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 04:02:28'),(45,'login_failed','{\"username\":\"superadmin\",\"ip_address\":\"10.21.223.43\",\"attempts\":3}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 04:02:29'),(46,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 04:02:34'),(47,'login_success','{\"user_id\":\"5\",\"username\":\"superadmin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 04:04:18'),(48,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 04:10:49'),(49,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-07 03:15:21'),(50,'login_success','{\"user_id\":\"1\",\"username\":\"admin\",\"ip_address\":\"10.21.223.43\"}','10.21.223.43','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-07 04:40:28');
/*!40000 ALTER TABLE `security_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,'super_admin','[\"read\", \"write\", \"delete\", \"manage_users\", \"manage_settings\", \"view_logs\"]','Super Administrator dengan akses penuh','2026-01-29 08:39:26'),(2,'admin','[\"read\", \"write\", \"delete\", \"moderate_comments\"]','Administrator dengan akses manajemen konten','2026-01-29 08:39:26'),(3,'editor','[\"read\", \"write\", \"moderate_comments\"]','Editor yang dapat menulis dan mengedit berita','2026-01-29 08:39:26'),(4,'viewer','[\"read\"]','Hanya dapat melihat konten','2026-01-29 08:39:26');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-11 19:14:45
