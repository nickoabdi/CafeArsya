-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 14 Nov 2026 pada 07.14
-- Versi server: 8.0.30
-- Versi PHP: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafe_arsya`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis` enum('makanan','minuman') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'makanan',
  `kategori` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `harga` int NOT NULL DEFAULT '0',
  `foto` varchar(300) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`id`, `nama`, `jenis`, `kategori`, `harga`, `foto`, `aktif`) VALUES
(4, 'ayam', 'makanan', 'makanan', 20000, '6ebcafc5f1c3f7a1ffe60d42295bb547.jpg', 1),
(5, 'kopi', 'minuman', 'minuman', 10000, 'a438b864c609aeb8c7c34ed1f463f1ef.jpg', 1),
(6, 'Ayam Geprek', 'makanan', 'makanan', 25000, '6f21a58a10041256a554315d3273f1fd.jpg', 1),
(7, 'Pop Ice', 'minuman', 'minuman', 7000, NULL, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_pelanggan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meja` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total` int NOT NULL,
  `status_bayar` enum('belum','lunas','batal') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'belum',
  `metode` enum('tunai','qris','transfer') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bayar` int NOT NULL DEFAULT '0',
  `kembali` int NOT NULL DEFAULT '0',
  `status` enum('baru','diproses','selesai','batal') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'baru',
  `kasir_id` int DEFAULT NULL,
  `dibuat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dibayar_pada` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id`, `kode`, `nama_pelanggan`, `meja`, `total`, `status_bayar`, `metode`, `bayar`, `kembali`, `status`, `kasir_id`, `dibuat`, `dibayar_pada`) VALUES
(1, 'CA261006-8CFF', 'Pelanggan', '', 2147483647, 'lunas', 'qris', 2147483647, 0, 'selesai', 1, '2026-10-06 22:18:07', '2026-10-06 22:18:07'),
(2, 'CA261006-2A1C', 'reuigruower', '', 2147483647, 'lunas', 'qris', 2147483647, 0, 'selesai', 1, '2026-10-07 02:57:54', '2026-10-06 21:58:21'),
(3, 'CA261007-7F0B', 'supri', '', 2147483647, 'lunas', 'qris', 2147483647, 0, 'selesai', 1, '2026-10-07 03:09:05', '2026-10-07 03:09:33'),
(4, 'CA261007-5F9E', 'alex', '', 200000000, 'lunas', 'qris', 200000000, 0, 'selesai', 1, '2026-10-07 03:09:15', '2026-10-07 03:09:39'),
(5, 'CA261007-BC63', 'golok', '', 400000000, 'batal', NULL, 0, 0, 'batal', 1, '2026-10-07 03:54:16', NULL),
(6, 'CA261007-8FBA', 'yama', '', 2147483647, 'lunas', 'tunai', 2147483647, 2147483647, 'selesai', 1, '2026-10-07 03:55:04', '2026-10-07 16:45:30'),
(7, 'CA261007-DC7B', 'alex', '1', 55000, 'lunas', 'tunai', 100000, 45000, 'selesai', 1, '2026-10-07 19:34:45', '2026-10-08 03:11:15'),
(8, 'CA261007-60BB', 'goilo', '2', 135000, 'belum', NULL, 0, 0, 'selesai', 1, '2026-10-07 19:37:59', NULL),
(9, 'CA261007-92C5', '2', '', 10000, 'belum', NULL, 0, 0, 'selesai', 1, '2026-10-07 19:38:17', NULL),
(10, 'CA261008-66A6', 'sds', '', 70000, 'belum', NULL, 0, 0, 'selesai', 1, '2026-10-08 02:57:19', NULL),
(11, 'CA261008-93B5', 's', '', 30000, 'belum', NULL, 0, 0, 'selesai', 1, '2026-10-08 03:01:53', NULL),
(12, 'CA261008-4C4A', 'akbar', '', 130000, 'lunas', 'tunai', 130000, 0, 'selesai', 1, '2026-10-08 03:08:16', '2026-10-08 07:53:38'),
(13, 'CA261008-920B', 'Akbar kelana', '02', 30000, 'lunas', 'tunai', 35000, 5000, 'selesai', 1, '2026-10-08 07:51:03', '2026-10-08 07:51:38'),
(14, 'CA261008-DE2C', 'fdfd', '', 20000, 'belum', NULL, 0, 0, 'selesai', 1, '2026-10-08 10:09:02', NULL),
(15, 'CA261016-D473', 'virda', '1', 20000, 'belum', NULL, 0, 0, 'selesai', 1, '2026-10-16 22:06:02', NULL),
(16, 'CA261016-DAB3', 'ayam', '2', 10000, 'belum', NULL, 0, 0, 'selesai', 1, '2026-10-16 22:06:36', NULL),
(17, 'CA261113-E890', 'Slamet', '', 192000, 'belum', NULL, 0, 0, 'baru', 1, '2026-11-13 15:28:35', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan_detail`
--

CREATE TABLE `pesanan_detail` (
  `id` int NOT NULL,
  `pesanan_id` int NOT NULL,
  `menu_id` int DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `harga` int NOT NULL,
  `qty` int NOT NULL,
  `subtotal` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan_detail`
--

INSERT INTO `pesanan_detail` (`id`, `pesanan_id`, `menu_id`, `nama`, `harga`, `qty`, `subtotal`) VALUES
(1, 1, 2, 'reuigruower', 100000000, 3, 300000000),
(2, 1, 3, 'wdsfads', 2147483647, 2, 2147483647),
(3, 2, 2, 'reuigruower', 100000000, 4, 400000000),
(4, 2, 3, 'wdsfads', 2147483647, 4, 2147483647),
(5, 3, 3, 'wdsfads', 2147483647, 5, 2147483647),
(6, 4, 2, 'reuigruower', 100000000, 2, 200000000),
(7, 5, 2, 'reuigruower', 100000000, 4, 400000000),
(9, 6, 3, 'wdsfads', 2147483647, 6, 2147483647),
(10, 7, 4, 'ayam', 20000, 1, 20000),
(11, 7, 5, 'kopi', 10000, 1, 10000),
(12, 7, 6, 'Yaprek', 25000, 1, 25000),
(13, 8, 5, 'kopi', 10000, 1, 10000),
(14, 8, 6, 'Yaprek', 25000, 5, 125000),
(15, 9, 5, 'kopi', 10000, 1, 10000),
(16, 10, 4, 'ayam', 20000, 1, 20000),
(17, 10, 6, 'Yaprek', 25000, 2, 50000),
(18, 11, 5, 'kopi', 10000, 3, 30000),
(21, 12, 4, 'ayam', 20000, 6, 120000),
(22, 12, 5, 'kopi', 10000, 1, 10000),
(23, 13, 4, 'ayam', 20000, 1, 20000),
(24, 13, 5, 'kopi', 10000, 1, 10000),
(25, 14, 5, 'kopi', 10000, 2, 20000),
(26, 15, 5, 'kopi', 10000, 2, 20000),
(27, 16, 5, 'kopi', 10000, 1, 10000),
(28, 17, 5, 'kopi', 10000, 1, 10000),
(29, 17, 6, 'Ayam Geprek', 25000, 7, 175000),
(30, 17, 7, 'Pop Ice', 7000, 1, 7000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','kasir','dapur') COLLATE utf8mb4_general_ci NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `aktif`) VALUES
(1, 'Super Admin', 'admin', '$2y$10$krElTtWzRGgMTAJKU0RmYOOj8NOmGGekSljqu7Y5UZ5zP92fkRYvu', 'admin', 1),
(2, 'kasir', 'alex', '$2y$10$6fOXu6nL0jJhGQVg9EOR8eyt3CQyHvoFPEhXH.W5GuFAMbjhdXn6.', 'kasir', 1),
(8, 'dapur', 'dapur', '$2y$10$t6HvyzoUSj0X5mrgzZOQHeGP5s7dGG18naAA4y8G4OoU9mtolViVO', 'dapur', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD CONSTRAINT `pesanan_detail_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
