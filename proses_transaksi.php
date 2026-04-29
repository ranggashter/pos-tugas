<?php
session_start();
include "koneksi.php";

// Cek session
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'kasir') {
    header("Location: login.php");
    exit();
}

$error = "";
$success = false;
$result_data = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $harga = isset($_POST['harga']) ? floatval($_POST['harga']) : 0;
    $diskon = isset($_POST['diskon']) ? floatval($_POST['diskon']) : 0;
    $bayar = isset($_POST['bayar']) ? floatval($_POST['bayar']) : 0;

    // Validasi input
    if (empty($harga) || $harga <= 0) {
        $error = "Harga harus lebih dari 0!";
    } else if ($diskon < 0 || $diskon > 100) {
        $error = "Diskon harus antara 0-100%!";
    } else {
        // Hitung total
        $potongan = $harga * ($diskon / 100);
        $total = $harga - $potongan;

        if ($bayar < $total) {
            $error = "Jumlah bayar (Rp " . number_format($bayar) . ") kurang dari total (Rp " . number_format($total) . ")!";
        } else {
            // Hitung kembalian
            $kembalian = $bayar - $total;

            // Gunakan prepared statement
            $stmt = $conn->prepare("INSERT INTO transaksi (tanggal, total, bayar, kembalian, user_id) VALUES (NOW(), ?, ?, ?, ?)");
            if ($stmt === false) {
                $error = "Database error: " . $conn->error;
            } else {
                $user_id = $_SESSION['user_id'];
                $stmt->bind_param("dddi", $total, $bayar, $kembalian, $user_id);
                
                if ($stmt->execute()) {
                    $success = true;
                    $result_data = array(
                        'harga' => $harga,
                        'diskon' => $diskon,
                        'potongan' => $potongan,
                        'total' => $total,
                        'bayar' => $bayar,
                        'kembalian' => $kembalian
                    );
                    $_SESSION['transaksi_success'] = true;
                    $_SESSION['transaksi_data'] = $result_data;
                } else {
                    $error = "Gagal menyimpan transaksi: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Jika error, redirect kembali dengan pesan
if (!empty($error)) {
    $_SESSION['transaksi_error'] = $error;
    header("Location: kasir.php");
    exit();
}

// Jika sukses, tampilkan hasil
if ($success && !empty($result_data)):
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hasil Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #28a745; text-align: center; margin-top: 0; }
        .receipt { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; font-family: 'Courier New', monospace; }
        .receipt-line { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dotted #ddd; }
        .receipt-line.total { font-weight: bold; border-bottom: 2px solid #333; margin-top: 10px; padding-top: 10px; font-size: 18px; }
        .receipt-line.kembalian { font-weight: bold; color: #28a745; font-size: 18px; border: none; margin-top: 10px; padding-top: 10px; }
        .buttons { display: flex; gap: 10px; margin-top: 20px; }
        .btn { flex: 1; padding: 12px; text-align: center; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; font-weight: bold; }
        .btn-print { background-color: #007bff; color: white; }
        .btn-print:hover { background-color: #0056b3; }
        .btn-back { background-color: #6c757d; color: white; }
        .btn-back:hover { background-color: #5a6268; }
        @media print {
            .buttons, body { display: none; }
            .container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✓ Transaksi Berhasil</h2>
        
        <div class="receipt">
            <div class="receipt-line">
                <span>Harga Barang:</span>
                <span>Rp <?php echo number_format($result_data['harga'], 0, ',', '.'); ?></span>
            </div>
            <div class="receipt-line">
                <span>Diskon:</span>
                <span><?php echo $result_data['diskon']; ?>% (-Rp <?php echo number_format($result_data['potongan'], 0, ',', '.'); ?>)</span>
            </div>
            <div class="receipt-line total">
                <span>Total:</span>
                <span>Rp <?php echo number_format($result_data['total'], 0, ',', '.'); ?></span>
            </div>
            <div class="receipt-line">
                <span>Dibayar:</span>
                <span>Rp <?php echo number_format($result_data['bayar'], 0, ',', '.'); ?></span>
            </div>
            <div class="receipt-line kembalian">
                <span>Kembalian:</span>
                <span>Rp <?php echo number_format($result_data['kembalian'], 0, ',', '.'); ?></span>
            </div>
        </div>
        
        <div class="buttons">
            <!-- <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button> -->
            <a href="kasir.php" class="btn btn-back">← Kembali</a>
        </div>
    </div>
</body>
</html>

<?php
else:
    // Jika bukan POST, redirect ke kasir
    header("Location: kasir.php");
    exit();
endif;
?>