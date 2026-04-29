<?php
// Koneksi database dengan error handling yang lebih baik
$conn = new mysqli("localhost", "root", "", "mini_kasir");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");
?>
