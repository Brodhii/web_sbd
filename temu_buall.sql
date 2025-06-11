-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 09:46 AM
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
(28, 24, 64, 5, 2000.00);

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
(64, 'es kosong', 2000, 'MINUMAN', 'es.jpg', 85, 1),
(65, 'teh obeng', 8000, 'MINUMAN', 'es.jpg', 100, 1),
(66, 'teh tarik hangat', 8000, 'MINUMAN', 'es.jpg', 45, 1),
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
(24, 57, 'meja 1', '2025-06-11 02:04:31', 'Dibatalkan');

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
(55, 'Piky', '08837474', 'PIKY', '$2y$10$twLRhHdgJlMXDMBtxhjpJu00fsvl5hQ0K4DiQtGHnbqFutMpakAOi', 'pelanggan'),
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
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
