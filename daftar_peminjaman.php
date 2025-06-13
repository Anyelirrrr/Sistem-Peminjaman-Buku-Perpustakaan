<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peminjaman - Sistem Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 2px 2px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .no-data { text-align: center; color: #666; margin-top: 20px; }
        .action-links a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        .add-button {
            display: inline-block;
            background-color: #28a745; /* Green for new transaction */
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-right: 10px; /* Space between buttons */
        }
        .add-button:hover {
            background-color: #218838;
        }
        .return-button {
            display: inline-block;
            background-color: #dc3545; /* Red for return transaction */
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
        .return-button:hover {
            background-color: #c82333;
        }
        .status-dipinjam { color: #dc3545; font-weight: bold; }
        .status-dikembalikan { color: #28a745; font-weight: bold; }
        .denda-ada { color: #ffc107; font-weight: bold; }
        .denda-nol { color: #6c757d; }

        .back-link { display: block; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Riwayat Peminjaman Buku</h2>
        <a href="pinjam_buku.php" class="add-button">Catat Peminjaman Baru</a>
        <a href="kembalikan_buku.php" class="return-button">Catat Pengembalian Buku</a>

        <?php
        // Sertakan file koneksi database
        require_once 'config/database.php';

        // Query untuk mengambil semua data peminjaman dengan JOIN ke tabel Anggota dan Buku
        $sql = "
            SELECT 
                p.id_peminjaman, 
                a.nama_anggota, 
                b.judul, 
                p.tanggal_pinjam, 
                p.tanggal_kembali_seharusnya, 
                p.tanggal_kembali_aktual, 
                p.status_peminjaman,
                p.denda
            FROM Peminjaman p
            JOIN Anggota a ON p.id_anggota = a.id_anggota
            JOIN Buku b ON p.id_buku = b.id_buku
            ORDER BY p.tanggal_pinjam DESC"; // Urutkan dari yang terbaru

        $result = mysqli_query($koneksi, $sql);

        // Cek apakah ada data peminjaman
        if (mysqli_num_rows($result) > 0) {
            // Tampilkan data dalam bentuk tabel
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>ID Peminjaman</th>";
            echo "<th>Nama Anggota</th>";
            echo "<th>Judul Buku</th>";
            echo "<th>Tgl Pinjam</th>";
            echo "<th>Tgl Kembali Seharusnya</th>";
            echo "<th>Tgl Kembali Aktual</th>";
            echo "<th>Status</th>";
            echo "<th>Denda</th>";
            echo "<th>Aksi</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            // Loop melalui setiap baris data dan tampilkan
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id_peminjaman']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_anggota']) . "</td>";
                echo "<td>" . htmlspecialchars($row['judul']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tanggal_kembali_seharusnya']) . "</td>";
                echo "<td>" . (empty($row['tanggal_kembali_aktual']) ? '-' : htmlspecialchars($row['tanggal_kembali_aktual'])) . "</td>";

                // Menampilkan status dengan warna
                echo "<td>";
                if ($row['status_peminjaman'] == 'Dipinjam') {
                    echo "<span class='status-dipinjam'>" . htmlspecialchars($row['status_peminjaman']) . "</span>";
                } else {
                    echo "<span class='status-dikembalikan'>" . htmlspecialchars($row['status_peminjaman']) . "</span>";
                }
                echo "</td>";

                // Menampilkan denda
                echo "<td>";
                if ($row['denda'] > 0) {
                    echo "<span class='denda-ada'>Rp " . number_format($row['denda'], 0, ',', '.') . "</span>";
                } else {
                    echo "<span class='denda-nol'>Rp 0</span>";
                }
                echo "</td>";

                // Kolom Aksi (misalnya untuk melihat detail lebih lanjut atau menghapus - optional)
                echo "<td class='action-links'>";
                if ($row['status_peminjaman'] == 'Dipinjam') {
                    // Opsi untuk mengembalikan buku dari daftar ini
                    echo "<a href='kembalikan_buku.php?id_peminjaman=" . urlencode($row['id_peminjaman']) . "'>Kembalikan</a>";
                } else {
                    echo "-"; // Tidak ada aksi jika sudah dikembalikan
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            // Jika tidak ada data
            echo "<p class='no-data'>Tidak ada riwayat peminjaman yang ditemukan.</p>";
        }

        // Bebaskan hasil query dari memori
        mysqli_free_result($result);

        // Tutup koneksi database
        mysqli_close($koneksi);
        ?>
        <a href="index.php" class="back-link">Kembali ke Beranda</a>
    </div>
</body>
</html>