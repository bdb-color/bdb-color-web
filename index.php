<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Ambil parameter dari URL
$filter = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;

// Bangun query dinamis
$query = "SELECT * FROM products WHERE 1";
$params = [];

if ($filter) {
    $query .= " AND type = ?";
    $params[] = $filter;
}

if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}

$query .= " ORDER BY id DESC LIMIT 6";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="text-center mb-5">
    <h1 class="mb-3">Selamat Datang di <span class="text-primary">CV BERKAH DOA BUNDA</span></h1>
    <p class="lead">PRODUK KAMI MEMILIKI KUALITAS YANG TERBAIK</p>

    <!-- Form Pencarian -->
    <form method="get" action="index.php" class="d-flex justify-content-center mb-4">
        <input type="text" name="search" value="<?= e($_GET['search'] ?? '') ?>" placeholder="Cari produk..." class="form-control w-50 me-2">
        <button type="submit" class="btn btn-outline-primary">Cari</button>
    </form>

    <?php if ($search): ?>
        <p class="text-muted">Hasil pencarian untuk <strong><?= e($search) ?></strong></p>
    <?php endif ?>
    <?php if ($filter): ?>
        <p class="text-muted">Kategori: <strong><?= e($filter) ?></strong></p>
    <?php endif ?>
</div>

<!-- Filter Kategori -->
<div class="text-center mb-4">
    <a href="index.php" class="btn btn-outline-secondary me-2 <?= !$filter ? 'active' : '' ?>">Semua</a>
    <a href="index.php?category=epoxy" class="btn btn-outline-secondary me-2 <?= $filter === 'epoxy' ? 'active' : '' ?>">Epoxy</a>
    <a href="index.php?category=zinkromath" class="btn btn-outline-secondary me-2 <?= $filter === 'zinkromath' ? 'active' : '' ?>">Zinkromath</a>
    <a href="index.php?category=minyak" class="btn btn-outline-secondary me-2 <?= $filter === 'minyak' ? 'active' : '' ?>">Minyak</a>
    <a href="index.php?category=air" class="btn btn-outline-secondary <?= $filter === 'air' ? 'active' : '' ?>">Air</a>
</div>

<!-- Produk -->
<div class="row">
    <?php if (empty($products)): ?>
        <p class="text-center">Belum ada produk yang sesuai.</p>
    <?php endif; ?>

    <?php foreach ($products as $p): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <?php if (!empty($p['image'])): ?>
                    <img src="uploads/<?= e($p['image']) ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
                <?php endif ?>
                <div class="card-body">
                    <h5 class="card-title"><?= e($p['name']) ?> (<?= e($p['type']) ?>)</h5>
                    <p class="card-text text-primary fw-bold"><?= format_rupiah($p['price']) ?></p>
                    <a href="dashboard_user/catalog.php" class="btn btn-outline-primary btn-sm">Lihat Detail</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- WhatsApp Button -->
<a href="https://wa.me/6281111852220" class="btn btn-success rounded-circle position-fixed shadow"
   style="bottom: 20px; right: 20px; z-index: 999;" target="_blank" title="Hubungi Kami di WhatsApp">
    <i class="bi bi-whatsapp fs-4"></i>
</a>

<?php require_once 'includes/footer.php'; ?>
