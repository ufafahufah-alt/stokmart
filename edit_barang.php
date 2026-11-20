<?php
require_once 'auth.php';
wajib_pemilik();
$id=(int)($_GET['id']??0);
$get=$koneksi->prepare("SELECT * FROM barang WHERE id_barang=?");$get->bind_param('i',$id);$get->execute();$row=$get->get_result()->fetch_assoc();
if(!$row){set_flash('gagal','Data barang tidak ditemukan.');pindah('barang.php');}
$error='';
if(isset($_POST['update'])){
 cek_csrf();
 $nama=trim($_POST['nama_barang']??'');$kat=baca_pilihan('kategori',50);$hbeli=(int)($_POST['harga_beli']??-1);$hjual=(int)($_POST['harga_jual']??-1);$stok=(int)($_POST['stok']??-1);$satuan=baca_pilihan('satuan',20);
 if($nama===''||$kat===''||$satuan===''||$hbeli<0||$hjual<0||$stok<0){$error='Lengkapi data. Harga dan stok tidak boleh negatif.';}
 else{$up=$koneksi->prepare("UPDATE barang SET nama_barang=?,kategori=?,harga_beli=?,harga_jual=?,stok=?,satuan=? WHERE id_barang=?");$up->bind_param('ssiiisi',$nama,$kat,$hbeli,$hjual,$stok,$satuan,$id);$up->execute();set_flash('sukses','Data barang berhasil diperbarui.');pindah('barang.php');}
}
$aktif = 'barang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StokMart - Edit Barang</title>
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
                <h1>Edit barang</h1>
                <p class="page-sub">Perbarui data untuk <?= htmlspecialchars($row['nama_barang']); ?>.</p>
            </div>
        </header>

        <?php if($error!==""): ?><div class="alert alert-danger"><?= e($error); ?></div><?php endif; ?>
        <section class="panel" style="max-width: 640px;">
            <form method="POST">
                <?= csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="f_nama">Nama barang</label>
                        <input type="text" id="f_nama" name="nama_barang" class="form-control" value="<?= htmlspecialchars($row['nama_barang']); ?>" required>
                    </div>
                    <?= field_pilihan('kategori','Kategori',opsi_kategori(),(string)$row['kategori']); ?>
                    <?= field_pilihan('satuan','Satuan',opsi_satuan(),(string)$row['satuan']); ?>
                    <div class="col-sm-6">
                        <label class="form-label" for="f_beli">Harga beli (Rp)</label>
                        <input type="number" min="0" id="f_beli" name="harga_beli" class="form-control" value="<?= htmlspecialchars($row['harga_beli']); ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="f_jual">Harga jual (Rp)</label>
                        <input type="number" min="0" id="f_jual" name="harga_jual" class="form-control" value="<?= htmlspecialchars($row['harga_jual']); ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="f_stok">Stok</label>
                        <input type="number" min="0" id="f_stok" name="stok" class="form-control" value="<?= htmlspecialchars($row['stok']); ?>" required>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="update" class="btn btn-pandan">Simpan perubahan</button>
                    <a href="barang.php" class="btn btn-line">Kembali</a>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>
