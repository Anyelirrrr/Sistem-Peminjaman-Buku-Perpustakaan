<?php
// Tampilkan error agar mudah debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/database.php';
$message = '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - Sistem Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 2px 2px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input[type="date"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background-color: #da190b; }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .denda-info {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            margin-top: 15px;
            border: 1px solid #ffeeba;
            border-radius: 4px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Catat Pengembalian Buku</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_peminjaman = mysqli_real_escape_string($koneksi, $_POST['id_peminjaman']);
        $tanggal_kembali_aktual = mysqli_real_escape_string($koneksi, $_POST['tanggal_kembali_aktual']);
        $status = 'Kembali';

        if (empty($id_peminjaman) || empty($tanggal_kembali_aktual)) {
            $message = "<div class='message error'>Silakan pilih peminjaman dan isi tanggal kembali.</div>";
        } else {
            $sql_tgl_pinjam = "SELECT tanggal_pinjam FROM Peminjaman WHERE id_peminjaman = '$id_peminjaman'";
            $result_tgl_pinjam = mysqli_query($koneksi, $sql_tgl_pinjam);
            $row_tgl_pinjam = mysqli_fetch_assoc($result_tgl_pinjam);
            $tanggal_pinjam = $row_tgl_pinjam['tanggal_pinjam'];

            if (strtotime($tanggal_kembali_aktual) < strtotime($tanggal_pinjam)) {
                $message = "<div class='message error'>Tanggal kembali tidak boleh lebih awal dari tanggal pinjam ($tanggal_pinjam).</div>";
            } else {
                $sql_get_peminjaman = "SELECT id_buku FROM Peminjaman WHERE id_peminjaman = '$id_peminjaman'";
                $result_get = mysqli_query($koneksi, $sql_get_peminjaman);
                $data = mysqli_fetch_assoc($result_get);
                $id_buku = $data['id_buku'];

                $sql_kembali = "UPDATE Peminjaman SET 
                                tanggal_kembali_aktual = '$tanggal_kembali_aktual',
                                status_peminjaman = '$status'
                                WHERE id_peminjaman = '$id_peminjaman'";

                if (mysqli_query($koneksi, $sql_kembali)) {
                    $sql_update_buku = "UPDATE Buku SET jumlah_tersedia = jumlah_tersedia + 1 WHERE id_buku = '$id_buku'";
                    if (mysqli_query($koneksi, $sql_update_buku)) {
                        $sql_info = "SELECT a.nama_anggota, b.judul, p.denda FROM Peminjaman p
                                     JOIN Anggota a ON p.id_anggota = a.id_anggota
                                     JOIN Buku b ON p.id_buku = b.id_buku
                                     WHERE p.id_peminjaman = '$id_peminjaman'";
                        $result_info = mysqli_query($koneksi, $sql_info);
                        $row_info = mysqli_fetch_assoc($result_info);
                        $nama = htmlspecialchars($row_info['nama_anggota']);
                        $judul = htmlspecialchars($row_info['judul']);
                        $denda = $row_info['denda'];

                        $message = "<div class='message success'>Buku <strong>\"$judul\"</strong> milik <strong>$nama</strong> berhasil dikembalikan!";
                        if ($denda > 0) {
                            $message .= "<div class='denda-info'>Denda keterlambatan: Rp " . number_format($denda, 0, ',', '.') . "</div>";
                        } else {
                            $message .= "<div class='denda-info'>Tidak ada denda keterlambatan. Terima kasih!</div>";
                        }
                        $message .= "</div>";
                    } else {
                        $message = "<div class='message error'>Gagal menambah jumlah buku: " . mysqli_error($koneksi) . "</div>";
                    }
                } else {
                    $message = "<div class='message error'>Gagal mencatat pengembalian: " . mysqli_error($koneksi) . "</div>";
                }
            }
        }
    }

    echo $message;
    ?>

    <!-- Form Pengembalian -->
    <form method="POST" action="">
        <label for="id_peminjaman">Pilih ID Peminjaman:</label>
        <select name="id_peminjaman" required>
            <option value="">-- Pilih Peminjaman --</option>
            <?php
            $sql_peminjaman = "SELECT p.id_peminjaman, a.nama_anggota, b.judul
                               FROM Peminjaman p
                               JOIN Anggota a ON p.id_anggota = a.id_anggota
                               JOIN Buku b ON p.id_buku = b.id_buku
                               WHERE p.status_peminjaman = 'Dipinjam'";
            $result_peminjaman = mysqli_query($koneksi, $sql_peminjaman);
            while ($row = mysqli_fetch_assoc($result_peminjaman)) {
                $id = $row['id_peminjaman'];
                $nama = htmlspecialchars($row['nama_anggota']);
                $judul = htmlspecialchars($row['judul']);
                echo "<option value='$id'>[$id] $nama - \"$judul\"</option>";
            }
            ?>
        </select>

        <label for="tanggal_kembali_aktual">Tanggal Kembali Aktual:</label>
        <input type="date" name="tanggal_kembali_aktual" required>

        <button type="submit">Simpan Pengembalian</button>
    </form>

    <a href="index.php" class="back-link">Kembali ke Beranda</a>
</div>
</body>
</html>
