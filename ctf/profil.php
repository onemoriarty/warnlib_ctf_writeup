<?php


if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php?belge=login");
    exit;
}
?>
<h2 class="text-3xl font-bold text-yellow-400 mb-4">Profilim</h2>
<div class="bg-gray-800 p-6 rounded-lg shadow-lg">
    <p class="text-lg text-white">Hoş geldiniz, <span class="font-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</p>
    <p class="text-gray-400">Rol: <span class="font-bold"><?php echo htmlspecialchars($_SESSION['role']); ?></span></p>
</div>
