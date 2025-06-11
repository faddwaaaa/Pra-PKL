<?php

include 'koneksi.php';

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h2 style="font-size: 4rem;">ABOUT US</h2>
   <p> <a href="home.php">home</a> / about </p>
</div>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="img/about.jpeg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <p>Pelanggan Booknest sangat menghargai kenyamanan dan kemudahan berbelanja, terutama dengan koleksi buku yang lengkap dan terkurasi dengan baik. Mereka puas dengan layanan pelanggan yang responsif serta proses pembelian yang lancar dan ramah pengguna. Pengiriman yang cepat dan aman memastikan buku tiba dalam kondisi prima, menambah kepuasan pelanggan. Selain itu, pilihan pembayaran yang beragam dan berbagai penawaran menarik membuat mereka merasa mendapatkan nilai lebih dalam setiap transaksi.</p>
         <a href="messages.php" class="btn">Message us</a>
      </div>

   </div>

</section>

<section class="authors">

   <h1 class="title">authors</h1>
   <div class="box-container">
      <div class="box">
         <img src="img/author (tere liye).jpg" alt="">
         <h3>Tere Liye</h3>
      </div>

      <div class="box">
         <img src="img/author (ahmad fuadi).jpg" alt="">
         <h3>Ahmad Fuadi</h3>
      </div>

      <div class="box">
         <img src="img/author (dhia).jpg" alt="">
         <h3>Dhia'an Farah</h3>
      </div>

      <div class="box">
         <img src="img/author (eka kurniawan).jpg" alt="">
         <h3>Eka Kurniawan</h3>
      </div>

      <!-- <div class="box">
         <img src="img/author (hassan shadily).jpg" alt="">
         <h3>Hassan Shadily</h3>
      </div> -->

      <div class="box">
         <img src="img/author (sapardi djoko damono).jpg" alt="">
         <h3>Sapardi Djoko Damono</h3>
      </div>

      <div class="box">
         <img src="img/author (wawan kurniawan).jpg" alt="">
         <h3>Wawan Kurniawan</h3>
      </div>

      <div class="box">
         <img src="img/author(didi suwardi).jpg" alt="">
         <h3>Didi Suwardi</h3>
      </div>

      <div class="box">
         <img src="img/author(nadzira shafa).jpg" alt="">
         <h3>Nadzira Shafa</h3>
      </div>
   </div>
</section>







<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>