<?php
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = '<div class="bg-red-500 p-4 rounded-lg text-white font-bold mt-4">Kullanıcı adı ve şifre boş bırakılamaz.</div>';
    } else {
        $check_sql = "SELECT id FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();//cevabın sonucunu al
        
        if ($check_result->num_rows > 0) { //sonucun satır sayısı 0'dan büyükse kullanıcı ztn var
            $message = '<div class="bg-red-500 p-4 rounded-lg text-white font-bold mt-4">Bu kullanıcı adı zaten mevcut.</div>';
        } else {
            $insert_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ss", $username, $password);

            if ($insert_stmt->execute()) {
                $message = '<div class="bg-green-500 p-4 rounded-lg text-white font-bold mt-4">Kayıt başarıyla tamamlandı. Şimdi giriş yapabilirsiniz.</div>';
            } else {
                $message = '<div class="bg-red-500 p-4 rounded-lg text-white font-bold mt-4">Kayıt sırasında bir hata oluştu.</div>';
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<h2 class="text-3xl font-bold text-yellow-400 mb-4">Yeni Hesap Oluştur</h2>
<?php echo $message; ?>
<form action="index.php?belge=register" method="post">
    <div class="mb-4">
        <label for="username" class="block text-gray-400">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" class="w-full bg-gray-700 p-3 rounded-lg text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-4">
        <label for="password" class="block text-gray-400">Şifre:</label>
        <input type="password" id="password" name="password" class="w-full bg-gray-700 p-3 rounded-lg text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded transition-colors duration-300">
        Kayıt Ol
    </button>
</form>
<a href="index.php?belge=login" class="mt-4 inline-block text-blue-400 hover:underline">Zaten hesabınız var mı? Giriş yapın.</a>
