# Sistem Kasir

Sistem kasir modern untuk restoran dengan fitur manajemen user, meja, menu, order, transaksi, dan laporan.

## 🚀 Fitur Utama

### 👤 Manajemen User
- Multi-role: Administrator, Waiter, Kasir, Owner
- Sistem login yang aman
- Manajemen hak akses per role

### 🪑 Manajemen Meja
- Pengaturan status meja
- Tracking penggunaan meja
- Manajemen kapasitas

### 🍽️ Manajemen Menu
- Kategori menu
- Harga dan stok
- Gambar menu
- Status ketersediaan

### 🛍️ Sistem Order
- Input pesanan per meja
- Tracking status pesanan
- Riwayat order

### 💰 Transaksi
- Proses pembayaran
- Cetak struk
- Riwayat transaksi
- Laporan keuangan

### 📊 Laporan
- Laporan penjualan
- Laporan menu terlaris
- Laporan keuangan
- Export data

## 🛠️ Teknologi

- PHP Native
- MySQL Database
- Bootstrap 5
- Bootstrap Icons
- JavaScript

## 📋 Requirements

- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache/Nginx)
- Composer (untuk dependencies)

## 🚀 Instalasi

1. Clone repository
```bash
git clone https://github.com/Hadi-Akram-Ramadhan/camel.git
```

2. Import database
```bash
mysql -u username -p database_name < database/kasirlsp.sql
```

3. Install dependencies
```bash
composer install
```

4. Konfigurasi database di `config/database.php`

5. Jalankan di web server

## 👥 Role & Hak Akses

### Administrator
- Manajemen user
- Manajemen meja
- Manajemen menu

### Waiter
- Lihat menu
- Input order
- Lihat laporan

### Kasir
- Proses transaksi
- Cetak struk
- Lihat laporan

### Owner
- Akses laporan
- Monitoring kinerja

## 🔒 Keamanan

- Session-based authentication
- Password hashing
- Role-based access control
- Input validation
- XSS protection

## 📱 Responsive Design

- Mobile-friendly interface
- Bootstrap 5 framework
- Modern UI/UX
- Clean & intuitive design

## 🤝 Kontribusi

Silakan buat pull request untuk kontribusi. Untuk perubahan besar, buka issue dulu untuk diskusi.

