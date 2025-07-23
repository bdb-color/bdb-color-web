<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once 'includes/db.php';

$client = new Google_Client();
$client->setClientId('558469514784-npks2kfphqcgjncd9qsv3p8fse22onlo.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/bdb_color/login-callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        exit('Gagal mendapatkan token Google: ' . htmlspecialchars($token['error_description'] ?? $token['error']));
    }

    $client->setAccessToken($token);
    $oauth = new Google_Service_Oauth2($client);
    $user = $oauth->userinfo->get();

    // Periksa user di database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$user->email]);
    $existing = $stmt->fetch();

    if (!$existing) {
        // Tambahkan user baru
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user->name, $user->email, '', 'customer']);
        $userId = $pdo->lastInsertId();
        $role = 'customer';
    } else {
        $userId = $existing['id'];
        $role = $existing['role'];
    }

    // Simpan sesi login
    $_SESSION['user_id'] = $userId;
    $_SESSION['role'] = $role;

    // Redirect berdasarkan peran
    $redirect = ($role === 'admin') ? 'dashboard_admin/products.php' : 'index.php';
    header("Location: $redirect");
    exit;
} else {
    header('Location: login.php');
    exit;
}
