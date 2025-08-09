<?php
$sql_users = "SELECT id, username, role FROM users";
$result_users = $conn->query($sql_users);
$users = [];
if ($result_users->num_rows > 0) {
    while($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<h2 class="text-3xl font-bold text-yellow-400 mb-4">Kullanıcılar</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach ($users as $user): ?>
        <div class="bg-gray-800 p-4 rounded-lg shadow">
            <p class="text-lg text-white font-bold"><?php echo htmlspecialchars($user['username']); ?></p>
            <p class="text-sm text-gray-400">Rol: <?php echo htmlspecialchars($user['role']); ?></p>
        </div>
    <?php endforeach; ?>
</div>
