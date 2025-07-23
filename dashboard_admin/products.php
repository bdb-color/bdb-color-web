<?php
require_once '../includes/auth.php';
redirect_if_not_admin();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$success = '';
$error = '';

// Tambah produk baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['update_id'])) {
    $image = null;

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, type, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['type'],
        $_POST['price'],
        $image
    ]);
    $success = "Produk baru berhasil ditambahkan.";
}

// Update produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['update_id'])) {
    $sql = "UPDATE products SET name = ?, type = ?, price = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['name'], $_POST['type'],
        $_POST['price'], $_POST['update_id']
    ]);
    $success = "Produk berhasil diperbarui.";
}

// Hapus produk
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: products.php?deleted=1");
    exit;
}

// Ambil semua produk
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Manajemen Produk</h2>
  <div class="d-flex gap-2">
    <a href="add-admin.php" class="btn btn-outline-primary">+ Tambah Admin</a>
    <a href="add-products.php" class="btn btn-outline-primary">+ Tambah Produk</a>
  </div>
</div>


<?php if (!empty($_GET['deleted'])): ?>
    <div class="alert alert-success">Produk berhasil dihapus.</div>
<?php elseif ($success): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php elseif ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif ?>

<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <form method="post">
                <td>
                    <?php if (!empty($p['image'])): ?>
                        <img src="../uploads/<?= e($p['image']) ?>" width="60">
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif ?>
                </td>
                <td><input name="name" class="form-control" value="<?= e($p['name']) ?>"></td>
                <td><input name="type" class="form-control" value="<?= e($p['type']) ?>"></td>
                <td><input name="price" type="number" class="form-control" value="<?= $p['price'] ?>"></td>
                <td class="text-center">
                    <input type="hidden" name="update_id" value="<?= $p['id'] ?>">
                    <button class="btn btn-sm btn-success me-1">Simpan</button>
                    <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')">🗑️</a>
                </td>
            </form>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<hr>
