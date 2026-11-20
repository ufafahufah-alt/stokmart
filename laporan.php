<?php
require_once 'auth.php';
wajib_pemilik();
$aktif='laporan';
require_once 'laporan_data.php';
[$dari,$sampai]=baca_filter();
$laporan=ambil_laporan($koneksi,$dari,$sampai);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StokMart - Laporan Penjualan</title>
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
                <h1>Laporan penjualan</h1>
                <p class="page-sub">Seluruh riwayat penjualan, dari yang terbaru.</p>
            </div>
            <a href="cetak_laporan.php?dari=<?= urlencode($dari); ?>&sampai=<?= urlencode($sampai); ?>" target="_blank" class="btn btn-pandan"><i class="bi bi-printer"></i> Cetak / unduh PDF</a>
        </header>

        <section class="panel mb-3"><form method="GET" class="row g-2 align-items-end">
<div class="col-md-4"><label class="form-label">Dari tanggal</label><input type="date" name="dari" class="form-control" value="<?= e($dari); ?>"></div>
<div class="col-md-4"><label class="form-label">Sampai tanggal</label><input type="date" name="sampai" class="form-control" value="<?= e($sampai); ?>"></div>
<div class="col-md-auto"><button class="btn btn-soft">Terapkan filter</button> <a href="laporan.php" class="btn btn-line">Reset</a></div>
</form><p class="panel-note mt-2 mb-0">Tanggal yang sama untuk laporan harian, atau rentang tanggal untuk laporan bulanan/periode.</p></section>
        <?= tampil_flash(); ?>
        <section class="panel">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID transaksi</th>
                            <th>Tanggal</th>
                            <th>Nama barang</th>
                            <th class="num">Jumlah</th>
                            <th class="num">Subtotal</th>
                            <th>Metode pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $total_semua = 0;
                        while ($row = $laporan->fetch_assoc()) {
                            $total_semua += $row['subtotal'];
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><span class="chip">#<?= $row['id_transaksi']; ?></span></td>
                            <td class="text-nowrap"><?= $row['tanggal_transaksi']; ?></td>
                            <td class="nama"><?= htmlspecialchars($row['nama_barang']); ?></td>
                            <td class="num"><?= $row['jumlah']; ?></td>
                            <td class="num">Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                            <td><span class="chip"><?= htmlspecialchars($row['metode_pembayaran']); ?></span></td>
                        </tr>
                        <?php } ?>
                        <?php if ($no === 1): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty">
                                    <i class="bi bi-bar-chart-line"></i>
                                    <strong>Belum ada data penjualan</strong>
                                    Laporan akan terisi setelah ada transaksi.
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if ($no > 1): ?>
                    <tfoot>
                        <tr>
                            <td colspan="5">Total penjualan</td>
                            <td class="num">Rp <?= number_format($total_semua, 0, ',', '.'); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </section>
    </main>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
