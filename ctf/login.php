<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, role FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        if ($_SESSION['role'] === 'admin') {
            header("Location: index.php?belge=admin/login");
        } else {
            header("Location: index.php?belge=profil");
        }
        exit;
    } else {
        echo '<div class="bg-red-500 p-4 rounded-lg text-white font-bold mt-4">
                Hatalı kullanıcı adı veya şifre.
              </div>';
    }
}
?>
<h2 class="text-3xl font-bold text-yellow-400 mb-4">Giriş Yap</h2>
<form action="index.php?belge=login" method="post">
    <div class="mb-4">
        <label for="username" class="block text-gray-400">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" class="w-full bg-gray-700 p-3 rounded-lg text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-4">
        <label for="password" class="block text-gray-400">Şifre:</label>
        <input type="password" id="password" name="password" class="w-full bg-gray-700 p-3 rounded-lg text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded transition-colors duration-300">
        Giriş Yap
    </button>
</form>

