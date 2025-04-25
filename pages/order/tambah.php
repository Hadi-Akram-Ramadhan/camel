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
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../../dashboard.php">Sistem Kasir</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Tambah Order</h4>
                    </div>
                    <div class="card-body">
                        <?php if(mysqli_num_rows($result_meja) == 0): ?>
                            <div class="alert alert-warning">
                                Tidak ada meja yang tersedia saat ini.
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <h5 class="mb-3">Data Pelanggan</h5>
                                <div class="mb-3">
                                    <label for="namapelanggan" class="form-label">Nama Pelanggan</label>
                                    <input type="text" class="form-control" id="namapelanggan" name="namapelanggan" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jeniskelamin" id="laki" value="1" required>
                                            <label class="form-check-label" for="laki">Laki-laki</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jeniskelamin" id="perempuan" value="0" required>
                                            <label class="form-check-label" for="perempuan">Perempuan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="nohp" class="form-label">No. HP</label>
                                    <input type="text" class="form-control" id="nohp" name="nohp" maxlength="13" required>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="2" maxlength="95" required></textarea>
                                </div>

                                <h5 class="mb-3 mt-4">Data Pesanan</h5>
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
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                                </div>
                                <div class="mb-3">
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
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="index.php" class="btn btn-secondary">Batal</a>
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