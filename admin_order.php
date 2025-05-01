<?php

include 'koneksi.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:loginregister.php');
   exit;
}

// Update status pesanan
if (isset($_POST['update_order'])) {
   $order_update_id = intval($_POST['order_id']);
   $update_status = mysqli_real_escape_string($koneksi, $_POST['update_status']);
   mysqli_query($koneksi, "UPDATE `orders` SET status = '$update_status' WHERE id = '$order_update_id'") or die('Query gagal');
   $message[] = 'Status pesanan telah diperbarui!';
}

// Hapus pesanan
if (isset($_GET['delete'])) {
   $delete_id = intval($_GET['delete']);
   mysqli_query($koneksi, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('Query gagal');
   header('location:admin_order.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pesanan (Admin)</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="orders">

   <h1 class="title">Data Pesanan</h1>

   <div class="box-container">
   <?php
   $select_orders = mysqli_query($koneksi, "SELECT o.*, p.metode_pembayaran 
      FROM orders o 
      LEFT JOIN pembayaran p ON o.id = p.orders_id 
      ORDER BY o.tanggal_pesanan DESC") or die('Query gagal');

   if (mysqli_num_rows($select_orders) > 0) {
      while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {

         // Hitung total produk
         $order_id = $fetch_orders['id'];
         $produk_query = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_produk FROM detail_pesanan WHERE orders_id = '$order_id'");
         $produk = mysqli_fetch_assoc($produk_query);
         $total_produk = $produk['total_produk'] ?? 0;
   ?>
   <div class="box">
      <p> User Id : <span><?= $fetch_orders['user_id']; ?></span> </p>
      <p> Tanggal Pesanan : <span><?= date('d-M-Y', strtotime($fetch_orders['tanggal_pesanan'])); ?></span> </p>
      <p> Alamat : <span><?= htmlspecialchars($fetch_orders['alamat_pengiriman']); ?></span> </p>
      <p> Total Produk : <span><?= $total_produk ?></span> </p>
      <p> Total Harga : <span>Rp.<?= number_format($fetch_orders['total_harga'], 0, ',', '.'); ?></span> </p>
      <p> Metode Pembayaran : <span><?= $fetch_orders['metode_pembayaran'] ?? 'Tidak Diketahui'; ?></span> </p>
      <p> Status Pesanan : <span><?= ucfirst($fetch_orders['status']); ?></span> </p>

      <form action="" method="post">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <select name="update_status" required>
            <option value="" disabled selected>Pilih Status</option>
            <option value="pending" <?= ($fetch_orders['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="diproses" <?= ($fetch_orders['status'] == 'diproses') ? 'selected' : '' ?>>Diproses</option>
            <option value="dikirim" <?= ($fetch_orders['status'] == 'dikirim') ? 'selected' : '' ?>>Dikirim</option>
            <option value="selesai" <?= ($fetch_orders['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
            <option value="dibatalkan" <?= ($fetch_orders['status'] == 'dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
         </select>
         <input type="submit" value="Update Status" name="update_order" class="option-btn">
         <a href="admin_order.php?delete=<?= $fetch_orders['id']; ?>" onclick="return confirm('Hapus pesanan ini?');" class="delete-btn">Hapus</a>
      </form>
   </div>
   <?php
      }
   } else {
      echo '<p class="empty">Belum ada pesanan yang masuk!</p>';
   }
   ?>
   </div>

</section>

<script src="js/admin_script.js"></script>
</body>
</html>
