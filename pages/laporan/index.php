<?php
session_start();
if(!isset($_SESSION['user'])) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

// Filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Base query
$query = "SELECT t.*, p.jumlah, m.namamenu, m.harga, pl.namapelanggan, mj.namameja 
          FROM transaksi t
          JOIN pesanan p ON t.idpesanan = p.idpesanan
          JOIN menu m ON p.idmenu = m.idmenu
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          WHERE DATE(t.tanggal) BETWEEN '$start_date' AND '$end_date'
          ORDER BY t.tanggal DESC";
$result = mysqli_query($conn, $query);

// Calculate summary
$total_pendapatan = 0;
$total_transaksi = 0;
$menu_terlaris = array();

if($result) {
    $total_transaksi = mysqli_num_rows($result);
    while($row = mysqli_fetch_assoc($result)) {
        $total_pendapatan += $row['total'];
        if(isset($menu_terlaris[$row['namamenu']])) {
            $menu_terlaris[$row['namamenu']] += $row['jumlah'];
        } else {
            $menu_terlaris[$row['namamenu']] = $row['jumlah'];
        }
    }
    // Reset pointer
    mysqli_data_seek($result, 0);
}

// Sort menu terlaris
arsort($menu_terlaris);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Kasir</title>
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
        <h3>Laporan Transaksi</h3>
        
        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pendapatan</h5>
                        <h3 class="mb-0">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Transaksi</h5>
                        <h3 class="mb-0"><?php echo $total_transaksi; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Menu Terlaris</h5>
                        <h3 class="mb-0"><?php 
                            $top_menu = array_key_first($menu_terlaris);
                            echo $top_menu ? htmlspecialchars($top_menu) : '-';
                        ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Transaksi</h5>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if(mysqli_num_rows($result) > 0): ?>
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
                                while($row = mysqli_fetch_assoc($result)): 
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
                        Tidak ada transaksi pada periode ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 