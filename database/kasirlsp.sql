-- Create database
CREATE DATABASE IF NOT EXISTS kasirlsp;
USE kasirlsp;

-- Create tables
CREATE TABLE user (
    iduser INT PRIMARY KEY AUTO_INCREMENT,
    namauser VARCHAR(100),
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role ENUM('administrator', 'waiter', 'kasir', 'owner')
);

CREATE TABLE meja (
    idmeja INT PRIMARY KEY AUTO_INCREMENT,
    namameja VARCHAR(50),
    kapasitas INT,
    status ENUM('tersedia', 'terisi') DEFAULT 'tersedia'
);

CREATE TABLE menu (
    idmenu INT PRIMARY KEY AUTO_INCREMENT,
    namamenu VARCHAR(100),
    harga INT
);

CREATE TABLE pelanggan (
    idpelanggan INT PRIMARY KEY AUTO_INCREMENT,
    namapelanggan VARCHAR(100),
    jeniskelamin BOOLEAN,
    nohp CHAR(13),
    alamat VARCHAR(95)
);

CREATE TABLE pesanan (
    idpesanan INT PRIMARY KEY AUTO_INCREMENT,
    idpelanggan INT,
    iduser INT,
    idmeja INT,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idpelanggan) REFERENCES pelanggan(idpelanggan),
    FOREIGN KEY (iduser) REFERENCES user(iduser),
    FOREIGN KEY (idmeja) REFERENCES meja(idmeja)
);

CREATE TABLE detail_pesanan (
    iddetail INT PRIMARY KEY AUTO_INCREMENT,
    idpesanan INT,
    idmenu INT,
    jumlah INT,
    FOREIGN KEY (idpesanan) REFERENCES pesanan(idpesanan),
    FOREIGN KEY (idmenu) REFERENCES menu(idmenu)
);

CREATE TABLE transaksi (
    idtransaksi INT PRIMARY KEY AUTO_INCREMENT,
    idpesanan INT,
    total INT,
    bayar INT,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idpesanan) REFERENCES pesanan(idpesanan)
);

-- Insert default admin user
INSERT INTO user (namauser, username, password, role) VALUES 
('Administrator', 'admin', '$2a$12$faqlYTUkZE.a3KoGwyfGteDLOKn0oTfIIPTCpRgK03NzPiNb1R.5.', 'administrator');
-- Default password: admin