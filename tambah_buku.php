<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku Baru - Sistem Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 2px 2px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"] {
            width: calc(100% - 22px); /* Adjust for padding and border */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
        <h2>Tambah Buku Baru</h2>

        <?php
        // Sertakan file koneksi database
        require_once 'config/database.php';

        $message = ''; // Variabel untuk menyimpan pesan sukses/error

        // Cek jika formulir sudah disubmit
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Ambil data dari formulir dan bersihkan dari potensi injeksi SQL
            $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
            $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
            $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
            $tahun_terbit = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
            $isbn = mysqli_real_escape_string($koneksi, $_POST['isbn']);
            $total_eksemplar = mysqli_real_escape_string($koneksi, $_POST['total_eksemplar']);

            // Validasi sederhana (opsional tapi disarankan)
            if (empty($judul) || empty($pengarang) || empty($isbn) || empty($total_eksemplar)) {
                $message = "<div class='message error'>Semua kolom dengan tanda * harus diisi!</div>";
            } elseif (!is_numeric($tahun_terbit) || $tahun_terbit <= 0) {
                 $message = "<div class='message error'>Tahun Terbit harus berupa angka positif.</div>";
            } elseif (!is_numeric($total_eksemplar) || $total_eksemplar <= 0) {
                 $message = "<div class='message error'>Total Eksemplar harus berupa angka positif.</div>";
            } else {
                // Query SQL untuk menyimpan data buku
                $sql = "INSERT INTO Buku (judul, pengarang, penerbit, tahun_terbit, isbn, jumlah_tersedia, total_eksemplar)
                        VALUES ('$judul', '$pengarang', '$penerbit', '$tahun_terbit', '$isbn', '$total_eksemplar', '$total_eksemplar')";

                if (mysqli_query($koneksi, $sql)) {
                    $message = "<div class='message success'>Buku <strong>\"" . $judul . "\"</strong> berhasil ditambahkan!</div>";
                    // Kosongkan formulir setelah sukses
                    $_POST = array(); // Untuk mengosongkan nilai input setelah submit
                } else {
                    // Cek jika error karena ISBN duplikat
                    if (mysqli_errno($koneksi) == 1062) { // Kode error MySQL untuk duplicate entry
                        $message = "<div class='message error'>Error: ISBN yang Anda masukkan sudah terdaftar.</div>";
                    } else {
                        $message = "<div class='message error'>Error: " . mysqli_error($koneksi) . "</div>";
                    }
                }
            }
        }
        // Tampilkan pesan jika ada
        echo $message;
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="judul">Judul Buku <span style="color: red;">*</span>:</label>
            <input type="text" id="judul" name="judul" required value="<?php echo isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''; ?>">

            <label for="pengarang">Pengarang <span style="color: red;">*</span>:</label>
            <input type="text" id="pengarang" name="pengarang" required value="<?php echo isset($_POST['pengarang']) ? htmlspecialchars($_POST['pengarang']) : ''; ?>">

            <label for="penerbit">Penerbit:</label>
            <input type="text" id="penerbit" name="penerbit" value="<?php echo isset($_POST['penerbit']) ? htmlspecialchars($_POST['penerbit']) : ''; ?>">

            <label for="tahun_terbit">Tahun Terbit:</label>
            <input type="number" id="tahun_terbit" name="tahun_terbit" value="<?php echo isset($_POST['tahun_terbit']) ? htmlspecialchars($_POST['tahun_terbit']) : ''; ?>">

            <label for="isbn">ISBN <span style="color: red;">*</span>:</label>
            <input type="text" id="isbn" name="isbn" required value="<?php echo isset($_POST['isbn']) ? htmlspecialchars($_POST['isbn']) : ''; ?>">

            <label for="total_eksemplar">Total Eksemplar <span style="color: red;">*</span>:</label>
            <input type="number" id="total_eksemplar" name="total_eksemplar" required value="<?php echo isset($_POST['total_eksemplar']) ? htmlspecialchars($_POST['total_eksemplar']) : ''; ?>">

            <button type="submit">Tambah Buku</button>
        </form>
        <a href="index.php" class="back-link">Kembali ke Beranda</a>
    </div>
</body>
</html>

<?php
// Tutup koneksi database setelah semua operasi selesai
mysqli_close($koneksi);
?>