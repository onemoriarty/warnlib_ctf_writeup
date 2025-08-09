<?php
require_once 'config.php';
$belge = $_GET['belge'] ?? 'anasayfa';
$file_to_include = $belge . '.php';
if (preg_match('/\.\.[\/|\\\]/', $belge)) {
    echo '<div class="container mx-auto mt-8 px-4"><div class="bg-green-500 p-4 rounded-lg text-white font-bold text-center">Tebrikler! LFI zafiyetini buldunuz. İşte ikinci bayrak: FLAG{LFI_Basarili_Giris}</div></div>';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarnLib</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <nav class="bg-gray-800 shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-yellow-400">WarnLib</a>
            <div class="flex space-x-4">
                <a href="index.php?belge=anasayfa" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md transition-colors">Anasayfa</a>
                <a href="index.php?belge=profil" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md transition-colors">Profilim</a>
                <a href="index.php?belge=kullanicilar" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md transition-colors">Kullanıcılar</a>
            </div>
            <div class="flex space-x-4">
                <?php if (isset($_SESSION['loggedin'])): ?>
                    <span class="text-gray-300 px-3 py-2">Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="index.php?belge=logout" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md font-bold transition-colors">Çıkış Yap</a>
                <?php else: ?>
                    <a href="index.php?belge=login" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md font-bold transition-colors">Giriş Yap</a>
                    <a href="index.php?belge=register" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md font-bold transition-colors">Kayıt Ol</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container mx-auto mt-8 px-4">
        <?php
        if (file_exists($file_to_include)) {
            include $file_to_include;
        } else {
            echo '<div class="bg-red-500 p-4 rounded-lg text-white font-bold">Aradığınız sayfa bulunamadı.</div>';
        }
        ?>
    </main>
</body>
</html>
