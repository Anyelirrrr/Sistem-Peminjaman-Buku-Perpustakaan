<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Buku - Sistem Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 2px 2px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input[type="date"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23000%22%20d%3D%22M287%2069.9a14.6%2014.6%200%200%200-10.4-4.3H15.8c-7.6%200-14%205.5-14.7%2012.9-.8%207.6%204.6%2014%2012.2%2014.7l130.6%20130.6c3.4%203.4%207.9%205.1%2012.5%205.1s9.1-1.7%2012.5-5.1l130.6-130.6c7.6-.8%2013-7.2%2012.2-14.7-.7-7.4-7.1-12.9-14.7-12.9z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 10px top 50%;
            background-size: 12px;
            padding-right: 30px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background-color: #45a049; }
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
        <h2>Catat Peminjaman Buku</h2>

        <?php
        require_once 'config/database.php';
        $message = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
            $id_buku = mysqli_real_escape_string($koneksi, $_POST['id_buku']);
            $tanggal_pinjam = mysqli_real_escape_string($koneksi, $_POST['tanggal_pinjam']);
            $tanggal_kembali_seharusnya = mysqli_real_escape_string($koneksi, $_POST['tanggal_kembali_seharusnya']);

            if (empty($id_anggota) || empty($id_buku) || empty($tanggal_pinjam) || empty($tanggal_kembali_seharusnya)) {
                $message = "<div class='message error'>Semua kolom harus diisi!</div>";
            } elseif (strtotime($tanggal_kembali_seharusnya) < strtotime($tanggal_pinjam)) {
                $message = "<div class='message error'>Tanggal kembali seharusnya tidak boleh lebih awal dari tanggal pinjam.</div>";
            } else {
                $sql_check_buku = "SELECT judul, jumlah_tersedia FROM Buku WHERE id_buku = '$id_buku'";
                $result_check_buku = mysqli_query($koneksi, $sql_check_buku);
                $buku_data = mysqli_fetch_assoc($result_check_buku);

                if ($buku_data['jumlah_tersedia'] > 0) {
                    $sql_pinjam = "INSERT INTO Peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali_seharusnya, status_peminjaman)
                                   VALUES ('$id_anggota', '$id_buku', '$tanggal_pinjam', '$tanggal_kembali_seharusnya', 'Dipinjam')";

                    if (mysqli_query($koneksi, $sql_pinjam)) {
                        $sql_update_buku = "UPDATE Buku SET jumlah_tersedia = jumlah_tersedia - 1 WHERE id_buku = '$id_buku'";
                        if (mysqli_query($koneksi, $sql_update_buku)) {
                            $message = "<div class='message success'>Buku <strong>\"" . htmlspecialchars($buku_data['judul']) . "\"</strong> berhasil dipinjam!</div>";
                            $_POST = array();
                        } else {
                            $message = "<div class='message error'>Error saat mengurangi jumlah buku tersedia: " . mysqli_error($koneksi) . "</div>";
                        }
                    } else {
                        $message = "<div class='message error'>Error saat mencatat peminjaman: " . mysqli_error($koneksi) . "</div>";
                    }
                } else {
                    $message = "<div class='message error'>Buku <strong>\"" . htmlspecialchars($buku_data['judul']) . "\"</strong> tidak tersedia untuk dipinjam.</div>";
                }
            }
        }
        echo $message;
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="id_anggota">Pilih Anggota:</label>
            <select id="id_anggota" name="id_anggota" required>
                <option value="">-- Pilih Anggota --</option>
                <?php
                $sql_anggota = "SELECT id_anggota, nama_anggota FROM Anggota ORDER BY nama_anggota ASC";
                $result_anggota = mysqli_query($koneksi, $sql_anggota);
                while ($row_anggota = mysqli_fetch_assoc($result_anggota)) {
                    echo "<option value='" . htmlspecialchars($row_anggota['id_anggota']) . "'" . (isset($_POST['id_anggota']) && $_POST['id_anggota'] == $row_anggota['id_anggota'] ? ' selected' : '') . ">" . htmlspecialchars($row_anggota['nama_anggota']) . "</option>";
                }
                mysqli_free_result($result_anggota);
                ?>
            </select>

            <label for="id_buku">Pilih Buku:</label>
            <select id="id_buku" name="id_buku" required>
                <option value="">-- Pilih Buku --</option>
                <?php
                $sql_buku = "SELECT id_buku, judul, pengarang, jumlah_tersedia FROM Buku WHERE jumlah_tersedia > 0 ORDER BY judul ASC";
                $result_buku = mysqli_query($koneksi, $sql_buku);
                while ($row_buku = mysqli_fetch_assoc($result_buku)) {
                    echo "<option value='" . htmlspecialchars($row_buku['id_buku']) . "'" . (isset($_POST['id_buku']) && $_POST['id_buku'] == $row_buku['id_buku'] ? ' selected' : '') . ">" . htmlspecialchars($row_buku['judul']) . " (" . htmlspecialchars($row_buku['pengarang']) . ") [Tersedia: " . htmlspecialchars($row_buku['jumlah_tersedia']) . "]</option>";
                }
                mysqli_free_result($result_buku);
                ?>
            </select>

            <label for="tanggal_pinjam">Tanggal Pinjam:</label>
            <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" required
                    value="<?php echo isset($_POST['tanggal_pinjam']) ? htmlspecialchars($_POST['tanggal_pinjam']) : date('Y-m-d'); ?>">

                   <label for="tanggal_kembali_seharusnya">Tanggal Kembali Seharusnya:</label>
                    <input type="date" id="tanggal_kembali_seharusnya" name="tanggal_kembali_seharusnya" required 
                           value="<?php echo isset($_POST['tanggal_kembali_seharusnya']) ? htmlspecialchars($_POST['tanggal_kembali_seharusnya']) : date('Y-m-d', strtotime('+7 days')); ?>">


            <button type="submit">Catat Peminjaman</button>
        </form>
        <a href="index.php" class="back-link">Kembali ke Beranda</a>
    </div>
</body>
</html>

<?php
mysqli_close($koneksi);
?>
