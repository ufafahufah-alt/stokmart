<?php
require_once 'auth.php';
wajib_login();

// Catat barang masuk ke tabel barang_masuk dan perbarui stok secara bersamaan.
if (isset($_POST['tambah_stok'])) {
    wajib_pemilik();
    cek_csrf();
    $id_barang=(int)($_POST['id_barang']??0); $jumlah=(int)($_POST['jumlah_masuk']??0);
    $keterangan=trim($_POST['keterangan']??'Barang masuk');
    if($id_barang<1||$jumlah<1){set_flash('gagal','Pilih barang dan masukkan jumlah lebih dari nol.');}
    else{
      $koneksi->begin_transaction();
      try{
        $cek=$koneksi->prepare("SELECT id_barang FROM barang WHERE id_barang=? FOR UPDATE");$cek->bind_param('i',$id_barang);$cek->execute();
        if(!$cek->get_result()->fetch_assoc()) throw new Exception('Barang tidak ditemukan.');
        $ins=$koneksi->prepare("INSERT INTO barang_masuk(id_barang,tanggal_masuk,jumlah,keterangan) VALUES(?,CURDATE(),?,?)");$ins->bind_param('iis',$id_barang,$jumlah,$keterangan);if(!$ins->execute())throw new Exception('Gagal mencatat barang masuk.');
        $up=$koneksi->prepare("UPDATE barang SET stok=stok+? WHERE id_barang=?");$up->bind_param('ii',$jumlah,$id_barang);if(!$up->execute())throw new Exception('Gagal memperbarui stok.');
        $koneksi->commit();set_flash('sukses','Barang masuk berhasil dicatat dan stok diperbarui.');
      }catch(Exception $e){$koneksi->rollback();set_flash('gagal',$e->getMessage());}
    }
    pindah('stok.php');
}
$aktif = 'stok';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StokMart - Manajemen Stok</title>
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
                <h1>Stok masuk</h1>
                <p class="page-sub">Pantau sisa stok barang. Pemilik dapat mencatat barang masuk.</p>
            </div>
        </header>

        <?= tampil_flash(); ?>
        <div class="split">
            <?php if (($_SESSION['role'] ?? '') === 'pemilik'): ?>
            <section class="panel">
                <h2 class="panel-title mb-3">Input barang masuk</h2>
                <form method="POST">
                    <?= csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label" for="s_barang">Pilih barang</label>
                        <select id="s_barang" name="id_barang" class="form-select" required>
                            <option value="">Pilih barang</option>
                            <?php
                            $brg = mysqli_query($koneksi, "SELECT * FROM barang");
                            while ($b = mysqli_fetch_assoc($brg)) {
                                echo "<option value='" . $b['id_barang'] . "'>" . htmlspecialchars($b['nama_barang']) . " (Sisa: " . $b['stok'] . ")</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="s_jumlah">Jumlah barang masuk</label>
                        <input type="number" id="s_jumlah" name="jumlah_masuk" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3"><label class="form-label" for="s_ket">Keterangan</label><input id="s_ket" name="keterangan" class="form-control" placeholder="Contoh: Pembelian dari pemasok"></div>
                    <button type="submit" name="tambah_stok" class="btn btn-pandan w-100">Tambah stok</button>
                </form>
            </section>
            <?php endif; ?>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2 class="panel-title">Stok barang saat ini</h2>
                        <p class="panel-note">Merah berarti stok 5 atau kurang.</p>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama barang</th>
                                <th>Kategori</th>
                                <th>Stok tersedia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nama_barang ASC");
                            while ($row = mysqli_fetch_assoc($data)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="nama"><?= htmlspecialchars($row['nama_barang']); ?></td>
                                <td><span class="chip"><?= htmlspecialchars($row['kategori']); ?></span></td>
                                <td>
                                    <span class="pill <?= $row['stok'] <= 5 ? 'pill-low' : 'pill-ok'; ?>"><?= $row['stok']; ?> <?= htmlspecialchars($row['satuan']); ?></span>
                                </td>
                            </tr>
                            <?php } ?>
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
