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
$query = "SELECT * FROM user WHERE iduser = '$id'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) != 1) {
    header("Location: index.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $namauser = mysqli_real_escape_string($conn, $_POST['namauser']);
    
    // Check if username exists
    $check = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username' AND iduser != '$id'");
    if(mysqli_num_rows($check) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = "UPDATE user SET username = '$username', role = '$role', namauser = '$namauser'";
        
        // Update password if provided
        if(!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query .= ", password = '$password'";
        }
        
        $query .= " WHERE iduser = '$id'";
        
        if(mysqli_query($conn, $query)) {
            $_SESSION['success'] = "User berhasil diupdate!";
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal mengupdate user!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Sistem Kasir</title>
    <link rel="icon" type="image/x-icon" href="../../assets/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #1e40af;
        --background: #f8fafc;
        --card-bg: #ffffff;
        --input-border: #e2e8f0;
        --input-focus: #2563eb;
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
        border-bottom: 1px solid var(--input-border);
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

    .form-label {
        font-weight: 500;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--input-border);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--input-focus);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-text {
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 0.5rem;
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
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-danger {
        background: #fef2f2;
        color: #dc2626;
    }

    .mb-3 {
        margin-bottom: 1.5rem !important;
    }

    .form-floating {
        position: relative;
    }

    .form-floating label {
        padding: 0.75rem 1rem;
    }

    .form-floating .form-control,
    .form-floating .form-select {
        height: calc(3.5rem + 2px);
        line-height: 1.25;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #64748b;
        transition: color 0.2s ease;
    }

    .password-toggle:hover {
        color: #0f172a;
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
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit User: <?php echo htmlspecialchars($user['namauser']); ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="<?php echo htmlspecialchars($user['username']); ?>"
                                        placeholder="Username" required>
                                    <label for="username"><i class="bi bi-person me-2"></i>Masukkan username</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="namauser" class="form-label">Nama User</label>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="namauser" name="namauser"
                                        value="<?php echo htmlspecialchars($user['namauser']); ?>"
                                        placeholder="Nama User" required>
                                    <label for="namauser"><i class="bi bi-person-badge me-2"></i>Masukkan nama
                                        lengkap</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Password">
                                    <label for="password"><i class="bi bi-lock me-2"></i>Masukkan password baru</label>
                                    <i class="bi bi-eye-slash password-toggle" onclick="togglePassword()"></i>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Kosongkan jika tidak ingin mengubah password.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <div class="form-floating">
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="" disabled>Pilih role user</option>
                                        <option value="administrator"
                                            <?php echo $user['role'] == 'administrator' ? 'selected' : ''; ?>>
                                            Administrator
                                        </option>
                                        <option value="kasir" <?php echo $user['role'] == 'kasir' ? 'selected' : ''; ?>>
                                            Kasir
                                        </option>
                                        <option value="waiter"
                                            <?php echo $user['role'] == 'waiter' ? 'selected' : ''; ?>>
                                            Waiter
                                        </option>
                                        <option value="owner" <?php echo $user['role'] == 'owner' ? 'selected' : ''; ?>>
                                            Owner
                                        </option>
                                    </select>
                                    <label for="role"><i class="bi bi-shield-lock me-2"></i>Pilih role user</label>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i>
                                    Simpan Perubahan
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-x-lg"></i>
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
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.password-toggle');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    }
    </script>
</body>

</html>