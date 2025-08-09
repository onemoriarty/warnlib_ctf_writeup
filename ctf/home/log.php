<?php
include '../config.php';
if (!isset($_SESSION['loggedin'])) {
    echo '<div class="bg-red-500 p-4 rounded-lg text-white font-bold">Bu sayfaya erişmek için giriş yapmalısınız.</div>';
    exit;
}

$is_admin = ($_SESSION['role'] === 'admin');
$current_username = $_SESSION['username'];

if (!$is_admin) {
    $remote_addr = $_SERVER['REMOTE_ADDR'];
    $x_forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $remote_addr;

    if ($x_forwarded_for !== '127.0.0.1') {
        http_response_code(403);
        die('<div class="bg-red-500 p-4 rounded-lg text-white font-bold">Bu alana erişiminiz yok.</div>');
    }
}

echo '<div class="container mx-auto mt-8 px-4">
        <div class="bg-green-500 p-4 rounded-lg text-white font-bold text-center">
            Tebrikler! Erişimi sağladınız. İşte üçüncü bayrak: FLAG{Log_Belgesine_Erisim}
        </div>
      </div>';

if ($is_admin) {
    $filter_username = $_GET['user'] ?? '';
    // Eğer 'user' parametresi 'all' değilse veya boşsa
    if ($filter_username === 'all' || empty($filter_username)) {
      $filter_username = 'all';
    }
} else {
    // Admin değilse, sadece kendi loglarını görebilir
    $filter_username = $current_username;
}
?>

<h2 class="text-3xl font-bold text-yellow-400 mb-4">Giriş Logları</h2>
<p class="text-gray-400 mb-8">Bu sayfa adminler için kısıtlamasız, normal kullanıcılar için belirli koşullarda erişilebilir.</p>

<div class="bg-gray-800 p-4 rounded-lg mt-4" style="white-space: pre-wrap; word-wrap: break-word;">
    <?php
    $sql_logs = "SELECT * FROM logs";
    $params = [];
    $types = '';
    
    // Admin değilse veya belirli bir kullanıcı için filtreleme yapılıyorsa
    if ($filter_username !== 'all') {
        $sql_logs .= " WHERE username = ?";
        $params[] = $filter_username;
        $types .= 's';
    }

    $sql_logs .= " ORDER BY timestamp DESC";

    $stmt_logs = $conn->prepare($sql_logs);
    if ($stmt_logs === false) {
        echo "<div class='bg-red-500 p-4 rounded-lg text-white font-bold'>Veritabanı sorgusu hatası: " . htmlspecialchars($conn->error) . "</div>";
    } else {
        if (!empty($params)) {
            $stmt_logs->bind_param($types, ...$params);
        }
        $stmt_logs->execute();
        $result_logs = $stmt_logs->get_result();

        if ($is_admin) {
            // **BURADA BİLİNÇLİ XSS ZAFİYETİ BULUNMAKTADIR**
            // Admin, `?user=<script>...</script>` gibi bir URL ile geldiğinde, bu script çalışacaktır.
            // Bu, oturum çalma (session hijacking) için kullanılabilir.
            echo "<h3 class='text-xl font-bold text-gray-300'>Admin Paneli: " . (($filter_username === 'all') ? "Tüm Loglar" : "'" . $filter_username . "' için Loglar") . "</h3>";
        } else {
            echo "<h3 class='text-xl font-bold text-gray-300'>Kullanıcı '" . htmlspecialchars($filter_username) . "' için Loglar</h3>";
        }

        if ($result_logs->num_rows > 0) {
            while ($log = $result_logs->fetch_assoc()) {
                // User-Agent değeri artık filtrelenmiyor!
                echo "<span>[" . htmlspecialchars($log['timestamp']) . "] IP: " . htmlspecialchars($log['ip_address']) . " | User: " . htmlspecialchars($log['username']) . " | User-Agent: " . $log['user_agent'] . " | Sebep: " . htmlspecialchars($log['reason']) . "</span><br>";
            }
        } else {
            echo "<p class='text-gray-400'>Hiç log bulunamadı.</p>";
        }
        $stmt_logs->close();
    }
    ?>
</div>

<?php if ($is_admin): ?>
    <div class="mt-8">
        <h4 class="text-xl font-bold text-white mb-2">Admin Log Filtreleme</h4>
        <p class="text-gray-400">Belirli bir kullanıcının loglarını görmek için aşağıdaki bağlantıları kullanın:</p>
        <?php
        $sql_users = "SELECT DISTINCT username FROM logs";
        $result_users = $conn->query($sql_users);
        if ($result_users && $result_users->num_rows > 0) {
            echo '<a href="index.php?belge=log&user=all" class="text-blue-400 hover:underline mr-4">Tüm Loglar</a>';
            while ($user = $result_users->fetch_assoc()) {
                echo '<a href="index.php?belge=log&user=' . urlencode($user['username']) . '" class="text-blue-400 hover:underline mr-4">' . htmlspecialchars($user['username']) . '</a>';
            }
        }
        ?>
    </div>

<?php endif; ?>
