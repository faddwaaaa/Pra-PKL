<?php

include 'koneksi.php';

session_start();
if(isset($_POST['send'])){
   if(!isset($_SESSION['user_id'])){
      header('Location: loginregister.php');
      exit;
   } else {
      $user_id = $_SESSION['user_id'];
      $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
      $email = mysqli_real_escape_string($koneksi, $_POST['email']);
      $user_message = mysqli_real_escape_string($koneksi, $_POST['message']);

      $select_message = mysqli_query($koneksi, "SELECT * FROM `message` WHERE nama = '$nama' AND email = '$email' AND message = '$user_message'") or die('query failed');

      if(mysqli_num_rows($select_message) > 0){
         $messages[] = 'Pesan sudah terkirim!';
      } else {
         mysqli_query($koneksi, "INSERT INTO `message`(user_id, nama, email, message) VALUES('$user_id', '$nama', '$email', '$user_message')") or die('query failed');
         $messages[] = 'Pesan berhasil terkirim!';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>message us</h3>
   <p> <a href="home.php">home</a> / messages </p>
</div>

<section class="contact">

   <form action="" method="post">
      <h3>Apa yang Ingin Kamu Sampaikan?</h3>
      <input type="text" name="nama" required placeholder="masukkan nama" class="box">
      <input type="email" name="email" required placeholder="masukkan email" class="box">
      <textarea name="message" class="box" placeholder="masukkan pesan" id="" cols="30" rows="10"></textarea>
      <input type="submit" value="kirim pesan" name="send" class="btn">
      
   </form>

</section>








<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>