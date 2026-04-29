<?php
session_start();
include "koneksi.php";

// cek session
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'kasir') {
    header("Location: login.php");
    exit();
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

$payment  = isset($_POST['payment']) ? floatval($_POST['payment']) : 0;
$discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;

// hitung subtotal
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['subtotal'];
}

// hitung diskon
$potongan = $subtotal * $discount / 100;
$total = $subtotal - $potongan;

// validasi
if (empty($cart) || $payment < $total) {
    $_SESSION['checkout_error'] = "Transaksi tidak valid!";
    header("Location: kasir.php");
    exit();
}

$change = $payment - $total;
$user_id = $_SESSION['id_user'];

// simpan transaksi
$stmt = $conn->prepare("INSERT INTO transaksi (tanggal, total, bayar, kembalian, user_id) VALUES (NOW(), ?, ?, ?, ?)");
$stmt->bind_param("dddi", $total, $payment, $change, $user_id);

if ($stmt->execute()) {

    $stmt->close();

    // simpan data struk
    $_SESSION['last_transaction'] = array(
        'items'      => $cart,
        'subtotal'   => $subtotal,
        'discount'   => $discount,
        'potongan'   => $potongan,
        'total'      => $total,
        'payment'    => $payment,
        'change'     => $change,
        'timestamp'  => date('Y-m-d H:i:s')
    );

    // kosongkan cart
    $_SESSION['cart'] = array();

    // redirect receipt
    header("Location: receipt.php");
    exit();

} else {

    $stmt->close();

    $_SESSION['checkout_error'] = "Gagal menyimpan transaksi!";
    header("Location: kasir.php");
    exit();
}
?>