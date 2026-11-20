<?php
require_once 'auth.php';
wajib_login();

if(isset($_POST['simpan_transaksi'])){
    cek_csrf();
    $id_barang=(int)($_POST['id_barang']??0);
    $jumlah=(int)($_POST['jumlah']??0);
    $metode=$_POST['metode_pembayaran']??'';
    if($id_barang<1||$jumlah<1||!in_array($metode,opsi_metode(),true)){
        set_flash('gagal','Data transaksi tidak valid.');
        pindah('transaksi.php');
    }
    $koneksi->begin_transaction();
    try{
        $get=$koneksi->prepare("SELECT harga_jual,stok FROM barang WHERE id_barang=? FOR UPDATE");
        $get->bind_param('i',$id_barang);$get->execute();$barang=$get->get_result()->fetch_assoc();
        if(!$barang)throw new Exception('Barang tidak ditemukan.');
        if((int)$barang['stok']<$jumlah)throw new Exception('Stok barang tidak mencukupi.');
        $harga=(int)$barang['harga_jual'];$subtotal=$harga*$jumlah;$tanggal=date('Y-m-d H:i:s');
        $ins=$koneksi->prepare("INSERT INTO transaksi(tanggal_transaksi,total,metode_pembayaran) VALUES(?,?,?)");
        $ins->bind_param('sis',$tanggal,$subtotal,$metode);
        if(!$ins->execute())throw new Exception('Gagal menyimpan transaksi.');
        $id_transaksi=(int)$koneksi->insert_id;
        $det=$koneksi->prepare("INSERT INTO detail_transaksi(id_transaksi,id_barang,jumlah,harga_satuan,subtotal) VALUES(?,?,?,?,?)");
        $det->bind_param('iiiii',$id_transaksi,$id_barang,$jumlah,$harga,$subtotal);
        if(!$det->execute())throw new Exception('Gagal menyimpan detail transaksi.');
        $up=$koneksi->prepare("UPDATE barang SET stok=stok-? WHERE id_barang=? AND stok>=?");
        $up->bind_param('iii',$jumlah,$id_barang,$jumlah);
        if(!$up->execute()||$up->affected_rows!==1)throw new Exception('Stok tidak mencukupi atau berubah. Ulangi transaksi.');
        $koneksi->commit();set_flash('sukses','Transaksi berhasil disimpan dan stok otomatis berkurang.');
    }catch(Exception $e){$koneksi->rollback();set_flash('gagal',$e->getMessage());}
    pindah('transaksi.php');
}

$aktif = 'transaksi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StokMart - Transaksi Penjualan</title>
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
                <h1>Kasir</h1>
                <p class="page-sub">Catat penjualan, stok otomatis berkurang.</p>
            </div>
        </header>

        <?= tampil_flash(); ?>
        <div class="split">
            <!-- Form Input Transaksi -->
            <section class="panel">
                <h2 class="panel-title mb-3">Penjualan baru</h2>
                <form method="POST">
                    <?= csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label" for="t_barang">Pilih barang</label>
                        <select id="t_barang" name="id_barang" class="form-select" required>
                            <option value="">Pilih barang sembako</option>
                            <?php
                            $barang = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok > 0 ORDER BY kategori, nama_barang");
                            $kat_now = null;
                            while ($row = mysqli_fetch_assoc($barang)) {
                                $kat = $row['kategori'] !== null && $row['kategori'] !== '' ? $row['kategori'] : 'Lainnya';
                                if ($kat !== $kat_now) {
                                    if ($kat_now !== null) echo '</optgroup>';
                                    echo '<optgroup label="' . htmlspecialchars($kat) . '">';
                                    $kat_now = $kat;
                                }
                                echo "<option value='" . (int)$row['id_barang'] . "'>" . htmlspecialchars($row['nama_barang']) . " - Rp " . number_format($row['harga_jual'], 0, ',', '.') . " (stok " . (int)$row['stok'] . " " . htmlspecialchars((string)$row['satuan']) . ")</option>";
                            }
                            if ($kat_now !== null) echo '</optgroup>';
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="t_jumlah">Jumlah beli</label>
                        <input type="number" id="t_jumlah" name="jumlah" class="form-control" min="1" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="t_metode">Metode pembayaran</label>
                        <select id="t_metode" name="metode_pembayaran" class="form-select">
                            <?php foreach (opsi_metode() as $m): ?>
                                <option value="<?= e($m); ?>"><?= e($m); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="simpan_transaksi" class="btn btn-pandan w-100">Simpan transaksi</button>
                </form>
            </section>

            <!-- Riwayat Transaksi Terakhir -->
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2 class="panel-title">Riwayat transaksi terbaru</h2>
                        <p class="panel-note">10 transaksi terakhir.</p>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th class="num">Total</th>
                                <th>Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $sql_t = mysqli_query($koneksi, "SELECT t.*, b.nama_barang, dt.jumlah FROM transaksi t JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi JOIN barang b ON dt.id_barang = b.id_barang ORDER BY t.id_transaksi DESC LIMIT 10");
                            while ($t = mysqli_fetch_assoc($sql_t)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="text-nowrap"><?= date('d-m-Y H:i', strtotime($t['tanggal_transaksi'])); ?></td>
                                <td class="nama"><?= htmlspecialchars($t['nama_barang']); ?> <span class="text-muted fw-normal">(<?= $t['jumlah']; ?>x)</span></td>
                                <td class="num">Rp <?= number_format($t['total'], 0, ',', '.'); ?></td>
                                <td><span class="chip"><?= htmlspecialchars($t['metode_pembayaran']); ?></span></td>
                            </tr>
                            <?php } ?>
                            <?php if ($no === 1): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty">
                                        <i class="bi bi-receipt"></i>
                                        <strong>Belum ada transaksi</strong>
                                        Transaksi yang disimpan akan muncul di sini.
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
