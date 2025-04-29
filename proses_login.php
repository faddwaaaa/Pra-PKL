<?php
include "koneksi.php";

$nama = $_POST['nama'];
$password = md5($_POST['password']);

$query = mysqli_query($koneksi, "SELECT * FROM user WHERE nama='$nama' AND password='$password'");

if (mysqli_num_rows($query) == 1) {
   $user = mysqli_fetch_assoc($query);
   session_start();
   $_SESSION['nama'] = $user['nama'];
   $_SESSION['user_type'] = $user['user_type'];

   if ($user['user_type'] == 'admin') {
       header("Location: admin_dashboard.php");
   } else {
       header("Location: home.php");
    }
} else {
    echo "<script>alert('Login gagal! Periksa username atau password.'); window.location.href='loginregister.php';</script>";
}
?>
