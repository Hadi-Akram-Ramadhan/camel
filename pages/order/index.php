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

$query = "SELECT p.*, m.namamenu, pl.namapelanggan, mj.namameja 
          FROM pesanan p 
          JOIN menu m ON p.idmenu = m.idmenu 
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          WHERE p.iduser = " . $_SESSION['user']['id'] . "
          ORDER BY p.idpesanan DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Order - Sistem Kasir</title>
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
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h3>Kelola Order</h3>
                <a href="tambah.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Order
                </a>
            </div>
        </div>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>Menu</th>
                            <th>Jumlah</th>
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
                                <td><?php echo htmlspecialchars($row['namapelanggan']); ?></td>
                                <td><?php echo htmlspecialchars($row['namamenu']); ?></td>
                                <td><?php echo $row['jumlah']; ?></td>
                                <td><?php echo htmlspecialchars($row['namameja']); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $row['idpesanan']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $row['idpesanan']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus order ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Belum ada data order. Silakan tambahkan order baru.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 