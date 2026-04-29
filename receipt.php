<?php
session_start();
include "koneksi.php";

// cek session
if (
    !isset($_SESSION['id_user']) ||
    $_SESSION['role'] != 'kasir' ||
    !isset($_SESSION['last_transaction'])
) {
    header("Location: login.php");
    exit();
}

$trans = $_SESSION['last_transaction'];

/*
data dari proses_checkout.php:
subtotal
discount
potongan
total
payment
change
timestamp
items
*/

$subtotal   = isset($trans['subtotal']) ? $trans['subtotal'] : 0;
$diskon     = isset($trans['discount']) ? $trans['discount'] : 0;
$potongan   = isset($trans['potongan']) ? $trans['potongan'] : 0;
$grandtotal = isset($trans['total']) ? $trans['total'] : 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt Kasir</title>

<style>
body{
    font-family: Courier New, monospace;
    background:#f5f5f5;
    padding:20px;
}
.box{
    max-width:400px;
    margin:auto;
    background:white;
    padding:25px;
    border:1px solid #ddd;
}
.center{text-align:center;}
.line{
    display:flex;
    justify-content:space-between;
    margin-bottom:8px;
    font-size:12px;
}
.bold{font-weight:bold;}
hr{
    border:0;
    border-top:1px dashed #999;
    margin:12px 0;
}
.btn{
    margin-top:20px;
    width:100%;
    padding:10px;
    background:#333;
    color:white;
    border:none;
    cursor:pointer;
}
@media print{
    .btn{display:none;}
    body{background:white;padding:0;}
    .box{border:none;}
}
</style>
</head>

<body>

<div class="box">

<div class="center">
    <h3>KASIR MINI</h3>
    <small><?php echo date('d/m/Y H:i:s', strtotime($trans['timestamp'])); ?></small>
</div>

<hr>

<?php
foreach($trans['items'] as $item){
    echo '
    <div class="line">
        <span>'.htmlspecialchars($item['name']).' ('.$item['qty'].'x)</span>
        <span>Rp '.number_format($item['subtotal'],0,',','.').'</span>
    </div>
    ';
}
?>

<hr>

<div class="line">
    <span>Subtotal</span>
    <span>Rp <?php echo number_format($subtotal,0,",","."); ?></span>
</div>

<div class="line">
    <span>Diskon (<?php echo $diskon; ?>%)</span>
    <span>- Rp <?php echo number_format($potongan,0,",","."); ?></span>
</div>

<div class="line bold">
    <span>Total</span>
    <span>Rp <?php echo number_format($grandtotal,0,",","."); ?></span>
</div>

<hr>

<div class="line">
    <span>Bayar</span>
    <span>Rp <?php echo number_format($trans['payment'],0,",","."); ?></span>
</div>

<div class="line bold">
    <span>Kembalian</span>
    <span>Rp <?php echo number_format($trans['change'],0,",","."); ?></span>
</div>

<hr>

<div class="center">
    Terima kasih<br>
    Selamat berbelanja kembali
</div>

<button class="btn" onclick="window.location.href='kasir.php'">
KEMBALI
</button>

</div>

</body>
</html>