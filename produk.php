<?php
include 'koneksi.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Ambil ID produk dari URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitasi angka
} else {
    echo "ID produk tidak ditemukan di URL.";
    exit;
}

// Proses tambah ke keranjang
if (isset($_POST['masukkan'])) {
    if (!$user_id) {
        // Jika belum login, redirect
        header('Location: loginregister.php');
        exit;
    }

    // Ambil data dari form
    $produk_id = mysqli_real_escape_string($koneksi, $_POST['produk_id']);
    $nama_buku = mysqli_real_escape_string($koneksi, $_POST['nama_buku']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $gambar = mysqli_real_escape_string($koneksi, $_POST['gambar']);
    $jumlah = intval($_POST['jumlah']); // pastikan integer

    // Cek apakah produk sudah di keranjang
    $check = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE id = '$user_id' AND id = '$produk_id'");

    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Sudah ditambahkan ke keranjang!');</script>";
    } else {
        mysqli_query($koneksi, "INSERT INTO keranjang (id, produk_id, nama_buku, harga, jumlah, gambar) 
            VALUES ('$user_id', '$produk_id', '$nama_buku', '$harga', '$jumlah', '$gambar')") 
            or die(mysqli_error($koneksi));
        echo "<script>alert('Produk ditambahkan ke keranjang!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Produk</title>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    
   <section class="products">
   <h1 class="title">Cerita Terbaik Untuk Kamu</h1>

   <div class="box-container">

      <?php  
         $select_products = mysqli_query($koneksi, "SELECT * FROM `produk` LIMIT 8") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
      <form action="" method="post" class="box" style="">
         <a href="detail_produk.php?id=<?php echo $fetch_products['id']; ?>">
            <img class="gambar" style="height: 180px; width: 100%;" src="img/<?php echo $fetch_products['gambar']; ?>" alt="">
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

   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="shop.php" class="option-btn">load more</a>
   </div>
</section>


   <?php include 'footer.php'; ?>
</body>
</html>
