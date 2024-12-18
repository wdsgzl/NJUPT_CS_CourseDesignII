<?php
// 获取 POST 数据
$dishid = $_POST['id'];
$newPrice = $_POST['price'];
$newStock = $_POST['stock'];

// 连接数据库
$db_file = '../database/Menu.sqlite3';
try {
    $db = new SQLite3($db_file);
} catch (Exception $e) {
    // 连接失败时返回错误信息
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$sql = "UPDATE Deal SET price = $newPrice, stock = $newStock WHERE ID = '$dishid'";
$db->exec($sql); 

// 关闭数据库连接
$db->close();
?>
