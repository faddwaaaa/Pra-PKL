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

<div class="heading">
   <h3>shop</h3>
   <p> <a href="home.php">home</a> / shop </p>
</div>

    <section class="products" >
   <h1 class="title">Temukan Cerita Baru</h1>
   <div class="box-container">
      <?php  
         $select_products = mysqli_query($koneksi, "SELECT * FROM `produk` ") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
      <form action="" method="post" class="box">
         <a href="detail_produk.php?id=<?php echo $fetch_products['id']; ?>">
            <img class="gambar" src="img/<?php echo $fetch_products['gambar']; ?>" alt="">
         </a>
         <div class="nama_buku"><?php echo $fetch_products['nama_buku']; ?></div>
         <div class="nama_pengarang"><?php echo $fetch_products['nama_pengarang']; ?></div>
         <div class="harga">Rp.<?php echo $fetch_products['harga']; ?></div>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">Tidak ada produk yang ditambahkan!</p>';
         }
      ?>
   </div>

</section>



<?php include 'footer.php'; ?>
</body>
</html>