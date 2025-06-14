-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 05:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GET_LAPORAN_PEMINJAMAN` (IN `bulan_param` INT, IN `tahun_param` INT)   BEGIN
    SELECT
        p.id_peminjaman,
        a.nama_anggota,
        b.judul,
        p.tanggal_pinjam,
        p.tanggal_kembali_seharusnya,
        p.tanggal_kembali_aktual,
        p.status_peminjaman,
        p.denda
    FROM
        Peminjaman p
    JOIN
        Anggota a ON p.id_anggota = a.id_anggota
    JOIN
        Buku b ON p.id_buku = b.id_buku
    WHERE
        MONTH(p.tanggal_pinjam) = bulan_param AND
        YEAR(p.tanggal_pinjam) = tahun_param
    ORDER BY
        p.tanggal_pinjam DESC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `nama_anggota` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tanggal_daftar` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `nama_anggota`, `alamat`, `nomor_telepon`, `email`, `tanggal_daftar`) VALUES
(1, 'neng futrie', 'kp cimencek', '000000000', 'nengfutrie@gmail.com', '2025-06-13');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pengarang` varchar(255) DEFAULT NULL,
  `penerbit` varchar(255) DEFAULT NULL,
  `tahun_terbit` int(11) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `jumlah_tersedia` int(11) NOT NULL DEFAULT 0,
  `total_eksemplar` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `isbn`, `jumlah_tersedia`, `total_eksemplar`) VALUES
(1, 'hujan', 'tere liye', 'gramedia', 2020, '1234567800000', 7, 8);

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_seharusnya` date NOT NULL,
  `tanggal_kembali_aktual` date DEFAULT NULL,
  `denda` decimal(10,2) DEFAULT 0.00,
  `status_peminjaman` enum('Dipinjam','Kembali','Terlambat') NOT NULL DEFAULT 'Dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_anggota`, `id_buku`, `tanggal_pinjam`, `tanggal_kembali_seharusnya`, `tanggal_kembali_aktual`, `denda`, `status_peminjaman`) VALUES
(1, 1, 1, '2025-06-13', '2025-06-18', '2025-06-13', 0.00, 'Kembali'),
(2, 1, 1, '2025-06-13', '2025-06-13', NULL, 0.00, 'Dipinjam');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_buku` (`id_buku`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE;
COMMIT;

DELIMITER //

DROP TRIGGER IF EXISTS hitung_denda_sebelum_update;
//

CREATE TRIGGER hitung_denda_sebelum_update
BEFORE UPDATE ON peminjaman
FOR EACH ROW
BEGIN
  DECLARE selisih INT DEFAULT 0;

  IF NEW.status_peminjaman = 'Kembali' THEN
    SET selisih = DATEDIFF(NEW.tanggal_kembali_aktual, NEW.tanggal_kembali_seharusnya);

    IF selisih > 0 THEN
      SET NEW.denda = selisih * 1000;
      SET NEW.status_peminjaman = 'Terlambat';
    ELSE
      SET NEW.denda = 0;
    END IF;
  END IF;
END;
//

DELIMITER ;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
