# StokMart: petunjuk pemasangan

## Di komputer toko (Laragon)
1. Jalankan Laragon, klik **Start All** (Apache dan MySQL harus hijau).
2. Salin seluruh isi folder ini ke `C:\laragon\www\stokmart\`.
3. Buka phpMyAdmin (`http://localhost/phpmyadmin`) atau HeidiSQL, lalu impor file `db_stokmart.sql`.
4. Buka `http://localhost/stokmart/` di browser.
5. Masuk dengan **username `admin`**, **password `admin123`** (kasir: `kasir` / `kasir123`), lalu segera ganti lewat menu **Ganti password** (di kiri bawah).

## Di hosting (supaya bisa dibuka dari mana saja)
1. Buat database MySQL lewat cPanel, lalu impor `db_stokmart.sql`. Jika hosting tidak mengizinkan perintah `CREATE DATABASE`, hapus dua baris `CREATE DATABASE ...` dan `USE ...` di dekat awal file lalu impor ke database yang sudah dibuat.
2. Ubah `koneksi.php`: isi `$host`, `$username`, `$password`, `$database` sesuai data dari hosting.
3. Unggah semua file `.php` dan `style.css` ke folder website.
4. Aktifkan HTTPS (SSL) dari panel hosting dan ganti password admin.

## Cadangan data
Ekspor database dari phpMyAdmin secara berkala (misalnya seminggu sekali) dan simpan di tempat lain.

## Daftar file
- `login.php`, `logout.php`, `ganti_password.php`: akun dan keamanan
- `index.php`: dashboard
- `barang.php`, `edit_barang.php`, `hapus_barang.php`: data barang
- `transaksi.php`: kasir (banyak barang per transaksi)
- `stok.php`: barang masuk (tabel `barang_masuk`)
- `laporan.php`, `cetak_laporan.php`, `laporan_data.php`: laporan dengan filter tanggal
- `auth.php`, `koneksi.php`, `sidebar.php`, `style.css`: pendukung
