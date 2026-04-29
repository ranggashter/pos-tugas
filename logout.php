<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .logout-box {
            background: white;
            padding: 40px;
            border-radius: 4px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        .message { font-size: 24px; margin-bottom: 15px; }
        h2 { font-size: 20px; color: #333; margin-bottom: 15px; }
        p { color: #666; font-size: 13px; line-height: 1.6; margin-bottom: 10px; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            transition: background 0.2s;
        }
        .btn:hover { background: #555; }
    </style>
</head>
<body>
    <div class="logout-box">
        <div class="message">✓</div>
        <h2>Logout Berhasil</h2>
        <p>Anda telah berhasil keluar dari sistem.</p>
        <p>Terima kasih telah menggunakan aplikasi Kasir Mini.</p>
        <a href="login.php" class="btn">← Kembali ke Login</a>
    </div>
</body>
</html>
