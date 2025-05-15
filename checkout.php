<?php
session_start();
include 'koneksi.php';

$user_id = $_SESSION['user_id'];
$ongkir = 10000;
$produk = [];
$subtotal = 0;

if (isset($_GET['produk_id']) && isset($_GET['jumlah'])) {
    // Checkout langsung dari halaman produk
    $produk_id = $_GET['produk_id'];
    $jumlah = $_GET['jumlah'];

    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = '$produk_id'");
    if ($row = mysqli_fetch_assoc($query)) {
        $row['jumlah'] = $jumlah;
        $produk[] = $row;
        $subtotal += $row['harga'] * $jumlah;
    }
} else {
    // Checkout dari keranjang
    $query = mysqli_query($koneksi, "SELECT k.*, p.nama_buku, p.harga, p.gambar, p.nama_pengarang FROM keranjang k JOIN produk p ON k.produk_id = p.id WHERE k.user_id = '$user_id'");
    while ($row = mysqli_fetch_assoc($query)) {
        $produk[] = $row;
        $subtotal += $row['harga'] * $row['jumlah'];
    }
}

$total = $subtotal + $ongkir;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Booknest</title>
    <link rel="stylesheet" href="css/checkout.css">
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="checkout-container">
    <form action="proses_checkout.php" method="post" enctype="multipart/form-data" class="checkout-form">
        <h2>Checkout</h2>

        <label for="alamat">Alamat Lengkap</label>
        <textarea name="alamat" placeholder="Contoh: Jl. Merdeka No.123, RT 01/RW 02" required></textarea>

        <h3>Metode Pembayaran</h3>
        <div class="payment-methods">
            <?php
            $metodes = ['BCA', 'DANA', 'OVO'];
            foreach ($metodes as $metode):
            ?>
                <label class="payment-option">
                    <input type="radio" name="metode" value="<?= $metode ?>" onclick="showPopup('<?= $metode ?>')" required>
                    <img src="images/<?= strtolower($metode) ?>.png" alt="<?= $metode ?>" style="max-width:15px">
                    <span>Bank <?= $metode ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <label>Upload Bukti Pembayaran</label>
        <div class="file-upload">
            <input type="file" name="bukti" accept="image/*" required>

        <label for="proof" class="upload-label">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Pilih file atau drop disini</span>
            <span class="file-name">Format: JPG, PNG (max 2MB)</span>
        </label>

        </div>

        <div id="popup" class="popup hidden">
            <p>Nomor rekening: <span id="rekening"></span></p>
            <button type="button" onclick="closePopup()">Tutup</button>
        </div>

            <div class="form-actions">
                <a href="keranjang.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Keranjang</a>
                <button type="submit" name="checkout" class="btn-primary">🛍️ Buat Pesanan</button>
            </div>

    </form>

    <div class="order-summary">
       <h2><i class="fas fa-shopping-cart"></i> Ringkasan Pesanan</h2>

        <?php foreach ($produk as $item): ?>
            <div class="produk-list">
                <img src="img/<?= $item['gambar'] ?>" alt="<?= $item['nama_buku'] ?>" style="max: width 15px;"> 
                <div class="product-item">
                    <strong><?= $item['nama_buku'] ?></strong><br>
                    <span class="pengarang"><?= $item['pengarang'] ?? 'Tidak diketahui' ?></span><br>
                    <span>Rp<?= number_format($item['harga']) ?> x <?= $item['jumlah'] ?></span>
                
                    Rp<?= number_format($item['harga'] * $item['jumlah']) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- <hr> -->
    <div class="summary-details">
        <div class="summary-row">
            <span>Subtotal</span>
            <span>  Rp<?= number_format($subtotal) ?> </span>
        </div>

        <div class="summary-row">
             <span>Ongkos Kirim</span>
            <span>Rp<?= number_format($ongkir) ?></span>
        </div>
        
        <div class="summary-row">
             <span>Total Pembayaran</span>
            <span>Rp<?= number_format($total) ?></span>
        </div>

        <p class="total">Total Pembayaran: <strong>Rp<?= number_format($total) ?></strong></p>
    </div>
    </div>
</div>








<script>
function showPopup(metode) {
    const popup = document.getElementById('popup');
    const rekening = document.getElementById('rekening');
    popup.classList.remove('hidden');

    let nomor = '';
    if (metode === 'DANA') nomor = '0812-3456-7890';
    else if (metode === 'OVO') nomor = '0813-1234-5678';
    else if (metode === 'BCA') nomor = '0812-9876-5432';

    rekening.innerText = nomor;
}

function closePopup() {
    document.getElementById('popup').classList.add('hidden');
}
</script>
</body>
</html>
