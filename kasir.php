    <?php
    session_start();
    include "koneksi.php";

    // Cek session
    if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'kasir') {
        header("Location: login.php");
        exit();
    }

    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['subtotal'];
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Point of Sale - Kasir</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: 'Arial', sans-serif;
                background: #f5f5f5;
            }
            
            .header {
                background: #333;
                color: white;
                padding: 15px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .header h1 { font-size: 20px; }
            .header-info { display: flex; gap: 20px; font-size: 12px; }
            .logout-btn { background: #666; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; }
            
            .main-container { display: grid; grid-template-columns: 2fr 1fr; gap: 15px; padding: 15px; min-height: calc(100vh - 50px); }
            
            /* PRODUCTS SECTION */
            .products-section { background: white; padding: 15px; border-radius: 4px; }
            .section-title { font-size: 14px; font-weight: bold; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #333; }
            
            .search-bar { 
                width: 100%;
                padding: 8px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 3px;
                font-size: 12px;
            }
            
            .products-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
                max-height: calc(100vh - 150px);
                overflow-y: auto;
                padding-right: 8px;
            }
            
            .product-card {
                background: #f9f9f9;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 3px;
                cursor: pointer;
                transition: all 0.2s;
                text-align: center;
            }
            .product-card:hover { background: #f0f0f0; border-color: #333; }
            .product-name { font-size: 12px; font-weight: bold; margin-bottom: 5px; }
            .product-price { font-size: 11px; color: #666; margin-bottom: 8px; }
            .product-qty { font-size: 10px; color: #999; }
            
            .btn-add { 
                width: 100%;
                padding: 5px;
                background: #333;
                color: white;
                border: none;
                border-radius: 2px;
                font-size: 11px;
                cursor: pointer;
                transition: background 0.2s;
            }
            .btn-add:hover { background: #555; }
            
            /* CART SECTION */
            .cart-section {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            
            .cart-box { 
                background: white; 
                padding: 15px;
                border-radius: 4px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }
            
            .cart-items {
                flex: 1;
                overflow-y: auto;
                max-height: 40vh;
                margin-bottom: 10px;
                padding-right: 8px;
            }
            
            .cart-item {
                background: #f9f9f9;
                padding: 8px;
                margin-bottom: 8px;
                border-left: 3px solid #333;
                font-size: 12px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .item-info {
                flex: 1;
            }
            .item-name { font-weight: bold; margin-bottom: 3px; }
            .item-detail { font-size: 11px; color: #666; }
            
            .item-actions {
                display: flex;
                gap: 5px;
                align-items: center;
            }
            
            .qty-input { 
                width: 40px; 
                padding: 3px; 
                border: 1px solid #ddd;
                border-radius: 2px;
                font-size: 11px;
                text-align: center;
            }
            
            .btn-remove {
                background: #ddd;
                color: #333;
                border: none;
                padding: 3px 6px;
                border-radius: 2px;
                cursor: pointer;
                font-size: 10px;
            }
            .btn-remove:hover { background: #ccc; }
            
            /* TOTALS SECTION */
            .totals-box {
                background: white;
                padding: 15px;
                border-radius: 4px;
            }
            
            .total-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
                font-size: 13px;
                padding-bottom: 8px;
                border-bottom: 1px solid #eee;
            }
            
            .total-row.grand { 
                border-bottom: 2px solid #333;
                font-weight: bold;
                font-size: 16px;
                margin: 10px 0;
                padding-bottom: 10px;
            }
            
            .payment-group { margin-bottom: 15px; }
            .payment-group label { display: block; font-size: 12px; margin-bottom: 5px; }
            .payment-group input {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 3px;
                font-size: 12px;
            }
            
            .buttons-group {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }
            
            .btn {
                padding: 10px;
                border: none;
                border-radius: 3px;
                font-size: 12px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.2s;
            }
            
            .btn-checkout {
                background: #333;
                color: white;
                grid-column: 1 / -1;
            }
            .btn-checkout:hover { background: #555; }
            
            .btn-clear {
                background: #ddd;
                color: #333;
            }
            .btn-clear:hover { background: #ccc; }
            
            .empty-cart {
                text-align: center;
                color: #999;
                font-size: 12px;
                padding: 20px;
            }
            
            ::-webkit-scrollbar {
                width: 6px;
            }
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            ::-webkit-scrollbar-thumb {
                background: #ddd;
                border-radius: 3px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #ccc;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>POINT OF SALE</h1>
            <div class="header-info">
                <div>Kasir: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
                <div id="time"></div>
                <a href="logout.php" class="logout-btn">LOGOUT</a>
            </div>
        </div>
        
        <div class="main-container">
            <!-- PRODUCTS SECTION -->
            <div class="products-section">
                <div class="section-title">📦 PRODUK</div>
                <input type="text" class="search-bar" id="searchInput" placeholder="Cari produk...">
                
                <div class="products-grid" id="productsGrid">
                    <?php
                    $result = $conn->query("SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '
                            <div class="product-card" onclick="addToCart(' . $row['id_barang'] . ', \'' . htmlspecialchars($row['nama_barang']) . '\', ' . $row['harga'] . ')">
                                <div class="product-name">' . htmlspecialchars($row['nama_barang']) . '</div>
                                <div class="product-price">Rp ' . number_format($row['harga'], 0, ',', '.') . '</div>
                                <div class="product-qty">Stok: ' . $row['stok'] . '</div>
                                <button type="button" class="btn-add">+ TAMBAH</button>
                            </div>
                            ';
                        }
                    } else {
                        echo '<div class="empty-cart" style="grid-column: 1/-1;">Tidak ada produk tersedia</div>';
                    }
                    ?>
                </div>
            </div>
            
            <!-- CART SECTION -->
            <div class="cart-section">
                <!-- Cart Items -->
                <div class="cart-box">
                    <div class="section-title">🛒 KERANJANG</div>
                    <div class="cart-items" id="cartItems">
                        <?php
                        if (empty($cart)) {
                            echo '<div class="empty-cart">Keranjang kosong</div>';
                        } else {
                            foreach ($cart as $key => $item) {
                                echo '
                                <div class="cart-item">
                                    <div class="item-info">
                                        <div class="item-name">' . htmlspecialchars($item['name']) . '</div>
                                        <div class="item-detail">' . $item['qty'] . 'x Rp ' . number_format($item['price'], 0, ',', '.') . '</div>
                                    </div>
                                    <div class="item-actions">
                                        <span style="min-width: 50px; text-align: right;">Rp ' . number_format($item['subtotal'], 0, ',', '.') . '</span>
                                        <button type="button" class="btn-remove" onclick="removeItem(' . $key . ')">HAPUS</button>
                                    </div>
                                </div>
                                ';
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Totals & Payment -->
                <div class="totals-box">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    
<label>Diskon (%)</label>
<input type="number" id="discount" value="0" onchange="calculateChange()">

<div class="total-row">
    <span>Potongan:</span>
    <span id="discountAmount">Rp 0</span>
</div>
                    
                    <div class="total-row grand">
                        <span>TOTAL:</span>
                        <span id="grandTotal">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    
                    <div class="payment-group">
                        <label>Nominal Bayar (Rp)</label>
                        <input type="number" id="payment" placeholder="0" onchange="calculateChange()" step="100" min="0">
                    </div>
                    
                    <div class="total-row">
                        <span>Kembalian:</span>
                        <span id="changeAmount">Rp 0</span>
                    </div>
                    
                    <div class="buttons-group">
                        <button type="button" class="btn btn-clear" onclick="clearCart()">BERSIHKAN</button>
                        <button type="button" class="btn btn-checkout" onclick="checkout()">CHECKOUT</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            // Update waktu
            function updateTime() {
                const now = new Date();
                document.getElementById('time').textContent = now.toLocaleTimeString('id-ID');
            }
            setInterval(updateTime, 1000);
            updateTime();
            
            // Cart system (simplified, using page reload untuk update)
            function addToCart(id, name, price) {
                // Redirect ke helper untuk tambah ke cart
                window.location.href = 'cart_helper.php?action=add&id=' + id + '&name=' + encodeURIComponent(name) + '&price=' + price;
            }
            
            function removeItem(key) {
                window.location.href = 'cart_helper.php?action=remove&key=' + key;
            }
            
            function clearCart() {
                if (confirm('Yakin ingin menghapus semua item?')) {
                    window.location.href = 'cart_helper.php?action=clear';
                }
            }
            
function calculateChange() {
    const payment = parseFloat(document.getElementById('payment').value) || 0;
    const subtotal = <?php echo $total; ?>;
    const discount = parseFloat(document.getElementById('discount').value) || 0;

    const potongan = subtotal * discount / 100;
    const grandTotal = subtotal - potongan;
    const change = payment >= grandTotal ? payment - grandTotal : 0;

    document.getElementById('discountAmount').textContent =
        'Rp ' + potongan.toLocaleString('id-ID');

    document.getElementById('grandTotal').textContent =
        'Rp ' + grandTotal.toLocaleString('id-ID');

    document.getElementById('changeAmount').textContent =
        'Rp ' + change.toLocaleString('id-ID');
}
function checkout() {

    const payment = parseFloat(document.getElementById('payment').value) || 0;
    const subtotal = <?php echo $total; ?>;
    const discount = parseFloat(document.getElementById('discount').value) || 0;

    const potongan = subtotal * discount / 100;
    const grandTotal = subtotal - potongan;

    if (payment < grandTotal) {
        alert("Uang bayar kurang!");
        return;
    }

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "proses_checkout.php";

    form.innerHTML += '<input type="hidden" name="payment" value="'+payment+'">';
    form.innerHTML += '<input type="hidden" name="discount" value="'+discount+'">';
    form.innerHTML += '<input type="hidden" name="total" value="'+grandTotal+'">';

    document.body.appendChild(form);
    form.submit();
}
            
            // Search produk
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                const search = e.target.value.toLowerCase();
                const products = document.querySelectorAll('.product-card');
                products.forEach(p => {
                    const name = p.querySelector('.product-name').textContent.toLowerCase();
                    p.style.display = name.includes(search) ? '' : 'none';
                });
            });
        </script>
    </body>
    </html>
