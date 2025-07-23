<?php
require_once '../includes/auth.php';
redirect_if_not_logged_in();

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Hapus item dari keranjang
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $user_id]);
    redirect_with_message('cart.php', 'Item dihapus dari keranjang!');
}

// Update jumlah & warna item di keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $cart_id = $_POST['cart_id'];
    $new_qty = max(1, (int)$_POST['quantity']);
    $color_id = isset($_POST['color_id']) ? (int)$_POST['color_id'] : null;
    $custom_hex = $_POST['custom_hex'] ?? null;
    $custom_name = null;

    if ($custom_hex) {
        $hex = ltrim($custom_hex, '#');
        $api = "https://api.palettes.dev/id?hex=$hex";
        $response = @file_get_contents($api);
        if ($response !== false) {
            $color_data = json_decode($response, true);
            $custom_name = $color_data['name'] ?? null;
        }
    }

        $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, color_id = ?, custom_hex = ?, custom_name = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$new_qty, $color_id, $custom_hex, $custom_name, $cart_id, $user_id]);


    redirect_with_message('cart.php', 'Berhasil diperbarui!');
}


// Ambil semua item keranjang
$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.image, c.quantity,
        c.color_id, c.custom_hex, c.custom_name,
        colors.name AS color_name, colors.hex_code
    FROM cart c
    JOIN products p ON c.product_id = p.id
    LEFT JOIN colors ON c.color_id = colors.id
    WHERE c.user_id = ?
    ORDER BY c.id DESC
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

// Hitung total
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<h2 class="mb-4">Keranjang Belanja 🛒</h2>
<?php show_flash_message(); ?>

<?php if (empty($items)): ?>
    <div class="alert alert-info text-center">Keranjangmu masih kosong. Yuk, belanja dulu dari <a href="catalog.php">katalog</a>!</div>
<?php else: ?>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah & Warna</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['image'])): ?>
                            <img src="../uploads/<?= e($item['image']) ?>" width="50" class="me-2 rounded">
                        <?php endif ?>
                        <?= e($item['name']) ?>
                        <?php if (!empty($item['color_name'])): ?>
                            <div class="mt-2">
                                <span class="badge" style="background-color:<?= $item['hex_code'] ?>; color:#fff;">
                                    <?= $item['color_name'] ?>
                                </span>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($item['custom_hex'])): ?>
                        <div class="mt-2">
                            <span class="badge" style="background-color:<?= $item['custom_hex'] ?>; color:#fff;">
                            <?= $item['custom_name'] ? e($item['custom_name']) : 'Warna Custom' ?>
                            </span>
                        </div>
                        <?php endif ?>
                    </td>
                    <td><?= format_rupiah($item['price']) ?></td>
                    <td>
                        <?php
                        $colors = get_colors_for_product($item['product_id'], $pdo);
                        $custom_hex = $item['custom_hex'] ?? '';
                        ?>
                        <form method="post" class="d-flex flex-column">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">

                            <!-- Warna custom (bebas) -->
                            <div class="mb-2">
                                <label class="form-label small mb-1" for="colorpicker-<?= $item['cart_id'] ?>">Pilih Warna:</label>
                                <input type="color" name="custom_hex" id="colorpicker-<?= $item['cart_id'] ?>" value="<?= e($custom_hex) ?>" class="form-control form-control-color">
                            </div>

                            <div class="d-flex align-items-center">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1"
                                    class="form-control form-control-sm me-2" style="width: 70px;">
                                <button type="submit" name="update_qty" class="btn btn-sm btn-outline-primary">Ubah</button>
                            </div>
                        </form>
                    </td>
                    <td><?= format_rupiah($item['price'] * $item['quantity']) ?></td>
                    <td class="text-center">
                        <a href="cart.php?delete=<?= $item['cart_id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Yakin ingin hapus item ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <h4>Total: <span class="text-primary"><?= format_rupiah($total) ?></span></h4>
        <div class="d-flex gap-2">
            <a href="catalog.php" class="btn btn-outline-danger btn-lg">Kembali</a>
            <a href="checkout.php" class="btn btn-success btn-lg">Lanjut ke Checkout</a>
        </div>
    </div>
<?php endif ?>
