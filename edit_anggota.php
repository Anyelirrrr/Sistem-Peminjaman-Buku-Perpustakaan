<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - Sistem Perpustakaan</title>
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
        <h2>Edit Detail Anggota</h2>

        <?php
        // Sertakan file koneksi database
        require_once 'config/database.php';

        $anggota = []; // Inisialisasi variabel anggota
        $message = ''; // Variabel untuk menyimpan pesan sukses/error
        $id_anggota = ''; // Untuk menyimpan ID anggota

        // Cek apakah ada ID anggota di URL (saat pertama kali link "Edit" diklik)
        if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
            $id_anggota = mysqli_real_escape_string($koneksi, $_GET['id']);

            // Query untuk mendapatkan data anggota berdasarkan ID
            $sql = "SELECT id_anggota, nama_anggota, alamat, nomor_telepon, email, tanggal_daftar FROM Anggota WHERE id_anggota = '$id_anggota'";
            $result = mysqli_query($koneksi, $sql);

            if (mysqli_num_rows($result) == 1) {
                $anggota = mysqli_fetch_assoc($result); // Ambil data anggota
            } else {
                $message = "<div class='message error'>Anggota tidak ditemukan.</div>";
                $anggota = []; // Kosongkan array jika anggota tidak ditemukan
            }
        } 

        // Cek jika formulir sudah disubmit (setelah tombol "Simpan Perubahan" diklik)
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Ambil data dari formulir
            $id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
            $nama_anggota = mysqli_real_escape_string($koneksi, $_POST['nama_anggota']);
            $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
            $nomor_telepon = mysqli_real_escape_string($koneksi, $_POST['nomor_telepon']);
            $email = mysqli_real_escape_string($koneksi, $_POST['email']);
            // Tanggal daftar tidak diubah, hanya diambil dari nilai form hidden atau dari data existing

            // Validasi sederhana
            if (empty($nama_anggota) || empty($email)) {
                $message = "<div class='message error'>Nama Anggota dan Email harus diisi!</div>";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "<div class='message error'>Format email tidak valid.</div>";
            } else {
                // Query SQL untuk memperbarui data anggota
                $sql_update = "UPDATE Anggota SET 
                                nama_anggota = '$nama_anggota', 
                                alamat = '$alamat', 
                                nomor_telepon = '$nomor_telepon', 
                                email = '$email'
                            WHERE id_anggota = '$id_anggota'";

                if (mysqli_query($koneksi, $sql_update)) {
                    $message = "<div class='message success'>Data anggota <strong>\"" . $nama_anggota . "\"</strong> berhasil diperbarui!</div>";
                    // Ambil data anggota terbaru setelah update berhasil agar form menampilkan data yang baru
                    $sql = "SELECT id_anggota, nama_anggota, alamat, nomor_telepon, email, tanggal_daftar FROM Anggota WHERE id_anggota = '$id_anggota'";
                    $result = mysqli_query($koneksi, $sql);
                    $anggota = mysqli_fetch_assoc($result);
                } else {
                    // Cek jika error karena email duplikat
                    if (mysqli_errno($koneksi) == 1062) {
                        $message = "<div class='message error'>Error: Email yang Anda masukkan sudah terdaftar untuk anggota lain.</div>";
                    } else {
                        $message = "<div class='message error'>Error: " . mysqli_error($koneksi) . "</div>";
                    }
                }
            }
        }
        // Tampilkan pesan jika ada
        echo $message;

        // Tampilkan formulir hanya jika data anggota ditemukan
        if (!empty($anggota)) {
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="id_anggota" value="<?php echo htmlspecialchars($anggota['id_anggota']); ?>">
            <input type="hidden" name="tanggal_daftar" value="<?php echo htmlspecialchars($anggota['tanggal_daftar']); ?>">

            <label for="nama_anggota">Nama Anggota <span style="color: red;">*</span>:</label>
            <input type="text" id="nama_anggota" name="nama_anggota" required value="<?php echo htmlspecialchars($anggota['nama_anggota']); ?>">

            <label for="alamat">Alamat:</label>
            <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($anggota['alamat']); ?>">

            <label for="nomor_telepon">Nomor Telepon:</label>
            <input type="text" id="nomor_telepon" name="nomor_telepon" value="<?php echo htmlspecialchars($anggota['nomor_telepon']); ?>">

            <label for="email">Email <span style="color: red;">*</span>:</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($anggota['email']); ?>">

            <p><strong>Tanggal Daftar:</strong> <?php echo htmlspecialchars($anggota['tanggal_daftar']); ?></p>

            <button type="submit">Simpan Perubahan</button>
        </form>
        <?php } ?>
        <a href="daftar_anggota.php" class="back-link">Kembali ke Daftar Anggota</a>
    </div>
</body>
</html>

<?php
// Tutup koneksi database setelah semua operasi selesai
mysqli_close($koneksi);
?>