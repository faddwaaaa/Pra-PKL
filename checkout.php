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
    $query = mysqli_query($koneksi, "SELECT k.*, p.nama_buku, p.harga, p.gambar FROM keranjang k JOIN produk p ON k.produk_id = p.id WHERE k.user_id = '$user_id'");
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
        <div class="metode-pembayaran">
            <?php
            $metodes = ['BCA', 'DANA', 'OVO'];
            foreach ($metodes as $metode):
            ?>
                <label class="metode-option">
                    <input type="radio" name="metode" value="<?= $metode ?>" onclick="showPopup('<?= $metode ?>')" required>
                    <img src="images/<?= strtolower($metode) ?>.png" alt="<?= $metode ?>">
                    <span>Bank <?= $metode ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <label>Upload Bukti Pembayaran</label>
        <div class="upload-box">
            <p><strong>📤</strong> Pilih file atau drop di sini</p>
            <p>Format: JPG, PNG (max 2MB)</p>
            <input type="file" name="bukti" accept="image/*" required>
        </div>

        <div id="popup" class="popup hidden">
            <p>Nomor rekening: <span id="rekening"></span></p>
            <button type="button" onclick="closePopup()">Tutup</button>
        </div>

        <div class="checkout-buttons">
            <a href="keranjang.php" class="btn-secondary">← Kembali ke Keranjang</a>
            <button type="submit" name="checkout" class="btn-primary">🛍️ Buat Pesanan</button>
        </div>
    </form>

    <div class="ringkasan-pesanan">
        <h3>🧾 Ringkasan Pesanan</h3>
        <?php foreach ($produk as $item): ?>
            <div class="produk-item">
                <img src="img/<?= $item['gambar'] ?>" alt="<?= $item['nama_buku'] ?>" style="max: width 20px;"> 
                <div class="detail">
                    <strong><?= $item['nama_buku'] ?></strong><br>
                    <span class="penulis"><?= $item['penulis'] ?? 'Penulis Tidak Diketahui' ?></span><br>
                    <span>Rp<?= number_format($item['harga']) ?> x <?= $item['jumlah'] ?></span>
                </div>
                <div class="subtotal">
                    Rp<?= number_format($item['harga'] * $item['jumlah']) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <hr>
        <p>Subtotal: <strong>Rp<?= number_format($subtotal) ?></strong></p>
        <p>Ongkos Kirim: <strong>Rp<?= number_format($ongkir) ?></strong></p>
        <p class="total">Total Pembayaran: <strong>Rp<?= number_format($total) ?></strong></p>
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
    else if (metode === 'GOPAY') nomor = '0812-9876-5432';

    rekening.innerText = nomor;
}

function closePopup() {
    document.getElementById('popup').classList.add('hidden');
}
</script>
</body>
</html>
