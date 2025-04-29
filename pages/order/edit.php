<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'waiter') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT p.*, pl.*, mj.idmeja, mj.namameja, mj.kapasitas,
          GROUP_CONCAT(CONCAT(m.namamenu, ' (', dp.jumlah, ')') SEPARATOR ', ') as menu_items
          FROM pesanan p 
          JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan
          JOIN meja mj ON p.idmeja = mj.idmeja
          JOIN detail_pesanan dp ON p.idpesanan = dp.idpesanan
          JOIN menu m ON dp.idmenu = m.idmenu
          WHERE p.idpesanan = '$id' AND p.iduser = " . $_SESSION['user']['id'] . "
          GROUP BY p.idpesanan, pl.idpelanggan, mj.idmeja";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) != 1) {
    header("Location: index.php");
    exit();
}

$order = mysqli_fetch_assoc($result);

// Get menu items
$query_menu = "SELECT * FROM menu ORDER BY namamenu";
$result_menu = mysqli_query($conn, $query_menu);
$menu_list = [];
while($menu = mysqli_fetch_assoc($result_menu)) {
    $menu_list[] = $menu;
}

// Get available tables
$query_meja = "SELECT * FROM meja WHERE status = 'tersedia' OR idmeja = " . $order['idmeja'] . " ORDER BY namameja";
$result_meja = mysqli_query($conn, $query_meja);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namapelanggan = mysqli_real_escape_string($conn, $_POST['namapelanggan']);
    $jeniskelamin = mysqli_real_escape_string($conn, $_POST['jeniskelamin']);
    $nohp = mysqli_real_escape_string($conn, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $idmeja_baru = mysqli_real_escape_string($conn, $_POST['idmeja']);
    
    // Update customer data
    $query_pelanggan = "UPDATE pelanggan SET 
                        namapelanggan = '$namapelanggan',
                        jeniskelamin = '$jeniskelamin',
                        nohp = '$nohp',
                        alamat = '$alamat'
                        WHERE idpelanggan = '" . $order['idpelanggan'] . "'";
    
    if(mysqli_query($conn, $query_pelanggan)) {
        // If table is changed
        if($idmeja_baru != $order['idmeja']) {
            // Set old table as available
            mysqli_query($conn, "UPDATE meja SET status = 'tersedia' WHERE idmeja = '" . $order['idmeja'] . "'");
            // Set new table as occupied
            mysqli_query($conn, "UPDATE meja SET status = 'terisi' WHERE idmeja = '$idmeja_baru'");
        }
        
        // Update order
        $query_pesanan = "UPDATE pesanan SET 
                         idmeja = '$idmeja_baru'
                         WHERE idpesanan = '$id'";
        
        if(mysqli_query($conn, $query_pesanan)) {
            // Delete old menu items
            mysqli_query($conn, "DELETE FROM detail_pesanan WHERE idpesanan = '$id'");
            
            // Insert new menu items
            $idmenu_array = $_POST['idmenu'];
            $jumlah_array = $_POST['jumlah'];
            
            $success = true;
            for($i = 0; $i < count($idmenu_array); $i++) {
                $idmenu = mysqli_real_escape_string($conn, $idmenu_array[$i]);
                $jumlah = mysqli_real_escape_string($conn, $jumlah_array[$i]);
                
                $query_detail = "INSERT INTO detail_pesanan (idpesanan, idmenu, jumlah) 
                               VALUES ('$id', '$idmenu', '$jumlah')";
                if(!mysqli_query($conn, $query_detail)) {
                    $success = false;
                    break;
                }
            }
            
            if($success) {
                header("Location: index.php");
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order - Sistem Kasir</title>
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
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit Order
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <h5 class="section-title">
                                <i class="bi bi-person"></i>
                                Data Pelanggan
                            </h5>
                            <div class="mb-3">
                                <label for="namapelanggan" class="form-label">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="namapelanggan" name="namapelanggan"
                                    value="<?php echo htmlspecialchars($order['namapelanggan']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jeniskelamin" id="laki"
                                            value="1" <?php echo $order['jeniskelamin'] == 1 ? 'checked' : ''; ?>
                                            required>
                                        <label class="form-check-label" for="laki">Laki-laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jeniskelamin" id="perempuan"
                                            value="0" <?php echo $order['jeniskelamin'] == 0 ? 'checked' : ''; ?>
                                            required>
                                        <label class="form-check-label" for="perempuan">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nohp" class="form-label">No. HP</label>
                                <input type="text" class="form-control" id="nohp" name="nohp" maxlength="13"
                                    value="<?php echo htmlspecialchars($order['nohp']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2" maxlength="95"
                                    required><?php echo htmlspecialchars($order['alamat']); ?></textarea>
                            </div>

                            <h5 class="section-title mt-4">
                                <i class="bi bi-cart3"></i>
                                Data Pesanan
                            </h5>
                            <div id="menu-container">
                                <?php
                                $menu_items = explode(', ', $order['menu_items']);
                                foreach($menu_items as $index => $item):
                                    $parts = explode(' (', $item);
                                    $menu_name = $parts[0];
                                    $jumlah = rtrim($parts[1], ')');
                                ?>
                                <div class="menu-item mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Menu</label>
                                            <select class="form-select" name="idmenu[]" required>
                                                <option value="">Pilih Menu</option>
                                                <?php foreach($menu_list as $menu): ?>
                                                <option value="<?php echo $menu['idmenu']; ?>"
                                                    <?php echo $menu['namamenu'] == $menu_name ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($menu['namamenu']); ?> -
                                                    Rp <?php echo number_format($menu['harga'], 0, ',', '.'); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Jumlah</label>
                                            <input type="number" class="form-control" name="jumlah[]" min="1"
                                                value="<?php echo $jumlah; ?>" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger remove-menu"
                                                <?php echo $index === 0 ? 'style="display: none;"' : ''; ?>>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-secondary mb-3" id="add-menu">
                                <i class="bi bi-plus"></i> Tambah Menu
                            </button>
                            <div class="mb-4">
                                <label for="idmeja" class="form-label">Meja</label>
                                <select class="form-select" id="idmeja" name="idmeja" required>
                                    <?php while($meja = mysqli_fetch_assoc($result_meja)): ?>
                                    <option value="<?php echo $meja['idmeja']; ?>"
                                        <?php echo $meja['idmeja'] == $order['idmeja'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($meja['namameja']); ?> -
                                        Kapasitas: <?php echo $meja['kapasitas']; ?> orang
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i>
                                    Simpan Perubahan
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
    document.addEventListener('DOMContentLoaded', function() {
        const menuContainer = document.getElementById('menu-container');
        const addMenuBtn = document.getElementById('add-menu');

        // Clone menu item template
        function cloneMenuItem() {
            const template = menuContainer.querySelector('.menu-item').cloneNode(true);
            template.querySelector('select').value = '';
            template.querySelector('input[type="number"]').value = '1';
            template.querySelector('.remove-menu').style.display = 'block';
            return template;
        }

        // Add new menu item
        addMenuBtn.addEventListener('click', function() {
            menuContainer.appendChild(cloneMenuItem());
        });

        // Remove menu item
        menuContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-menu')) {
                const menuItem = e.target.closest('.menu-item');
                if (menuContainer.children.length > 1) {
                    menuItem.remove();
                }
            }
        });
    });
    </script>
</body>

</html>