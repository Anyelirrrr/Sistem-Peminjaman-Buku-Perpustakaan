<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota Baru - Sistem Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 2px 2px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="date"] {
            width: calc(100% - 22px);
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
        <h2>Tambah Anggota Baru</h2>

        <?php
        // Sertakan file koneksi database
        require_once 'config/database.php';

        $message = ''; // Variabel untuk menyimpan pesan sukses/error

        // Cek jika formulir sudah disubmit
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Ambil data dari formulir dan bersihkan
            $nama_anggota = mysqli_real_escape_string($koneksi, $_POST['nama_anggota']);
            $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
            $nomor_telepon = mysqli_real_escape_string($koneksi, $_POST['nomor_telepon']);
            $email = mysqli_real_escape_string($koneksi, $_POST['email']);
            $tanggal_daftar = date('Y-m-d'); // Tanggal pendaftaran otomatis hari ini

            // Validasi sederhana
            if (empty($nama_anggota) || empty($email)) {
                $message = "<div class='message error'>Nama Anggota dan Email harus diisi!</div>";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "<div class='message error'>Format email tidak valid.</div>";
            } else {
                // Query SQL untuk menyimpan data anggota
                $sql = "INSERT INTO Anggota (nama_anggota, alamat, nomor_telepon, email, tanggal_daftar)
                        VALUES ('$nama_anggota', '$alamat', '$nomor_telepon', '$email', '$tanggal_daftar')";

                if (mysqli_query($koneksi, $sql)) {
                    $message = "<div class='message success'>Anggota <strong>\"" . $nama_anggota . "\"</strong> berhasil ditambahkan!</div>";
                    // Kosongkan formulir setelah sukses
                    $_POST = array(); 
                } else {
                    // Cek jika error karena email duplikat
                    if (mysqli_errno($koneksi) == 1062) { // Kode error MySQL untuk duplicate entry
                        $message = "<div class='message error'>Error: Email yang Anda masukkan sudah terdaftar.</div>";
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
            <label for="nama_anggota">Nama Anggota <span style="color: red;">*</span>:</label>
            <input type="text" id="nama_anggota" name="nama_anggota" required value="<?php echo isset($_POST['nama_anggota']) ? htmlspecialchars($_POST['nama_anggota']) : ''; ?>">

            <label for="alamat">Alamat:</label>
            <input type="text" id="alamat" name="alamat" value="<?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?>">

            <label for="nomor_telepon">Nomor Telepon:</label>
            <input type="text" id="nomor_telepon" name="nomor_telepon" value="<?php echo isset($_POST['nomor_telepon']) ? htmlspecialchars($_POST['nomor_telepon']) : ''; ?>">

            <label for="email">Email <span style="color: red;">*</span>:</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <button type="submit">Tambah Anggota</button>
        </form>
        <a href="index.php" class="back-link">Kembali ke Beranda</a>
    </div>
</body>
</html>

<?php
// Tutup koneksi database setelah semua operasi selesai
mysqli_close($koneksi);
?>