<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('558469514784-npks2kfphqcgjncd9qsv3p8fse22onlo.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/bdb_color/login-callback.php');
$client->addScope('email');
$client->addScope('profile');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
$client->setIncludeGrantedScopes(false);

// Arahkan user ke halaman login Google dengan prompt izin
header('Location: ' . $client->createAuthUrl());
exit;
