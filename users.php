<?php
require_once 'auth.php'; wajib_pemilik(); $aktif='users';
if(isset($_POST['tambah_user'])){
 cek_csrf();$nama=trim($_POST['nama_lengkap']??'');$username=trim($_POST['username']??'');$password=$_POST['password']??'';$role=$_POST['role']??'karyawan';
 if($nama===''||$username===''||strlen($password)<6||!in_array($role,['pemilik','karyawan'],true))set_flash('gagal','Lengkapi data. Password minimal 6 karakter.');
 else{$c=$koneksi->prepare("SELECT id_user FROM users WHERE username=?");$c->bind_param('s',$username);$c->execute();if($c->get_result()->num_rows)set_flash('gagal','Username sudah digunakan.');else{$hash=password_hash($password,PASSWORD_DEFAULT);$i=$koneksi->prepare("INSERT INTO users(nama_lengkap,username,password,role) VALUES(?,?,?,?)");$i->bind_param('ssss',$nama,$username,$hash,$role);$ok=$i->execute();set_flash($ok?'sukses':'gagal',$ok?'Akun berhasil ditambahkan.':'Akun gagal ditambahkan.');}}
 pindah('users.php');
}
if(isset($_POST['hapus_user'])){
 cek_csrf();$id=(int)($_POST['id_user']??0);
 if($id===(int)$_SESSION['id_user'])set_flash('gagal','Akun yang sedang digunakan tidak dapat dihapus.');
 else{$d=$koneksi->prepare("DELETE FROM users WHERE id_user=?");$d->bind_param('i',$id);$ok=$d->execute()&&$d->affected_rows>0;set_flash($ok?'sukses':'gagal',$ok?'Akun berhasil dihapus.':'Akun gagal dihapus.');}
 pindah('users.php');
}
$daftar=$koneksi->query("SELECT id_user,nama_lengkap,username,role FROM users ORDER BY id_user DESC");
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>StokMart - Pengguna</title><link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/fonts.css" rel="stylesheet"><link href="style.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg"></head><body><div class="app"><?php include 'sidebar.php'; ?><main class="main">
<header class="page-head"><div><h1>Manajemen pengguna</h1><p class="page-sub">Pemilik dapat menambah, mengubah, dan menghapus akun pengguna.</p></div><button class="btn btn-pandan" data-bs-toggle="modal" data-bs-target="#tambah"><i class="bi bi-person-plus"></i> Tambah pengguna</button></header>
<?= tampil_flash(); ?>
<section class="panel"><div class="table-wrap"><table class="tbl"><thead><tr><th>No</th><th>Nama lengkap</th><th>Username</th><th>Hak akses</th><th>Aksi</th></tr></thead><tbody>
<?php $no=1;while($u=$daftar->fetch_assoc()): ?><tr><td><?= $no++; ?></td><td class="nama"><?= e($u['nama_lengkap']); ?></td><td><?= e($u['username']); ?></td><td><span class="chip"><?= e(ucfirst($u['role'])); ?></span></td><td class="text-nowrap">
<a class="btn btn-sm btn-soft" href="edit_user.php?id=<?= (int)$u['id_user']; ?>"><i class="bi bi-pencil"></i> Edit</a>
<?php if((int)$u['id_user']!==(int)$_SESSION['id_user']): ?><form method="post" class="d-inline" onsubmit="return confirm('Hapus akun ini?')"><?= csrf_field(); ?><input type="hidden" name="id_user" value="<?= (int)$u['id_user']; ?>"><button name="hapus_user" class="btn btn-sm btn-soft-danger"><i class="bi bi-trash"></i> Hapus</button></form><?php else: ?><span class="small text-muted">Akun aktif</span><?php endif; ?>
</td></tr><?php endwhile; ?>
</tbody></table></div></section></main></div>
<div class="modal fade" id="tambah" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post"><?= csrf_field(); ?><div class="modal-header"><h5 class="modal-title">Tambah pengguna</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
<label class="form-label">Nama lengkap</label><input class="form-control mb-3" name="nama_lengkap" required>
<label class="form-label">Username / Email</label><input class="form-control mb-3" name="username" required>
<label class="form-label">Password (minimal 6 karakter)</label><input type="password" class="form-control mb-3" name="password" minlength="6" required>
<label class="form-label">Hak akses</label><select class="form-select" name="role"><option value="karyawan">Karyawan</option><option value="pemilik">Pemilik</option></select>
</div><div class="modal-footer"><button type="button" class="btn btn-line" data-bs-dismiss="modal">Batal</button><button name="tambah_user" class="btn btn-pandan">Simpan akun</button></div></form></div></div></div>
<script src="assets/js/bootstrap.bundle.min.js"></script></body></html>
