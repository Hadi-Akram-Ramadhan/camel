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
    <link rel="icon" type="image/x-icon" href="assets/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #1e40af;
        --background: #f8fafc;
        --card-bg: #ffffff;
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

    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        background-color: #f8fafc;
        padding: 1rem;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .table-striped>tbody>tr:nth-of-type(odd)>* {
        background-color: #f8fafc;
    }

    .btn {
        padding: 0.5rem 1rem;
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
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
    }

    .btn-warning {
        background: #f59e0b;
        border: none;
        color: white;
    }

    .btn-danger {
        background: #ef4444;
        border: none;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75em;
        letter-spacing: 0.025em;
    }

    .alert {
        border: none;
        border-radius: 12px;
    }

    .page-title {
        font-weight: 600;
        color: #0f172a;
        margin: 0;
    }

    .table-actions {
        display: flex;
        gap: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../../dashboard.php">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="page-title">Kelola User</h3>
            <a href="tambah.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Tambah User
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
            <div class="card-body p-0">
                <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Username</th>
                                <th width="25%">Nama User</th>
                                <th width="20%">Role</th>
                                <th width="30%">Aksi</th>
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
                                    <div class="table-actions">
                                        <a href="edit.php?id=<?php echo $row['iduser']; ?>"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            Edit
                                        </a>
                                        <?php if($row['iduser'] != $current_user_id): ?>
                                        <a href="?delete=<?php echo $row['iduser']; ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus user ini?')">
                                            <i class="bi bi-trash"></i>
                                            Hapus
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <p>Belum ada data user.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>