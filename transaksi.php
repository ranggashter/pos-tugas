<?php
session_start();
include "koneksi.php";

// Cek session
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'kasir') {
    header("Location: login.php");
    exit();
}
?>

<form method="POST" action="proses_transaksi.php" onsubmit="return validateForm()">
    <div style="max-width: 500px; margin: 20px auto; padding: 20px; background-color: #f8f9fa; border-radius: 5px; font-family: Arial, sans-serif;">
        <h3 style="margin-top: 0;">Form Transaksi</h3>
        
        <div style="margin-bottom: 15px;">
            <label for="harga" style="display: block; margin-bottom: 5px; font-weight: bold;">Harga Barang (Rp):</label>
            <input type="number" id="harga" name="harga" min="0" step="100" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="diskon" style="display: block; margin-bottom: 5px; font-weight: bold;">Diskon (%):</label>
            <input type="number" id="diskon" name="diskon" min="0" max="100" value="0" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label for="bayar" style="display: block; margin-bottom: 5px; font-weight: bold;">Jumlah Bayar (Rp):</label>
            <input type="number" id="bayar" name="bayar" min="0" step="100" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>
        
        <button type="submit" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold;">
            Proses Transaksi
        </button>
    </div>
</form>

<script>
function validateForm() {
    const harga = parseFloat(document.getElementById('harga').value);
    const bayar = parseFloat(document.getElementById('bayar').value);
    const diskon = parseFloat(document.getElementById('diskon').value);
    
    if (harga <= 0) {
        alert('Harga harus lebih dari 0!');
        return false;
    }
    
    if (diskon < 0 || diskon > 100) {
        alert('Diskon harus antara 0-100%!');
        return false;
    }
    
    const total = harga - (harga * diskon / 100);
    
    if (bayar < total) {
        alert('Jumlah bayar harus >= total harga (Rp ' + Math.round(total) + ')');
        return false;
    }
    
    return true;
}
</script>
