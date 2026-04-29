<?php
session_start();
include "koneksi.php";

// Cek session
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Query berdasarkan role
if ($_SESSION['role'] == 'admin') {
    $query = "SELECT t.*, u.username FROM transaksi t LEFT JOIN users u ON t.user_id = u.id_user ORDER BY t.tanggal DESC LIMIT 100";
} else {
    $query = "SELECT * FROM transaksi WHERE user_id = " . intval($_SESSION['id_user']) . " ORDER BY tanggal DESC LIMIT 100";
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
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
        
        .summary {
            background: white;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
            font-weight: bold;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .summary-label { font-size: 12px; color: #666; margin-bottom: 5px; }
        .summary-value { font-size: 18px; color: #333; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RIWAYAT TRANSAKSI</h1>
        <div class="header-nav">
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="admin.php">← DASHBOARD</a>
            <?php else: ?>
                <a href="kasir.php">← KASIR</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <div class="section-title">📊 DAFTAR TRANSAKSI</div>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal & Jam</th>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <th>Kasir</th>
                            <?php endif; ?>
                            <th class="text-right">Total</th>
                            <th class="text-right">Bayar</th>
                            <th class="text-right">Kembalian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $total_all = 0;
                        $total_bayar_all = 0;
                        $total_kembalian_all = 0;
                        
                        while ($row = $result->fetch_assoc()):
                            $total_all += $row['total'];
                            $total_bayar_all += $row['bayar'];
                            $total_kembalian_all += $row['kembalian'];
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $no; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <td><?php echo isset($row['username']) ? htmlspecialchars($row['username']) : '-'; ?></td>
                                <?php endif; ?>
                                <td class="text-right">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                <td class="text-right">Rp <?php echo number_format($row['bayar'], 0, ',', '.'); ?></td>
                                <td class="text-right">Rp <?php echo number_format($row['kembalian'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php $no++; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="summary">
                <div class="summary-item">
                    <div class="summary-label">Total Transaksi</div>
                    <div class="summary-value"><?php echo $no - 1; ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Penjualan</div>
                    <div class="summary-value">Rp <?php echo number_format($total_all, 0, ',', '.'); ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Kembalian</div>
                    <div class="summary-value">Rp <?php echo number_format($total_kembalian_all, 0, ',', '.'); ?></div>
                </div>
            </div>
        <?php else: ?>
            <div style="background: white; padding: 20px; border-radius: 4px; text-align: center; color: #999;">
                Belum ada transaksi
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
