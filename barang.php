<?php
session_start();
include "koneksi.php";

// Cek session
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : "";
    $harga = isset($_POST['harga']) ? floatval($_POST['harga']) : 0;
    $stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;

    // Validasi input
    if (empty($nama)) {
        $error = "Nama barang tidak boleh kosong!";
    } else if (strlen($nama) < 3) {
        $error = "Nama barang minimal 3 karakter!";
    } else if ($harga <= 0) {
        $error = "Harga harus lebih dari 0!";
    } else if ($stok < 0) {
        $error = "Stok tidak boleh negatif!";
    } else {
        // Gunakan prepared statement
        $stmt = $conn->prepare("INSERT INTO barang (nama_barang, harga, stok) VALUES (?, ?, ?)");
        if ($stmt === false) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("sdi", $nama, $harga, $stok);
            if ($stmt->execute()) {
                $success = "Barang berhasil ditambahkan!";
                // Reset form
                $_POST = array();
            } else {
                $error = "Gagal menambahkan barang!";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
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
        
        .container { max-width: 600px; margin: 20px auto; padding: 20px; }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .form-box {
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .message-box {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 13px;
        }
        
        .error-box {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .success-box {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 13px;
        }
        
        input:focus {
            outline: none;
            border-color: #333;
            box-shadow: 0 0 3px rgba(51,51,51,0.2);
        }
        
        .buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .btn {
            padding: 10px;
            border: none;
            border-radius: 3px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #333;
            color: white;
        }
        .btn-primary:hover { background: #555; }
        
        .btn-secondary {
            background: #ddd;
            color: #333;
        }
        .btn-secondary:hover { background: #ccc; }
        
        .action-links {
            margin-top: 15px;
        }
        
        .action-links a {
            display: inline-block;
            margin-right: 10px;
            padding: 8px 15px;
            background: #666;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
            transition: background 0.2s;
        }
        
        .action-links a:hover { background: #888; }
    </style>
</head>
<body>
    <div class="header">
        <h1>TAMBAH BARANG</h1>
        <div class="header-nav">
            <a href="admin.php">← DASHBOARD</a>
        </div>
    </div>
    
    <div class="container">
        <div class="section-title">➕ TAMBAH PRODUK BARU</div>
        
        <?php if (!empty($error)): ?>
            <div class="message-box error-box">⚠ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="message-box success-box">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="form-box">
            <form method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="nama">Nama Barang</label>
                    <input type="text" id="nama" name="nama" value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" required autofocus>
                    <small style="color: #666; font-size: 11px;">Minimal 3 karakter</small>
                </div>
                
                <div class="form-group">
                    <label for="harga">Harga (Rp)</label>
                    <input type="number" id="harga" name="harga" min="0" step="100" value="<?php echo isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" min="0" value="<?php echo isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : '0'; ?>" required>
                </div>
                
                <div class="buttons">
                    <button type="submit" class="btn btn-primary">💾 SIMPAN</button>
                    <button type="reset" class="btn btn-secondary">↻ BERSIHKAN</button>
                </div>
            </form>
        </div>
        
        <div class="action-links">
            <a href="tampil_barang.php">📦 Lihat Daftar Barang</a>
        </div>
    </div>
    
    <script>
        function validateForm() {
            const nama = document.getElementById('nama').value.trim();
            const harga = parseFloat(document.getElementById('harga').value);
            const stok = parseInt(document.getElementById('stok').value);
            
            if (nama.length < 3) {
                alert('Nama barang minimal 3 karakter!');
                return false;
            }
            
            if (harga <= 0) {
                alert('Harga harus lebih dari 0!');
                return false;
            }
            
            if (stok < 0) {
                alert('Stok tidak boleh negatif!');
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>