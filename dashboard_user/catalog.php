<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$user_id = $_SESSION['user_id'] ?? null;

// Filter, search, sort
$search = $_GET['search'] ?? null;
$category = $_GET['category'] ?? null;
$sort = $_GET['sort'] ?? null;

$query = "SELECT * FROM products WHERE 1";
$params = [];

if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}

if ($category) {
    $query .= " AND type = ?";
    $params[] = $category;
}

switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    default:
        $query .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Tambah ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$user_id) {
        redirect_with_message('catalog.php', 'Silakan login terlebih dahulu untuk belanja!');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity)
        VALUES (?, ?, 1) 
        ON DUPLICATE KEY UPDATE quantity = quantity + 1");
    $stmt->execute([$user_id, $_POST['product_id']]);
    redirect_with_message('catalog.php', 'Produk berhasil ditambahkan ke keranjang!');
}
?>

<!-- Search & Sorting -->
<form method="get" class="mb-3 d-flex justify-content-between">
    <div>
        <a href="catalog.php" class="btn btn-outline-secondary <?= !$category ? 'active' : '' ?>">Semua</a>
        <?php foreach (['epoxy', 'zinkromath', 'minyak', 'air'] as $cat): ?>
            <a href="catalog.php?category=<?= $cat ?>" class="btn btn-outline-secondary <?= $category === $cat ? 'active' : '' ?>">
                <?= ucfirst($cat) ?>
            </a>
        <?php endforeach ?>
    </div>
    <div class="d-flex">
        <input type="text" name="search" value="<?= e($search) ?>" class="form-control me-2" placeholder="Cari produk...">
        <select name="sort" onchange="this.form.submit()" class="form-select">
            <option value="">Urutkan</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Harga Termurah</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Harga Termahal</option>
        </select>
    </div>
</form>

<h2 class="mb-4">Katalog Produk</h2>
<?php show_flash_message(); ?>
<?php if (empty($products)): ?>
    <div class="alert alert-warning text-center">Produk tidak ditemukan.</div>
<?php endif ?>

<!-- Produk -->
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
<?php foreach ($products as $p): ?>
    <?php
        $badge = '';
        if (!empty($p['discount']) && $p['discount'] > 0) {
            $badge = '<span class="badge bg-danger position-absolute top-0 start-0 m-2">Diskon ' . $p['discount'] . '%</span>';
        } elseif (strtotime($p['created_at'] ?? '') >= strtotime('-7 days')) {
            $badge = '<span class="badge bg-success position-absolute top-0 start-0 m-2">Baru!</span>';
        }
    ?>
    <div class="col">
        <div class="card h-100 position-relative">
            <?= $badge ?>
            <?php if (!empty($p['image'])): ?>
                <img src="../uploads/<?= e($p['image']) ?>" class="card-img-top" alt="<?= e($p['name']) ?>" style="height: 160px; object-fit: contain;">
            <?php endif ?>
            <div class="card-body">
                <h5 class="card-title"><?= e($p['name']) ?> (<?= e($p['type']) ?>)</h5>
                <p class="text-primary fw-bold"><?= format_rupiah($p['price']) ?></p>

                <?php if ($user_id): ?>
                    <?php if ($_SESSION['role'] !== 'admin'): ?>
                        <form method="post" class="mt-3">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-outline-warning w-100 btn-sm">+ Keranjang</button>
                        </form>
                    <?php endif ?>
                <?php else: ?>
                    <a href="../login.php" class="btn btn-outline-secondary btn-sm w-100 mt-3">Login untuk belanja</a>
                <?php endif ?>
                <button class="btn btn-outline-primary w-100 btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modal<?= $p['id'] ?>">
                    Lihat Detail
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Produk -->
    <div class="modal fade" id="modal<?= $p['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $p['id'] ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel<?= $p['id'] ?>"><?= e($p['name']) ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <?php if (!empty($p['image'])): ?>
              <img src="../uploads/<?= e($p['image']) ?>" class="img-fluid mb-3" alt="<?= e($p['name']) ?>">
            <?php endif ?>
            <p>Jenis: <?= e($p['type']) ?></p>
            <p>Harga: <strong><?= format_rupiah($p['price']) ?></strong></p>
          </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include '../includes/footer.php'; ?>
