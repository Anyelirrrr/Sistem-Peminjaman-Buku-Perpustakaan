<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sistem Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Beberapa styling kustom jika diperlukan, tapi sebagian besar sudah dihandle Bootstrap */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            /* Bootstrap 'd-flex justify-content-center align-items-center min-vh-100' di container div */
            /* sudah menggantikan ini di body */
        }
        /* Styling lama yang diganti Bootstrap bisa dihapus atau dikomentari */
        /*
        .dashboard-container {
            padding: 30px 40px;
            text-align: center;
            max-width: 800px;
            width: 90%;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .feature-card {
            background-color: #007bff;
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1.2em;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .feature-card:hover {
            background-color: #0056b3;
            transform: translateY(-5px);
        }
        .feature-card.books { background-color: #28a745; }
        .feature-card.books:hover { background-color: #218838; }
        .feature-card.members { background-color: #ffc107; color: #333; }
        .feature-card.members:hover { background-color: #e0a800; }
        .feature-card.transactions { background-color: #17a2b8; }
        .feature-card.transactions:hover { background-color: #138496; }
        .feature-card.reports { background-color: #6c757d; }
        .feature-card.reports:hover { background-color: #5a6268; }
        */
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow p-4" style="max-width: 800px; width: 100%;">
            <div class="card-body text-center">
                <h1 class="card-title text-primary mb-4">Selamat Datang di Sistem Perpustakaan</h1>
                <p class="card-text text-muted mb-4">Pilih salah satu menu di bawah untuk mengelola data atau melihat laporan.</p>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    <div class="col">
                        <a href="daftar_buku.php" class="btn btn-success d-block py-4 fw-bold">
                            Manajemen Buku
                        </a>
                    </div>
                    <div class="col">
                        <a href="daftar_anggota.php" class="btn btn-warning text-dark d-block py-4 fw-bold">
                            Manajemen Anggota
                        </a>
                    </div>
                    <div class="col">
                        <a href="daftar_peminjaman.php" class="btn btn-info text-white d-block py-4 fw-bold">
                            Daftar Transaksi Peminjaman
                        </a>
                    </div>
                    <div class="col">
                        <a href="pinjam_buku.php" class="btn btn-primary d-block py-4 fw-bold">
                            Catat Peminjaman Baru
                        </a>
                    </div>
                    <div class="col">
                        <a href="kembalikan_buku.php" class="btn btn-danger d-block py-4 fw-bold">
                            Catat Pengembalian Buku
                        </a>
                    </div>
                    <div class="col">
                        <a href="laporan_peminjaman.php" class="btn btn-secondary d-block py-4 fw-bold">
                            Laporan Peminjaman Bulanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>