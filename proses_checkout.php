<?php
session_start();
include 'koneksi.php';

// echo $_POST['produk_id'];
// echo $_POST['harga'];

if (isset($_POST['checkout'])) {
    $user_id = $_SESSION['user_id'];
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $metode = $_POST['payment_method'];
    $tanggal = date('Y-m-d');
    $ongkir = 10000;

    // Validasi file upload
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $namaFile = $_FILES['proof']['name'];
        $tmpName = $_FILES['proof']['tmp_name'];
        $ext = pathinfo($namaFile, PATHINFO_EXTENSION);
        $namaBaru = uniqid() . '.' . $ext;
        $uploadPath = 'bukti/' . $namaBaru;

        if (!is_dir('proof')) {
            mkdir('proof');
        }

        move_uploaded_file($tmpName, $uploadPath);
    } else {
        echo "Upload bukti pembayaran gagal!";
        exit;
    }

    // if (isset($_POST['checkout'])){
    //     $produk_id = $_POST['id'];
    //     $query = mysqli_query($koneksi, "SELECT * FROM produk where id = '$produk_id'");
    //     // $sql = "SELECT * FROM produk where id = '$produk_id'";
    //     // $query = mysqli_query($koneksi, $sql);
    //     // $harga = mysqli_fetch_assoc($query['harga']);
    // } else {
    // Ambil data keranjang
    $query = mysqli_query($koneksi, "SELECT k.*, p.harga FROM keranjang k JOIN produk p ON k.produk_id = p.id WHERE k.user_id = '$user_id'");
    $total = $ongkir;
    $keranjang = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $subtotal = $row['harga'] * $row['jumlah'];
        $total += $subtotal;
        $keranjang[] = $row;
    }
    // Insert ke tabel orders
    mysqli_query($koneksi, "INSERT INTO orders (user_id, alamat_pengiriman, total_harga, tanggal_pesanan, status) 
                         VALUES ('$user_id', '$alamat', '$total', '$tanggal', 'pending')");
    $order_id = mysqli_insert_id($koneksi);

    // Insert ke detail_pesanan
    foreach ($keranjang as $item) {
        $produk_id = $item['produk_id'];
        $jumlah = $item['jumlah'];
        mysqli_query($koneksi, "INSERT INTO detail_pesanan (orders_id, produk_id, jumlah) 
                             VALUES ('$order_id', '$produk_id', '$jumlah')");
    }

    // Insert ke pembayaran
    mysqli_query($koneksi, "INSERT INTO pembayaran (orders_id, metode_pembayaran, bukti_pembayaran) 
                         VALUES ('$order_id', '$metode', '$namaBaru')");

    // Hapus keranjang
    mysqli_query($koneksi, "DELETE FROM keranjang WHERE user_id = '$user_id'");

    // Redirect ke halaman orders
    header("Location: orders.php");
    exit;
} else {
    echo "Akses tidak sah.";
    exit;
}
?>
