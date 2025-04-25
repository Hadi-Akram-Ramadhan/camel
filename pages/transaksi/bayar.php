<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'kasir') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT p.*, m.namamenu, m.harga, pl.namapelanggan, mj.namameja, mj.idmeja
          FROM pesanan p 
          JOIN menu m ON p.idmenu = m.idmenu 
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          LEFT JOIN transaksi t ON p.idpesanan = t.idpesanan
          WHERE p.idpesanan = '$id' AND t.idtransaksi IS NULL";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) != 1) {
    header("Location: index.php");
    exit();
}

$order = mysqli_fetch_assoc($result);
$total = $order['jumlah'] * $order['harga'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bayar = mysqli_real_escape_string($conn, $_POST['bayar']);
    
    if($bayar >= $total) {
        $query_transaksi = "INSERT INTO transaksi (idpesanan, total, bayar) VALUES ('$id', '$total', '$bayar')";
        if(mysqli_query($conn, $query_transaksi)) {
            // Set table as available
            mysqli_query($conn, "UPDATE meja SET status = 'tersedia' WHERE idmeja = '" . $order['idmeja'] . "'");
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
    <title>Proses Pembayaran - Sistem Kasir</title>
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
                        <h4 class="mb-0">Proses Pembayaran</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5>Detail Order:</h5>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td>Pelanggan</td>
                                    <td>:</td>
                                    <td><?php echo htmlspecialchars($order['namapelanggan']); ?></td>
                                </tr>
                                <tr>
                                    <td>Menu</td>
                                    <td>:</td>
                                    <td><?php echo htmlspecialchars($order['namamenu']); ?></td>
                                </tr>
                                <tr>
                                    <td>Jumlah</td>
                                    <td>:</td>
                                    <td><?php echo $order['jumlah']; ?></td>
                                </tr>
                                <tr>
                                    <td>Harga</td>
                                    <td>:</td>
                                    <td>Rp <?php echo number_format($order['harga'], 0, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>:</td>
                                    <td>Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td>Meja</td>
                                    <td>:</td>
                                    <td><?php echo htmlspecialchars($order['namameja']); ?></td>
                                </tr>
                            </table>
                        </div>

                        <form method="POST" id="formBayar">
                            <div class="mb-3">
                                <label for="bayar" class="form-label">Jumlah Bayar</label>
                                <input type="number" class="form-control" id="bayar" name="bayar" min="<?php echo $total; ?>" required>
                                <div class="form-text">Minimal pembayaran: Rp <?php echo number_format($total, 0, ',', '.'); ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="kembalian" class="form-label">Kembalian</label>
                                <input type="text" class="form-control" id="kembalian" readonly>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">Proses Pembayaran</button>
                                <a href="index.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calculate change
        document.getElementById('bayar').addEventListener('input', function() {
            const total = <?php echo $total; ?>;
            const bayar = this.value || 0;
            const kembalian = bayar - total;
            document.getElementById('kembalian').value = 'Rp ' + kembalian.toLocaleString('id-ID');
        });

        // Validate payment
        document.getElementById('formBayar').addEventListener('submit', function(e) {
            const total = <?php echo $total; ?>;
            const bayar = document.getElementById('bayar').value;
            if(bayar < total) {
                e.preventDefault();
                alert('Jumlah pembayaran kurang dari total yang harus dibayar!');
            }
        });
    </script>
</body>
</html> 