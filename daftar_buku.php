<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku - Sistem Perpustakaan</title>
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
        .back-link { display: block; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Buku Perpustakaan</h2>
        <a href="tambah_buku.php" class="add-button">Tambah Buku Baru</a>

        <?php
        // Sertakan file koneksi database
        require_once 'config/database.php';

        // Query untuk mengambil semua data buku
        $sql = "SELECT id_buku, judul, pengarang, penerbit, tahun_terbit, isbn, jumlah_tersedia, total_eksemplar FROM Buku ORDER BY judul ASC";
        $result = mysqli_query($koneksi, $sql);

        // Cek apakah ada data buku
        if (mysqli_num_rows($result) > 0) {
            // Tampilkan data dalam bentuk tabel
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Judul</th>";
            echo "<th>Pengarang</th>";
            echo "<th>Penerbit</th>";
            echo "<th>Tahun Terbit</th>";
            echo "<th>ISBN</th>";
            echo "<th>Tersedia</th>";
            echo "<th>Total</th>";
            echo "<th>Aksi</th>"; // Kolom untuk Edit dan Hapus
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            // Loop melalui setiap baris data dan tampilkan
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id_buku']) . "</td>";
                echo "<td>" . htmlspecialchars($row['judul']) . "</td>";
                echo "<td>" . htmlspecialchars($row['pengarang']) . "</td>";
                echo "<td>" . htmlspecialchars($row['penerbit']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tahun_terbit']) . "</td>";
                echo "<td>" . htmlspecialchars($row['isbn']) . "</td>";
                echo "<td>" . htmlspecialchars($row['jumlah_tersedia']) . "</td>";
                echo "<td>" . htmlspecialchars($row['total_eksemplar']) . "</td>";
                echo "<td class='action-links'>";
                // Link untuk Edit dan Hapus (akan kita buat nanti)
                echo "<a href='edit_buku.php?id=" . htmlspecialchars($row['id_buku']) . "'>Edit</a> | ";
                echo "<a href='hapus_buku.php?id=" . htmlspecialchars($row['id_buku']) . "' onclick=\"return confirm('Anda yakin ingin menghapus buku ini?');\">Hapus</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            // Jika tidak ada data
            echo "<p class='no-data'>Tidak ada buku yang ditemukan di database.</p>";
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