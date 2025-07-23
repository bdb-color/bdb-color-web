<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();
require_once '../includes/db.php';

$is_admin = $_SESSION['role'] === 'admin';

// Admin ubah status
if ($is_admin && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
}

// User membatalkan pesanan
if (!$is_admin && isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];

    // Hanya boleh batalkan pesanan sendiri
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch();

    if ($order) {
        $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);
        $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$order_id]);
    }
}

// Ambil semua pesanan
$sql = "SELECT o.*, u.name FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$orders = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
  <h2 class="mb-4">📦 Kelola Pesanan</h2>

  <?php if (empty($orders)): ?>
    <div class="alert alert-info text-center">Belum ada pesanan yang tercatat 📭</div>
  <?php else: ?>
    <?php foreach ($orders as $order): ?>
      <div class="card mb-4 shadow-sm border">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <strong>Pesanan #<?= $order['id'] ?> – <?= htmlspecialchars($order['name']) ?></strong>
          <small class="text-muted"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></small>
        </div>

        <div class="card-body">
          <p class="mb-1">💰 <strong>Total:</strong> Rp <?= number_format($order['total'], 0, ',', '.') ?></p>
          <p class="mb-3 d-flex justify-content-between align-items-center">
            <div class="mb-3">
              <p class="mb-1">👤 <strong>Nama Penerima:</strong> <?= htmlspecialchars($order['recipient_name']) ?></p>
              <p class="mb-1">📱 <strong>Telepon:</strong> <?= htmlspecialchars($order['phone']) ?></p>
              <p class="mb-1">🏠 <strong>Alamat:</strong><br><?= nl2br(htmlspecialchars($order['address'])) ?></p>
              <?php if (!empty($order['message'])): ?>
                <p class="mb-1">📝 <strong>Catatan:</strong> <?= htmlspecialchars($order['message']) ?></p>
              <?php endif; ?>
            </div>
            <span>🚚 <strong>Status:</strong> <span class="badge bg-info text-dark"><?= ucfirst($order['status']) ?></span></span>
            
            <?php if (!$is_admin && $order['user_id'] == $_SESSION['user_id']): ?>
              <form method="post" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button type="submit" name="cancel_order" class="btn btn-outline-danger btn-sm">Batalkan</button>
              </form>
            <?php endif; ?>
          </p>

          <?php if ($is_admin): ?>
            <form method="post" class="row g-2 align-items-center mb-3">
              <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
              <div class="col-auto">
                <select name="status" class="form-select form-select-sm" required>
                  <option value="diproses" <?= $order['status']=='diproses'?'selected':'' ?>>Diproses</option>
                  <option value="dikirim" <?= $order['status']=='dikirim'?'selected':'' ?>>Dikirim</option>
                  <option value="selesai" <?= $order['status']=='selesai'?'selected':'' ?>>Selesai</option>
                </select>
              </div>
              <div class="col-auto">
                <button type="submit" name="update_status" class="btn btn-primary btn-sm">Ubah Status</button>
              </div>
            </form>
          <?php endif; ?>

          <hr class="my-3">
          <h6 class="mb-2">🧾 Item Pesanan:</h6>
          <ul class="list-group list-group-flush">
            <?php
              $stmt = $pdo->prepare("SELECT p.name, oi.quantity, oi.price 
                                     FROM order_items oi 
                                     JOIN products p ON oi.product_id = p.id 
                                     WHERE oi.order_id = ?");
              $stmt->execute([$order['id']]);
              $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($items as $item):
            ?>
              <li class="list-group-item"><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?>x) – Rp <?= number_format($item['price'], 0, ',', '.') ?></li>
            <?php endforeach ?>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
