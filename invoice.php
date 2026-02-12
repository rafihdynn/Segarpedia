<?php
include '../config/db.php';
$id=$_GET['id'];


$q = mysqli_query($conn,"
SELECT p.nama_produk, p.harga, oi.qty
FROM order_items oi
JOIN produk p ON oi.produk_id=p.id
WHERE oi.order_id=$id
");
$total=0;
?>


<h2>Invoice Pesanan #<?= $id ?></h2>
<table border="1" cellpadding="10">
<tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr>
<?php while($d=mysqli_fetch_assoc($q)){
$sub = $d['harga']*$d['qty'];
$total+=$sub;
?>
<tr>
<td><?= $d['nama_produk'] ?></td>
<td>Rp <?= $d['harga'] ?></td>
<td><?= $d['qty'] ?></td>
<td>Rp <?= $sub ?></td>
</tr>
<?php } ?>
<tr><th colspan="3">Total</th><th>Rp <?= $total ?></th></tr>
</table>