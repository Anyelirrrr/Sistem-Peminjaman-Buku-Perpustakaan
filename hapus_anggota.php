<?php
// Sertakan file koneksi database
require_once 'config/database.php';

// Periksa apakah ID anggota disertakan dalam URL
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    // Ambil ID anggota dari URL
    $id_anggota = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Query SQL untuk menghapus anggota
    $sql = "DELETE FROM Anggota WHERE id_anggota = '$id_anggota'";

    // Eksekusi query
    if (mysqli_query($koneksi, $sql)) {
        // Jika berhasil, redirect kembali ke halaman daftar anggota dengan pesan sukses
        header("location: daftar_anggota.php?status=deleted");
        exit(); // Penting: hentikan eksekusi skrip setelah redirect
    } else {
        // Jika gagal, redirect kembali dengan pesan error
        header("location: daftar_anggota.php?status=error&message=" . urlencode(mysqli_error($koneksi)));
        exit();
    }
} else {
    // Jika tidak ada ID anggota, redirect kembali ke daftar anggota dengan pesan error
    header("location: daftar_anggota.php?status=error&message=" . urlencode("ID anggota tidak ditemukan."));
    exit();
}

// Tutup koneksi database (ini mungkin tidak dieksekusi jika ada redirect)
mysqli_close($koneksi);
?>