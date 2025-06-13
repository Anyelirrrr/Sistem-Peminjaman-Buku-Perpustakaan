<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Per Bulan - Sistem Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 2px 2px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        .filter-form { margin-bottom: 20px; text-align: center; }
        .filter-form label { margin-right: 10px; font-weight: bold; }
        .filter-form select, .filter-form button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 5px;
        }
        .filter-form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #0056b3;
        }
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
        .status-dipinjam { color: #dc3545; font-weight: bold; }
        .status-dikembalikan { color: #28a745; font-weight: bold; }
        .denda-ada { color: #ffc107; font-weight: bold; }
        .denda-nol { color: #6c757d; }
        .back-link { display: block; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Laporan Peminjaman Per Bulan</h2>

        <?php
        require_once 'config/database.php';

        $selected_bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('m');
        $selected_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
        ?>

        <div class="filter-form">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                <label for="bulan">Bulan:</label>
                <select id="bulan" name="bulan">
                    <?php
                    $bulan_names = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    foreach ($bulan_names as $num => $name) {
                        echo "<option value='{$num}'" . ($selected_bulan == $num ? ' selected' : '') . ">" . $name . "</option>";
                    }
                    ?>
                </select>

                <label for="tahun">Tahun:</label>
                <select id="tahun" name="tahun">
                    <?php
                    $current_year = date('Y');
                    for ($year = $current_year - 5; $year <= $current_year + 1; $year++) { // Tampilkan 5 tahun ke belakang dan 1 tahun ke depan
                        echo "<option value='{$year}'" . ($selected_tahun == $year ? ' selected' : '') . ">" . $year . "</option>";
                    }
                    ?>
                </select>
                <button type="submit">Tampilkan Laporan</button>
            </form>
        </div>

        <?php
        // Memanggil Stored Procedure
        // Pastikan stored procedure sudah dibuat di database Anda dengan nama GET_LAPORAN_PEMINJAMAN
        // CALL GET_LAPORAN_PEMINJAMAN(bulan_param, tahun_param);
        $sql_call_sp = "CALL GET_LAPORAN_PEMINJAMAN($selected_bulan, $selected_tahun)";
        $result = mysqli_query($koneksi, $sql_call_sp);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
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
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

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
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p class='no-data'>Tidak ada data peminjaman untuk bulan " . $bulan_names[$selected_bulan] . " tahun " . $selected_tahun . ".</p>";
            }
            // Penting: Tutup hasil dari stored procedure.
            // Jika ada lebih dari satu result set dari stored procedure, Anda mungkin perlu memanggil mysqli_next_result().
            mysqli_free_result($result); 
        } else {
            echo "<div class='message error'>Error saat memanggil stored procedure: " . mysqli_error($koneksi) . "</div>";
        }

        // Tutup koneksi database
        mysqli_close($koneksi);
        ?>
        <a href="index.php" class="back-link">Kembali ke Beranda</a>
    </div>
</body>
</html>