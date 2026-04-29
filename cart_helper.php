<?php
session_start();
include "koneksi.php";

// Cek session
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'kasir') {
    header("Location: login.php");
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Inisialisasi cart jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

switch ($action) {
    case 'add':
        $id = intval($_GET['id']);
        $name = isset($_GET['name']) ? urldecode($_GET['name']) : '';
        $price = floatval($_GET['price']);
        
        // Cek apakah produk sudah di cart
        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $id) {
                $_SESSION['cart'][$key]['qty']++;
                $_SESSION['cart'][$key]['subtotal'] = $_SESSION['cart'][$key]['qty'] * $price;
                $found = true;
                break;
            }
        }
        
        // Jika belum ada, tambahkan item baru
        if (!$found) {
            $_SESSION['cart'][] = array(
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'qty' => 1,
                'subtotal' => $price
            );
        }
        break;
    
    case 'remove':
        $key = intval($_GET['key']);
        if (isset($_SESSION['cart'][$key])) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
        }
        break;
    
    case 'clear':
        $_SESSION['cart'] = array();
        break;
}

// Redirect kembali ke kasir
header("Location: kasir.php");
exit();
?>
