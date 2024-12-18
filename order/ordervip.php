<?php

$db_file = '../database/Menu.sqlite3';
$user= $_GET['user'];

// 创建一个 SQLite3 数据库连接
try {
    $db = new SQLite3($db_file);
    //echo "连接成功！<br>";
} catch (Exception $e) {
    echo "连接失败: " . $e->getMessage() . "<br>";
    exit;
}

$getarray=[];
$count=0;
$result = $db->query('select * from Deal');
while($row=$result->fetchArray()){
    array_push($getarray,$row);
    $count=$count+1;
}
$dishes_data=json_encode($getarray,JSON_UNESCAPED_UNICODE);

?>


<script>
    // 用于存储系统的全局状态
    let isMember = true;    // 是否为会员
    let isAdmin = false;     // 是否为管理员
    let cart = [];           // 购物车数组
    let currentCategory = '全部';  // 当前选中的菜品分类
    var amount=<?php echo $count;?>;
    var getdishes_data=<?php echo $dishes_data;?>;


    // 定义所有可用的菜品分类
    const categories = ['全部', '热菜', '凉菜', '主食', '饮品'];
    // 菜品数据数组，包含每个菜品的详细信息
    const dishes = [];
    for(var i=0;i<amount;i++){
        dishes.push({
            id:i,
            name:getdishes_data[i].name,
            category:getdishes_data[i].category,
            regularPrice:getdishes_data[i].price,
            pointsPrice:getdishes_data[i].point,
            stock:getdishes_data[i].stock,
            spicyLevel:getdishes_data[i].spicy
        })
        
    }


        /**
         * 渲染分类按钮函数
         */
        function renderCategories() {
            const container = document.getElementById('categoriesContainer');
            container.innerHTML = categories.map(category => `
                <button class="button category-button ${category === currentCategory ? 'active' : ''}" 
                        data-category="${category}">
                    ${category}
                </button>
            `).join('');
        }

        /**
         * 渲染菜品列表函数
         * 1. 根据当前分类筛选显示菜品
         * 2. 为每个菜品添加库存显示和数量选择功能
         * 3. 根据库存状态显示可选数量
         */
        function renderDishes() {
            const menuContainer = document.getElementById('menuContainer');
            const displayedDishes = currentCategory === '全部' 
                ? dishes 
                : dishes.filter(dish => dish.category === currentCategory);

            menuContainer.innerHTML = displayedDishes.map(dish => {
                // 计算当前菜品在购物车中的数量
                const cartItem = cart.find(item => item.id === dish.id);
                const cartQuantity = cartItem ? cartItem.quantity : 0;
                // 计算剩余可选数量
                const remainingStock = dish.stock - cartQuantity;
                updateMenu(dish.id,dish.regularPrice,remainingStock);
                return `
                    <div class="dish-card ${remainingStock === 0 ? 'sold-out' : ''}" data-dish-id="${dish.id}">
                        <div class="dish-info">
                            <div class="dish-name">
                                ${dish.name}
                                ${dish.spicyLevel ? '<span class="spicy-level">' + '🌶'.repeat(dish.spicyLevel) + '</span>' : ''}
                            </div>
                            <div class="price-info">
                                <p>会员价格: ¥${dish.regularPrice-1}</p>
                                <p>剩余库存: ${remainingStock}</p>
                            </div>
                            <div class="quantity-controls">
                                ${remainingStock > 0 ? `
                                    <button class="button" 
                                            onclick="updateDishQuantity(${dish.id}, ${cartQuantity - 1})" 
                                            ${cartQuantity === 0 ? 'disabled' : ''}>
                                        -
                                    </button>
                                    <span>${cartQuantity}</span>
                                    <button class="button" 
                                            onclick="updateDishQuantity(${dish.id}, ${cartQuantity + 1})"
                                            ${cartQuantity >= remainingStock ? 'disabled' : ''}>
                                        +
                                    </button>
                                ` : '<span>已售罄</span>'}
                            </div>
                        </div>
                        ${isAdmin ? `
                            <button class="button admin-edit" onclick="editDish(${dish.id})">
                                编辑
                            </button>
                        ` : ''}
                    </div>
                `;
            }).join('');
        }

        function updateMenu(dishid,newprice,newstock){
        //if(dishid!=0){
        fetch('update_menu.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${dishid}&stock=${newstock}&price=${newprice}`
            })
        //alert("修改了"+dishid+'为'+newprice);
        //}
    }
        /**
         * 更新菜品数量函数
         * @param {number} dishId - 菜品ID
         * @param {number} newQuantity - 新的数量
         * 1. 检查新数量是否在有效范围内
         * 2. 更新购物车中的数量或移除项目
         * 3. 重新渲染菜品列表和购物车
         */
        function updateDishQuantity(dishId, newQuantity) {
            const dish = dishes.find(d => d.id === dishId);
            
            // 确保数量在有效范围内
            if (newQuantity < 0 || newQuantity > dish.stock) return;

            if (newQuantity === 0) {
                // 移除购物车项目
                cart = cart.filter(item => item.id !== dishId);
            } else {
                const cartItem = cart.find(item => item.id === dishId);
                if (cartItem) {
                    cartItem.quantity = newQuantity;
                } else {
                    cart.push({
                        id: dish.id,
                        name: dish.name,
                        quantity: newQuantity
                    });
                }
            }

            // 更新显示
            renderDishes();
            renderCart();
        }

        /**
         * 更新购物车数量显示函数
         */
        function updateCartCount() {
            const cartCount = document.getElementById('cartCount');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = totalItems;
        }

        /**
         * 渲染购物车内容函数
         */
        function renderCart() {
            const cartItems = document.getElementById('cartItems');
            const totalPrice = document.getElementById('totalPrice');
            
            cartItems.innerHTML = cart.map(item => {
                const dish = dishes.find(d => d.id === item.id);
                return `
                    <div class="cart-item">
                        <span>${item.name}</span>
                        <div class="quantity-controls">
                            <button class="button remove" 
                                    onclick="updateDishQuantity(${item.id}, ${item.quantity - 1})">
                                -
                            </button>
                            <span>${item.quantity}</span>
                            <button class="button" 
                                    onclick="updateDishQuantity(${item.id}, ${item.quantity + 1})"
                                    ${item.quantity >= dish.stock ? 'disabled' : ''}>
                                +
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            const total = cart.reduce((sum, item) => {
                const dish = dishes.find(d => d.id === item.id);
                const price = isMember ? dish.regularPrice-1 : dish.regularPrice;
                return sum + (price * item.quantity);
            }, 0);

            const adjust = cart.reduce((sum, item) => {
                const dish = dishes.find(d => d.id === item.id);
                const price = isMember ?  dish.regularPrice:dish.regularPrice-1;
                return sum + (price * item.quantity);
            }, 0);

            totalPrice.innerHTML = `
                <h3>总计: ¥${total.toFixed(2)}</h3>
                <p>${isMember ? '会员价' : '普通价'}</p>
            `;

            updateCartCount();
        }

        /**
         * 编辑菜品信息函数（管理员功能）
         */
        function editDish(dishId) {
            const dish = dishes.find(d => d.id === dishId);
            const newPrice = prompt(`请输入新的价格（当前价格：¥${dish.regularPrice}）：`);
            const newStock = prompt(`请输入新的库存数量（当前库存：${dish.stock}）：`);
            
            if (newPrice && !isNaN(newPrice)) {
                dish.regularPrice = parseFloat(newPrice);
            }
            
            if (newStock && !isNaN(newStock)) {
                dish.stock = parseInt(newStock);
                // 如果新库存小于购物车中的数量，更新购物车
                const cartItem = cart.find(item => item.id === dishId);
                if (cartItem && cartItem.quantity > dish.stock) {
                    cartItem.quantity = dish.stock;
                }
            }
            
            renderDishes();
            renderCart();
        }
        function getStockByDishId(dishId, callback) {
        const query = 'SELECT stock FROM dishes WHERE id = ?';
    db.get(query, [dishId], (err, row) => {
        if (err) {
            console.error('Database query error:', err);
            callback(err, null);
        } else {
            callback(null, row ? row.stock : 0); // 返回库存，若没有找到则返回 0
        }
    });
}
function updateScore(userid, newscore) {
    // 判断 dishid 是否为 null 或 undefined
    
        // 使用 fetch 发起 POST 请求
        fetch('update_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user=${userid}&score=${newscore}`
        })
        
    } 
function ToOrderscore(){
        var user = "<?php echo isset($user) ? urlencode($user) : ''; ?>";
        if (!user) {
            alert("用户信息为空，无法跳转！");
            return;
        }
        var targetUrl = "orderscore.php?user=" + encodeURIComponent(user);
        console.log("跳转到: " + targetUrl);

        // 跳转到目标页面
        location.href = targetUrl;
    }
        /**
         * 页面加载完成后的初始化函数
         */
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('categoriesContainer').addEventListener('click', (e) => {
                if (e.target.classList.contains('category-button')) {
                    currentCategory = e.target.dataset.category;
                    renderCategories();
                    renderDishes();
                }
            });



            document.getElementById('cartButton').addEventListener('click', () => {
                document.getElementById('cartModal').style.display = 'block';
            });

            document.getElementById('closeCart').addEventListener('click', () => {
                document.getElementById('cartModal').style.display = 'none';
            });

            document.getElementById('checkout').addEventListener('click', () => {
                const tableNumber = document.getElementById('tableNumber').value;
                var nuser=<?php echo json_encode($user);?>;
                if (!tableNumber) {
                    alert('请输入台号');
                    return;
                }
                const total = cart.reduce((sum, item) => {
                    const dish = dishes.find(d => d.id === item.id);
                    const price = isMember ? dish.regularPrice-1 : dish.regularPrice;
                    return sum + (price * item.quantity);
                }, 0);

                // 更新库存
                cart.forEach(item => {
                    const dish = dishes.find(d => d.id === item.id);
                    dish.stock -= item.quantity;
                    updateMenu(dish.id,dish.regularPrice,dish.stock);
                });

                alert(`订单提交成功！\n台号：${tableNumber}\n总价：¥${total}\n获得积分:${total*15}`);
                //alert(total);
                updateScore(nuser,total*15);
                
                cart = [];
                renderDishes();
                renderCart();
                
                
                //alert(nuser);
                
                document.getElementById('cartModal').style.display = 'none';
                
            });
            
            renderCategories();
            renderDishes();
            renderCart();
        });
    </script>


<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>饭店点餐系统</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f5f5f5;
        }

        .header {
            background-color: #333;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .menu-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .dish-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }

        .dish-info {
            margin-top: 10px;
        }

        .price-info {
            margin: 10px 0;
            color: #666;
        }

        .cart-button {
            position: fixed;
            right: 20px;
            bottom: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 100;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

        .cart-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .cart-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .button:hover {
            background-color: #45a049;
        }

        .button.remove {
            background-color: #f44336;
        }

        .button.remove:hover {
            background-color: #da190b;
        }

        .table-number {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }

        .user-controls {
            display: flex;
            gap: 10px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sold-out {
            opacity: 0.6;
            position: relative;
        }

        .sold-out::after {
            content: '已售罄';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
        }

        .dish-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .modal-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .categories {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            overflow-x: auto;
            padding: 10px 0;
        }

        .category-button {
            white-space: nowrap;
            padding: 8px 16px;
            background-color: #f0f0f0;
            color: #333;
        }

        .category-button:hover,
        .category-button.active {
            background-color: #4CAF50;
            color: white;
        }

        .admin-edit {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #2196F3;
        }

        .spicy-level {
            color: #ff4444;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>美味饭店点餐系统</h1>
        <div class="user-controls">
        <button class="button" onclick="window.history.back();">退出登录</button>
        <button class="button" onclick='ToOrderscore()'>积分兑换</button>
        <button class="button" id="toggleMember">会员模式</button>
            
        </div>
    </header>

    <div class="container">
        <input type="text" class="table-number" placeholder="请输入台号" id="tableNumber">
        <div id="categoriesContainer" class="categories"></div>
        <div class="menu-container" id="menuContainer"></div>
    </div>

    <div class="cart-button" id="cartButton">
        🛒
        <span class="cart-count" id="cartCount">0</span>
    </div>

    <div class="cart-modal" id="cartModal">
        <div class="cart-content">
            <h2>购物车</h2>
            <div id="cartItems"></div>
            <div id="totalPrice"></div>
            <div class="modal-buttons">
                <button class="button remove" id="closeCart">关闭</button>
                <button class="button" id="checkout">结算</button>
            </div>
        </div>
    </div>

  
</body>
</html>