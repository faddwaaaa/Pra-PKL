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

<div class="detail-container">
  <h3>DETAIL PRODUK</h3>

  <div class="product-detail">
  <?php
      $select = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = $id") or die(mysqli_error($koneksi));
      if(mysqli_num_rows($select) > 0){
         $produk = mysqli_fetch_assoc($select);
  ?>
    <div class="gambar_produk">
      <img class="gambar_produk" src="img/<?php echo $produk['gambar']; ?>" alt="">
    </div>
    <div class="info">
      <h3><?php echo $produk['nama_buku']; ?></h3>
      <p class="nama_pengarang"><?php echo $produk['nama_pengarang']; ?></p>
      <p class="harga">Rp. <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
      <p class="deskripsi"><?php echo $produk['deskripsi']; ?> </p>

      <form action="" method="post">
        <input type="hidden" name="produk_id" value="<?php echo $produk['id']; ?>">
        <input type="hidden" name="nama_buku" value="<?php echo $produk['nama_buku']; ?>">
        <input type="hidden" name="harga" value="<?php echo $produk['harga']; ?>">
        <input type="hidden" name="gambar" value="<?php echo $produk['gambar']; ?>">
        <input type="number" name="jumlah" value="1" min="1" class="qty-input">
        <button type="submit" name="checkout" class="btn scheckout">Checkout</button>
        <button type="submit" name="masukkan" class="btn keranjang">Masukkan keranjang</button>
      </form>
    </div>
  <?php  }  else { ?>
    <p class="empty">Produk tidak ditemukan!</p>
  <?php } ?>
  </div>
</div>

<section class="products">
  
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



</section>

   <?php include 'footer.php'; ?>
</body>
</html>
