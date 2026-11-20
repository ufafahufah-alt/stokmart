<?php
// Dipakai laporan.php dan cetak_laporan.php agar filter tanggalnya sama.

/** Baca filter tanggal dari URL (?dari=YYYY-MM-DD&sampai=YYYY-MM-DD) */
function baca_filter() {
    $cek = function ($t) {
        return (preg_match('/^\d{4}-\d{2}-\d{2}$/', $t) && strtotime($t) !== false) ? $t : '';
    };
    return [$cek($_GET['dari'] ?? ''), $cek($_GET['sampai'] ?? '')];
}

/** Ambil baris penjualan (satu baris per barang) sesuai filter */
function ambil_laporan($koneksi, $dari, $sampai) {
    $sql = "SELECT t.id_transaksi, t.tanggal_transaksi, b.nama_barang, dt.jumlah, dt.subtotal, t.metode_pembayaran
            FROM transaksi t
            JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
            JOIN barang b ON dt.id_barang = b.id_barang";
    $kondisi = [];
    $tipe = '';
    $nilai = [];
    if ($dari !== '')   { $kondisi[] = "DATE(t.tanggal_transaksi) >= ?"; $tipe .= 's'; $nilai[] = $dari; }
    if ($sampai !== '') { $kondisi[] = "DATE(t.tanggal_transaksi) <= ?"; $tipe .= 's'; $nilai[] = $sampai; }
    if ($kondisi) {
        $sql .= " WHERE " . implode(' AND ', $kondisi);
    }
    $sql .= " ORDER BY t.id_transaksi DESC, dt.id_detail ASC";

    $stmt = $koneksi->prepare($sql);
    if ($nilai) {
        $stmt->bind_param($tipe, ...$nilai);
    }
    $stmt->execute();
    return $stmt->get_result();
}
