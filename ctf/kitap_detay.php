<?php
if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], '<script>') !== false) {
    echo '<div class="container mx-auto mt-8 px-4">
            <div class="bg-green-500 p-4 rounded-lg text-white font-bold text-center">
                User-Agent\'ınızda zararlı bir girişim tespit edildi! Tebrikler, bu bir güvenlik denemesidir.
            </div>
          </div>';
    warnlib_log_user_action($conn, $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'], "User-Agent'ta XSS denemesi tespit edildi.", 'misafir');
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="bg-red-500 p-4 rounded-lg text-white font-bold">Geçerli bir kitap ID’si belirtilmedi.</div>';
    return;
}

$book_id = $_GET['id'];
$sql_book = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($sql_book);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result_book = $stmt->get_result();

if ($result_book->num_rows == 0) {
    echo '<div class="bg-red-500 p-4 rounded-lg text-white font-bold">Kitap bulunamadı.</div>';
    return;
}

$book = $result_book->fetch_assoc();
?>
<div class="container mx-auto mt-8 px-4">
    <h2 class="text-3xl font-bold text-yellow-400 mb-4"><?php echo htmlspecialchars($book['title']); ?></h2>
    <p class="text-gray-400 mb-2"><b>Yazar:</b> <?php echo htmlspecialchars($book['author']); ?></p>
    <p class="text-gray-300 mb-8"><b>Açıklama:</b> <?php echo htmlspecialchars($book['description']); ?></p>

    <h3 class="text-2xl font-bold text-white mb-4">Yorumlar</h3>
    <div class="space-y-4">
        <?php
        $sql_comments = "SELECT username, comment FROM comments WHERE book_id = ?";
        $stmt_comments = $conn->prepare($sql_comments);
        $stmt_comments->bind_param("i", $book_id);
        $stmt_comments->execute();
        $result_comments = $stmt_comments->get_result();

        if ($result_comments->num_rows > 0) {
            while($comment = $result_comments->fetch_assoc()) {
                echo '<div class="bg-gray-800 p-4 rounded-lg">';
                echo '<p class="text-white font-bold">' . htmlspecialchars($comment['username']) . ':</p>';
                echo '<p class="text-gray-300">' . htmlspecialchars($comment['comment']) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-gray-400">Henüz yorum yok, ilk yorumu siz yapın.</p>';
        }
        $stmt_comments->close();
        ?>
    </div>

    <?php if (isset($_SESSION['loggedin'])): ?>
        <h4 class="text-xl font-bold text-white mt-8 mb-4">Yorum Yapın</h4>
        <?php
        $message = "";
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
            $comment = trim($_POST['comment']);

            if (!empty($comment)) {
                $sql_insert_comment = "INSERT INTO comments (book_id, username, comment) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert_comment);
                $stmt_insert->bind_param("iss", $book_id, $_SESSION['username'], $comment);
                
                if ($stmt_insert->execute()) {
                    warnlib_log_user_action($conn, $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'], "Kullanıcı yorum yaptı.", $_SESSION['username']);
                    $message = '<div class="bg-green-500 p-4 rounded-lg text-white font-bold mt-4">Yorumunuz başarıyla eklendi.</div>';
                } else {
                    $message = '<div class="bg-red-500 p-4 rounded-lg text-white font-bold mt-4">Yorum eklenirken hata oluştu.</div>';
                }
                $stmt_insert->close();
            } else {
                $message = '<div class="bg-red-500 p-4 rounded-lg text-white font-bold mt-4">Yorum alanı boş bırakılamaz.</div>';
            }
        }
        echo $message;
        ?>
        <form action="index.php?belge=kitap_detay&id=<?php echo $book_id; ?>" method="post" class="mt-4">
            <textarea name="comment" rows="4" class="w-full bg-gray-700 p-3 rounded-lg text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Yorumunuzu buraya yazın..."></textarea>
            <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                Yorumu Gönder
            </button>
        </form>
    <?php else: ?>
        <p class="mt-8 text-gray-400">Yorum yapmak için <a href="index.php?belge=giris" class="text-blue-400 hover:underline">giriş yapmalısınız</a>.</p>
    <?php endif; ?>
    <a href="index.php?belge=anasayfa" class="mt-8 inline-block text-blue-400 hover:underline">Anasayfaya Dön</a>

</div>
