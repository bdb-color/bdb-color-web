<?php
require_once '../includes/auth.php';
redirect_if_not_logged_in();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../vendor/autoload.php'; // Midtrans

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'] ?? 'user@example.com'; // fallback biar nggak error

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'Mid-server-O1-lfCa8TMNbaWp0yTKwh9QT';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$snapToken = null;
$items = [];
$total = 0;

// Ambil isi keranjang
$sql = "SELECT cart.*, products.price, products.name, 
        colors.name AS color_name, colors.hex_code, cart.product_id, cart.color_id
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        LEFT JOIN colors ON cart.color_id = colors.id 
        WHERE cart.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $total > 0) {
    $recipient_name = $_POST['recipient_name'];
    $phone          = $_POST['phone'];
    $address        = $_POST['address'];
    $message        = $_POST['message'];
    $order_id       = 'ORDER-' . rand(100000, 999999);

    // Simpan ke tabel orders
    $stmt = $pdo->prepare("INSERT INTO orders 
        (user_id, recipient_name, phone, address, message, total, midtrans_order_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id, $recipient_name, $phone, $address, $message, $total, $order_id
    ]);

    $order_db_id = $pdo->lastInsertId();

    // Simpan ke order_items
    foreach ($items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items 
            (order_id, product_id, color_id, quantity, price)
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $order_db_id,
            $item['product_id'],
            $item['color_id'] ?? null,
            $item['quantity'],
            $item['price']
        ]);
    }

    // Buat Snap Token
    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => $total
        ],
        'customer_details' => [
            'first_name' => $recipient_name,
            'email' => $user_email,
            'phone' => $phone
        ]
    ];

    $snapToken = \Midtrans\Snap::getSnapToken($params);

    // Kosongkan keranjang setelah token sukses dibuat
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
}


include '../includes/header.php';
?>

<div class="container mt-5" style="max-width: 700px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
  <?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-success alert-dismissible fade show text-center mb-4" role="alert">
      <?= htmlspecialchars($_SESSION['flash']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif ?>

  <?php if (empty($items)): ?>
    <div class="alert alert-info text-center">
      Keranjang kamu kosong. <a href="catalog.php" class="text-decoration-none fw-semibold">Belanja yuk!</a>
    </div>
  <?php else: ?>
    <div class="card shadow border-0">
      <div class="card-body">
        <h3 class="mb-4 text-center text-primary">🛍️ Checkout Pesanan Kamu</h3>

        <div class="mb-3">
          <label class="form-label fw-bold">Total Belanja</label>
          <p class="form-control-plaintext fs-4 text-success">Rp <?= number_format($total, 0, ',', '.') ?></p>
        </div>

        <form method="post" class="mb-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Penerima</label>
            <input type="text" name="recipient_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Nomor Telepon</label>
            <input type="tel" name="phone" class="form-control" required pattern="[0-9+\s]{8,15}">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Alamat Pengiriman</label>
            <textarea name="address" class="form-control" rows="3" required></textarea>
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold">Pesan / Catatan</label>
            <textarea name="message" class="form-control" placeholder="Opsional..."></textarea>
          </div>

          <button type="submit" class="btn btn-success w-100 py-2">Konfirmasi &amp; Bayar</button>
          <a href="cart.php" class="btn btn-outline-dark w-100 mt-2">Kembali ke Keranjang</a>
        </form>

        <?php if ($snapToken): ?>
          <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-5i0KrYzu-DvmV8gL"></script>
          <script type="text/javascript">
            snap.pay('<?= $snapToken ?>', {
              onSuccess: function(result) {
                alert('Pembayaran berhasil!');
                console.log(result);
                window.location.href = 'checkout.php';
              },
              onPending: function(result) {
                alert('Menunggu pembayaran...');
                console.log(result);
                window.location.href = 'checkout.php';
              },
              onError: function(result) {
                alert('Terjadi kesalahan pembayaran!');
                console.log(result);
              },
              onClose: function() {
                alert('Kamu menutup popup pembayaran.');
              }
            });
          </script>
        <?php endif; ?>

        <hr class="my-4">
        <h5 class="mb-3 text-secondary">🧾 Rincian Keranjang</h5>
        <ul class="list-group list-group-flush">
          <?php foreach ($items as $item): ?>
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div class="me-auto">
                <div class="fw-bold"><?= e($item['name']) ?></div>
                <small class="text-muted">Jumlah: <?= $item['quantity'] ?>x
                  <?php if (!empty($item['color_name'])): ?>
                    • Warna: <?= e($item['color_name']) ?>
                  <?php elseif (!empty($item['custom_hex'])): ?>
                    • Warna: 
                    <span style="display:inline-block; width:12px; height:12px; background:<?= e($item['custom_hex']) ?>; border-radius:2px; border:1px solid #ccc;"></span>
                    <?= e($item['custom_name'] ?? 'Warna Custom') ?>
                  <?php endif ?>
                </small>
              </div>
              <span class="text-end fw-semibold">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></span>
            </li>
          <?php endforeach ?>
        </ul>
      </div>
    </div>
  <?php endif ?>
</div>
