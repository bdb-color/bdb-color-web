<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>BDB Color</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="/bdb_color/index.php">
      <img src="/bdb_color/uploads/logo.jpg" alt="Logo" height="32" class="me-2">
      CV BERKAH DOA BUNDA
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarBDB">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarBDB">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a href="/bdb_color/dashboard_user/catalog.php" class="nav-link text-white">Katalog</a>
        </li>

        <?php if ($is_logged_in && !$is_admin): ?>
          <li class="nav-item">
            <a href="/bdb_color/dashboard_user/cart.php" class="nav-link text-white">Keranjang</a>
          </li>
        <?php endif; ?>

        <?php if ($is_logged_in): ?>
          <li class="nav-item">
            <a href="/bdb_color/dashboard_user/orders.php" class="nav-link text-white">
              <?= $is_admin ? 'Pesanan' : 'Pesanan Saya' ?>
            </a>
          </li>

          <!-- <?php if (!$is_admin): ?>
            <li class="nav-item">
              <a href="/bdb_color/dashboard_user/profile.php" class="nav-link text-white">Profil</a>
            </li>
          <?php endif; ?> -->

          <?php if ($is_admin): ?>
            <li class="nav-item">
              <a href="/bdb_color/dashboard_admin/products.php" class="nav-link text-white">Admin Panel</a>
            </li>
          <?php endif; ?>

          <li class="nav-item">
            <a href="/bdb_color/logout.php" class="nav-link text-white">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a href="/bdb_color/login.php" class="nav-link text-white">Login</a>
          </li>
          <li class="nav-item">
            <a href="/bdb_color/register.php" class="nav-link text-white">Daftar</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
