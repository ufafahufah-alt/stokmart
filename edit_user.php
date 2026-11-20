<?php
require_once 'auth.php'; wajib_pemilik();
$id=(int)($_GET['id']??0);
$get=$koneksi->prepare("SELECT id_user,nama_lengkap,username,role FROM users WHERE id_user=?");$get->bind_param('i',$id);$get->execute();$user=$get->get_result()->fetch_assoc();
if(!$user){set_flash('gagal','Pengguna tidak ditemukan.');pindah('users.php');}
$error='';
if(isset($_POST['simpan'])){
 cek_csrf();$nama=trim($_POST['nama_lengkap']??'');$username=trim($_POST['username']??'');$role=$_POST['role']??'';$pass=$_POST['password_baru']??'';
 if($nama===''||$username===''||!in_array($role,['pemilik','karyawan'],true))$error='Lengkapi semua data dengan benar.';
 elseif($pass!==''&&strlen($pass)<6)$error='Password baru minimal 6 karakter.';
 else{
  $cek=$koneksi->prepare("SELECT id_user FROM users WHERE username=? AND id_user<>?");$cek->bind_param('si',$username,$id);$cek->execute();
  if($cek->get_result()->num_rows)$error='Username sudah digunakan.';
  else{
   if($pass!==''){$hash=password_hash($pass,PASSWORD_DEFAULT);$up=$koneksi->prepare("UPDATE users SET nama_lengkap=?,username=?,role=?,password=? WHERE id_user=?");$up->bind_param('ssssi',$nama,$username,$role,$hash,$id);}
   else{$up=$koneksi->prepare("UPDATE users SET nama_lengkap=?,username=?,role=? WHERE id_user=?");$up->bind_param('sssi',$nama,$username,$role,$id);}
   if($up->execute()){if($id===(int)$_SESSION['id_user']){$_SESSION['nama']=$nama;$_SESSION['role']=$role;}set_flash('sukses','Data pengguna berhasil diperbarui.');pindah('users.php');}
   $error='Gagal memperbarui pengguna.';
  }
 }
}
$aktif='users';
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Edit Pengguna - StokMart</title><link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/fonts.css" rel="stylesheet"><link href="style.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg"></head><body><div class="app"><?php include 'sidebar.php'; ?><main class="main">
<header class="page-head"><div><h1>Edit pengguna</h1><p class="page-sub">Perbarui identitas dan hak akses akun.</p></div></header>
<?php if($error!==''): ?><div class="alert alert-danger"><?= e($error); ?></div><?php endif; ?>
<section class="panel" style="max-width:650px"><form method="post"><?= csrf_field(); ?>
<div class="mb-3"><label class="form-label">Nama lengkap</label><input class="form-control" name="nama_lengkap" value="<?= e($user['nama_lengkap']); ?>" required></div>
<div class="mb-3"><label class="form-label">Username / Email</label><input class="form-control" name="username" value="<?= e($user['username']); ?>" required></div>
<div class="mb-3"><label class="form-label">Hak akses</label><select class="form-select" name="role"><option value="pemilik" <?= $user['role']==='pemilik'?'selected':''; ?>>Pemilik</option><option value="karyawan" <?= $user['role']==='karyawan'?'selected':''; ?>>Karyawan</option></select></div>
<div class="mb-4"><label class="form-label">Password baru</label><input type="password" class="form-control" name="password_baru" minlength="6" placeholder="Kosongkan jika tidak ingin mengganti"></div>
<button class="btn btn-pandan" name="simpan">Simpan perubahan</button> <a href="users.php" class="btn btn-line">Kembali</a>
</form></section></main></div></body></html>
