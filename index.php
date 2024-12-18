<?php
// 数据库文件路径（SQLite3 数据库是一个文件）
$db_file = './database/Personal.sqlite3';

// 创建一个 SQLite3 数据库连接
try {
    $db = new SQLite3($db_file);
    //echo "连接成功！<br>";
} catch (Exception $e) {
    echo "连接失败: " . $e->getMessage() . "<br>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取表单数据
    $user = $_POST['user'];
    $password = $_POST['password'];
    $result = $db->query('select * from Manager');
    $vip=1;
    $manager=1;
    $error=1;
    $score=0;
    while($row=$result->fetchArray()){
        $judgeuser=$row['user'];
        $judgepassword=$row['password'];
        $judgevip=$row['vip'];
        $judgemanager=$row['manager'];
        //管理员登录
        if($user==$judgeuser && $password==$judgepassword && $judgemanager==$manager){
            $error=0;
            //echo "<script>alert('管理员您好，登陆成功')</script>";
            header("Location: ./order/ordermanager.php?user=".urlencode($user));

        }
        //会员登陆
        else if($user==$judgeuser && $password==$judgepassword  && $judgevip==$vip){
            $error=0;
            
            //echo "<script>alert('尊敬的会员您好，登陆成功')</script>";
            header("Location: ./order/ordervip.php?user=".urlencode($user));

        }
        //普通登录
        else if($user==$judgeuser && $password==$judgepassword && $judgemanager!=$manager && $judgevip!=$vip){
            $error=0;
            //echo "<script>alert('顾客您好，登陆成功')</script>";
            header("Location: /order/order.php?user=" . urlencode($user));
        }

    }
    
    if($error)
    echo "<script>alert('账号或密码错误')</script>";

}

?>




<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .link {
            margin-top: 10px;
        }
        .link a {
            color: #007bff;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

 <div class="container">
    <h1>登录</h1>
    <form  method="post">
        <label for="user">用户名:</label>
        <input type="text" id="user" name="user" required>
        <label for="password">密码:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">登录</button>
    </form>

</div> 
</body>
</html>
