<?php
session_start();
if(!isset($_SESSION['user'])) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

// Filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Base query
$query = "SELECT t.*, pl.namapelanggan, mj.namameja,
          GROUP_CONCAT(CONCAT(m.namamenu, ' (', dp.jumlah, ')') SEPARATOR ', ') as menu_items
          FROM transaksi t
          JOIN pesanan p ON t.idpesanan = p.idpesanan
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          JOIN detail_pesanan dp ON p.idpesanan = dp.idpesanan
          JOIN menu m ON dp.idmenu = m.idmenu
          WHERE DATE(t.tanggal) BETWEEN '$start_date' AND '$end_date'
          GROUP BY t.idtransaksi, pl.namapelanggan, mj.namameja
          ORDER BY t.tanggal DESC";
$result = mysqli_query($conn, $query);

// Calculate summary
$total_pendapatan = 0;
$total_transaksi = 0;
$menu_terlaris = array();

if($result) {
    $total_transaksi = mysqli_num_rows($result);
    while($row = mysqli_fetch_assoc($result)) {
        $total_pendapatan += $row['total'];
        
        // Count menu items for menu terlaris
        $items = explode(', ', $row['menu_items']);
        foreach($items as $item) {
            preg_match('/(.+) \((\d+)\)/', $item, $matches);
            if (isset($matches[1]) && isset($matches[2])) {
                $menu_name = trim($matches[1]);
                $quantity = (int)$matches[2];
                if (!isset($menu_terlaris[$menu_name])) {
                    $menu_terlaris[$menu_name] = 0;
                }
                $menu_terlaris[$menu_name] += $quantity;
            }
        }
    }
    // Reset pointer
    mysqli_data_seek($result, 0);
}

// Sort menu terlaris
if (!empty($menu_terlaris)) {
    arsort($menu_terlaris);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <link rel="icon" type="image/x-icon" href="../../assets/icon.png">
    <style>
    @page {
        size: A4;
        margin: 0;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        font-size: 12px;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }

    .header h1 {
        margin: 0;
        font-size: 24px;
    }

    .header p {
        margin: 5px 0;
    }

    .info {
        margin-bottom: 20px;
    }

    .info p {
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f0f0f0;
    }

    .summary {
        margin-top: 20px;
        border-top: 2px solid #000;
        padding-top: 10px;
    }

    .summary p {
        margin: 5px 0;
    }

    .footer {
        margin-top: 30px;
        text-align: right;
    }

    .footer p {
        margin: 5px 0;
    }
    </style>
    <script>
    window.onload = function() {
        window.print();
    }
    </script>
</head>

<body>
    <div class="header">
        <h1>Laporan Transaksi</h1>
        <p>Periode: <?php echo date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date)); ?>
        </p>
    </div>

    <div class="info">
        <p><strong>Total Pendapatan:</strong> Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></p>
        <p><strong>Total Transaksi:</strong> <?php echo $total_transaksi; ?> Transaksi</p>
        <p><strong>Menu Terlaris:</strong> <?php echo !empty($menu_terlaris) ? array_key_first($menu_terlaris) : '-'; ?>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Menu</th>
                <th>Total</th>
                <th>Bayar</th>
                <th>Kembalian</th>
                <th>Meja</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($row = mysqli_fetch_assoc($result)): 
                $kembalian = $row['bayar'] - $row['total'];
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                <td><?php echo htmlspecialchars($row['namapelanggan']); ?></td>
                <td><?php echo htmlspecialchars($row['menu_items']); ?></td>
                <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($row['bayar'], 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($kembalian, 0, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($row['namameja']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Oleh: <?php echo $_SESSION['user']['nama']; ?></p>
    </div>
</body>

</html>