<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['iduser'],
                'nama' => $user['namauser'],
                'role' => $user['role']
            ];
            header("Location: ../dashboard.php");
            exit();
        }
    }
    
    header("Location: ../index.php?error=1");
    exit();
}

header("Location: ../index.php");
exit(); 