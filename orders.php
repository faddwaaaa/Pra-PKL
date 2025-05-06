<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['user_id'])){
    header('location:loginregister.php');
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Jika ada ID (tampilkan detail pesanan)
if ($order_id) {
    $order_query = mysqli_query($koneksi, "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id'");
    $order = mysqli_fetch_assoc($order_query);

    if (!$order) {
        die("<h2 style='color:red; text-align:center;'>Pesanan tidak ditemukan atau bukan milik kamu!</h2>");
    }

    // Ambil produk pesanan
    $produk_query = mysqli_query($koneksi, "SELECT p.nama_buku, dp.jumlah FROM detail_pesanan dp JOIN produk p ON dp.produk_id = p.id WHERE dp.orders_id='$order_id'");
    $produk_list = [];
    while ($row = mysqli_fetch_assoc($produk_query)) {
        $produk_list[] = $row;
    }

    // Ambil pembayaran
    $pembayaran_query = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE orders_id='$order_id'");
    $pembayaran = mysqli_fetch_assoc($pembayaran_query);
}
else {
    // Jika tidak ada ID, tampilkan daftar semua pesanan
    $orders_query = mysqli_query($koneksi, "SELECT * FROM orders WHERE user_id='$user_id' ORDER BY tanggal_pesanan DESC");
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Orders - Booknest</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }


    .detail {
        text-align: center;
        padding: 40px 20px;
    }

    .detail h2 {
        font-size: 24px;
        margin-bottom: 20px;
    }

    .card {
        background: #f5f5f5;
        display: inline-block;
        padding: 20px;
        border-radius: 8px;
        text-align: left;
        max-width: 500px;
    }

    .card p {
        margin: 10px 0;
        font-size: 15px;
        color: #8B5E3C;
    }

    .card strong {
        color: #333;
    }

    /* .card p:nth-child(5) strong,
    .card p:nth-child(5) {
        color
    } */

   
    </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>pesanan </h3>
   <p> <a href="home.php">home</a> / orders </p>
</div>


   <!-- Detail atau List Pesanan -->
   <section class="detail">
        <?php if ($order_id): ?>
            <!-- Detail Pesanan -->
            <h2>DETAIL PESANAN</h2>
            <div class="card">
                <p><strong>Tanggal Pesanan :</strong> <?= date('d-M-Y', strtotime($order['tanggal_pesanan'])) ?></p>
                <p><strong>Alamat :</strong> <?= htmlspecialchars($order['alamat_pengiriman']) ?></p>
                <p><strong>Metode Pembayaran :</strong> 
                    <?= $pembayaran ? htmlspecialchars($pembayaran['metode_pembayaran']) : 'Tidak diketahui' ?>
                </p>
                <p><strong>Pesanan Kamu :</strong> 
                    <?php if ($produk_list): ?>
                        <?php foreach ($produk_list as $produk): ?>
                            <?= htmlspecialchars($produk['nama_buku']) ?> (<?= $produk['jumlah'] ?>)
                        <?php endforeach; ?>
                    <?php else: ?>
                        Tidak ada produk.
                    <?php endif; ?>
                </p>
                <p><strong>Total Harga :</strong> Rp.<?= number_format($order['total_harga'], 0, ',', '.') ?></p>
                <p><strong>Status Pesanan :</strong> <?= htmlspecialchars($order['status']) ?></p>
            </div>
        <?php else: ?>
            <!-- List Pesanan -->
            <h2>DAFTAR PESANAN KAMU</h2>
            <?php while ($row = mysqli_fetch_assoc($orders_query)): ?>
                <div class="card">
                    <p><strong>Tanggal:</strong> <?= date('d-M-Y', strtotime($row['tanggal_pesanan'])) ?></p>
                    <p><strong>Total:</strong> Rp.<?= number_format($row['total_harga'], 0, ',', '.') ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
                    <p><a href="orders.php?id=<?= $row['id'] ?>" style="color:#8B5E3C; ">Lihat Detail</a></p>
                </div>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($orders_query) == 0): ?>
                <p>Kamu belum punya pesanan.</p>
            <?php endif; ?>
        <?php endif; ?>
    </section>


<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
    
</body>
</html>
