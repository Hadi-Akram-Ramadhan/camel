<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'administrator') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namameja = mysqli_real_escape_string($conn, $_POST['namameja']);
    $kapasitas = mysqli_real_escape_string($conn, $_POST['kapasitas']);
    
    $query = "INSERT INTO meja (namameja, kapasitas) VALUES ('$namameja', '$kapasitas')";
    if(mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Meja - Sistem Kasir</title>
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

    .card-header {
        background: var(--card-bg);
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem;
    }

    .card-header h5 {
        margin: 0;
        font-weight: 600;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-body {
        padding: 1.5rem;
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

    .input-group-text {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
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

    .btn-secondary {
        background: #f1f5f9;
        border: none;
        color: #64748b;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: #475569;
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Daftar Meja
            </a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <i class="bi bi-plus-circle"></i>
                            Tambah Meja Baru
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="namameja" class="form-label">Nama Meja</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-grid-3x3"></i>
                                    </span>
                                    <input type="text" class="form-control" id="namameja" name="namameja" placeholder="Contoh: Meja 1" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="kapasitas" class="form-label">Kapasitas</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-people"></i>
                                    </span>
                                    <input type="number" class="form-control" id="kapasitas" name="kapasitas" placeholder="Jumlah orang" required>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i>
                                    Simpan Meja
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-x"></i>
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 