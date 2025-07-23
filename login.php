<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard_admin/products.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        header('Location: ' . ($_SESSION['role'] === 'admin' ? 'dashboard_admin/products.php' : 'index.php'));
        exit;
    } else {
        $error = 'Email atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - BDB Color</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #dfe9f3, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 100%;
            max-width: 420px;
            padding: 35px 30px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
        }
        .btn-google {
            background-color: #fff;
            color: #444;
            border: 1px solid #ccc;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            font-weight: 500;
        }

        .btn-google:hover {
            background-color: #f7f7f7;
            color: #222;
            border-color: #999;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
<div class="login-box">
    <div class="text-center mb-4">
        <span class="logo">CV BERKAH DOA BUNDA</span>
        <p class="text-muted small">Masuk untuk melanjutkan</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">Masuk</button>
    </form>

    <div class="text-center mb-3">
        <a href="login-google.php" class="btn btn-google w-100 d-flex align-items-center justify-content-center">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="20" height="20" class="me-2" alt="Google logo">
            Login dengan Google
        </a>
    </div>

    <div class="text-center text-muted small">
        Belum punya akun? <a href="register.php">Daftar di sini</a><br>
        <a href="index.php" class="btn btn-outline-secondary btn-sm mt-2">Kembali ke Beranda</a>
    </div>
</div>
</body>
</html>
