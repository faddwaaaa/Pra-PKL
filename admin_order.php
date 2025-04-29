<?php

include 'koneksi.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:loginregister.php');
}

if(isset($_POST['update_order'])){

   $order_update_id = $_POST['order_id'];
   $update_status = $_POST['update_status'];
   mysqli_query($koneksi, "UPDATE `orders` SET status = '$update_status' WHERE id = '$order_update_id'") or die('query failed');
   $message[] = 'Status pengiriman telah diperbarui!';

}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($koneksi, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_order.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="orders">

   <h1 class="title">Pesanan</h1>

   <div class="box-container">
      <?php
      $select_orders = mysqli_query($koneksi, "SELECT * FROM `orders`") or die('query failed');
      if(mysqli_num_rows($select_orders) > 0){
         while($fetch_orders = mysqli_fetch_assoc($select_orders)){
      ?>
      <div class="box">
         <p> User Id : <span><?php echo $fetch_orders['user_id']; ?></span> </p>
         <p> Tanggal Pengiriman : <span><?php echo $fetch_orders['tanggal']; ?></span> </p>
         <p> Nama : <span><?php echo $fetch_orders['nama']; ?></span> </p>
         <p> Nomor Telepon : <span><?php echo $fetch_orders['number']; ?></span> </p>
         <p> Email : <span><?php echo $fetch_orders['email']; ?></span> </p>
         <p> Alamat : <span><?php echo $fetch_orders['alamat']; ?></span> </p>
         <p> Total Produk : <span><?php echo $fetch_orders['total_produk']; ?></span> </p>
         <p> Total Harga : <span>$<?php echo $fetch_orders['total_harga']; ?>/-</span> </p>
         <p> Metode Pembayaran : <span><?php echo $fetch_orders['metode']; ?></span> </p>
         <form action="" method="post">
            <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
            <select name="update_payment">
               <option value="" selected disabled><?php echo $fetch_orders['payment_status']; ?></option>
               <option value="pending">Pending</option>
               <option value="Selesai">Selesai</option>
            </select>
            <input type="submit" value="update" name="update_order" class="option-btn">
            <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>" onclick="return confirm('Hapus pesanan ini?');" class="delete-btn">Hapus</a>
         </form>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Belum ada pesanan yang dilakukan!</p>';
      }
      ?>
   </div>

</section>




<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>