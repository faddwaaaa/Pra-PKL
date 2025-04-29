<?php
include "koneksi.php";

$nama = $_POST['nama'];
$email = $_POST['email'];
$password = md5($_POST['password']); // Pakai MD5 (bisa diganti bcrypt nanti)
$user_type     = $_POST['user_type'];

// Cek nama sudah ada
$check = mysqli_query($koneksi, "SELECT * FROM user WHERE nama='$nama'");
if (mysqli_num_rows($check) > 0) {
  echo "<script>alert('Nama sudah digunakan!'); window.location.href='loginregister.php';</script>";
} else {
  $query = mysqli_query($koneksi, "INSERT INTO user (nama, email, password, user_type) VALUES ('$nama', '$email', '$password', '$user_type')");

  if ($query) {
    echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='loginregister.php';</script>";
  } else {
     echo "<script>alert('Registrasi gagal!'); window.location.href='loginregister.php';</script>";
  }
}
?>
