<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'administrator') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM meja WHERE idmeja = '$id'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) != 1) {
    header("Location: index.php");
    exit();
}

$meja = mysqli_fetch_assoc($result);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namameja = mysqli_real_escape_string($conn, $_POST['namameja']);
    $kapasitas = mysqli_real_escape_string($conn, $_POST['kapasitas']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query = "UPDATE meja SET namameja = '$namameja', kapasitas = '$kapasitas', status = '$status' WHERE idmeja = '$id'";
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
    <title>Edit Meja - Sistem Kasir</title>
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
                        <h4 class="mb-0">Edit Meja</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="namameja" class="form-label">Nama Meja</label>
                                <input type="text" class="form-control" id="namameja" name="namameja" value="<?php echo htmlspecialchars($meja['namameja']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="kapasitas" class="form-label">Kapasitas (orang)</label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas" value="<?php echo $meja['kapasitas']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="tersedia" <?php echo $meja['status'] == 'tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                                    <option value="terisi" <?php echo $meja['status'] == 'terisi' ? 'selected' : ''; ?>>Terisi</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update</button>
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