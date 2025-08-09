<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header('Content-Type: text/html; charset=utf-8');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kekekekeme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

function warnlib_log_user_action($conn, $user_agent, $ip_address, $reason, $username) {
    $sanitized_user_agent = str_replace(
        ['<?php', '<?=', '<?', '?>'], 
        ['&lt;?php', '&lt;?=', '&lt;?', '?&gt;'], 
        $user_agent
    );
    $sql = "INSERT INTO logs (ip_address, user_agent, reason, username) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Veritabanı sorgusu hazırlanamadı: " . $conn->error);
        return;
    }

    $stmt->bind_param("ssss", $ip_address, $sanitized_user_agent, $reason, $username);
    $stmt->execute();
    $stmt->close();
}

function sanitize($conn, $data) {
    return $conn->real_escape_string($data);
}

?>

