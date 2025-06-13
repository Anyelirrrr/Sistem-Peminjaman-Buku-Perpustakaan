<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Sistem Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 2px 2px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background-color: #0056b3; }
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
        <h2>Edit Detail Buku</h2>

        <?php
        // Sertakan file koneksi database
        require_once 'config/database.php';

        $buku = []; // Inisialisasi variabel buku
        $message = ''; // Variabel untuk menyimpan pesan sukses/error
        $id_buku = ''; // Untuk menyimpan ID buku

        // Cek apakah ada ID buku di URL (saat pertama kali link "Edit" diklik)
        if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
            $id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);

            // Query untuk mendapatkan data buku berdasarkan ID
            $sql = "SELECT id_buku, judul, pengarang, penerbit, tahun_terbit, isbn, jumlah_tersedia, total_eksemplar FROM Buku WHERE id_buku = '$id_buku'";
            $result = mysqli_query($koneksi, $sql);

            if (mysqli_num_rows($result) == 1) {
                $buku = mysqli_fetch_assoc($result); // Ambil data buku
            } else {
                $message = "<div class='message error'>Buku tidak ditemukan.</div>";
                $buku = []; // Kosongkan array jika buku tidak ditemukan
            }
        } 

        // Cek jika formulir sudah disubmit (setelah tombol "Simpan Perubahan" diklik)
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Ambil data dari formulir
            $id_buku = mysqli_real_escape_string($koneksi, $_POST['id_buku']);
            $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
            $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
            $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
            $tahun_terbit = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
            $isbn = mysqli_real_escape_string($koneksi, $_POST['isbn']);
            $jumlah_tersedia = mysqli_real_escape_string($koneksi, $_POST['jumlah_tersedia']); // Bisa diubah jika ada kebijakan manual
            $total_eksemplar = mysqli_real_escape_string($koneksi, $_POST['total_eksemplar']);

            // Validasi sederhana
            if (empty($judul) || empty($pengarang) || empty($isbn) || empty($total_eksemplar)) {
                $message = "<div class='message error'>Semua kolom dengan tanda * harus diisi!</div>";
            } elseif (!is_numeric($tahun_terbit) || $tahun_terbit <= 0) {
                 $message = "<div class='message error'>Tahun Terbit harus berupa angka positif.</div>";
            } elseif (!is_numeric($total_eksemplar) || $total_eksemplar <= 0) {
                 $message = "<div class='message error'>Total Eksemplar harus berupa angka positif.</div>";
            } elseif (!is_numeric($jumlah_tersedia) || $jumlah_tersedia < 0 || $jumlah_tersedia > $total_eksemplar) {
                $message = "<div class='message error'>Jumlah Tersedia tidak valid atau melebihi Total Eksemplar.</div>";
            } else {
                // Query SQL untuk memperbarui data buku
                $sql_update = "UPDATE Buku SET 
                                judul = '$judul', 
                                pengarang = '$pengarang', 
                                penerbit = '$penerbit', 
                                tahun_terbit = '$tahun_terbit', 
                                isbn = '$isbn',
                                jumlah_tersedia = '$jumlah_tersedia',
                                total_eksemplar = '$total_eksemplar'
                            WHERE id_buku = '$id_buku'";

                if (mysqli_query($koneksi, $sql_update)) {
                    $message = "<div class='message success'>Data buku <strong>\"" . $judul . "\"</strong> berhasil diperbarui!</div>";
                    // Ambil data buku terbaru setelah update berhasil agar form menampilkan data yang baru
                    $sql = "SELECT id_buku, judul, pengarang, penerbit, tahun_terbit, isbn, jumlah_tersedia, total_eksemplar FROM Buku WHERE id_buku = '$id_buku'";
                    $result = mysqli_query($koneksi, $sql);
                    $buku = mysqli_fetch_assoc($result);
                } else {
                    // Cek jika error karena ISBN duplikat
                    if (mysqli_errno($koneksi) == 1062) {
                        $message = "<div class='message error'>Error: ISBN yang Anda masukkan sudah terdaftar untuk buku lain.</div>";
                    } else {
                        $message = "<div class='message error'>Error: " . mysqli_error($koneksi) . "</div>";
                    }
                }
            }
        }
        // Tampilkan pesan jika ada
        echo $message;

        // Tampilkan formulir hanya jika data buku ditemukan
        if (!empty($buku)) {
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="id_buku" value="<?php echo htmlspecialchars($buku['id_buku']); ?>">

            <label for="judul">Judul Buku <span style="color: red;">*</span>:</label>
            <input type="text" id="judul" name="judul" required value="<?php echo htmlspecialchars($buku['judul']); ?>">

            <label for="pengarang">Pengarang <span style="color: red;">*</span>:</label>
            <input type="text" id="pengarang" name="pengarang" required value="<?php echo htmlspecialchars($buku['pengarang']); ?>">

            <label for="penerbit">Penerbit:</label>
            <input type="text" id="penerbit" name="penerbit" value="<?php echo htmlspecialchars($buku['penerbit']); ?>">

            <label for="tahun_terbit">Tahun Terbit:</label>
            <input type="number" id="tahun_terbit" name="tahun_terbit" value="<?php echo htmlspecialchars($buku['tahun_terbit']); ?>">

            <label for="isbn">ISBN <span style="color: red;">*</span>:</label>
            <input type="text" id="isbn" name="isbn" required value="<?php echo htmlspecialchars($buku['isbn']); ?>">

            <label for="jumlah_tersedia">Jumlah Tersedia <span style="color: red;">*</span>:</label>
            <input type="number" id="jumlah_tersedia" name="jumlah_tersedia" required value="<?php echo htmlspecialchars($buku['jumlah_tersedia']); ?>">

            <label for="total_eksemplar">Total Eksemplar <span style="color: red;">*</span>:</label>
            <input type="number" id="total_eksemplar" name="total_eksemplar" required value="<?php echo htmlspecialchars($buku['total_eksemplar']); ?>">

            <button type="submit">Simpan Perubahan</button>
        </form>
        <?php } ?>
        <a href="daftar_buku.php" class="back-link">Kembali ke Daftar Buku</a>
    </div>
</body>
</html>

<?php
// Tutup koneksi database setelah semua operasi selesai
mysqli_close($koneksi);
?>