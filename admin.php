<?php
session_start();

// Cek session
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        .logout-btn { background: #666; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; }
        
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background: white;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        
        .card-icon { font-size: 32px; margin-bottom: 10px; }
        .card-title { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .card-desc { font-size: 14px; color: #666; }
        
        .menu-section {
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .menu-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .menu-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .menu-item {
            background: #f9f9f9;
            padding: 15px;
            border-left: 3px solid #333;
            text-decoration: none;
            color: #333;
            border-radius: 3px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .menu-item:hover {
            background: #f0f0f0;
            border-left-color: #666;
        }
        
        .menu-icon { font-size: 18px; }

        .anjay { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ADMIN PANEL</h1>
        <div>
            <span style="margin-right: 20px;">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="logout-btn">LOGOUT</a>
        </div>
    </div>
    
    <div class="container">
        <!-- Quick Stats -->
        <div class="dashboard-grid">
            <div class="dashboard-card" onclick="window.location.href='tampil_barang.php'">
                <!-- <div class="card-icon">📦</div> -->
                <div class="card-title">Total Produk</div>
                <div class="card-desc">Kelola Barang</div>
            </div>
            
            <div class="dashboard-card" onclick="window.location.href='tampil_transaksi.php'">
                <!-- <div class="card-icon">💰</div> -->
                <div class="card-title">Transaksi</div>
                <div class="card-desc">Riwayat Penjualan</div>
            </div>
            
            <div class="dashboard-card" onclick="window.location.href='barang.php'">
                <!-- <div class="card-icon">➕</div> -->
                <div class="card-title">Tambah Produk</div>
                <div class="card-desc">Produk Baru</div>
            </div>
        </div>
        
        <!-- Menu Section -->
        <div class="menu-section">
            <div class="menu-title">MANAJEMEN</div>
            <div class="menu-list">
                <a href="barang.php" class="menu-item">
                    <!-- <div class="menu-icon">➕</div> -->
                    <div class="anjay">Tambah Barang Baru</div>
                </a>
                <a href="tampil_barang.php" class="menu-item">
                    <!-- <div class="menu-icon">📦</div> -->
                    <div class="anjay">Daftar Barang</div>
                </a>
                <a href="tampil_transaksi.php" class="menu-item">
                    <!-- <div class="menu-icon">💵</div> -->
                    <div class="anjay">Riwayat Transaksi</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
