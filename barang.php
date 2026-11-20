<?php
require_once 'auth.php';
wajib_pemilik();

// Proses tambah barang dengan validasi dan prepared statement
if(isset($_POST['tambah'])){
    cek_csrf();
    $nama=trim($_POST['nama_barang']??''); $kat=baca_pilihan('kategori',50);
    $hbeli=(int)($_POST['harga_beli']??-1); $hjual=(int)($_POST['harga_jual']??-1);
    $stok=(int)($_POST['stok']??-1); $satuan=baca_pilihan('satuan',20);
    if($nama===''||$kat===''||$satuan===''||$hbeli<0||$hjual<0||$stok<0) set_flash('gagal','Lengkapi data. Harga dan stok tidak boleh negatif.');
    else {
        $ins=$koneksi->prepare("INSERT INTO barang(nama_barang,kategori,harga_beli,harga_jual,stok,satuan) VALUES(?,?,?,?,?,?)");
        $ins->bind_param('ssiiis',$nama,$kat,$hbeli,$hjual,$stok,$satuan);
        if($ins->execute()) set_flash('sukses','Barang berhasil ditambahkan.');
        else set_flash('gagal','Barang gagal ditambahkan.');
    }
    pindah('barang.php');
}

// Fitur Pencarian
$keyword = trim($_GET['cari'] ?? '');
if ($keyword !== '') {
    $like = '%' . $keyword . '%';
    $st = $koneksi->prepare("SELECT * FROM barang WHERE nama_barang LIKE ? OR kategori LIKE ? ORDER BY id_barang DESC");
    $st->bind_param('ss', $like, $like);
    $st->execute();
    $result = $st->get_result();
} else {
    $result = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY id_barang DESC");
}

$aktif = 'barang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StokMart - Data Barang</title>
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
                <h1>Data barang</h1>
                <p class="page-sub">Kelola daftar barang, harga, dan stok.</p>
            </div>
            <button class="btn btn-pandan" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg"></i> Tambah barang
            </button>
        </header>

        <?= tampil_flash(); ?>
        <section class="panel">
            <div class="panel-head">
                <form method="GET" class="d-flex gap-2 flex-grow-1 flex-sm-grow-0">
                    <div class="search flex-grow-1">
                        <i class="bi bi-search"></i>
                        <input type="text" name="cari" class="form-control" placeholder="Cari nama barang atau kategori" value="<?= htmlspecialchars($keyword); ?>" aria-label="Cari barang">
                    </div>
                    <button type="submit" class="btn btn-soft">Cari</button>
                    <?php if ($keyword !== ''): ?>
                        <a href="barang.php" class="btn btn-line">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama barang</th>
                            <th>Kategori</th>
                            <th class="num">Harga beli</th>
                            <th class="num">Harga jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td class="nama"><?= htmlspecialchars($row['nama_barang']); ?></td>
                            <td><span class="chip"><?= htmlspecialchars($row['kategori']); ?></span></td>
                            <td class="num">Rp <?= number_format($row['harga_beli'], 0, ',', '.'); ?></td>
                            <td class="num">Rp <?= number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($row['stok'] <= 5): ?>
                                    <span class="pill pill-low"><?= $row['stok']; ?> <?= htmlspecialchars($row['satuan']); ?> (menipis)</span>
                                <?php else: ?>
                                    <span class="pill pill-ok"><?= $row['stok']; ?> <?= htmlspecialchars($row['satuan']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <a href="edit_barang.php?id=<?= $row['id_barang']; ?>" class="btn btn-sm btn-soft"><i class="bi bi-pencil"></i> Edit</a>
                                <form method="POST" action="hapus_barang.php" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    <?= csrf_field(); ?><input type="hidden" name="id" value="<?= (int)$row['id_barang']; ?>">
                                    <button type="submit" class="btn btn-sm btn-soft-danger"><i class="bi bi-trash"></i> Hapus</button></form>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if ($no === 1): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty">
                                    <i class="bi bi-search"></i>
                                    <strong><?= $keyword !== '' ? 'Barang tidak ditemukan' : 'Belum ada barang'; ?></strong>
                                    <?= $keyword !== '' ? 'Coba kata kunci lain atau reset pencarian.' : 'Klik Tambah barang untuk mulai mengisi daftar.'; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="judulTambah" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="judulTambah">Tambah barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="f_nama">Nama barang</label>
                            <input type="text" id="f_nama" name="nama_barang" class="form-control" required>
                        </div>
                        <?= field_pilihan('kategori','Kategori',opsi_kategori()); ?>
                        <?= field_pilihan('satuan','Satuan',opsi_satuan()); ?>
                        <div class="col-sm-6">
                            <label class="form-label" for="f_beli">Harga beli (Rp)</label>
                            <input type="number" min="0" id="f_beli" name="harga_beli" class="form-control" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="f_jual">Harga jual (Rp)</label>
                            <input type="number" min="0" id="f_jual" name="harga_jual" class="form-control" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="f_stok">Stok awal</label>
                            <input type="number" min="0" id="f_stok" name="stok" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-line" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-pandan">Simpan barang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
