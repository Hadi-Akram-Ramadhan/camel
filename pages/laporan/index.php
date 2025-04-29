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
$query = "SELECT t.*, pl.namapelanggan, mj.namameja,
          GROUP_CONCAT(CONCAT(m.namamenu, ' (', dp.jumlah, ')') SEPARATOR ', ') as menu_items
          FROM transaksi t
          JOIN pesanan p ON t.idpesanan = p.idpesanan
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          JOIN detail_pesanan dp ON p.idpesanan = dp.idpesanan
          JOIN menu m ON dp.idmenu = m.idmenu
          WHERE DATE(t.tanggal) BETWEEN '$start_date' AND '$end_date'
          GROUP BY t.idtransaksi, pl.namapelanggan, mj.namameja
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
        
        // Count menu items for menu terlaris
        $items = explode(', ', $row['menu_items']);
        foreach($items as $item) {
            preg_match('/(.+) \((\d+)\)/', $item, $matches);
            if (isset($matches[1]) && isset($matches[2])) {
                $menu_name = trim($matches[1]); // Clean up the menu name
                $quantity = (int)$matches[2];
                if (!isset($menu_terlaris[$menu_name])) {
                    $menu_terlaris[$menu_name] = 0;
                }
                $menu_terlaris[$menu_name] += $quantity;
            }
        }
    }
    // Reset pointer
    mysqli_data_seek($result, 0);
}

// Sort menu terlaris
if (!empty($menu_terlaris)) {
    arsort($menu_terlaris);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Kasir</title>
    <link rel="icon" type="image/x-icon" href="assets/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #1e40af;
        --success-color: #059669;
        --info-color: #0891b2;
        --background: #f8fafc;
        --card-bg: #ffffff;
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
        overflow: hidden;
    }

    .page-title {
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label {
        font-weight: 500;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn {
        padding: 0.75rem 1.5rem;
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

    .btn-primary {
        background: var(--primary-color);
        border: none;
    }

    .btn-outline-primary {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-outline-primary:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
    }

    .stats-card {
        padding: 1.5rem;
        border-radius: 12px;
        height: 100%;
    }

    .stats-card.primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }

    .stats-card.success {
        background: linear-gradient(135deg, #10b981, var(--success-color));
    }

    .stats-card.info {
        background: linear-gradient(135deg, #06b6d4, var(--info-color));
    }

    .stats-card h5 {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.75rem;
    }

    .stats-card h3 {
        color: white;
        font-weight: 600;
        margin-bottom: 0;
        font-size: 1.5rem;
    }

    .stats-card i {
        font-size: 1.5rem;
        color: rgba(255, 255, 255, 0.5);
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

    .table-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #0f172a;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: white !important;
        }

        .card {
            box-shadow: none !important;
        }

        .stats-card {
            border: 1px solid #e2e8f0;
        }

        .stats-card h5,
        .stats-card h3 {
            color: #0f172a !important;
        }
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg no-print">
        <div class="container">
            <a class="navbar-brand" href="../../dashboard.php">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </nav>

    <div class="container py-4">
        <h3 class="page-title">
            <i class="bi bi-graph-up"></i>
            Laporan Transaksi
        </h3>

        <!-- Filter Form -->
        <div class="card mb-4 no-print">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="<?php echo $start_date; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="<?php echo $end_date; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">
                            <i class="bi bi-funnel"></i>
                            Filter Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stats-card primary">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5>Total Pendapatan</h5>
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <h3>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card success">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5>Total Transaksi</h5>
                        <i class="bi bi-receipt"></i>
                    </div>
                    <h3><?php echo $total_transaksi; ?> Transaksi</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5>Menu Terlaris</h5>
                        <i class="bi bi-star"></i>
                    </div>
                    <h3><?php 
                        if (!empty($menu_terlaris)) {
                            $top_menu = array_key_first($menu_terlaris);
                            echo htmlspecialchars($top_menu);
                        } else {
                            echo '-';
                        }
                    ?></h3>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-table me-2"></i>
                        Detail Transaksi
                    </h5>
                    <a href="print.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                        target="_blank" class="btn btn-outline-primary no-print">
                        <i class="bi bi-printer"></i>
                        Cetak Laporan
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if(mysqli_num_rows($result) > 0): ?>
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
                                while($row = mysqli_fetch_assoc($result)): 
                                    $kembalian = $row['bayar'] - $row['total'];
                                ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <div class="table-info">
                                        <i class="bi bi-calendar2"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-info">
                                        <i class="bi bi-person"></i>
                                        <?php echo htmlspecialchars($row['namapelanggan']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-info">
                                        <i class="bi bi-cup-hot"></i>
                                        <?php echo htmlspecialchars($row['menu_items']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-info">
                                        <i class="bi bi-cash"></i>
                                        Rp <?php echo number_format($row['total'], 0, ',', '.'); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-info">
                                        <i class="bi bi-wallet2"></i>
                                        Rp <?php echo number_format($row['bayar'], 0, ',', '.'); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-info">
                                        <i class="bi bi-cash-coin"></i>
                                        Rp <?php echo number_format($kembalian, 0, ',', '.'); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-info">
                                        <i class="bi bi-grid-3x3"></i>
                                        <?php echo htmlspecialchars($row['namameja']); ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-4 text-center">
                    <i class="bi bi-inbox text-muted" style="font-size: 2rem"></i>
                    <p class="mt-2 mb-0 text-muted">Tidak ada transaksi pada periode ini.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>