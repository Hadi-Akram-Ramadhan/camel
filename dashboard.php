<?php
session_start();
if(!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['user']['role'];
$nama = $_SESSION['user']['nama'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem Kasir</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if($role == 'administrator'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/meja/"><i class="bi bi-table"></i> Meja</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/menu/"><i class="bi bi-card-list"></i> Menu</a>
                        </li>
                    <?php endif; ?>

                    <?php if($role == 'waiter'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/menu/"><i class="bi bi-card-list"></i> Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/order/"><i class="bi bi-cart"></i> Order</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/laporan/"><i class="bi bi-file-text"></i> Laporan</a>
                        </li>
                    <?php endif; ?>

                    <?php if($role == 'kasir'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/transaksi/"><i class="bi bi-cash"></i> Transaksi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/laporan/"><i class="bi bi-file-text"></i> Laporan</a>
                        </li>
                    <?php endif; ?>

                    <?php if($role == 'owner'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/laporan/"><i class="bi bi-file-text"></i> Laporan</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> <?php echo $nama; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Selamat datang, <?php echo $nama; ?>!</h4>
                        <p>Anda login sebagai <?php echo $role; ?>.</p>
                        
                        <div class="row mt-4">
                            <?php if($role == 'administrator'): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h5><i class="bi bi-table"></i> Kelola Meja</h5>
                                            <a href="pages/meja/" class="btn btn-light mt-2">Akses</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h5><i class="bi bi-card-list"></i> Kelola Menu</h5>
                                            <a href="pages/menu/" class="btn btn-light mt-2">Akses</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if($role == 'waiter'): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h5><i class="bi bi-card-list"></i> Kelola Menu</h5>
                                            <a href="pages/menu/" class="btn btn-light mt-2">Akses</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h5><i class="bi bi-cart"></i> Kelola Order</h5>
                                            <a href="pages/order/" class="btn btn-light mt-2">Akses</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if($role == 'kasir'): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-warning">
                                        <div class="card-body">
                                            <h5><i class="bi bi-cash"></i> Kelola Transaksi</h5>
                                            <a href="pages/transaksi/" class="btn btn-light mt-2">Akses</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if($role != 'administrator'): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-secondary text-white">
                                        <div class="card-body">
                                            <h5><i class="bi bi-file-text"></i> Laporan</h5>
                                            <a href="pages/laporan/" class="btn btn-light mt-2">Akses</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 