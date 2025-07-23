<?php
require_once '../includes/auth.php';
redirect_if_not_admin();

require_once '../includes/db.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'Email sudah digunakan.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$name, $email, $hash]);
            $success = 'Akun admin berhasil dibuat!';
        }
    }
}
include '../includes/header.php';
?>

<style>
    .admin-form-box {
        max-width: 500px;
        background: #ffffff;
        padding: 30px 25px;
        margin: 60px auto;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }
    .admin-form-box h2 {
        font-weight: 600;
        margin-bottom: 25px;
        color: #2c3e50;
    }
    .admin-form-box label {
        font-weight: 500;
    }
</style>

<div class="admin-form-box">
    <h2 class="text-center">Tambah Admin</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif ?>

    <form method="post">
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-4">
            <label>Konfirmasi Password</label>
            <input type="password" name="confirm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Tambah Admin</button>
    </form>

    <div class="text-center mt-4">
        <a href="products.php" class="btn btn-outline-secondary btn-sm">← Kembali ke Admin Panel</a>
    </div>
</div>
