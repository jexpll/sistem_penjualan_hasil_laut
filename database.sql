//Ini ai slop 
CREATE DATABASE IF NOT EXISTS hasil_laut_rembang;
USE hasil_laut_rembang;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Login awal: admin / GantiPassword123! (segera ubah setelah instalasi)
INSERT IGNORE INTO users (username, password) VALUES ('admin', '$2y$12$Tn6L5XOfVxsWtngCyYuV..qZGTONekIjM3Eielki75htoW24ttlDm');

CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    kategori_id INT NOT NULL,
    satuan VARCHAR(30) NOT NULL DEFAULT 'Kg',
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produk_id INT NOT NULL,
    jumlah DECIMAL(10,2) NOT NULL,
    harga_satuan INT NOT NULL,
    total_harga BIGINT NOT NULL,
    pembeli VARCHAR(150) DEFAULT 'Umum',
    tanggal DATE NOT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO kategori (nama) VALUES
('Ikan Segar'),('Rumput Laut'),('Garam Laut'),
('Udang & Kepiting'),('Kerang & Cumi'),('Olahan Laut');

INSERT INTO produk (nama, kategori_id, satuan) VALUES
('Ikan Tongkol',1,'Kg'),('Ikan Bandeng',1,'Kg'),('Ikan Kembung',1,'Kg'),
('Ikan Tenggiri',1,'Kg'),('Ikan Kakap Merah',1,'Kg'),('Ikan Pari',1,'Kg'),
('Rumput Laut Kering',2,'Kg'),('Rumput Laut Basah',2,'Kg'),('Agar-Agar Rumput Laut',2,'Bungkus'),
('Garam Krosok',3,'Kg'),('Garam Halus',3,'Kg'),('Garam Beryodium',3,'Kg'),
('Udang Windu',4,'Kg'),('Udang Vaname',4,'Kg'),('Kepiting Rajungan',4,'Kg'),
('Cumi-Cumi Segar',5,'Kg'),('Kerang Hijau',5,'Kg'),('Kerang Dara',5,'Kg'),
('Terasi Rembang',6,'Kg'),('Ikan Asin',6,'Kg'),('Petis Ikan',6,'Botol');

INSERT INTO penjualan (produk_id, jumlah, harga_satuan, total_harga, pembeli, tanggal, catatan) VALUES
(1, 25, 28000, 700000, 'Pak Slamet', '2026-11-01', 'Pengiriman ke Pasar Rembang'),
(2, 30, 35000, 1050000, 'Bu Hartini', '2026-11-02', 'Bandeng segar pagi'),
(4, 10, 55000, 550000, 'RM Bahari', '2026-11-03', 'Pesanan restoran'),
(7, 50, 15000, 750000, 'CV Laut Jaya', '2026-11-04', 'Rumput laut kering grade A'),
(10, 100, 5000, 500000, 'Toko Berkah', '2026-11-05', 'Garam krosok untuk industri'),
(13, 15, 85000, 1275000, 'Pak Darmo', '2026-11-06', 'Udang windu ukuran besar'),
(16, 8, 45000, 360000, 'Bu Ani', '2026-11-07', NULL),
(19, 20, 40000, 800000, 'Toko Oleh-Oleh Pantai', '2026-11-08', 'Terasi khas Rembang'),
(3, 40, 22000, 880000, 'Pak Joko', '2026-11-09', 'Ikan kembung segar'),
(11, 200, 6000, 1200000, 'UD Makmur', '2026-11-10', 'Garam halus kemasan');
