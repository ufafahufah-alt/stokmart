<?php
require_once 'auth.php';
wajib_login();


// Hitung total barang
$q_barang = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang");
$d_barang = mysqli_fetch_assoc($q_barang);

// Hitung total stok menipis (<= 5)
$q_stok = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang WHERE stok <= 5");
$d_stok = mysqli_fetch_assoc($q_stok);

// Hitung total transaksi hari ini
$q_trans = mysqli_query($koneksi, "SELECT COUNT(*) as total, SUM(total) as pendapatan FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()");
$d_trans = mysqli_fetch_assoc($q_trans);

$aktif = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StokMart - Dashboard</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/fonts.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
</head>
<body>
<div class="app">
    <?php include 'sidebar.php'; ?>

    <main class="main">
        <header class="page-head">
            <div>
                <h1>Dashboard</h1>
                <p class="page-sub">Ringkasan toko hari ini.</p>
            </div>
        </header>

        <section class="stat-grid">
            <div class="stat-hero">
                <span class="stat-label">Pendapatan hari ini</span>
                <span class="stat-hero-value"><small>Rp</small><?= number_format($d_trans['pendapatan'] ?? 0, 0, ',', '.'); ?></span>
                <span class="stat-note"><?= $d_trans['total']; ?> transaksi tercatat hari ini</span>
            </div>

            <div class="panel stat">
                <span class="stat-icon"><i class="bi bi-box-seam"></i></span>
                <div>
                    <span class="stat-label">Jenis barang</span>
                    <span class="stat-value"><?= $d_barang['total']; ?><small>item</small></span>
                </div>
            </div>

            <div class="panel stat stat-warn">
                <span class="stat-icon"><i class="bi bi-exclamation-triangle"></i></span>
                <div>
                    <span class="stat-label">Stok menipis (5 atau kurang)</span>
                    <span class="stat-value"><?= $d_stok['total']; ?><small>barang</small></span>
                </div>
            </div>
        </section>

        <!-- Tabel Peringatan Stok Menipis -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Barang yang perlu diisi ulang</h2>
                    <p class="panel-note">Diurutkan dari stok paling sedikit.</p>
                </div>
                <a href="stok.php" class="btn btn-soft btn-sm"><i class="bi bi-plus-lg"></i> Input stok masuk</a>
            </div>

            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama barang</th>
                            <th>Kategori</th>
                            <th>Sisa stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $sql_min = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok <= 5 ORDER BY stok ASC");
                        if (mysqli_num_rows($sql_min) > 0) {
                            while ($r = mysqli_fetch_assoc($sql_min)) {
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td class="nama"><?= htmlspecialchars($r['nama_barang']); ?></td>
                            <td><span class="chip"><?= htmlspecialchars($r['kategori']); ?></span></td>
                            <td><span class="pill pill-low"><?= $r['stok']; ?> <?= htmlspecialchars($r['satuan']); ?></span></td>
                        </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='4'><div class='empty'><i class='bi bi-check-circle'></i><strong>Semua stok aman</strong>Belum ada barang dengan stok menipis.</div></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
