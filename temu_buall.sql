-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 08:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `temu_buall`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_saat_pesan` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_menu`, `jumlah`, `harga_saat_pesan`) VALUES
(1, 1, 64, 7, 2000.00),
(2, 2, 64, 1, 2000.00),
(3, 2, 66, 1, 8000.00),
(4, 2, 67, 1, 12000.00),
(5, 3, 64, 1, 2000.00),
(6, 3, 66, 1, 8000.00),
(7, 4, 65, 1, 8000.00),
(8, 5, 67, 1, 12000.00),
(9, 6, 64, 1, 2000.00),
(10, 7, 70, 1, 10000.00),
(11, 8, 69, 1, 8000.00),
(12, 9, 71, 1, 8000.00),
(13, 10, 64, 1, 2000.00),
(14, 11, 64, 1, 2000.00),
(15, 11, 66, 1, 8000.00),
(16, 12, 68, 1, 6000.00),
(17, 13, 66, 1, 8000.00),
(18, 14, 67, 1, 12000.00),
(19, 15, 71, 1, 8000.00),
(20, 16, 64, 1, 2000.00),
(21, 17, 65, 1, 8000.00),
(22, 18, 65, 1, 8000.00),
(23, 19, 64, 9, 2000.00),
(24, 20, 65, 10, 8000.00),
(25, 21, 64, 9, 2000.00),
(26, 22, 65, 10, 8000.00);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `harga` int(8) DEFAULT NULL,
  `kategori` varchar(35) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `status_aktif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `nama`, `harga`, `kategori`, `gambar`, `stok`, `status_aktif`) VALUES
(64, 'es kosong', 2000, 'MINUMAN', 'es.jpg', 90, 1),
(65, 'teh obeng', 8000, 'MINUMAN', 'es.jpg', 90, 1),
(66, 'teh tarik hangat', 8000, 'MINUMAN', 'es.jpg', 50, 1),
(67, 'teh tarik dingin', 12000, 'MINUMAN', 'es.jpg', 0, 1),
(68, 'teh hangat', 6000, 'MINUMAN', 'es.jpg', 0, 1),
(69, 'kopi hangat', 8000, 'MINUMAN', 'es.jpg', 0, 1),
(70, 'kopi dingin', 10000, 'MINUMAN', 'es.jpg', 0, 1),
(71, 'lemon tea hangat', 8000, 'MINUMAN', 'es.jpg', 0, 1),
(72, 'lemon tea dingin', 10000, 'MINUMAN', 'es.jpg', 0, 1),
(73, 'extrajos susu', 12000, 'MINUMAN', 'es.jpg', 0, 1),
(74, 'kuku bima susu', 12000, 'MINUMAN', 'es.jpg', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nomor_meja` varchar(10) NOT NULL,
  `tanggal_pesanan` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_pesanan` varchar(50) DEFAULT 'Diproses'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `user_id`, `nomor_meja`, `tanggal_pesanan`, `status_pesanan`) VALUES
(1, 57, 'meja 1', '2025-06-10 12:23:52', 'Selesai'),
(2, 57, 'meja 2', '2025-06-10 12:35:40', 'Selesai'),
(3, 57, 'meja 3', '2025-06-10 14:23:45', 'Diproses'),
(4, 57, 'meja 3', '2025-06-10 14:24:37', 'Diproses'),
(5, 57, 'meja 3', '2025-06-10 14:25:32', 'Diproses'),
(6, 57, 'meja 8', '2025-06-10 14:33:14', 'Diproses'),
(7, 57, 'meja 6', '2025-06-10 14:34:37', 'Diproses'),
(8, 57, 'meja 7', '2025-06-10 14:37:25', 'Diproses'),
(9, 57, 'meja 2', '2025-06-10 14:42:12', 'Selesai'),
(10, 57, 'meja 5', '2025-06-10 14:49:49', 'Selesai'),
(11, 57, 'meja 7', '2025-06-10 14:55:31', 'Selesai'),
(12, 57, 'meja 4', '2025-06-10 15:00:45', 'Diproses'),
(13, 57, 'meja 4', '2025-06-10 15:05:25', 'Diproses'),
(14, 57, 'meja 5', '2025-06-10 15:06:18', 'Diproses'),
(15, 57, 'meja 1', '2025-06-10 15:12:25', 'Diproses'),
(16, 57, 'meja 1', '2025-06-10 15:15:48', 'Diproses'),
(17, 57, 'meja 10', '2025-06-10 15:18:38', 'Diproses'),
(18, 58, 'meja 20', '2025-06-11 04:56:57', 'Diproses'),
(19, 57, 'meja 5', '2025-06-11 06:24:59', 'Diproses'),
(20, 57, 'meja 10', '2025-06-11 06:27:37', 'Diproses'),
(21, 57, 'meja 10', '2025-06-11 01:32:25', 'Diproses'),
(22, 57, 'meja 9', '2025-06-11 01:36:38', 'Diproses');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `nama` varchar(64) NOT NULL,
  `kontak` varchar(13) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pelanggan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `nama`, `kontak`, `username`, `password`, `role`) VALUES
(55, 'PIKY', '08837474', 'PIKY', '$2y$10$twLRhHdgJlMXDMBtxhjpJu00fsvl5hQ0K4DiQtGHnbqFutMpakAOi', 'pelanggan'),
(56, 'admin', 'admin', 'admin', '$2y$10$D7uENmJUZkjb7IijfZRFeObaqZfE.dyw6G/JdyQFjqoEfw1nAfqFO', 'admin'),
(57, 'udin', '081111111111', 'udin', '$2y$10$824Y2DXs6rkny5L.C3wBLu7GxiFjjWUesazhfmTrYe7EFbjbZWjj2', 'pelanggan'),
(58, 'asep', '082222222222', 'asep', '$2y$10$XtS98Glke0cnGUS.XQOcBeXlX1cqXYPQyHDmKa5gAg2WhSzEBpBwO', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`menu_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
