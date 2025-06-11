<?php
include 'koneksi.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Ambil ID produk dari URL
if (!isset($_GET['id'])) {
    echo "ID produk tidak ditemukan di URL.";
    exit;
}
$id = intval($_GET['id']);

// Proses Checkout
if (isset($_POST['checkout'])) {
    if (!$user_id) {
        header('Location: loginregister.php');
        exit;
    }

    $produk_id = intval($_POST['produk_id']);
    $jumlah = intval($_POST['jumlah']);

    // Cek apakah produk sudah ada di keranjang
    $check = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE user_id = '$user_id' AND produk_id = '$produk_id'");
    if (mysqli_num_rows($check) == 0) {
        // Jika belum ada, masukkan ke keranjang
        $insert = mysqli_query($koneksi, "INSERT INTO keranjang (user_id, produk_id, jumlah) VALUES ('$user_id', '$produk_id', '$jumlah')");
        if (!$insert) {
            echo "<script>alert('Gagal menambahkan ke keranjang: " . mysqli_error($koneksi) . "');</script>";
            exit;
        }
    } else {
        // Jika sudah ada, update jumlah
        $update = mysqli_query($koneksi, "UPDATE keranjang SET jumlah = jumlah + $jumlah WHERE user_id = '$user_id' AND produk_id = '$produk_id'");
    }

    // Redirect ke halaman checkout
    header("Location: checkout.php?source=single&produk_id=$produk_id&jumlah=$jumlah");
    exit;
}


// Proses tambah ke keranjang
if (isset($_POST['masukkan'])) {
    if (!$user_id) {
        header('Location: loginregister.php');
        exit;
    }

    $produk_id = intval($_POST['produk_id']);
    $jumlah = intval($_POST['jumlah']);

    $check = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE user_id = '$user_id' AND produk_id = '$produk_id'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Produk sudah ada di keranjang!');</script>";
    } else {
        $insert = mysqli_query($koneksi, "INSERT INTO keranjang (user_id, produk_id, jumlah) VALUES ('$user_id', '$produk_id', '$jumlah')");
        if ($insert) {
            echo "<script>alert('Produk ditambahkan ke keranjang!');</script>";
        } else {
            echo "<script>alert('Gagal menambahkan ke keranjang: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Produk</title>
  <link rel="stylesheet" href="./css/style.css">
  <style>
   .btn {
  padding: 6px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: 0.3s;
  font-family: inherit;
  color: #fff;
  background-color: #a87c52;
  box-shadow: none;
  filter: none;
  opacity: 1;
  text-shadow: none;
}

 .scheckout,
 .keranjang {
   background-color: #a87c52;
   color: #fff;
 }

.scheckout:hover,
.keranjang:hover {
  background-color: #cfcac0;
  color: #000;
}
  </style>
</head>
<body>


<?php include 'header.php'; ?>

<div class="heading">
   <h3>Detail Produk</h3>
   <p> <a href="home.php">home</a> / detail</p> 
</div>



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
      <h3 style = "font-size: 3rem;"><?php echo $produk['nama_buku']; ?></h3>
      <p class="nama_pengarang"><?php echo $produk['nama_pengarang']; ?></p>
      <p class="harga">Rp. <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
      <p class="stok" style = "font-size: 1.3rem;">Stok : <?php echo $produk['stok']; ?></p>
      <p class="deskripsi" style = "font-size: 1.5rem;"><?php echo $produk['deskripsi']; ?> </p>

      <?php if ($produk['stok'] > 0): ?>
  <form action="" method="post">
    <input type="hidden" name="produk_id" value="<?php echo $produk['id']; ?>">
    <input type="hidden" name="nama_buku" value="<?php echo $produk['nama_buku']; ?>">
    <input type="hidden" name="harga" value="<?php echo $produk['harga']; ?>">
    <input type="hidden" name="stok" value="<?php echo $produk['stok']; ?>">
    <input type="hidden" name="gambar" value="<?php echo $produk['gambar']; ?>">
    <input type="number" name="jumlah" value="1" min="1" max="<?php echo $produk['stok']; ?>" class="qty-input" style="margin-top:1.3rem;">
    <button type="submit" name="checkout" class="btn scheckout">Beli Sekarang</button>
    <button type="submit" name="masukkan" class="btn keranjang">Masukkan keranjang</button>
  </form>
<?php else: ?>
  <p style="color: red; font-weight: bold;">Stok habis. Tidak dapat membeli saat ini.</p>
<?php endif; ?>



    </div>
  <?php  }  else { ?>
    <p class="empty">Produk tidak ditemukan!</p>
  <?php } ?>
  </div>
</div>

<section class="products">
   <h1 class="title" style="font-size: 2.5rem;">Cerita Terbaik Untuk Kamu</h1>
   <div class="box-container" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; justify-items: center;">

      <?php  
         $select_products = mysqli_query($koneksi, "SELECT * FROM `produk` LIMIT 8") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
      <form action="" method="post" class="box" style="border: 1px solid #eee; padding: 10px; position: relative; border-radius: 8px; width: fit-content; display: flex; flex-direction: column; align-items: center;">
      <a href="detail_produk.php?id=<?php echo $fetch_products['id']; ?>">
      <img class="gambar" style="height: auto; width: 150px;" src="img/<?php echo $fetch_products['gambar']; ?>" alt="">
      </a>
      <div class="nama_buku" style="font-size: 1.8rem; text-align: center; margin: 0;"><?php echo $fetch_products['nama_buku']; ?></div>
      <div class="nama_pengarang" style="font-size: 1.5rem; text-align: center; margin: 0;"><?php echo $fetch_products['nama_pengarang']; ?></div>
      <div class="harga" style="padding: 4px 8px; border-radius: 4px; font-size: 1.5rem; background: red; color: white; position: absolute; top: 8px; left: 8px;">Rp.<?php echo $fetch_products['harga']; ?></div>
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

