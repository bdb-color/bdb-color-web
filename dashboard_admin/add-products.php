<?php
require_once '../includes/auth.php';
redirect_if_not_admin();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $type  = $_POST['type'];
    $price = $_POST['price'];
    $image = null;

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, type, price, image)
        VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $type, $price, $image]);

    redirect_with_message('products.php', 'Produk berhasil ditambahkan!');
}
include '../includes/header.php';
?>

<style>
    .product-form-container {
        max-width: 550px;
        background: #ffffff;
        padding: 35px 30px;
        margin: 60px auto;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }
    .product-form-container h2 {
        font-weight: 600;
        margin-bottom: 25px;
        color: #2c3e50;
    }
    .product-form-container label {
        font-weight: 500;
    }
</style>

<div class="product-form-container">
    <h2 class="text-center">Tambah Produk</h2>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nama Produk</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Jenis</label>
            <select name="type" class="form-select" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="epoxy">Epoxy</option>
                <option value="zinkromath">Zinkromath</option>
                <option value="minyak">Minyak</option>
                <option value="air">Air</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Harga (Rp)</label>
            <input type="number" name="price" class="form-control" required>
        </div>
        <div class="mb-4">
            <label>Gambar Produk</label>
            <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
        </div>
        <button type="submit" class="btn btn-primary w-100">Simpan Produk</button>
    </form>

    <div class="text-center mt-4">
        <a href="products.php" class="btn btn-outline-secondary btn-sm">← Kembali ke Daftar Produk</a>
    </div>
</div>
