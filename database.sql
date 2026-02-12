-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 31 Jan 2026 pada 07.27
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

CREATE DATABASE IF NOT EXISTS segarpedia;
USE segarpedia;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `segarpedia`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `pembeli_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `cart`
--

INSERT INTO `cart` (`id`, `pembeli_id`, `produk_id`, `qty`) VALUES
(18, 1, 13, 1),
(19, 1, 12, 1),
(20, 1, 11, 1),
(21, 1, 10, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `pembeli_id` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `status` enum('pending','dibayar','dikirim') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `pembeli_id`, `total`, `status`, `created_at`) VALUES
(1, 1, 0, 'dibayar', '2026-01-29 21:14:30'),
(2, 1, 0, 'dibayar', '2026-01-29 21:20:49'),
(3, 1, 0, 'dibayar', '2026-01-29 21:28:02'),
(4, 1, 0, 'dibayar', '2026-01-29 21:28:03'),
(5, 1, 0, 'dibayar', '2026-01-29 21:31:24'),
(6, 1, 0, 'dibayar', '2026-01-29 22:02:44'),
(7, 1, 80000, 'dibayar', '2026-01-29 22:13:13'),
(8, 1, 80000, 'dibayar', '2026-01-29 22:14:51'),
(9, 1, 17000, 'dibayar', '2026-01-29 23:48:37'),
(10, 1, 0, 'dibayar', '2026-01-29 23:48:47'),
(11, 1, 0, 'dibayar', '2026-01-29 23:50:07'),
(12, 1, 0, 'dibayar', '2026-01-29 23:50:47'),
(13, 2, 0, 'dibayar', '2026-01-29 23:52:04'),
(14, 1, 23000, 'dibayar', '2026-01-29 23:59:25'),
(15, 1, 5000, 'dibayar', '2026-01-30 00:02:48'),
(16, 1, 11000, 'dibayar', '2026-01-30 00:04:44'),
(17, 1, 28000, 'dibayar', '2026-01-30 01:01:48'),
(18, 1, 6000, 'dibayar', '2026-01-30 01:04:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `produk_id`, `harga`, `qty`) VALUES
(1, 8, 7, 0, 1),
(2, 8, 8, 0, 2),
(3, 9, 10, 0, 1),
(4, 9, 11, 0, 1),
(5, 14, 12, 0, 1),
(6, 14, 11, 0, 1),
(7, 14, 10, 0, 1),
(8, 15, 11, 0, 1),
(9, 16, 11, 0, 1),
(10, 16, 12, 0, 1),
(11, 17, 10, 0, 1),
(12, 17, 11, 0, 2),
(13, 17, 12, 0, 1),
(14, 18, 12, 0, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `penjual_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `status` enum('aktif','hapus') DEFAULT 'aktif',
  `gambar` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `harga`, `stok`, `penjual_id`, `stock`, `status`, `gambar`) VALUES
(10, 'sawi', 12000, 1, 2, 0, 'aktif', 'sawi-hijau.webp'),
(11, 'bawang', 5000, 0, 2, 0, 'aktif', 'ar4-26okt20.webp'),
(12, 'tomat', 6000, 1, 2, 0, 'aktif', '1769729923_images.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `toko`
--

CREATE TABLE `toko` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_toko` varchar(100) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `toko`
--

INSERT INTO `toko` (`id`, `user_id`, `nama_toko`, `lokasi`, `deskripsi`, `foto`) VALUES
(1, 5, 'Kebunku', 'bandung', 'jual segala macam hasil kebun', 'login.png'),
(2, 2, 'Kebunku', 'bandung', 'ppp', 'login.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('pembeli','penjual') DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `telp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `tgl_lahir`, `jenis_kelamin`, `telp`) VALUES
(1, 'ell', 'ridzky231@gmail.com', '$2y$10$4y1c4GRz2q61D3xKj6AQEumWEVHRWifDmAQe2j5GIH6EIpWwb6cWK', 'pembeli', NULL, NULL, NULL),
(2, 'abel', 'ridzkyabel31@gmail.com', '$2y$10$Csb8T1ZdG6Nki3D7A0fIHOshx5HePIePVe38BoZp4szmL4kXJ3sEm', 'penjual', '0000-00-00', 'Laki-laki', '081234567'),
(3, 'ell', '123@gmail.com', '$2y$10$5CqlTmLbJCZib3VDBQN42./GMn8WK8zIk/jeKpep1680/e6rKwS8S', 'pembeli', NULL, NULL, NULL),
(4, 'ell', '1234@gmail.com', '$2y$10$xSYoKPqY1ni6N1xOODaPROWOiynJFYxKIqlGwTliEvWgeTHRBGt0u', 'penjual', NULL, NULL, NULL),
(5, 'abel', '12345@gmail.com', '$2y$10$yMoMdUxJBBp547o3FIqaK.0FBAwh4/SCrbePEZzQ58Jqcnt7/9fli', 'penjual', NULL, NULL, NULL),
(6, 'rapusu', 'rapsu@gmail.com', '$2y$10$YGpLGl4gEfZNSJm53rIsB.rF8zysF/ttZzXFF1X/SbRsviRdGUU2q', 'penjual', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `toko`
--
ALTER TABLE `toko`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `toko`
--
ALTER TABLE `toko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
