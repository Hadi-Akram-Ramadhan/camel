<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'kasir') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

// Get pending orders (not yet in transactions)
$query = "SELECT p.*, pl.namapelanggan, mj.namameja,
          GROUP_CONCAT(CONCAT(m.namamenu, ' (', dp.jumlah, ')') SEPARATOR ', ') as menu_items,
          SUM(dp.jumlah * m.harga) as total
          FROM pesanan p 
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          JOIN detail_pesanan dp ON p.idpesanan = dp.idpesanan
          JOIN menu m ON dp.idmenu = m.idmenu
          LEFT JOIN transaksi t ON p.idpesanan = t.idpesanan
          WHERE t.idtransaksi IS NULL
          GROUP BY p.idpesanan, pl.namapelanggan, mj.namameja
          ORDER BY p.idpesanan DESC";
$result = mysqli_query($conn, $query);

// Get completed transactions
$query_transaksi = "SELECT t.*, pl.namapelanggan, mj.namameja,
                    GROUP_CONCAT(CONCAT(m.namamenu, ' (', dp.jumlah, ')') SEPARATOR ', ') as menu_items
                    FROM transaksi t
                    JOIN pesanan p ON t.idpesanan = p.idpesanan
                    JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
                    JOIN meja mj ON p.idmeja = mj.idmeja
                    JOIN detail_pesanan dp ON p.idpesanan = dp.idpesanan
                    JOIN menu m ON dp.idmenu = m.idmenu
                    GROUP BY t.idtransaksi, pl.namapelanggan, mj.namameja
                    ORDER BY t.tanggal DESC";
$result_transaksi = mysqli_query($conn, $query_transaksi);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi - Sistem Kasir</title>
    <link rel="icon" type="image/x-icon" href="../../assets/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #1e40af;
        --background: #f8fafc;
        --card-bg: #ffffff;
        --warning-gradient: linear-gradient(135deg, #f59e0b, #d97706);
        --success-gradient: linear-gradient(135deg, #10b981, #059669);
    }

    body {
        background-color: var(--background);
        font-family: system-ui, -apple-system, sans-serif;
    }

    .navbar {
        background: var(--card-bg) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        color: #0f172a !important;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        padding: 1.25rem;
    }

    .card-header.warning {
        background: var(--warning-gradient);
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .card-header.success {
        background: var(--success-gradient);
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .card-header h5 {
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-body {
        padding: 1.25rem;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        background-color: #f8fafc;
        padding: 1rem;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .table-striped>tbody>tr:nth-of-type(odd)>* {
        background-color: #f8fafc;
    }

    .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-success {
        background: var(--success-gradient);
        border: none;
        color: white;
    }

    .btn-success:hover {
        background: var(--success-gradient);
        color: white;
        opacity: 0.9;
    }

    .page-title {
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert {
        border: none;
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-info {
        background: #f0f9ff;
        color: #0369a1;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .price {
        font-weight: 600;
        color: #0f172a;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .status-badge.completed {
        background: #dcfce7;
        color: #059669;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../../dashboard.php">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </nav>

    <div class="container py-4">
        <h3 class="page-title">
            <i class="bi bi-cash-stack me-2"></i>
            Kelola Transaksi
        </h3>

        <!-- Pending Orders -->
        <div class="card">
            <div class="card-header warning">
                <h5>
                    <i class="bi bi-clock-history"></i>
                    Order yang Belum Dibayar
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Menu</th>
                                <th>Total</th>
                                <th>Meja</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no = 1;
                                while($row = mysqli_fetch_assoc($result)): 
                                ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person text-muted"></i>
                                        <?php echo htmlspecialchars($row['namapelanggan']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($row['menu_items']); ?></td>
                                <td class="price">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-diagram-3 text-muted"></i>
                                        <?php echo htmlspecialchars($row['namameja']); ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="bayar.php?id=<?php echo $row['idpesanan']; ?>"
                                        lass="btn btn-success btn-sm">
                                        <i class="bi bi-cash"></i>
                                        Bayar
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-cash-coin"></i>
                    <p>Tidak ada order yang perlu dibayar.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Completed Transactions -->
        <div class="card">
            <div class="card-header success">
                <h5>
                    <i class="bi bi-journal-check"></i>
                    Riwayat Transaksi
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if(mysqli_num_rows($result_transaksi) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Menu</th>
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
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar3 text-muted"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person text-muted"></i>
                                        <?php echo htmlspecialchars($row['namapelanggan']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($row['menu_items']); ?></td>
                                <td class="price">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                <td class="price">Rp <?php echo number_format($row['bayar'], 0, ',', '.'); ?></td>
                                <td class="price">Rp <?php echo number_format($kembalian, 0, ',', '.'); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-diagram-3 text-muted"></i>
                                        <?php echo htmlspecialchars($row['namameja']); ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-journal-x"></i>
                    <p>Belum ada riwayat transaksi.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>