<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'administrator') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

// Handle delete
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM user WHERE iduser = '$id'");
    header("Location: index.php");
    exit();
}

// Get current user's ID
$current_user_id = isset($_SESSION['user']['iduser']) ? $_SESSION['user']['iduser'] : 0;

// Get all users
$query = "SELECT * FROM user ORDER BY role, username";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Sistem Kasir</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Kelola User</h3>
            <a href="tambah.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah User
            </a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Nama User</th>
                                <th>Role</th>
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
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['namauser']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                                echo $row['role'] == 'administrator' ? 'danger' : 
                                                    ($row['role'] == 'kasir' ? 'success' : 
                                                    ($row['role'] == 'owner' ? 'primary' : 'warning')); 
                                            ?>">
                                        <?php echo ucfirst($row['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $row['iduser']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <?php if($row['iduser'] != $current_user_id): ?>
                                    <a href="?delete=<?php echo $row['iduser']; ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    Belum ada data user.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>