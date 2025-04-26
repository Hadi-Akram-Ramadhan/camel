<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'waiter') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

// Get available tables
$query_meja = "SELECT * FROM meja WHERE status = 'tersedia' ORDER BY namameja";
$result_meja = mysqli_query($conn, $query_meja);

// Get menu items
$query_menu = "SELECT * FROM menu ORDER BY namamenu";
$result_menu = mysqli_query($conn, $query_menu);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namapelanggan = mysqli_real_escape_string($conn, $_POST['namapelanggan']);
    $jeniskelamin = mysqli_real_escape_string($conn, $_POST['jeniskelamin']);
    $nohp = mysqli_real_escape_string($conn, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $idmenu = mysqli_real_escape_string($conn, $_POST['idmenu']);
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
    $idmeja = mysqli_real_escape_string($conn, $_POST['idmeja']);
    
    // Insert customer first
    $query_pelanggan = "INSERT INTO pelanggan (namapelanggan, jeniskelamin, nohp, alamat) 
                        VALUES ('$namapelanggan', '$jeniskelamin', '$nohp', '$alamat')";
    if(mysqli_query($conn, $query_pelanggan)) {
        $idpelanggan = mysqli_insert_id($conn);
        
        // Then insert order
        $query_pesanan = "INSERT INTO pesanan (idmenu, idpelanggan, jumlah, iduser, idmeja) 
                         VALUES ('$idmenu', '$idpelanggan', '$jumlah', '" . $_SESSION['user']['id'] . "', '$idmeja')";
        if(mysqli_query($conn, $query_pesanan)) {
            // Update table status
            mysqli_query($conn, "UPDATE meja SET status = 'terisi' WHERE idmeja = '$idmeja'");
            header("Location: index.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Order - Sistem Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #1e40af;
        --background: #f8fafc;
        --card-bg: #ffffff;
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

    .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }

    .card-header h4 {
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

    .section-title {
        color: #0f172a;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-label {
        font-weight: 500;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-check-input {
        width: 1.1em;
        height: 1.1em;
        margin-top: 0.25em;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn {
        padding: 0.75rem 1rem;
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
        color: #0f172a;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .alert {
        border: none;
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .form-text {
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <i class="bi bi-plus-circle me-2"></i>
                            Tambah Order Baru
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if(mysqli_num_rows($result_meja) == 0): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Tidak ada meja yang tersedia saat ini.
                        </div>
                        <?php else: ?>
                        <form method="POST">
                            <h5 class="section-title">
                                <i class="bi bi-person"></i>
                                Data Pelanggan
                            </h5>
                            <div class="mb-3">
                                <label for="namapelanggan" class="form-label">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="namapelanggan" name="namapelanggan"
                                    placeholder="Masukkan nama pelanggan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jeniskelamin" id="laki"
                                            value="1" required>
                                        <label class="form-check-label" for="laki">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jeniskelamin" id="perempuan"
                                            value="0" required>
                                        <label class="form-check-label" for="perempuan">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nohp" class="form-label">No. HP</label>
                                <input type="text" class="form-control" id="nohp" name="nohp" maxlength="13"
                                    placeholder="Masukkan nomor HP" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2" maxlength="95"
                                    placeholder="Masukkan alamat" required></textarea>
                            </div>

                            <h5 class="section-title mt-4">
                                <i class="bi bi-cart3"></i>
                                Data Pesanan
                            </h5>
                            <div class="mb-3">
                                <label for="idmenu" class="form-label">Menu</label>
                                <select class="form-select" id="idmenu" name="idmenu" required>
                                    <option value="">Pilih Menu</option>
                                    <?php while($menu = mysqli_fetch_assoc($result_menu)): ?>
                                    <option value="<?php echo $menu['idmenu']; ?>">
                                        <?php echo htmlspecialchars($menu['namamenu']); ?> -
                                        Rp <?php echo number_format($menu['harga'], 0, ',', '.'); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" min="1"
                                    placeholder="Masukkan jumlah pesanan" required>
                            </div>
                            <div class="mb-4">
                                <label for="idmeja" class="form-label">Meja</label>
                                <select class="form-select" id="idmeja" name="idmeja" required>
                                    <option value="">Pilih Meja</option>
                                    <?php while($meja = mysqli_fetch_assoc($result_meja)): ?>
                                    <option value="<?php echo $meja['idmeja']; ?>">
                                        <?php echo htmlspecialchars($meja['namameja']); ?> -
                                        Kapasitas: <?php echo $meja['kapasitas']; ?> orang
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i>
                                    Simpan Order
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i>
                                    Batal
                                </a>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>