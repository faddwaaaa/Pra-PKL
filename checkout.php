<?php
session_start();
include 'koneksi.php';

// $id = $_POST['produk_id'];
// $nama_buku = $_POST['nama_buku'];
// $harga = $_POST['harga'];


if (!isset($_SESSION['user_id'])) {
    header('Location: loginregister.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$ongkir = 10000;
$produk = [];
$subtotal = 0;

// Cek apakah dari single product
if (isset($_GET['source']) && $_GET['source'] === 'single' && isset($_GET['produk_id']) && isset($_GET['jumlah'])) {
    $produk_id = intval($_GET['produk_id']);
    $jumlah = intval($_GET['jumlah']);

    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = '$produk_id'");
    if ($row = mysqli_fetch_assoc($query)) {
        $row['jumlah'] = $jumlah;
        $produk[] = $row;
        $subtotal += $row['harga'] * $jumlah;
    }
} else {
    // Dari keranjang
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
    <div class="checkout-grid">
        <form action="proses_checkout.php" method="post" enctype="multipart/form-data" class="checkout-form">
            <h2>Checkout</h2>

            <!-- <input type="hidden" name="id" value="">
            <input type="hidden" name="nama_buku" value="">
            <input type="hidden" name="harga" value=""> -->

            <h4><i class="fa-solid fa-location-dot"></i> Alamat Lengkap</h4>
            <textarea name="alamat" placeholder="Contoh: Jl. Merdeka No.123, RT 01/RW 02" required></textarea>

            <h4><i class="fas fa-credit-card"></i> Metode Pembayaran</h4>
            <div class="payment-methods">
                <div class="payment-option" data-method="bca">
                    <input type="radio" id="bca" name="payment_method" value="BCA" required>
                    <label for="bca">
                        <img src="images/bca.png" alt="BCA">
                        <span>Bank BCA</span>
                    </label>
                </div>
                            
                <div class="payment-option" data-method="dana">
                    <input type="radio" id="dana" name="payment_method" value="DANA" required>
                        <label for="dana">
                            <img src="images/dana.png" alt="DANA">
                            <span>DANA</span>
                        </label>
                </div>
                            
                <div class="payment-option" data-method="ovo">
                    <input type="radio" id="ovo" name="payment_method" value="OVO" required>
                        <label for="ovo">
                            <img src="images/ovo.png" alt="OVO">
                            <span>OVO</span>
                        </label>
                </div>
            </div>

            <!-- <div id="popup" class="popup hidden">
                <p>Nomor rekening: <span id="rekening"></span></p>
                <button type="button" onclick="closePopup()">Tutup</button>
            </div> -->
            
            <div class="form-group">
                    <h4><i class="fa-solid fa-upload"></i> Upload Bukti Pembayaran</h4>
                        <div class="file-upload">
                            <input type="file" id="proof" name="proof" accept="bukti/*" required>
                            <label for="proof" class="upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Pilih file atau drop disini</span>
                                <span class="file-name">Format: JPG, PNG, JPEG (max 2MB)</span>
                            </label>      
                        </div>
                    </label>
            </div>

           


             <div class="form-actions">
                <a href="keranjang.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Keranjang</a>
                <button type="submit" name="checkout" class="btn-submit"><i class="fas fa-shopping-bag"></i> Buat Pesanan</button>
             </div>

        </form>

    <!-- bagian kanan -->
        <div class="order-summary">
            <h3><i class="fas fa-shopping-cart"></i> Ringkasan Pesanan</h3>

            <div class="product-list">
                <?php foreach ($produk as $item): ?>
                    <div class="produk-item" style="display: flex;
                        align-items: flex-start;
                        gap: 15px;
                        border: 1px solid #ddd;
                        padding: 10px;
                        border-radius: 8px;
                        background-color: #f9f9f9;">
                        <img src="img/<?= $item['gambar'] ?>" alt="<?= $item['nama_buku'] ?>" style="width: 100px; height: auto;"> 
                        <div class="product-details">
                            <h5><?= $item['nama_buku'] ?></h5>
                            <div class="product-meta">
                                <span class="pengarang"><?= $item['nama_pengarang'] ?? 'Tidak diketahui' ?></span><br>
                                <span class="harga">Rp<?= number_format($item['harga']) ?> x <?= $item['jumlah'] ?></span>       
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

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
</div>

 <!-- Payment Modal -->
    <div class="modal" id="paymentModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3><i class="fas fa-wallet"></i> Pembayaran via <span id="paymentMethodName"></span></h3>
            <div class="payment-instruction">
                <div class="account-info">
                    <div class="account-number">
                        <span>Nomor Rekening:</span>
                        <strong id="accountNumber">1234567890</strong>
                        <button class="btn-copy" onclick="copyToClipboard('#accountNumber')">
                            <i class="far fa-copy"></i> Salin
                        </button>
                    </div>
                    <div class="account-name">
                        <span>Atas Nama:</span>
                        <strong>BOOOKNEST</strong>
                    </div>
                </div>
                <div class="payment-note">
                    <p><i class="fas fa-info-circle"></i> Total pembayaran: <strong>Rp. <?php echo number_format($total, 0, ',', '.'); ?></strong></p>
                    <p><i class="fas fa-clock"></i> Batas pembayaran: <strong>24 jam</strong> setelah pesanan dibuat</p>
                    <p><i class="fas fa-upload"></i> Jangan lupa upload bukti transfer</p>
                </div>
            </div>
        </div>
    </div>

 



<script src="js/checkout.js"></script>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById('proof');
    const fileName = document.querySelector('.file-name');

    fileInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            fileName.textContent = "Dipilih: " + this.files[0].name;
        } else {
            fileName.textContent = 'Format: JPG, PNG, JPEG (max 2MB)';
        }
    });

    // === Tambahan Baru Untuk Popup Metode Pembayaran ===
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    paymentRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            showPopup(this.value); // Panggil showPopup dengan metode terpilih
        });
    });
});


  
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
