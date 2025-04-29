<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'waiter') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

// Delete order
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM pesanan WHERE idpesanan = '$id'");
    header("Location: index.php");
    exit();
}

$query = "SELECT p.*, pl.namapelanggan, mj.namameja,
          GROUP_CONCAT(CONCAT(m.namamenu, ' (', dp.jumlah, ')') SEPARATOR ', ') as menu_items
          FROM pesanan p 
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          JOIN detail_pesanan dp ON p.idpesanan = dp.idpesanan
          JOIN menu m ON dp.idmenu = m.idmenu
          WHERE p.iduser = " . $_SESSION['user']['id'] . "
          GROUP BY p.idpesanan
          ORDER BY p.idpesanan DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Order - Sistem Kasir</title>
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
        --danger-gradient: linear-gradient(135deg, #ef4444, #dc2626);
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
    }

    .page-title {
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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

    .btn-primary {
        background: var(--primary-color);
        border: none;
    }

    .btn-warning {
        background: var(--warning-gradient);
        border: none;
        color: white;
    }

    .btn-danger {
        background: var(--danger-gradient);
        border: none;
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

    .alert {
        border: none;
        border-radius: 8px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0;
    }

    .alert-info {
        background: #f0f9ff;
        color: #0369a1;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #94a3b8;
    }

    .empty-state p {
        margin-bottom: 1.5rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .action-buttons .btn {
        padding: 0.4rem 0.75rem;
        font-size: 0.875rem;
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="page-title">
                <i class="bi bi-cart3 me-2"></i>
                Kelola Order
            </h3>
            <a href="tambah.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Tambah Order
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Menu</th>
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
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-cup-hot text-muted"></i>
                                        <?php echo htmlspecialchars($row['menu_items']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-diagram-3 text-muted"></i>
                                        <?php echo htmlspecialchars($row['namameja']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit.php?id=<?php echo $row['idpesanan']; ?>" class="btn btn-warning">
                                            <i class="bi bi-pencil"></i>
                                            Edit
                                        </a>
                                        <a href="?delete=<?php echo $row['idpesanan']; ?>" class="btn btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus order ini?')">
                                            <i class="bi bi-trash"></i>
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-cart-x"></i>
                    <p>Belum ada data order yang tersedia</p>
                    <a href="tambah.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i>
                        Tambah Order Baru
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>