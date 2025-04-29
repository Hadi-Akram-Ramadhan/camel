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
    <link rel="icon" type="image/x-icon" href="assets/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #1e40af;
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

    .navbar-brand,
    .nav-link {
        color: #0f172a !important;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .nav-link:hover {
        color: var(--primary-color) !important;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .feature-card {
        height: 100%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }

    .feature-card .card-body {
        padding: 1.5rem;
    }

    .feature-card h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .btn-access {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-access:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        transform: scale(1.02);
    }

    .welcome-card {
        background: var(--card-bg);
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .welcome-title {
        color: #0f172a;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .role-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: var(--primary-color);
        color: white;
        border-radius: 9999px;
        font-size: 0.875rem;
    }

    .feature-icon {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-shop me-2"></i>Sistem Kasir
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if($role == 'administrator'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/user/"><i class="bi bi-people me-1"></i>User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/meja/"><i class="bi bi-table me-1"></i>Meja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/menu/"><i class="bi bi-card-list me-1"></i>Menu</a>
                    </li>
                    <?php endif; ?>

                    <?php if($role == 'waiter'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/menu/"><i class="bi bi-card-list me-1"></i>Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/order/"><i class="bi bi-cart me-1"></i>Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/laporan/"><i class="bi bi-file-text me-1"></i>Laporan</a>
                    </li>
                    <?php endif; ?>

                    <?php if($role == 'kasir'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/transaksi/"><i class="bi bi-cash me-1"></i>Transaksi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/laporan/"><i class="bi bi-file-text me-1"></i>Laporan</a>
                    </li>
                    <?php endif; ?>

                    <?php if($role == 'owner'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/laporan/"><i class="bi bi-file-text me-1"></i>Laporan</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="navbarDropdown"
                            role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <?php echo $nama; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                            <li><a class="dropdown-item" href="auth/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a></li>
                        </ul>
                    </li>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="welcome-card">
            <h4 class="welcome-title">Selamat datang, <?php echo $nama; ?>!</h4>
            <span class="role-badge"><?php echo ucfirst($role); ?></span>
        </div>

        <div class="row g-4">
            <?php if($role == 'administrator'): ?>
            <div class="col-md-4">
                <div class="card feature-card text-white">
                    <div class="card-body">
                        <i class="bi bi-people-fill feature-icon"></i>
                        <h5>Kelola User</h5>
                        <p class="text-white-50 mb-4">Manajemen pengguna sistem</p>
                        <a href="pages/user/" class="btn btn-access w-100">
                            <i class="bi bi-arrow-right me-2"></i>Akses
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-white">
                    <div class="card-body">
                        <i class="bi bi-table feature-icon"></i>
                        <h5>Kelola Meja</h5>
                        <p class="text-white-50 mb-4">Pengaturan meja restoran</p>
                        <a href="pages/meja/" class="btn btn-access w-100">
                            <i class="bi bi-arrow-right me-2"></i>Akses
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-white">
                    <div class="card-body">
                        <i class="bi bi-card-list feature-icon"></i>
                        <h5>Kelola Menu</h5>
                        <p class="text-white-50 mb-4">Manajemen menu restoran</p>
                        <a href="pages/menu/" class="btn btn-access w-100">
                            <i class="bi bi-arrow-right me-2"></i>Akses
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($role == 'waiter'): ?>
            <div class="col-md-4">
                <div class="card feature-card text-white">
                    <div class="card-body">
                        <i class="bi bi-card-list feature-icon"></i>
                        <h5>Menu</h5>
                        <p class="text-white-50 mb-4">Lihat daftar menu</p>
                        <a href="pages/menu/" class="btn btn-access w-100">
                            <i class="bi bi-arrow-right me-2"></i>Akses
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-white">
                    <div class="card-body">
                        <i class="bi bi-cart feature-icon"></i>
                        <h5>Order</h5>
                        <p class="text-white-50 mb-4">Kelola pesanan pelanggan</p>
                        <a href="pages/order/" class="btn btn-access w-100">
                            <i class="bi bi-arrow-right me-2"></i>Akses
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-white">
                    <div class="card-body">
                        <i class="bi bi-file-text feature-icon"></i>
                        <h5>Laporan</h5>
                        <p class="text-white-50 mb-4">Lihat laporan pesanan</p>
                        <a href="pages/laporan/" class="btn btn-access w-100">
                            <i class="bi bi-arrow-right me-2"></i>Akses
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($role == 'kasir'): ?>
            <div class="col-md-6">
                <div class="card feature-card text-white">
                    <div class="card-body">
                        <i class="bi bi-cash feature-icon"></i>
                        <h5>Transaksi</h5>
                        <p class="text-white-50 mb-4">Kelola pembayaran</p>
                        <a href="pages/transaksi/" class="btn btn-access w-100">
                            <i class="bi bi-arrow-right me-2"></i>Akses
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card feature-card text-white">
                    <div class="card-body">
                        <i class="bi bi-file-text feature-icon"></i>
                        <h5>Laporan</h5>
                        <p class="text-white-50 mb-4">Lihat laporan transaksi</p>
                        <a href="pages/laporan/" class="btn btn-access w-100">
                            <i class="bi bi-arrow-right me-2"></i>Akses
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($role == 'owner'): ?>
            <div class="col-md-6 mx-auto">
                <div class="card feature-card text-white">
                    <div class="card-body">
                        <i class="bi bi-file-text feature-icon"></i>
                        <h5>Laporan</h5>
                        <p class="text-white-50 mb-4">Lihat laporan bisnis</p>
                        <a href="pages/laporan/" class="btn btn-access w-100">
                            <i class="bi bi-arrow-right me-2"></i>Akses
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>