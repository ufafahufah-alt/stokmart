-- ============================================================
-- StokMart - Data barang contoh (54 barang sembako)
-- Aman dijalankan berulang kali: barang dengan nama yang sama
-- tidak akan dobel dan data Anda yang sudah ada tidak diubah.
-- Jalankan di database db_stokmart (HeidiSQL: tab Query, lalu F9).
-- ============================================================
USE `db_stokmart`;

INSERT INTO `barang` (`nama_barang`, `kategori`, `harga_beli`, `harga_jual`, `stok`, `satuan`)
SELECT t.nama_barang, t.kategori, t.harga_beli, t.harga_jual, t.stok, t.satuan
FROM (
SELECT 'Beras Premium 5 kg' AS nama_barang, 'Beras' AS kategori, 62000 AS harga_beli, 68000 AS harga_jual, 20 AS stok, 'karung' AS satuan
UNION ALL SELECT 'Beras Medium 5 kg', 'Beras', 56000, 61000, 20, 'karung'
UNION ALL SELECT 'Beras Pulen 1 kg', 'Beras', 12500, 14000, 50, 'kg'
UNION ALL SELECT 'Minyak Goreng 1 L', 'Minyak Goreng', 16500, 18500, 40, 'botol'
UNION ALL SELECT 'Minyak Goreng 2 L', 'Minyak Goreng', 32000, 35500, 25, 'botol'
UNION ALL SELECT 'Minyak Goreng Curah', 'Minyak Goreng', 15000, 17000, 20, 'liter'
UNION ALL SELECT 'Gula Pasir 1 kg', 'Gula & Pemanis', 15000, 17000, 40, 'kg'
UNION ALL SELECT 'Gula Merah', 'Gula & Pemanis', 16000, 19000, 15, 'kg'
UNION ALL SELECT 'Sirup Marjan 460 ml', 'Gula & Pemanis', 15500, 18000, 12, 'botol'
UNION ALL SELECT 'Tepung Terigu 1 kg', 'Tepung', 10500, 12500, 30, 'kg'
UNION ALL SELECT 'Tepung Tapioka 500 g', 'Tepung', 6500, 8000, 20, 'bungkus'
UNION ALL SELECT 'Tepung Beras 500 g', 'Tepung', 6000, 7500, 20, 'bungkus'
UNION ALL SELECT 'Telur Ayam', 'Telur', 26000, 29000, 15, 'kg'
UNION ALL SELECT 'Telur Bebek Asin', 'Telur', 3500, 4500, 30, 'butir'
UNION ALL SELECT 'Mie Instan Goreng', 'Mie & Pasta', 2800, 3500, 100, 'bungkus'
UNION ALL SELECT 'Mie Instan Kuah', 'Mie & Pasta', 2700, 3400, 100, 'bungkus'
UNION ALL SELECT 'Bihun Jagung 150 g', 'Mie & Pasta', 4500, 6000, 25, 'bungkus'
UNION ALL SELECT 'Spaghetti 500 g', 'Mie & Pasta', 11000, 14000, 15, 'bungkus'
UNION ALL SELECT 'Bawang Merah', 'Bumbu Dapur', 30000, 36000, 10, 'kg'
UNION ALL SELECT 'Bawang Putih', 'Bumbu Dapur', 28000, 34000, 10, 'kg'
UNION ALL SELECT 'Cabai Rawit', 'Bumbu Dapur', 45000, 55000, 5, 'kg'
UNION ALL SELECT 'Garam Dapur 250 g', 'Bumbu Dapur', 1500, 2500, 40, 'bungkus'
UNION ALL SELECT 'Merica Bubuk', 'Bumbu Dapur', 1000, 2000, 40, 'sachet'
UNION ALL SELECT 'Kecap Manis 275 ml', 'Penyedap & Bumbu Instan', 9000, 11500, 25, 'botol'
UNION ALL SELECT 'Saus Sambal 275 ml', 'Penyedap & Bumbu Instan', 8500, 11000, 25, 'botol'
UNION ALL SELECT 'Penyedap Rasa Sachet', 'Penyedap & Bumbu Instan', 500, 1000, 200, 'sachet'
UNION ALL SELECT 'Santan Instan 65 ml', 'Penyedap & Bumbu Instan', 2800, 3800, 40, 'sachet'
UNION ALL SELECT 'Kopi Hitam Bubuk 165 g', 'Kopi & Teh', 8500, 11000, 20, 'bungkus'
UNION ALL SELECT 'Kopi Sachet 3 in 1', 'Kopi & Teh', 1100, 1500, 150, 'sachet'
UNION ALL SELECT 'Teh Celup isi 25', 'Kopi & Teh', 5500, 7500, 30, 'dus'
UNION ALL SELECT 'Susu Kental Manis 370 g', 'Susu', 11500, 13500, 30, 'kaleng'
UNION ALL SELECT 'Susu UHT 1 L', 'Susu', 16000, 19000, 24, 'dus'
UNION ALL SELECT 'Susu Bubuk 400 g', 'Susu', 42000, 48000, 10, 'dus'
UNION ALL SELECT 'Air Mineral 600 ml', 'Minuman', 2200, 3000, 48, 'botol'
UNION ALL SELECT 'Air Mineral Galon 19 L', 'Minuman', 14000, 19000, 15, 'botol'
UNION ALL SELECT 'Teh Botol 450 ml', 'Minuman', 3500, 4500, 36, 'botol'
UNION ALL SELECT 'Minuman Soda 390 ml', 'Minuman', 3800, 5000, 24, 'botol'
UNION ALL SELECT 'Keripik Singkong', 'Makanan Ringan', 7000, 9000, 20, 'bungkus'
UNION ALL SELECT 'Biskuit Kaleng', 'Makanan Ringan', 38000, 45000, 6, 'kaleng'
UNION ALL SELECT 'Wafer Cokelat', 'Makanan Ringan', 1600, 2500, 60, 'pcs'
UNION ALL SELECT 'Sarden Kaleng 155 g', 'Makanan Kaleng', 8500, 11000, 24, 'kaleng'
UNION ALL SELECT 'Kornet Kaleng 198 g', 'Makanan Kaleng', 17000, 21000, 12, 'kaleng'
UNION ALL SELECT 'Roti Tawar', 'Roti & Kue', 12000, 15000, 10, 'bungkus'
UNION ALL SELECT 'Sabun Mandi Batang', 'Sabun & Deterjen', 3000, 4000, 50, 'pcs'
UNION ALL SELECT 'Deterjen Bubuk 800 g', 'Sabun & Deterjen', 15500, 19000, 20, 'bungkus'
UNION ALL SELECT 'Sabun Cuci Piring 400 ml', 'Sabun & Deterjen', 8500, 11000, 20, 'botol'
UNION ALL SELECT 'Pasta Gigi 120 g', 'Perawatan Diri', 9000, 12000, 24, 'pcs'
UNION ALL SELECT 'Shampo Sachet', 'Perawatan Diri', 500, 1000, 150, 'sachet'
UNION ALL SELECT 'Tisu Gulung', 'Kebutuhan Rumah Tangga', 5000, 7000, 30, 'pcs'
UNION ALL SELECT 'Kantong Plastik', 'Kebutuhan Rumah Tangga', 9000, 12000, 20, 'pak'
UNION ALL SELECT 'Korek Api Gas', 'Kebutuhan Rumah Tangga', 2000, 3000, 50, 'pcs'
UNION ALL SELECT 'Buku Tulis 38 Lembar', 'Alat Tulis', 3500, 5000, 40, 'pcs'
UNION ALL SELECT 'Pulpen', 'Alat Tulis', 1500, 2500, 60, 'pcs'
UNION ALL SELECT 'Paracetamol Strip', 'Obat Ringan', 3000, 4500, 30, 'pcs'
) AS t
WHERE NOT EXISTS (SELECT 1 FROM `barang` b WHERE b.nama_barang = t.nama_barang);
