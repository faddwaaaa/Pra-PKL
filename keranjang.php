<?php

include 'koneksi.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:loginregister.php');
}

if(isset($_POST['update_cart'])){
   $cart_id = $_POST['id'];
   $cart_quantity = $_POST['jumlah'];
   mysqli_query($koneksi, "UPDATE `keranjang` SET jumlah = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
   $message[] = 'Keranjang diperbarui!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($koneksi, "DELETE FROM `keranjang` WHERE id = '$delete_id'") or die('query failed');
   header('location:keranjang.php');
}

if(isset($_GET['delete_all'])){
   mysqli_query($koneksi, "DELETE FROM `keranjang` WHERE user_id = '$user_id'") or die('query failed');
   header('location:keranjang.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Keranjang</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Keranjang</h3>
   <p> <a href="home.php">home</a> / keranjang </p>
</div>

<section class="shopping-cart">

   <h1 class="title">Produk ditambahkan</h1>

   <div class="box-container">
      <?php
         $grand_total = 0;
         $select_cart = mysqli_query($koneksi, "SELECT keranjang.*, produk.nama_buku, produk.harga, produk.gambar FROM keranjang JOIN produk ON keranjang.produk_id = produk.id WHERE keranjang.user_id = '$user_id'") or die('query failed');
         if(mysqli_num_rows($select_cart) > 0){
            while($fetch_cart = mysqli_fetch_assoc($select_cart)){   
      ?>
      <div class="box">
         <a href="keranjang.php?delete=<?php echo $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('Hapus ini dari keranjang?');"></a>
         <img src="img/<?php echo $fetch_cart['gambar']; ?>" alt="">
         <div class="nama_buku" style="font-size: 2.3rem;"><?php echo $fetch_cart['nama_buku']; ?></div>
         <div class="harga">Rp.<?php echo $fetch_cart['harga']; ?></div>

         <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $fetch_cart['id']; ?>">
            <!-- <input type="number" min="1" name="jumlah" value=""> -->
            <div class="quantity-control" data-id="<?php echo $fetch_cart['id']; ?>" data-harga="<?php echo $fetch_cart['harga']; ?>">    
               <button style="padding: 6px 15px;
                  font-size: 18px;
                  background-color: #8B5E3C;
                  color: white;
                  border: none;
                  border-radius: 6px;
                  cursor: pointer;"class="minus">-</button>
               <span style="font-size: 18px;
                  min-width: 30px;
                  text-align: center;"class="quantity"><?php echo $fetch_cart['jumlah']; ?></span>
               <button style="  padding: 6px 15px;
                  font-size: 18px;
                  background-color: #8B5E3C;
                  color: white;
                  border: none;
                  border-radius: 6px;
                  cursor: pointer;"class="plus">+</button>
            </div>
         </form>
         <div class="sub-total"> sub total : <span>Rp.<?php echo $sub_total = ($fetch_cart['jumlah'] * $fetch_cart['harga']); ?></span> </div>
      </div>
      <?php
      $grand_total += $sub_total;
         }
      }else{
         echo '<p class="empty">Keranjang kamu kosong</p>';
      }
      ?>
   </div>

   <div style="margin-top: 2rem; text-align:center;">
      <a href="keranjang.php?delete_all" class="delete-btn <?php echo ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('Hapus semua dari keranjang?');">Hapus semua</a>
   </div>

   <div class="cart-total">
   <p>Jumlah Total : <span id="grand-total">Rp.<?php echo $grand_total; ?></span></p>
      <div class="flex">
         <a href="shop.php" class="option-btn">Lanjutkan Belanja</a>
         <a href="checkout.php" class="option-btn <?php echo ($grand_total > 1)?'':'disabled'; ?>">Checkout</a>
      </div>
   </div>

</section>








<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script>
document.querySelectorAll('.quantity-control').forEach(control => {
   const minusBtn = control.querySelector('.minus');
   const plusBtn = control.querySelector('.plus');
   const quantitySpan = control.querySelector('.quantity');
   const box = control.closest('.box');
   const subTotalSpan = box.querySelector('.sub-total span');
   const harga = parseInt(control.dataset.harga); // Harga harus berupa angka
   const cartId = control.dataset.id;

   minusBtn.addEventListener('click', (e) => {
      e.preventDefault();
      let qty = parseInt(quantitySpan.textContent);
      if (qty > 1) {
         qty--;
         updateQty(qty);
      }
   });

   plusBtn.addEventListener('click', (e) => {
      e.preventDefault();
      let qty = parseInt(quantitySpan.textContent);
      qty++;
      updateQty(qty);
   });

   function updateQty(qty) {
   quantitySpan.textContent = qty;
   const subTotal = qty * harga;
   subTotalSpan.textContent = 'Rp.' + subTotal.toLocaleString('id-ID');

   // Simpan ke database
   fetch('update_qty.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${cartId}&jumlah=${qty}`
   });

   // Update grand total
   updateGrandTotal();
}

   function updateGrandTotal() {
   let grandTotal = 0;
   document.querySelectorAll('.sub-total span').forEach(span => {
      const subTotalStr = span.textContent.replace(/[^\d]/g, '');
      grandTotal += parseInt(subTotalStr);
   });
   document.getElementById('grand-total').textContent = 'Rp.' + grandTotal.toLocaleString('id-ID');
}



});

</script>


</body>
</html>