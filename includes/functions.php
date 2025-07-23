<?php

// Format angka ke bentuk Rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Cek apakah promosi aktif
function is_promo_active($start_date, $end_date) {
    $today = date('Y-m-d');
    return ($start_date <= $today && $today <= $end_date);
}

// Redirect dengan alert (optional)
function redirect_with_message($url, $message) {
    $_SESSION['flash_message'] = $message;
    header("Location: $url");
    exit;
}

// Tampilkan flash message (sekali tampil)
function show_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        echo "<div style='background:#e0ffe0; padding:10px; margin-bottom:10px;'>" . $_SESSION['flash_message'] . "</div>";
        unset($_SESSION['flash_message']);
    }
}

// Escape output (untuk keamanan XSS)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function get_colors_for_product($product_id, $pdo) {
  $stmt = $pdo->prepare("
    SELECT c.id, c.name, c.hex_code 
    FROM product_colors pc
    JOIN colors c ON pc.color_id = c.id
    WHERE pc.product_id = ?
  ");
  $stmt->execute([$product_id]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function set_flash($msg) {
  $_SESSION['flash'] = $msg;
}

function show_flash() {
  if (isset($_SESSION['flash'])) {
    echo '<div class="alert alert-success text-center mb-4">' . htmlspecialchars($_SESSION['flash']) . '</div>';
    unset($_SESSION['flash']);
  }
}


