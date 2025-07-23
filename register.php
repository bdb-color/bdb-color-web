<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $password]);

    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - BDB Color</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #dfe9f3, #ffffff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-box {
            max-width: 420px;
            width: 100%;
            padding: 35px 30px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="register-box">
    <div class="text-center mb-4">
        <span class="logo">Daftar ke <span class="text-primary">CV BERKAH DOA BUNDA</span></span>
        <p class="text-muted small">Buat akun barumu di sini</p>
    </div>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Daftar</button>
    </form>

    <div class="text-center text-muted small mt-4">
        Sudah punya akun? <a href="login.php">Masuk di sini</a><br>
        <a href="index.php" class="btn btn-outline-secondary btn-sm mt-2">Kembali ke Beranda</a>
    </div>
</div>
</body>
</html>
