<?php
session_start();
include "koneksi.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : "";
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";

    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        // Gunakan prepared statement
        $stmt = $conn->prepare("SELECT id_user, username, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Cek password (plain text untuk sekarang, bisa pakai password_hash kemudian)
            $stmt2 = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt2->bind_param("ss", $username, $password);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            if ($result2->num_rows > 0) {
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                if ($user['role'] == 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: kasir.php");
                }
                exit();
            } else {
                $error = "Password salah!";
            }
            $stmt2->close();
        } else {
            $error = "Username tidak ditemukan!";
        }
        $stmt->close();
    }
}

// Jika ada error, redirect ke login
if (!empty($error)) {
    $_SESSION['login_error'] = $error;
    header("Location: login.php");
    exit();
}
?>
