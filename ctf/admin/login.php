<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['role'] === 'admin') {
    echo '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="p-8 bg-gray-800 rounded-lg shadow-2xl text-center">
        <h1 class="text-4xl font-bold text-green-500 mb-4">TEBRİKLER!</h1>
        <p class="text-lg text-gray-300 mb-8">Admin oturumunu ele geçirdiniz!</p>
        <div class="bg-yellow-400 text-gray-900 p-6 rounded-lg text-3xl font-bold break-all">
            FLAG{BASARILI_CTF_ZINCIRI_KURULDU}
        </div>
    </div>
</body>
</html>';
} else {
    echo '<div class="bg-red-500 p-4 rounded-lg text-white font-bold">
            Bu alana erişiminiz yok.
          </div>';
}
?>

