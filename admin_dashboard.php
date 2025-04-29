<?php
include 'koneksi.php';

session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
   header('location:loginregister.php');
   exit();
}

$admin_id = $_SESSION['admin_id']; // id dari user yang role-nya 'admin'


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Panel</title>

   <!-- font awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- css -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="dashboard">

   <h1 class="title">dashboard</h1>

   <div class="box-container">

      <div class="box">
         <?php
            $total_penjualan = 0;
            $select_penjualan = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total_penjualan FROM orders WHERE status = 'selesai'") or die('query failed');
            if(mysqli_num_rows($select_penjualan) > 0){
               $fetch_total = mysqli_fetch_assoc($select_penjualan);
               $total_penjualan = $fetch_total['total_penjualan']??0;
            }
            ?>
         <h3>Rp.<?php echo $total_penjualan; ?></h3>
         <p>Total Penjualan</p>
      </div>

      <div class="box">
      <?php
         $total_pesanan = 0;
         $select_pesanan = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total_pesanan FROM orders WHERE status IN ('diproses', 'dikirim')") or die('query failed');
         if(mysqli_num_rows($select_pesanan) > 0){
            $fetch_pesanan = mysqli_fetch_assoc($select_pesanan);
            $total_pesanan = $fetch_pesanan['total_pesanan'] ?? 0;
         }
      ?>
         <h3><?php echo $total_pesanan; ?></h3>
         <a href="admin_order.php"><p>Pesanan Dikirim/Diproses</p></a>
      </div>

      <div class="box">
         <?php 
            $select_orders = mysqli_query($koneksi, "SELECT * FROM `orders`") or die('query failed');
            $number_of_orders = mysqli_num_rows($select_orders);
         ?>
         <h3><?php echo $number_of_orders; ?></h3>
         <a href="admin_order.php"><p>Pesanan Masuk</p></a>
      </div>

      <div class="box">
         <?php 
            $select_products = mysqli_query($koneksi, "SELECT * FROM `produk`") or die('query failed');
            $number_of_products = mysqli_num_rows($select_products);
         ?>
         <h3><?php echo $number_of_products; ?></h3>
         <a href="admin_produk.php"><p>Produk Ditambahkan</p></a>
      </div>

      <div class="box">
         <?php 
            $select_users = mysqli_query($koneksi, "SELECT * FROM `user` WHERE user_type = 'user'") or die('query failed');
            $number_of_users = mysqli_num_rows($select_users);
         ?>
         <h3><?php echo $number_of_users; ?></h3>
         <a href="admin_users.php"><p>Normal Users</p></a>
      </div>

      <div class="box">
         <?php 
            $select_admins = mysqli_query($koneksi, "SELECT * FROM `user` WHERE user_type = 'admin'") or die('query failed');
            $number_of_admins = mysqli_num_rows($select_admins);
         ?>
         <h3><?php echo $number_of_admins; ?></h3>
         <a href="admin_users.php"><p>Admin Users</p></a>
      </div>

      <div class="box">
         <?php 
            $select_account = mysqli_query($koneksi, "SELECT * FROM `user`") or die('query failed');
            $number_of_account = mysqli_num_rows($select_account);
         ?>
         <h3><?php echo $number_of_account; ?></h3>
         <a href="admin_users.php"><p>Total Akun</p></a>
      </div>

      <div class="box">
         <?php 
            $select_messages = mysqli_query($koneksi, "SELECT * FROM `message`") or die('query failed');
            $number_of_messages = mysqli_num_rows($select_messages);
         ?>
         <h3><?php echo $number_of_messages; ?></h3>
         <a href="admin_message.php"><p>Pesan Baru</p></a>
      </div>

   </div>

</section>

<!-- admin dashboard section ends -->

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>