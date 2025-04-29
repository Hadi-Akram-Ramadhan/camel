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
$query = "SELECT p.*, pl.namapelanggan, mj.namameja, mj.idmeja,
          GROUP_CONCAT(CONCAT(m.namamenu, ' (', dp.jumlah, ')') SEPARATOR ', ') as menu_items,
          SUM(dp.jumlah * m.harga) as total
          FROM pesanan p 
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          JOIN detail_pesanan dp ON p.idpesanan = dp.idpesanan
          JOIN menu m ON dp.idmenu = m.idmenu
          LEFT JOIN transaksi t ON p.idpesanan = t.idpesanan
          WHERE p.idpesanan = '$id' AND t.idtransaksi IS NULL
          GROUP BY p.idpesanan, pl.namapelanggan, mj.namameja, mj.idmeja";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) != 1) {
    header("Location: index.php");
    exit();
}

$order = mysqli_fetch_assoc($result);
$total = $order['total'];

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
    <link rel="icon" type="image/x-icon" href="../../assets/icon.png">
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

    .order-details {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .order-details h5 {
        color: #0f172a;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-table {
        width: 100%;
    }

    .detail-table td {
        padding: 0.5rem 0;
        color: #475569;
    }

    .detail-table td:first-child {
        width: 35%;
        color: #64748b;
    }

    .detail-table td:nth-child(2) {
        width: 5%;
        text-align: center;
    }

    .detail-table .total-row td {
        padding-top: 1rem;
        font-weight: 600;
        color: #0f172a;
        border-top: 1px solid #e2e8f0;
    }

    .form-label {
        font-weight: 500;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-control:disabled {
        background: #f8fafc;
        color: #64748b;
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

    .btn-success {
        background: var(--success-gradient);
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

    .form-text {
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .price-input {
        font-size: 1.25rem;
        font-weight: 600;
        color: #0f172a;
    }

    .price-display {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--primary-color);
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
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <i class="bi bi-cash-coin me-2"></i>
                            Proses Pembayaran
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="order-details">
                            <h5>
                                <i class="bi bi-info-circle"></i>
                                Detail Order
                            </h5>
                            <table class="detail-table">
                                <tr>
                                    <td>Pelanggan</td>
                                    <td>:</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person text-muted"></i>
                                            <?php echo htmlspecialchars($order['namapelanggan']); ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Menu</td>
                                    <td>:</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-cup-hot text-muted"></i>
                                            <?php echo htmlspecialchars($order['menu_items']); ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>:</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-receipt text-primary"></i>
                                            Rp <?php echo number_format($order['total'], 0, ',', '.'); ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Meja</td>
                                    <td>:</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-diagram-3 text-muted"></i>
                                            <?php echo htmlspecialchars($order['namameja']); ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <form method="POST" id="formBayar">
                            <div class="mb-3">
                                <label for="bayar" class="form-label">Jumlah Bayar</label>
                                <input type="number" class="form-control price-input" id="bayar" name="bayar"
                                    min="<?php echo $total; ?>" placeholder="Masukkan jumlah pembayaran" required>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i>
                                    Minimal pembayaran: Rp <?php echo number_format($total, 0, ',', '.'); ?>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="kembalian" class="form-label">Kembalian</label>
                                <input type="text" class="form-control price-display" id="kembalian" readonly>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i>
                                    Proses Pembayaran
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i>
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
    <script>
    // Format number to currency
    function formatCurrency(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

    // Calculate change
    document.getElementById('bayar').addEventListener('input', function() {
        const total = <?php echo $total; ?>;
        const bayar = this.value || 0;
        const kembalian = bayar - total;
        document.getElementById('kembalian').value = formatCurrency(kembalian);
    });

    // Validate payment
    document.getElementById('formBayar').addEventListener('submit', function(e) {
        const total = <?php echo $total; ?>;
        const bayar = document.getElementById('bayar').value;
        if (bayar < total) {
            e.preventDefault();
            alert('Jumlah pembayaran kurang dari total yang harus dibayar!');
        }
    });
    </script>
</body>

</html>