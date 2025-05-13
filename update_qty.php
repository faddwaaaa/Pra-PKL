<?php
include 'koneksi.php';
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
   http_response_code(401); // Unauthorized
   echo 'Unauthorized';
   exit;
}

// Ambil data dari POST
if (isset($_POST['id']) && isset($_POST['jumlah'])) {
   $cart_id = mysqli_real_escape_string($koneksi, $_POST['id']);
   $jumlah = intval($_POST['jumlah']);

   if ($jumlah > 0) {
      // Update jumlah item di keranjang
      $query = "UPDATE keranjang SET jumlah = '$jumlah' WHERE id = '$cart_id'";
      if (mysqli_query($koneksi, $query)) {
         echo 'Berhasil update jumlah';
      } else {
         http_response_code(500);
         echo 'Gagal update';
      }
   } else {
      // Jika jumlah 0 atau kurang, hapus item dari keranjang
      $query = "DELETE FROM keranjang WHERE id = '$cart_id'";
      if (mysqli_query($koneksi, $query)) {
         echo 'Item dihapus karena jumlah 0';
      } else {
         http_response_code(500);
         echo 'Gagal menghapus item';
      }
   }
} else {
   http_response_code(400); // Bad Request
   echo 'Data tidak lengkap';
}
?>
