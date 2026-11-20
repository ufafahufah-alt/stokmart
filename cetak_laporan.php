<?php
require_once 'auth.php';
wajib_pemilik();
require_once 'laporan_data.php';
[$dari,$sampai]=baca_filter();
$rows=ambil_laporan($koneksi,$dari,$sampai);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Penjualan - StokMart</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/fonts.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
</head>
<body class="print-body" onload="window.print()">
    <div class="print-sheet">
        <header class="print-head">
            <h1>Laporan penjualan toko sembako StokMart</h1>
            <?php if($dari!==''||$sampai!==''): ?><p>Periode: <?= e($dari!==''?$dari:'awal'); ?> s/d <?= e($sampai!==''?$sampai:'sekarang'); ?></p><?php endif; ?>
            <p>Dicetak pada <?= date('d-m-Y H:i:s'); ?></p>
        </header>

        <table class="tbl-print">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama barang</th>
                    <th class="num">Jumlah</th>
                    <th class="num">Subtotal</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $total_semua = 0;
                while ($row = $rows->fetch_assoc()) {
                    $total_semua += $row['subtotal'];
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['tanggal_transaksi']; ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                    <td class="num"><?= $row['jumlah']; ?></td>
                    <td class="num">Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                    <td><?= htmlspecialchars($row['metode_pembayaran']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Total penjualan</td>
                    <td class="num">Rp <?= number_format($total_semua, 0, ',', '.'); ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
