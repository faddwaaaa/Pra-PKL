<?php
include "koneksi.php";

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
  header('location:loginregister.php');
};

if(isset($_POST['add_product'])){

    $nama_buku= mysqli_real_escape_string($koneksi, $_POST['nama_buku']);
    $harga = $_POST['harga'];
    $nama_pengarang = $_POST['nama_pengarang'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = $_FILES['gambar']['name'];
    $image_size = $_FILES['gambar']['size'];
    $image_tmp_name = $_FILES['gambar']['tmp_name'];
    $image_folder = 'img/'.$gambar;
 
    $select_product_name = mysqli_query($koneksi, "SELECT nama_buku FROM `produk` WHERE nama_buku = '$nama_buku'") or die('query failed');
 
    if(mysqli_num_rows($select_product_name) > 0){
       $message[] = 'Nama produk sudah ditambahkan!';
    }else{
       $add_product_query = mysqli_query($koneksi, "INSERT INTO `produk`(nama_buku, harga, nama_pengarang, deskripsi, gambar) VALUES('$nama_buku', '$harga', '$nama_pengarang', '$deskripsi', '$gambar')") or die('query failed');
 
       if($add_product_query){
          if($image_size > 2000000){
             $message[] = 'Ukuran gambar terlalu besar!';
          }else{
             move_uploaded_file($image_tmp_name, $image_folder);
             $message[] = 'Produk berhasil ditambahkan!';
          }
       }else{
          $message[] = 'Produk tidak dapat ditambahkan!';
       }
    }
 }
 
 if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $delete_image_query = mysqli_query($koneksi, "SELECT gambar FROM `produk` WHERE id = '$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
    unlink('img/'.$fetch_delete_image['gambar']);
    mysqli_query($koneksi, "DELETE FROM `produk` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_produk.php');
 }
 
 if(isset($_POST['update_product'])){
 
    $update_p_id = $_POST['update_p_id'];
    $update_name = $_POST['update_name'];
    $update_price = $_POST['update_price'];
    $update_stok = $_POST['update_stok'];
    $update_nama_pengarang = $_POST['update_nama_pengarang'];
    $update_deskripsi = $_POST['update_deskripsi'];
 
    mysqli_query($koneksi, "UPDATE `produk` SET nama_buku = '$update_name', harga = '$update_price', stok = '$update_stok', nama_pengarang = '$update_nama_pengarang', deskripsi = '$update_deskripsi'  WHERE id = '$update_p_id'") or die('query failed');
 
    $update_image = $_FILES['update_image']['nama_buku'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = 'img/'.$update_image;
    $update_old_image = $_POST['update_old_image'];
 
    if(!empty($update_image)){
       if($update_image_size > 2000000){
          $message[] = 'Ukuran file terlalu besar!';
       }else{
          mysqli_query($koneksi, "UPDATE `produk` SET gambar = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
          move_uploaded_file($update_image_tmp_name, $update_folder);
          unlink('img/'.$update_old_image);
       }
    }
 
    header('location:admin_produk.php');
 
 }
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Produk</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- product CRUD section starts  -->

<section class="add-products">

   <h1 class="title">shop products</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Tambahkan Produk</h3>
      <input type="text" name="nama_buku" class="box" placeholder="masukkan judul buku" required>
      <input type="number" min="0" name="harga" class="box" placeholder="masukkan harga" required>
      <input type="number" min="0" name="stok" class="box" placeholder="masukkan stok" required>
      <input type="text" name="nama_pengarang" class="box" placeholder="masukkan nama pengarang" required>
      <input type="text" name="deskripsi" class="box" placeholder="masukkan deskripsi" required>
      <input type="file" name="gambar" accept="img/jpg, img/jpeg, img/png" class="box" required>
      <input type="submit" value="Tambah Produk" name="add_product" class="btn">
   </form>

</section>

<!-- product CRUD section ends -->

<!-- show products  -->

<section class="show-products">
   <div class="box-container">

   <?php
      $select_products = mysqli_query($koneksi, "SELECT * FROM `produk`") or die('query failed');
      if(mysqli_num_rows($select_products) > 0){
         while($fetch_products = mysqli_fetch_assoc($select_products)){
   ?>
   <div class="box">
      <img src="img/<?php echo $fetch_products['gambar']; ?>" alt="">
      
      <!-- Nama buku dan pengarang -->
      <div class="name">
         <strong><?php echo $fetch_products['nama_buku']; ?></strong> - <?php echo $fetch_products['nama_pengarang']; ?>
      </div>

      <!-- Harga -->
      <div class="price">Rp.<?php echo $fetch_products['harga']; ?></div>

       <!-- stok -->
       <div class="stok" style = "font-size: 1.3rem;">Stok : <?php echo $fetch_products['stok']; ?></div>

      <!-- Deskripsi -->
      <div class="deskripsi"><?php echo $fetch_products['deskripsi']; ?></div>

      <!-- Tombol -->
      <a href="admin_produk.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Update</a>
      <a href="admin_produk.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Hapus produk ini?');">Hapus</a>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">Belum ada produk yang ditambahkan!</p>';
      }
   ?>
   </div>
</section>


<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){
         $update_id = $_GET['update'];
         $update_query = mysqli_query($koneksi, "SELECT * FROM `produk` WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
      <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['gambar']; ?>">
      <img src="img/<?php echo $fetch_update['gambar']; ?>" alt="">
      <input type="text" name="update_name" value="<?php echo $fetch_update['nama_buku']; ?>" class="box" required placeholder="masukkan nama">

      <div class="flex-input"  style = "display: flex; gap: 1rem; justify-content: space-between;">
   <input type="number" name="update_price" value="<?php echo $fetch_update['harga']; ?>" min="0" class="box" style = "flex: 1; margin: 1rem 0;" required placeholder="masukkan harga">
   <input type="number" name="update_stok" value="<?php echo $fetch_update['stok']; ?>" min="0" class="box" style = "flex: 1; margin: 1rem 0;" required placeholder="masukkan stok">
</div>


      <input type="text" name="update_nama_pengarang" value="<?php echo $fetch_update['nama_pengarang']; ?>" class="box" required placeholder="masukkan nama_pengarang">
      <input type="text" name="update_deskripsi" value="<?php echo $fetch_update['deskripsi']; ?>" class="box" required placeholder="masukkan deskripsi">

      <input type="file" class="box" name="update_image" accept="img/jpg, img/jpeg, img/png">
      <input type="submit" value="update" name="update_product" class="btn">
      <input type="reset" value="cancel" id="close-update" class="option-btn">
   </form>
   <?php
         }
      }
      }else{
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>







<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>