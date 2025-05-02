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

   <?php
   $select_orders = mysqli_query($koneksi, "SELECT o.*, p.metode_pembayaran, p.bukti_pembayaran
      FROM orders o 
      LEFT JOIN pembayaran p ON o.id = p.orders_id 
      ORDER BY o.tanggal_pesanan DESC") or die('Query gagal');
      
   if (mysqli_num_rows($select_orders) > 0) {
      echo '<table>
            <tr>
               <th>ID Pesanan</th>
               <th>Tanggal</th>
               <th>Alamat</th>
               <th>Total Produk</th>
               <th>Total Harga</th>
               <th>Metode Pembayaran</th>
               <th>Bukti Pembayaran</th>
               <th>Status</th>
               <th>Aksi</th>
            </tr>';

      while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
         $order_id = $fetch_orders['id'];
         $produk_query = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_produk FROM detail_pesanan WHERE orders_id = '$order_id'");
         $produk = mysqli_fetch_assoc($produk_query);
         $total_produk = $produk['total_produk'] ?? 0;

         $bukti_pembayaran = $fetch_orders['bukti_pembayaran'];
         $bukti_display = $bukti_pembayaran 
            ? '<img src="uploads/' . htmlspecialchars($bukti_pembayaran) . '" alt="Bukti" class="bukti-preview">'
            : 'Belum ada';

         echo '<tr>';
         echo '<td>' . $order_id . '</td>';
         echo '<td>' . date('d-M-Y', strtotime($fetch_orders['tanggal_pesanan'])) . '</td>';
         echo '<td>' . htmlspecialchars($fetch_orders['alamat_pengiriman']) . '</td>';
         echo '<td>' . $total_produk . '</td>';
         echo '<td>Rp ' . number_format($fetch_orders['total_harga'], 0, ',', '.') . '</td>';
         echo '<td>' . htmlspecialchars($fetch_orders['metode_pembayaran'] ?? 'Tidak Diketahui') . '</td>';
         echo '<td>' . $bukti_display . '</td>';
         echo '<td>';

         echo '<form action="" method="post">';
         echo '<input type="hidden" name="order_id" value="' . $order_id . '">';
         $disabled = empty($bukti_pembayaran) ? 'disabled' : '';
         echo '<select name="update_status" onchange="this.form.submit()" ' . $disabled . '>';
         echo '<option value="pending"' . ($fetch_orders['status'] == 'pending' ? ' selected' : '') . '>Pending</option>';
         echo '<option value="dikirim"' . ($fetch_orders['status'] == 'dikirim' ? ' selected' : '') . '>Dikirim</option>';
         echo '<option value="selesai"' . ($fetch_orders['status'] == 'selesai' ? ' selected' : '') . '>Selesai</option>';
         echo '<option value="dibatalkan"' . ($fetch_orders['status'] == 'dibatalkan' ? ' selected' : '') . '>Dibatalkan</option>';
         echo '</select>';
         echo '<input type="hidden" name="update_order" value="1">';
         echo '</form>';

         if (empty($bukti_pembayaran)) {
            echo "<small style='color:red;'>Upload bukti terlebih dahulu</small>";
         }

         echo '</td>';
         echo '<td><a href="?delete=' . $order_id . '" onclick="return confirm(\'Yakin ingin menghapus pesanan ini?\')" class="delete-btn">Hapus</a></td>';
         echo '</tr>';
      }

      echo '</table>';
   } else {
      echo '<p class="empty">Belum ada pesanan yang masuk!</p>';
   }
   ?>
</section>

<script src="js/admin_script.js"></script>
</body>
</html>
