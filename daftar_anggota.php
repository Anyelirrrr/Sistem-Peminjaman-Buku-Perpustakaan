<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - Sistem Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 2px 2px 12px rgba(0,0,0,0.1); }
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
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .add-button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-link { display: block; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Anggota Perpustakaan</h2>
        <a href="tambah_anggota.php" class="add-button">Tambah Anggota Baru</a>

        <?php
        // Tambahkan bagian ini untuk menangani pesan status dari halaman lain (Edit/Hapus)
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'updated') {
                echo "<div class='message success'>Anggota berhasil diperbarui!</div>";
            } elseif ($_GET['status'] == 'deleted') {
                echo "<div class='message success'>Anggota berhasil dihapus!</div>";
            } elseif ($_GET['status'] == 'error') {
                $errorMessage = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Terjadi kesalahan.';
                echo "<div class='message error'>Gagal melakukan operasi. Error: " . $errorMessage . "</div>";
            }
        }
        ?>

        <?php
        // Sertakan file koneksi database
        require_once 'config/database.php';

        // Query untuk mengambil semua data anggota
        $sql = "SELECT id_anggota, nama_anggota, alamat, nomor_telepon, email, tanggal_daftar FROM Anggota ORDER BY nama_anggota ASC";
        $result = mysqli_query($koneksi, $sql);

        // Cek apakah ada data anggota
        if (mysqli_num_rows($result) > 0) {
            // Tampilkan data dalam bentuk tabel
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Nama Anggota</th>";
            echo "<th>Alamat</th>";
            echo "<th>No. Telepon</th>";
            echo "<th>Email</th>";
            echo "<th>Tgl Daftar</th>";
            echo "<th>Aksi</th>"; // Kolom untuk Edit dan Hapus
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            // Loop melalui setiap baris data dan tampilkan
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id_anggota']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_anggota']) . "</td>";
                echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nomor_telepon']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tanggal_daftar']) . "</td>";
                echo "<td class='action-links'>";
                // Link untuk Edit dan Hapus (akan kita buat nanti)
                echo "<a href='edit_anggota.php?id=" . htmlspecialchars($row['id_anggota']) . "'>Edit</a> | ";
                echo "<a href='hapus_anggota.php?id=" . htmlspecialchars($row['id_anggota']) . "' onclick=\"return confirm('Anda yakin ingin menghapus anggota ini?');\">Hapus</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            // Jika tidak ada data
            echo "<p class='no-data'>Tidak ada anggota yang ditemukan di database.</p>";
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