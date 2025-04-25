<?php
session_start();
if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] != 'administrator' && $_SESSION['user']['role'] != 'waiter')) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namamenu = mysqli_real_escape_string($conn, $_POST['namamenu']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    
    $query = "INSERT INTO menu (namamenu, harga) VALUES ('$namamenu', '$harga')";
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
    <title>Tambah Menu - Sistem Kasir</title>
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
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Tambah Menu</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="namamenu" class="form-label">Nama Menu</label>
                                <input type="text" class="form-control" id="namamenu" name="namamenu" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="index.php" class="btn btn-secondary">Batal</a>
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