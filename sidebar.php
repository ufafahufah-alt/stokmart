<?php
$aktif=$aktif??'';
$hari=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$bulan=[1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$tgl=$hari[(int)date('w')].', '.date('j').' '.$bulan[(int)date('n')].' '.date('Y');
$menu=['dashboard'=>['index.php','bi-grid-1x2','Dashboard'],'barang'=>['barang.php','bi-box-seam','Data barang'],'transaksi'=>['transaksi.php','bi-receipt','Transaksi'],'stok'=>['stok.php','bi-boxes','Stok masuk'],'laporan'=>['laporan.php','bi-bar-chart-line','Laporan']];
?>
<aside class="sidebar">
<a href="index.php" class="brand"><span class="brand-mark"><img src="assets/img/logo.svg" alt="Logo StokMart"></span><span><span class="brand-name">StokMart</span><span class="brand-sub">Toko sembako</span></span></a>
<nav class="side-nav" aria-label="Menu utama">
<?php foreach($menu as $k=>$m): ?>
<?php if(($_SESSION['role']??'')==='pemilik' || in_array($k,['dashboard','transaksi','stok'],true)): ?>
<a href="<?= e($m[0]); ?>" class="<?= $aktif===$k?'active':''; ?>"><i class="bi <?= e($m[1]); ?>"></i><?= e($m[2]); ?></a>
<?php endif; ?>
<?php endforeach; ?>
<?php if(($_SESSION['role']??'')==='pemilik'): ?><a href="users.php" class="<?= $aktif==='users'?'active':''; ?>"><i class="bi bi-people"></i>Pengguna</a><?php endif; ?>
</nav>
<div class="side-foot">
<div class="side-user"><span class="side-avatar"><?= e(strtoupper(function_exists('mb_substr')?mb_substr($_SESSION['nama']??'P',0,1):substr($_SESSION['nama']??'P',0,1))); ?></span><span><span class="side-user-name d-block"><?= e($_SESSION['nama']??'Pengguna'); ?></span><span class="side-user-role d-block"><?= e(ucfirst($_SESSION['role']??'')); ?> · <?= e($tgl); ?></span></span></div>
<a href="ganti_password.php" class="side-link"><i class="bi bi-key"></i> Ganti password</a>
<button type="button" class="btn-keluar" data-keluar><i class="bi bi-box-arrow-right"></i> Keluar</button>
</div>
<button type="button" class="keluar-mobile" data-keluar aria-label="Keluar dari StokMart"><i class="bi bi-box-arrow-right"></i> Keluar</button>
</aside>

<dialog id="dlgKeluar" class="dlg-keluar" aria-labelledby="dlgKeluarJudul">
    <div class="dlg-icon"><i class="bi bi-box-arrow-right"></i></div>
    <h3 id="dlgKeluarJudul">Keluar dari StokMart?</h3>
    <p>Sesi Anda akan diakhiri. Untuk masuk lagi, Anda perlu memasukkan username dan password.</p>
    <form method="POST" action="logout.php" class="dlg-aksi">
        <?= csrf_field(); ?>
        <button type="button" class="btn btn-line" data-batal>Batal</button>
        <button type="submit" class="btn btn-danger-solid"><i class="bi bi-box-arrow-right"></i> Ya, keluar</button>
    </form>
</dialog>
<script>
(function(){
    var d=document.getElementById('dlgKeluar');
    document.querySelectorAll('[data-keluar]').forEach(function(b){
        b.addEventListener('click',function(){
            if(d&&d.showModal){d.showModal();}
            else if(confirm('Keluar dari StokMart?')){d.querySelector('form').submit();}
        });
    });
    if(d){
        d.querySelector('[data-batal]').addEventListener('click',function(){d.close();});
        d.addEventListener('click',function(e){if(e.target===d)d.close();});
    }
})();
</script>
