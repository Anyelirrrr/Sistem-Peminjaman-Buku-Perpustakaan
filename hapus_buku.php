<?php
// Sertakan file koneksi database
require_once 'config/database.php';

// Periksa apakah ID buku disertakan dalam URL
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    // Ambil ID buku dari URL
    $id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Query SQL untuk menghapus buku
    $sql = "DELETE FROM Buku WHERE id_buku = '$id_buku'";

    // Eksekusi query
    if (mysqli_query($koneksi, $sql)) {
        // Jika berhasil, redirect kembali ke halaman daftar buku dengan pesan sukses
        header("location: daftar_buku.php?status=deleted");
        exit(); // Penting: hentikan eksekusi skrip setelah redirect
    } else {
        // Jika gagal, redirect kembali dengan pesan error
        header("location: daftar_buku.php?status=error&message=" . urlencode(mysqli_error($koneksi)));
        exit();
    }
} else {
    // Jika tidak ada ID buku, redirect kembali ke daftar buku dengan pesan error
    header("location: daftar_buku.php?status=error&message=" . urlencode("ID buku tidak ditemukan."));
    exit();
}

// Tutup koneksi database (ini mungkin tidak dieksekusi jika ada redirect)
mysqli_close($koneksi);
?>