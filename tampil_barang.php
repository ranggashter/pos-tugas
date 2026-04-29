<?php
session_start();
include "koneksi.php";

// Cek session
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id']) && $_SESSION['role'] == 'admin') {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM barang WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: tampil_barang.php");
    exit();
}

$result = $conn->query("SELECT * FROM barang ORDER BY nama_barang ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang</title>
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
        .header-nav a { color: white; text-decoration: none; margin-left: 15px; padding: 8px 15px; background: #666; border-radius: 3px; }
        
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .table-container {
            background: white;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        th {
            background: #333;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .stock-ok { color: #28a745; font-weight: bold; }
        .stock-low { color: #dc3545; font-weight: bold; }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
            transition: background 0.2s;
        }
        .btn-delete:hover { background: #c82333; }
        
        .action-container {
            margin-top: 15px;
        }
        
        .btn {
            padding: 8px 15px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            display: inline-block;
            font-size: 12px;
            transition: background 0.2s;
        }
        .btn:hover { background: #555; }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DAFTAR BARANG</h1>
        <div class="header-nav">
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="admin.php">← DASHBOARD</a>
            <?php else: ?>
                <a href="kasir.php">← KASIR</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <div class="section-title">PRODUK TERSEDIA</div>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 40%;">Nama Barang</th>
                            <th style="width: 25%; text-align: right;">Harga</th>
                            <th style="width: 15%; text-align: center;">Stok</th>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <th style="width: 15%; text-align: center;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                            $stock_class = $row['stok'] > 5 ? 'stock-ok' : ($row['stok'] > 0 ? 'stock-low' : 'stock-low');
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $no; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                <td class="text-right">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td class="text-center <?php echo $stock_class; ?>"><?php echo $row['stok']; ?></td>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <td class="text-center">
                                        <button class="btn-delete" onclick="if(confirm('Yakin ingin menghapus?')) window.location.href='tampil_barang.php?action=hapus&id=<?php echo $row['id_barang']; ?>'">HAPUS</button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php $no++; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="action-container">
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="barang.php" class="btn">TAMBAH BARANG BARU</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                Belum ada barang. <?php echo $_SESSION['role'] == 'admin' ? '<a href="barang.php" class="btn">Tambah barang</a>' : ''; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
