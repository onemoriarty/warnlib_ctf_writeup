<?php


$sql_books = "SELECT id, title, author FROM books";
$result_books = $conn->query($sql_books);
$books = [];
if ($result_books->num_rows > 0) {
    while($row = $result_books->fetch_assoc()) {
        $books[] = $row;
    }
}
?>
<h1 class="text-4xl font-bold text-center text-yellow-400 mb-8">warnlib'e Hoş Geldiniz</h1>
<p class="text-center text-gray-400 mb-12">En sevdiğiniz kitapları bulun ve yorumlarınızı paylaşın.</p>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach ($books as $book): ?>
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300">
            <h3 class="text-2xl font-bold text-white mb-2"><?php echo htmlspecialchars($book['title']); ?></h3>
            <p class="text-gray-400 mb-4">Yazar: <?php echo htmlspecialchars($book['author']); ?></p>
            <a href="index.php?belge=kitap_detay&id=<?php echo $book['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                Detayları Görüntüle
            </a>
        </div>
    <?php endforeach; ?>
</div>
