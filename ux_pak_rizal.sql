-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2026 at 05:56 AM
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
-- Database: `ux_pak_rizal`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Fire Dragon', 'Naga dengan elemen api dan kemampuan membakar', '2026-01-06 07:14:22'),
(2, 'Water Dragon', 'Naga dengan elemen air dan kemampuan mengendalikan air', '2026-01-06 07:14:22'),
(3, 'Earth Dragon', 'Naga dengan elemen tanah dan kemampuan geokinesis', '2026-01-06 07:14:22'),
(4, 'Air Dragon', 'Naga dengan elemen angin dan kemampuan aerokinesis', '2026-01-06 07:14:22'),
(5, 'Lightning Dragon', 'Naga dengan elemen petir dan kemampuan elektrokinesis', '2026-01-06 07:14:22'),
(6, 'Ice Dragon', 'Naga dengan elemen es dan kemampuan kriokinesis', '2026-01-06 07:14:22'),
(7, 'Legendary Dragon', 'Naga legendaris dengan kekuatan luar biasa', '2026-01-06 07:14:22');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `user_id`, `total_amount`, `payment_method`, `status`, `shipping_address`, `created_at`) VALUES
(1, '', NULL, 26000000.00, 'bank_transfer', 'paid', NULL, '2026-01-06 07:14:23');

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `tr_before_insert_order` BEFORE INSERT ON `orders` FOR EACH ROW BEGIN
    IF NEW.order_code IS NULL THEN
        SET NEW.order_code = CONCAT('ORD-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', 
                                   LPAD(FLOOR(RAND() * 10000), 4, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 1, 2, 13000000.00, 26000000.00);

--
-- Triggers `order_items`
--
DELIMITER $$
CREATE TRIGGER `tr_after_insert_order_item` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    UPDATE produk 
    SET stok = stok - NEW.quantity,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `harga`, `deskripsi`, `gambar`, `kategori`, `stok`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Naga Api Merah', 13000000.00, 'Naga dengan elemen api murni. Dapat menyemburkan api hingga 1000?C. Cocok untuk pertempuran darat.', '6966fe11aa94c_Naga_Api_Merah.jpg', 'Fire Dragon', 1, 'active', '2026-01-06 07:14:22', '2026-01-14 06:49:45'),
(2, 'Naga Air Biru', 10000000.00, 'Naga penguasa lautan. Dapat mengendalikan gelombang tsunami. Cocok untuk pertahanan pantai.', '696435f77841e_Naga_Air_Biru.jpg', 'Water Dragon', 5, 'active', '2026-01-06 07:14:22', '2026-01-21 02:51:37'),
(3, 'Naga Tanah Hijau', 15000000.00, 'Naga penjaga pegunungan. Dapat menggerakkan lempeng bumi. Cocok untuk konstruksi.', '696435e22e722_Naga_Tanah_Hijau.jpg', 'Earth Dragon', 2, 'active', '2026-01-06 07:14:22', '2026-01-21 04:50:38'),
(4, 'Naga Angin Putih', 20000000.00, 'Naga penguasa angin. Dapat terbang dengan kecepatan supersonik. Cocok untuk transportasi.', '696435d28973e_Naga_Angin_Putih.jpg', 'Air Dragon', 4, 'active', '2026-01-06 07:14:22', '2026-01-11 23:44:18'),
(5, 'Naga Petir Emas', 18000000.00, 'Naga pembawa petir. Dapat memanggil badai petir. Cocok untuk pembangkit listrik.', '696435c09b1f3_Naga_Petir_Emas.jpg', 'Lightning Dragon', 6, 'active', '2026-01-06 07:14:22', '2026-01-11 23:44:00'),
(6, 'Naga Es Perak', 23000000.00, 'Naga penguasa es abadi. Dapat membekukan segala sesuatu. Cocok untuk preservasi.', '696435af7f3d2_Naga_Es_Perak.jpg', 'Ice Dragon', 2, 'active', '2026-01-06 07:14:22', '2026-01-11 23:43:43'),
(7, 'Naga Legenda Hitam', 25000000.00, 'Naga legenda dari zaman purba. Memiliki semua elemen dasar. Sangat langka!', '696052d3f02d8_Naga_Legenda_Hitam.jpg', 'Legendary Dragon', 1, 'active', '2026-01-06 07:14:22', '2026-01-09 00:58:59'),
(8, 'Naga Phoenix', 17000000.00, 'Naga dengan kemampuan regenerasi seperti phoenix. Dapat bangkit dari abu.', '695dee188dce8_Naga_Phoenix.jpg', 'Fire Dragon', 7, 'active', '2026-01-06 07:14:22', '2026-01-07 05:24:40'),
(9, 'Naga Leviathan', 27000000.00, 'Naga laut raksasa penguasa palung mariana. Ukuran mencapai 100 meter.', '695dee00649c2_Naga_Leviathan.jpg', 'Water Dragon', 2, 'active', '2026-01-06 07:14:22', '2026-01-14 05:15:58'),
(10, 'Naga Bonar', 1000000000.00, 'Naga paling kuat dalam sejarah. Hanya ada 1 di seluruh dunia. Investasi terbaik!', '695cbfc6bedb2_Naga_Bonar.jpg', '', 2, 'active', '2026-01-06 07:14:22', '2026-01-14 05:15:36'),
(12, 'Naga Sari', 1500000000000.00, 'Legendary Dragon of Gunung sari this is the end Sigma skibidi ', '695de93a8b2e2_Naga_Sari.png', 'Earth Dragon', 1, 'active', '2026-01-07 05:03:54', '2026-01-14 05:15:48'),
(14, 'watu', 12000.00, 'watu jengger', '69703948cedd8_watu.png', 'Earth Dragon', 0, 'active', '2026-01-21 02:26:16', '2026-01-21 02:26:41');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `items` text DEFAULT NULL,
  `status` enum('pending','diproses','selesai','dibatalkan') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `user_id`, `username`, `total_harga`, `metode_pembayaran`, `items`, `status`, `created_at`) VALUES
(1, 15, 'tes', 1300000000.00, 'Kartu Debit', '[{\"id\":\"1\",\"name\":\"Naga Api Merah\",\"price\":1300000000,\"quantity\":1,\"img\":\"images shop/6966fe11aa94c_Naga_Api_Merah.jpg\"}]', 'pending', '2026-01-14 06:49:45'),
(2, 15, 'tes', 1000000000.00, 'OVO', '[{\"id\":\"2\",\"name\":\"Naga Air Biru\",\"price\":1000000000,\"quantity\":1,\"img\":\"images shop/696435f77841e_Naga_Air_Biru.jpg\"}]', 'selesai', '2026-01-14 06:49:57'),
(3, 14, 'coba', 1000000000.00, 'Kartu Debit', '[{\"id\":\"2\",\"name\":\"Naga Air Biru\",\"price\":1000000000,\"quantity\":1,\"img\":\"images shop/696435f77841e_Naga_Air_Biru.jpg\"}]', 'selesai', '2026-01-21 02:24:45'),
(4, 14, 'coba', 1200000.00, 'OVO', '[{\"id\":\"14\",\"name\":\"watu\",\"price\":1200000,\"quantity\":1,\"img\":\"images shop/69703948cedd8_watu.png\"}]', 'selesai', '2026-01-21 02:26:41'),
(5, 14, 'coba', 1000000000.00, 'COD', '[{\"id\":\"2\",\"name\":\"Naga Air Biru\",\"price\":1000000000,\"quantity\":1,\"img\":\"images shop/696435f77841e_Naga_Air_Biru.jpg\"}]', 'pending', '2026-01-21 02:51:37'),
(6, 14, 'coba', 1500000000.00, 'COD', '[{\"id\":\"3\",\"name\":\"Naga Tanah Hijau\",\"price\":1500000000,\"quantity\":1,\"img\":\"images shop/696435e22e722_Naga_Tanah_Hijau.jpg\"}]', 'selesai', '2026-01-21 04:50:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(14, 'coba', 'coba@gmail.com', '$2y$10$.zCIae2NDt1zKjSGGFPvOOrv97ilEYV2bN67hbkNcgxRXKlzxHRbK', 'user', '2026-01-06 07:23:25', '2026-01-06 07:23:25'),
(15, 'tes', 'tes@tes.com', '$2y$10$y9G41g3V7rxiJTRkC1YxxeEE2lVZkEcyESSGJfnOpq38fFghaIqWC', 'admin', '2026-01-07 05:42:17', '2026-01-07 07:00:04'),
(17, 'user', 'user@hmzstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '2026-01-07 06:48:16', '2026-01-07 06:48:16'),
(20, 'admin', 'admin@hmzstore.com', '$2y$10$27C7MzodBpQev33kGvzPBedoPy66DcwNYF7jqCuuwHsGptw9e9I8y', 'admin', '2026-01-14 05:13:17', '2026-01-14 05:13:17');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_produk_populer`
-- (See below for the actual view)
--
CREATE TABLE `view_produk_populer` (
`id` int(11)
,`nama_produk` varchar(255)
,`harga` decimal(15,2)
,`kategori` varchar(100)
,`total_favorite` bigint(21)
,`total_cart` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_produk_tersedia`
-- (See below for the actual view)
--
CREATE TABLE `view_produk_tersedia` (
`id` int(11)
,`nama_produk` varchar(255)
,`harga` decimal(15,2)
,`stok` int(11)
,`kategori` varchar(100)
,`gambar` varchar(255)
,`status_stok` varchar(15)
);

-- --------------------------------------------------------

--
-- Structure for view `view_produk_populer`
--
DROP TABLE IF EXISTS `view_produk_populer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_produk_populer`  AS SELECT `p`.`id` AS `id`, `p`.`nama_produk` AS `nama_produk`, `p`.`harga` AS `harga`, `p`.`kategori` AS `kategori`, count(`f`.`id`) AS `total_favorite`, count(distinct `c`.`id`) AS `total_cart` FROM ((`produk` `p` left join `favorites` `f` on(`p`.`id` = `f`.`product_id`)) left join `cart` `c` on(`p`.`id` = `c`.`product_id`)) GROUP BY `p`.`id` ORDER BY count(`f`.`id`) DESC, count(distinct `c`.`id`) DESC LIMIT 0, 10 ;

-- --------------------------------------------------------

--
-- Structure for view `view_produk_tersedia`
--
DROP TABLE IF EXISTS `view_produk_tersedia`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_produk_tersedia`  AS SELECT `p`.`id` AS `id`, `p`.`nama_produk` AS `nama_produk`, `p`.`harga` AS `harga`, `p`.`stok` AS `stok`, `p`.`kategori` AS `kategori`, `p`.`gambar` AS `gambar`, CASE WHEN `p`.`stok` > 10 THEN 'Tersedia Banyak' WHEN `p`.`stok` > 0 THEN 'Tersedia' ELSE 'Habis' END AS `status_stok` FROM `produk` AS `p` WHERE `p`.`status` = 'active' ORDER BY `p`.`created_at` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_session` (`session_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `idx_order_code` (`order_code`),
  ADD KEY `idx_user_order` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nama` (`nama_produk`),
  ADD KEY `idx_kategori` (`kategori`),
  ADD KEY `idx_harga` (`harga`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `produk` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
