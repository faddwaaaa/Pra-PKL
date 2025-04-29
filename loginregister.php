<?php
session_start();
include 'koneksi.php';


// Proses Registrasi
if (isset($_POST['submit_register'])) {

   $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
   $email = mysqli_real_escape_string($koneksi, $_POST['email']);
   $password = mysqli_real_escape_string($koneksi, md5($_POST['password']));
   $user_type = $_POST['role'];

   $select_users = mysqli_query($koneksi, "SELECT * FROM `user` WHERE email = '$email' AND password = '$password'") or die(mysqli_error($koneksi));;

   if (mysqli_num_rows($select_users) > 0) {
      $message[] = 'Pengguna sudah ada!';
    } else {
         mysqli_query($koneksi, "INSERT INTO `user`(nama, email, password, user_type) VALUES('$nama', '$email', '$password', '$user_type')") or die(mysqli_error($koneksi));;
         $message[] = 'Berhasil Terdaftar!';
         header('location:loginregister.php');
      }
   }


// Proses Login
if (isset($_POST['submit_login'])) {

   $email = mysqli_real_escape_string($koneksi, $_POST['email']);
   $password = mysqli_real_escape_string($koneksi, md5($_POST['password']));

   $select_users = mysqli_query($koneksi, "SELECT * FROM `user` WHERE email = '$email' AND password = '$password'") or die(mysqli_error($koneksi));;

   if (mysqli_num_rows($select_users) > 0) {

      $row = mysqli_fetch_assoc($select_users);
      if($row['user_type'] == 'admin'){

        $_SESSION['admin_name'] = $row['nama'];
        $_SESSION['admin_email'] = $row['email'];
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['user_type'] = 'admin';
        header('location:admin_dashboard.php');
        exit();

     }else if($row['user_type'] == 'user'){

        $_SESSION['user_name'] = $row['nama'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_type'] = 'user';
        header('location:home.php');
        exit();

     }
   } else {
      $message[] = 'Email atau kata sandi salah!';
   }

}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script
      src="https://kit.fontawesome.com/64d58efce2.js"
      crossorigin="anonymous"
    ></script>
    <link rel="stylesheet" href="css/login.css" />
    <title>Register & login</title>
  </head>
  <body>

  <?php
// Menampilkan pesan jika ada
if (isset($message)) {
   foreach ($message as $msg) {
      echo "<p>$msg</p>";
   }
}
?>

    <div class="container">
      <div class="forms-container">
        <div class="signin-signup">

          <form action="" method="POST" class="sign-in-form">
            <h2 class="title">Login</h2>
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="text" name="email" placeholder="Email" required/>
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" placeholder="Password" required/>
            </div>
            <input type="submit" name="submit_login" value="Login" class="btn solid">
          </form>

          <form action="" method="POST" class="sign-up-form">
            <h2 class="title">Register</h2>
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="text" name="nama" placeholder="Username" required/>
            </div>
            <div class="input-field">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" placeholder="Email" required/>
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" placeholder="Password" required/>
            </div>
    
            <div class="input-box">
              <select name="role" class="custom-select" required>
              <option value="user" selected>User</option>
              <option value="admin">Admin</option>
              </select>
            </div>
            <input type="submit" name="submit_register" class="btn" value="Simpan" />
          </form>
        </div>
      </div>

      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>Belum punya akun ?</h3>
            <p>
            Dengan bergabung bersama kami, Anda tidak hanya mendapatkan akun biasa, tetapi sebuah kunci menuju kesempatan tanpa batas. kilik register dan buktikan bahwa anda adalah salah satu yang terpilih !
            </p>
            <button class="btn transparent" id="sign-up-btn">
              Register
            </button>
          </div>
          <img src="img/login.svg" class="image" alt="" />
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>Hai kamu kembali ?</h3>
            <p>
            Kami sudah menunggumu!  Login sekarang dan akses kembali semua hal keren yang sudah kamu mulai. masuk dan nikmati perjalananmu membuka jendela dunia
            </p>
            <button class="btn transparent" id="sign-in-btn">
              Login
            </button>
          </div>
          <img src="img/register.svg" class="image" alt="" />
        </div>
      </div>
    </div>

    <script src="js/login.js"></script>
  </body>
</html>