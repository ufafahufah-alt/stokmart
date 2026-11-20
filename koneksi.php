<?php
// Konfigurasi database (Laragon default: user root, tanpa password).
// Jika aplikasi dipasang di hosting, ganti nilai di bawah ini sesuai data dari penyedia hosting.
$host     = "localhost";
$username = "root";
$password = "";
$database = "db_stokmart";

// Error dicek manual lewat nilai kembalian, bukan dilempar sebagai exception
mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    $no = mysqli_connect_errno();
    error_log("Koneksi database gagal ($no): " . mysqli_connect_error());
    http_response_code(500);
    if ($no == 1049)      $sebab = "Database <b>$database</b> belum ada. Impor file <b>db_stokmart.sql</b> lewat HeidiSQL atau phpMyAdmin.";
    elseif ($no == 1045)  $sebab = "Username atau password MySQL di <b>koneksi.php</b> salah.";
    elseif ($no == 2002)  $sebab = "MySQL belum berjalan. Buka Laragon lalu klik <b>Start All</b>.";
    else                  $sebab = "Kode error $no. Periksa pengaturan di <b>koneksi.php</b>.";
    die("<div style='font-family:system-ui,sans-serif;max-width:520px;margin:15vh auto;padding:24px;border:1px solid #e4e6f3;border-radius:14px'><h2 style='margin:0 0 8px;color:#E11D48'>Database belum terhubung</h2><p style='margin:0'>$sebab</p></div>");
}
mysqli_set_charset($koneksi, 'utf8mb4');
mysqli_query($koneksi, "SET time_zone = '+07:00'"); // WIB, sama dengan PHP
