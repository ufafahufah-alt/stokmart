<?php
require_once 'auth.php'; wajib_pemilik();
if($_SERVER['REQUEST_METHOD']!=='POST') pindah('barang.php');
cek_csrf(); $id=(int)($_POST['id']??0);
$stmt=$koneksi->prepare("DELETE FROM barang WHERE id_barang=?");$stmt->bind_param('i',$id);
if($stmt->execute()&&$stmt->affected_rows>0)set_flash('sukses','Barang berhasil dihapus.');
elseif($stmt->errno===1451)set_flash('gagal','Barang tidak dapat dihapus karena sudah tercatat dalam riwayat stok atau transaksi.');
else set_flash('gagal','Barang tidak ditemukan.');
pindah('barang.php');
