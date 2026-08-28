-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 23, 2026 at 08:48 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sig_lahan_pertanian`
--

-- --------------------------------------------------------

--
-- Table structure for table `kabupaten`
--

CREATE TABLE `kabupaten` (
  `id` int NOT NULL,
  `id_provinsi` int DEFAULT NULL,
  `nama_kabupaten` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kabupaten`
--

INSERT INTO `kabupaten` (`id`, `id_provinsi`, `nama_kabupaten`) VALUES
(1, 1, 'Teluk Bintuni'),
(2, 2, 'Ambon');

-- --------------------------------------------------------

--
-- Table structure for table `kecamatan`
--

CREATE TABLE `kecamatan` (
  `id` int NOT NULL,
  `id_kabupaten` int DEFAULT NULL,
  `nama_kecamatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kecamatan`
--

INSERT INTO `kecamatan` (`id`, `id_kabupaten`, `nama_kecamatan`) VALUES
(1, 1, 'Moskona Selatan'),
(2, 1, 'Moskona'),
(3, 1, 'Babo');

-- --------------------------------------------------------

--
-- Table structure for table `komoditas`
--

CREATE TABLE `komoditas` (
  `id` int NOT NULL,
  `nama_komoditas` varchar(100) NOT NULL,
  `deskripsi` text,
  `warna_polygon` varchar(20) DEFAULT '#3388ff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `komoditas`
--

INSERT INTO `komoditas` (`id`, `nama_komoditas`, `deskripsi`, `warna_polygon`) VALUES
(1, 'Padi', 'Tanaman pangan utama', '#00ff00'),
(2, 'Jagung', 'Tanaman palawija', '#ffff00'),
(3, 'Kacang Tanah', 'Tanaman kacang-kacangan', '#8b4513');

-- --------------------------------------------------------

--
-- Table structure for table `lahan`
--

CREATE TABLE `lahan` (
  `id` int NOT NULL,
  `kode_lahan` varchar(50) DEFAULT NULL,
  `nama_pemilik` varchar(100) NOT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `luas` decimal(10,2) NOT NULL COMMENT 'Luas dalam Hektar',
  `id_komoditas` int DEFAULT NULL,
  `geojson` longtext NOT NULL COMMENT 'Data spasial polygon',
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lahan`
--

INSERT INTO `lahan` (`id`, `kode_lahan`, `nama_pemilik`, `kecamatan`, `luas`, `id_komoditas`, `geojson`, `keterangan`, `created_at`) VALUES
(3, NULL, 'Budi', NULL, 5.00, 3, '{\"type\":\"Polygon\",\"coordinates\":[[[131.271256,-0.869926],[131.271272,-0.870014],[131.271304,-0.870111],[131.271299,-0.87018],[131.271323,-0.87029],[131.271307,-0.87039],[131.271304,-0.870475],[131.271358,-0.870564],[131.271396,-0.870652],[131.271398,-0.870762],[131.271425,-0.870797],[131.271572,-0.8708],[131.271656,-0.870711],[131.271808,-0.870639],[131.271841,-0.87054],[131.271875,-0.870416],[131.27187,-0.870309],[131.271841,-0.870205],[131.271833,-0.870111],[131.271723,-0.870076],[131.271637,-0.869993],[131.27157,-0.869936],[131.271473,-0.869899],[131.271256,-0.869926]]]}', 'lahan ini punya perusahaan', '2026-08-05 16:19:06'),
(4, 'LHN-002', 'Lembah Agro', 'Moskona', 5.00, 2, '{\"type\":\"Polygon\",\"coordinates\":[[[128.252893,-3.637639],[128.25279,-3.637569],[128.252935,-3.637605],[128.252889,-3.637594],[128.252862,-3.637641],[128.252922,-3.637641],[128.252965,-3.63762],[128.253012,-3.637621],[128.253036,-3.63766],[128.253045,-3.637706],[128.253048,-3.637755],[128.253071,-3.637799],[128.253104,-3.637832],[128.253056,-3.63786],[128.25301,-3.637863],[128.252972,-3.637835],[128.252944,-3.637797],[128.252911,-3.637764],[128.252873,-3.637734],[128.252846,-3.637696],[128.25284,-3.637651],[128.252874,-3.637621],[128.252893,-3.637639]]]}', 'Untuk perusahaan ', '2026-08-22 06:14:23'),
(5, 'LHN-001', 'BRAM', 'Babo', 5.00, 1, '{\"type\":\"Polygon\",\"coordinates\":[[[133.185539,-1.900063],[133.285033,-1.940552],[133.185539,-2.000941],[133.185539,-1.900063]]]}', 'pribadi', '2026-08-22 06:15:38'),
(6, 'LHN-001', 'BRAM', 'Babo', 5.00, 1, '{\"type\":\"Polygon\",\"coordinates\":[[[133.185539,-1.900063],[133.285033,-1.940552],[133.185539,-2.000941],[133.185539,-1.900063]]]}', 'pribadi', '2026-08-22 06:24:27');

-- --------------------------------------------------------

--
-- Table structure for table `provinsi`
--

CREATE TABLE `provinsi` (
  `id` int NOT NULL,
  `nama_provinsi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `provinsi`
--

INSERT INTO `provinsi` (`id`, `nama_provinsi`) VALUES
(1, 'Papua Barat'),
(2, 'Maluku');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','petugas') NOT NULL DEFAULT 'petugas',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$I2U0svd48UQorgApUVvJ0uNPxYJhK6TaRl4DGhD2QPvm7Shy2PeTu', 'Administrator', 'admin', '2026-08-04 04:40:40'),
(11, 'petugas', '$2y$10$jMTHep5JRC2fPqZlYFdjfOmC7cTWi2IBko4ngO7fb6y6iNKBY5Bf.', 'Petugas Lapangan', 'petugas', '2026-08-22 07:48:37'),
(12, 'Bram', '$2y$10$5zZMp587ueDVbEk906KAxe52P7mWKYhpnBJTltCG5mN/kruknxRcy', 'Bram petra', 'petugas', '2026-08-22 08:05:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kabupaten`
--
ALTER TABLE `kabupaten`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_provinsi` (`id_provinsi`);

--
-- Indexes for table `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kabupaten` (`id_kabupaten`);

--
-- Indexes for table `komoditas`
--
ALTER TABLE `komoditas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lahan`
--
ALTER TABLE `lahan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_komoditas` (`id_komoditas`);

--
-- Indexes for table `provinsi`
--
ALTER TABLE `provinsi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kabupaten`
--
ALTER TABLE `kabupaten`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kecamatan`
--
ALTER TABLE `kecamatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `komoditas`
--
ALTER TABLE `komoditas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lahan`
--
ALTER TABLE `lahan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `provinsi`
--
ALTER TABLE `provinsi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kabupaten`
--
ALTER TABLE `kabupaten`
  ADD CONSTRAINT `kabupaten_ibfk_1` FOREIGN KEY (`id_provinsi`) REFERENCES `provinsi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD CONSTRAINT `kecamatan_ibfk_1` FOREIGN KEY (`id_kabupaten`) REFERENCES `kabupaten` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lahan`
--
ALTER TABLE `lahan`
  ADD CONSTRAINT `lahan_ibfk_1` FOREIGN KEY (`id_komoditas`) REFERENCES `komoditas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
