<?php
include 'koneksi.php';
session_start();

// Ambil user_id dari session, tanpa memulai session di home
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (isset($_POST['add_to_cart'])) {
    // Cek apakah user sudah login sebelum menambahkan ke keranjang
    if ($user_id) {
        // Redirect ke halaman login jika belum login
        header('Location: loginregister.php');
        exit;

    } else {

      // Mengambil data dari form dengan aman
      $nama_buku = mysqli_real_escape_string($koneksi, $_POST['nama_buku']);
      $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
      $gambar = mysqli_real_escape_string($koneksi, $_POST['gambar']);
      $jumlah = (int) $_POST['jumlah'];

      // Periksa apakah produk sudah ada di keranjang
      $check_cart_numbers = mysqli_query($koneksi, "SELECT  produk.nama_buku FROM produk JOIN keranjang ON user.id = keranjang.user_id WHERE nama_buku = '$nama_buku' AND user_id = '$user_id'") or die('Query gagal');

      if (mysqli_num_rows($check_cart_numbers) > 0) {
         $message[] = 'Sudah ditambahkan ke keranjang!';
      } else {
         // Tambahkan produk ke keranjang
         mysqli_query($koneksi, "INSERT INTO `keranjang` (user_id, nama_buku, harga, jumlah, gambar) VALUES ('$user_id', '$nama_buku', '$harga', '$jumlah', '$gambar')") or die('Query gagal');
         $message[] = 'Produk ditambahkan ke keranjang!';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

     <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">    
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="home">
        <div class="content">
            <h3>JELAJAHI DUNIA BARU DI SETIAP HALAMAN</h3>
            <p>Selamat datang di <span>Booknest</span>, toko buku online yang menyediakan koleksi buku terlengkap dari berbagai genre, penulis, dan penerbit. Temukan buku-buku favorit Anda dengan mudah dan nikmati pengalaman belanja yang nyaman dengan berbagai pilihan pembayaran dan pengiriman cepat. Dari novel best-seller hingga buku akademik, kami siap memenuhi kebutuhan membaca Anda. Jelajahi sekarang dan temukan dunia baru melalui setiap halaman buku yang kami tawarkan.</p>
            <a href="about.php" class="white-btn">Jelajahi</a>
        </div>
    </section>

    <section class="products">
   <h1 class="title" style="font-size: 2.5rem;">Cerita Terbaik Untuk Kamu</h1>

   <div class="box-container" style=" grid-template-columns: repeat(4, 1fr);">

      <?php  
         $select_products = mysqli_query($koneksi, "SELECT * FROM `produk` LIMIT 8") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
      <form action="" method="post" class="box"style="display: block; border: 1px solid #eee; padding: 10px; position: relative; border-radius: 8px;">
         <a href="detail_produk.php?id=<?php echo $fetch_products['id']; ?>">
            <img class="gambar" style="height: 350px; width: 100%;" src="img/<?php echo $fetch_products['gambar']; ?>" alt="">
         </a>
         <div class="nama_buku" style="font-size: 1.8rem;"><?php echo $fetch_products['nama_buku']; ?></div>
         <div class="nama_pengarang" style="font-size: 1.5rem;"><?php echo $fetch_products['nama_pengarang']; ?></div>
         <div class="harga" style="padding: 4px 8px; border-radius: 4px; font-size: 1.8rem;">Rp.<?php echo $fetch_products['harga']; ?></div>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">Tidak ada produk yang ditambahkan!</p>';
         }
      ?>
   </div>

   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="shop.php" class="option-btn">load more</a>
   </div>
</section>


<section class="about">
   <div class="flex">
      <div class="image">
         <img src="img/about.jpeg" alt="">
      </div>

      <div class="content">
         <h3>About Us</h3>
         <p>Booknest adalah salah satu toko buku terkenal di Indonesia. Toko buku online yang menyediakan berbagai koleksi buku dari berbagai genre, penulis, dan penerbit. Kami memiliki antarmuka pengguna yang intuitif dan menyediakan berbagai pilihan pembayaran serta pengiriman cepat. Dari novel hingga buku akademik, Anda dapat menemukan berbagai pilihan bacaan di sini. Jelajahi dan temukan buku yang Anda cari.</p>
         <a href="about.php" class="btn">read more</a>
      </div>
   </div>
</section>


<section class="home-contact">
        <div class="content">
            <h3>have any questions?</h3>
            <p>Temukan inspirasimu di Booknest, Pilihan buku terlengkap dengan kemudahan berbelanja online.</p>
             <a href="messages.php" class="white-btn1">Messages us</a>
        </div>
    </section>

<?php include 'footer.php'; ?>
</body>
</html>