-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 05:31 AM
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
-- Database: `restaurantmanagement`
--

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
  `rating` decimal(3,2) DEFAULT 0.00,
  `stok` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `nama`, `harga`, `kategori`, `gambar`, `rating`, `stok`) VALUES
(22, '🍰CHOCOBERRY HALFCAKE', 15000, 'Makanan', 'download (7).jpg', 0.00, 8),
(39, '🍩DONOUT', 1000, 'Snack', 'Donuts🍩___.jpg', 0.00, 3),
(42, '🍛KOREAN CURRY RICE', 40000, 'Makanan', 'Korean Curry Rice - Yeji\'s Kitchen Stories.jpeg', 0.00, 5),
(43, '🍮PUDDING', 20000, 'Makanan', 'koiko.jpeg', 0.00, 5),
(44, '🥟DIMSUM', 35000, 'Makanan', 'Premium Photo _ Siomay or su mai or steamed dumpling dimsum.jpeg', 0.00, 2),
(45, '🧇WAFFLE', 21000, 'Makanan', 'food.jpeg', 0.00, 1),
(46, '🍔BURGER', 23000, 'Makanan', 'download (10).jpeg', 0.00, 9),
(47, '🍕MINI PIZZA', 15000, 'Makanan', '♡.jpg', 0.00, 1),
(48, '🍡CREAMY MOCHI', 12000, 'Makanan', 'creamy mochi.jpg', 0.00, 0),
(49, '🍧TAIYAKI ICECREAM', 18000, 'Makanan', 'download (15).jpeg', 0.00, 0),
(50, '🧁MINT CHOCOLATE ICE', 31000, 'Makanan', '• __Save=follow me.jpeg', 0.00, 0),
(51, '🍤KIMCHI FRIED RICE', 34000, 'Makanan', '_☆save & follow☆_.jpeg', 0.00, 0),
(52, '🫕PARFAIT ICE', 13000, 'Makanan', 'download (14).jpeg', 0.00, 2),
(53, '🍔DEMI-GLACE BURGER', 32000, 'Makanan', 'download (12).jpeg', 0.00, 0),
(54, '🧋BOBBA', 50000, 'Makanan', '@𝙬𝙝𝙤𝙨𝙢𝙞𝙘𝙝𝙝.jpg', 0.00, 2),
(55, '🧋MINI BOBBA', 19000, 'Makanan', 'download (1).jpg', 0.00, 0),
(56, '🍵MATCHA DRINK', 25000, 'Makanan', 'download.jpg', 0.00, 1),
(57, '🍠TONKATSU', 58000, 'Makanan', 'download (11).jpeg', 0.00, 0),
(58, 'GNNOCHI', 100000, 'MAKANAN', 'download (2).jpg', 0.00, 0),
(59, 'TANGHULU', 20000, 'SNACK', '🍓🍇.jpg', 0.00, 0),
(60, 'CHICKEN CRISPY', 58000, 'MAKANAN', '🦋𝒞𝓇ℯ𝒹𝒾𝓉𝓈 𝓉ℴ ℴ𝓌𝓃ℯ𝓇🦋.jpg', 0.00, 0),
(61, 'STRAWBERRY FRUIT TEA', 34000, 'MINUMAN', 'download (10).jpg', 0.00, 0),
(62, 'CAPPUCINO COFFE', 41000, 'MINUMAN', 'download (13).jpg', 0.00, 0),
(63, '🍡MOCHI JAPANESE 20-PIECES', 130000, 'SNACK', 'save + follow _𖥔 ݁ ˖.jpg', 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `pesanan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `status` enum('pending','proses','selesai') NOT NULL DEFAULT 'pending',
  `tanggal_pemesanan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`pesanan_id`, `user_id`, `menu_id`, `jumlah`, `status`, `tanggal_pemesanan`) VALUES
(43, 17, 22, 1, 'pending', '2025-05-06'),
(44, 17, 22, 3, 'pending', '2025-05-06'),
(45, 17, 22, 3, 'pending', '2025-05-06'),
(46, 54, 39, 4, 'pending', '2025-05-06'),
(47, 54, 42, 4, 'pending', '2025-05-06'),
(48, 54, 22, 3, 'pending', '2025-05-06'),
(49, 54, 39, 5, 'pending', '2025-05-06'),
(50, 54, 58, 1, 'pending', '2025-05-14');

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
(4, 'sandra', '082274154636', 'sandrai', '$2y$10$DZLhEiVbMwAMSPf30p6JQOQhoCXWu3CnVqsYOM/TQSwFJjLuBUk.e', 'pelanggan'),
(12, 'ADMIN', 'ADMIN', 'ADMIN', '$2y$10$cdBfMnsF/50Ygbt1nhw6n.Nlfkj5BIX8iLbsGDBBBEAzbFOtpd9.W', 'admin'),
(15, 'xiaulinn', 'xiaulinn', 'xiaulinn', '$2y$10$EiKSokRUiLFbdoRAM0a31..gbtpU6Ywp7n6y.fz.2rlYPHXwLWmOi', 'admin'),
(17, 'citra', 'citra', 'citra', '$2y$10$TRhSuok/BloaK86ABexEAeevsCtYSBJp.C6cIvYijJeOAGfKdVjR.', 'pelanggan'),
(52, 'sandra', 'sandra', 'sandra', '$2y$10$4QDxx6SgJsyuFb/Y.jY1j.XGfmKI1i/9IxSjQoaQMZC2pAcp3GmF2', 'pelanggan'),
(53, 'miko', 'miko', 'miko', '$2y$10$QfnPwexd6eLVIZzAy9ciFuYbiGzHLEv2j3XVxk.pM3RVlUzpe66Gi', 'pelanggan'),
(54, 'yadra', 'yadra', 'yadra', '$2y$10$N09/Qv0KbyhbWwh3dpA3fufEKMjVDHai1II0mjth9XNDJYsEkXf/m', 'pelanggan'),
(55, 'aling', 'aling', 'aling', '$2y$10$twLRhHdgJlMXDMBtxhjpJu00fsvl5hQ0K4DiQtGHnbqFutMpakAOi', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`pesanan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `menu_id` (`menu_id`);

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
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `pesanan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
