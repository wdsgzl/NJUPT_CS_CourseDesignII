<?php
// 获取 POST 数据
$user = $_POST['user'];
$newscore = $_POST['score'];


// 连接数据库
$db_file = '../database/Personal.sqlite3';
try {
    $db = new SQLite3($db_file);
} catch (Exception $e) {
    // 连接失败时返回错误信息
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// // 使用预处理语句来防止 SQL 注入
// $stmt = $db->prepare('UPDATE Manager SET point = :score,WHERE user = :user');

// // 绑定参数
// $stmt->bindValue(':score', $newscore, SQLITE3_INTEGER); // 
// $stmt->bindValue(':user', $user, SQLITE3_TEXT); // 

$sql = "UPDATE Manager SET point = point+$newscore WHERE user = '$user'";
$db->exec($sql);

// 关闭数据库连接
$db->close();
?>
