<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'kasir') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

// Get pending orders (not yet in transactions)
$query = "SELECT p.*, m.namamenu, m.harga, pl.namapelanggan, mj.namameja 
          FROM pesanan p 
          JOIN menu m ON p.idmenu = m.idmenu 
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          LEFT JOIN transaksi t ON p.idpesanan = t.idpesanan
          WHERE t.idtransaksi IS NULL
          ORDER BY p.idpesanan DESC";
$result = mysqli_query($conn, $query);

// Get completed transactions
$query_transaksi = "SELECT t.*, p.jumlah, m.namamenu, m.harga, pl.namapelanggan, mj.namameja 
                    FROM transaksi t
                    JOIN pesanan p ON t.idpesanan = p.idpesanan
                    JOIN menu m ON p.idmenu = m.idmenu
                    JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
                    JOIN meja mj ON p.idmeja = mj.idmeja
                    ORDER BY t.tanggal DESC";
$result_transaksi = mysqli_query($conn, $query_transaksi);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi - Sistem Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../../dashboard.php">Sistem Kasir</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Kelola Transaksi</h3>
        
        <!-- Pending Orders -->
        <div class="card mt-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Order yang Belum Dibayar</h5>
            </div>
            <div class="card-body">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pelanggan</th>
                                    <th>Menu</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                    <th>Meja</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while($row = mysqli_fetch_assoc($result)): 
                                    $total = $row['jumlah'] * $row['harga'];
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['namapelanggan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['namamenu']); ?></td>
                                        <td><?php echo $row['jumlah']; ?></td>
                                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($row['namameja']); ?></td>
                                        <td>
                                            <a href="bayar.php?id=<?php echo $row['idpesanan']; ?>" class="btn btn-sm btn-success">
                                                <i class="bi bi-cash"></i> Bayar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Tidak ada order yang perlu dibayar.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Completed Transactions -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Riwayat Transaksi</h5>
            </div>
            <div class="card-body">
                <?php if(mysqli_num_rows($result_transaksi) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Menu</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Bayar</th>
                                    <th>Kembalian</th>
                                    <th>Meja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while($row = mysqli_fetch_assoc($result_transaksi)): 
                                    $kembalian = $row['bayar'] - $row['total'];
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['namapelanggan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['namamenu']); ?></td>
                                        <td><?php echo $row['jumlah']; ?></td>
                                        <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($row['bayar'], 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($kembalian, 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($row['namameja']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Belum ada riwayat transaksi.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 