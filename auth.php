<?php
date_default_timezone_set('Asia/Jakarta');
if (session_status() === PHP_SESSION_NONE) {
 session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off']);
 session_start();
}
require_once __DIR__.'/koneksi.php';
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function rupiah($v){return 'Rp '.number_format((float)$v,0,',','.');}
function wajib_login(){if(empty($_SESSION['id_user'])||empty($_SESSION['login'])){header('Location: login.php');exit;}}
function wajib_pemilik(){wajib_login();if(($_SESSION['role']??'')!=='pemilik'){set_flash('gagal','Akses hanya tersedia untuk pemilik toko.');header('Location: index.php');exit;}}
function set_flash($t,$m){$_SESSION['flash']=['tipe'=>$t,'pesan'=>$m];}
function tampil_flash(){if(empty($_SESSION['flash']))return ''; $f=$_SESSION['flash'];unset($_SESSION['flash']);$ok=($f['tipe']??'')==='sukses';return '<div class="notice '.($ok?'notice-ok':'notice-err').'"><i class="bi '.($ok?'bi-check-circle':'bi-exclamation-circle').'"></i><span>'.e($f['pesan']).'</span></div>';}
function csrf_token(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function csrf_field(){return '<input type="hidden" name="csrf" value="'.e(csrf_token()).'">';}
function cek_csrf(){if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(400);exit('Permintaan tidak valid. Muat ulang halaman dan coba lagi.');}}
function pindah($u){header('Location: '.$u);exit;}

// ---------- Pilihan kategori & satuan barang ----------
function opsi_kategori(){return ['Beras','Minyak Goreng','Gula & Pemanis','Tepung','Telur','Mie & Pasta','Bumbu Dapur','Penyedap & Bumbu Instan','Kopi & Teh','Susu','Minuman','Makanan Ringan','Makanan Kaleng','Roti & Kue','Sayur & Buah','Daging & Ikan','Sabun & Deterjen','Perawatan Diri','Kebutuhan Rumah Tangga','Rokok','Obat Ringan','Alat Tulis','Sembako Lainnya'];}
function opsi_satuan(){return ['pcs','kg','gram','liter','botol','pak','bungkus','sachet','kaleng','dus','karton','karung','butir','ikat','renteng','lusin'];}
function field_pilihan($nama,$label,$daftar,$nilai='',$col='col-sm-6'){
    static $js=false; $id='f_'.$nama; $lain=($nilai!==''&&!in_array($nilai,$daftar,true));
    $h='<div class="'.e($col).'"><label class="form-label" for="'.e($id).'">'.e($label).'</label><select id="'.e($id).'" name="'.e($nama).'" class="form-select pilih-lain" required><option value="">Pilih '.e(strtolower($label)).'</option>';
    foreach($daftar as $d)$h.='<option value="'.e($d).'"'.($nilai===$d?' selected':'').'>'.e($d).'</option>';
    $h.='<option value="__lain"'.($lain?' selected':'').'>Lainnya (ketik sendiri)</option></select>';
    $h.='<input type="text" name="'.e($nama).'_lain" maxlength="50" class="form-control mt-2'.($lain?'':' d-none').'" placeholder="Ketik '.e(strtolower($label)).' baru" value="'.e($lain?$nilai:'').'"'.($lain?' required':'').'></div>';
    if(!$js){$js=true;$h.='<script>document.addEventListener("change",function(e){if(!e.target.classList.contains("pilih-lain"))return;var t=e.target.parentNode.querySelector("input[type=text]");var on=e.target.value==="__lain";t.classList.toggle("d-none",!on);t.required=on;if(on)t.focus();});</script>';}
    return $h;
}
function baca_pilihan($nama,$maks=50){$v=trim($_POST[$nama]??'');if($v==='__lain')$v=trim($_POST[$nama.'_lain']??'');return function_exists('mb_substr')?mb_substr($v,0,$maks):substr($v,0,$maks);}

function opsi_metode(){return ['Tunai','QRIS','Transfer Bank','Kartu Debit','E-Wallet (GoPay/OVO/DANA)','Kasbon / Hutang'];}
