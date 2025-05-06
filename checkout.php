<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])){
    header('location:loginregister.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['buat_pesanan'])) {
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat_pengiriman']);
    $metode = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
    $tanggal = date('Y-m-d');
    $total_harga = 0;

    // Tangani upload file bukti pembayaran
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['proof']['name'];
        $file_tmp = $_FILES['proof']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (in_array($file_ext, $allowed_ext) && $_FILES['proof']['size'] <= 2 * 1024 * 1024) {
            $bukti_transfer = 'proof_' . time() . '.' . $file_ext;
            move_uploaded_file($file_tmp, 'bukti/' . $bukti_transfer);
        } else {
            echo "<script>alert('Bukti pembayaran harus JPG, JPEG, PNG, dan maksimal 2MB'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Harap upload bukti pembayaran'); window.history.back();</script>";
        exit;
    }

    // Hitung total harga
    if (!empty($_POST['produk_id'])) {
        // Pembelian langsung
        $produk_id = intval($_POST['produk_id']);
        $jumlah = intval($_POST['jumlah']);
        $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id = '$produk_id'"));
        $total_harga = $produk['harga'] * $jumlah;
    } else {
        // Dari keranjang
        $keranjang_query = mysqli_query($koneksi, "SELECT keranjang.*, produk.harga FROM keranjang JOIN produk ON keranjang.produk_id = produk.id WHERE user_id = '$user_id'");
        while ($row = mysqli_fetch_assoc($keranjang_query)) {
            $total_harga += $row['harga'] * $row['jumlah'];
        }
    }

    $total_harga += 10000; // Ongkir tetap

    // Simpan order
    $insert_order = mysqli_query($koneksi, "INSERT INTO orders (user_id, alamat_pengiriman, total_harga, tanggal_pesanan, status) VALUES ('$user_id', '$alamat', '$total_harga', '$tanggal', 'pending')");
    if (!$insert_order) {
        die('Gagal membuat pesanan: ' . mysqli_error($koneksi));
    }

    $order_id = mysqli_insert_id($koneksi);

    // Simpan detail produk
    if (!empty($_POST['produk_id'])) {
        mysqli_query($koneksi, "INSERT INTO detail_pesanan (orders_id, produk_id, jumlah) VALUES ('$order_id', '$produk_id', '$jumlah')");
    } else {
        $produk_query = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE user_id = '$user_id'");
        while ($row = mysqli_fetch_assoc($produk_query)) {
            mysqli_query($koneksi, "INSERT INTO detail_pesanan (orders_id, produk_id, jumlah) VALUES ('$order_id', '{$row['produk_id']}', '{$row['jumlah']}')");
        }
        mysqli_query($koneksi, "DELETE FROM keranjang WHERE user_id = '$user_id'");
    }

    // Simpan pembayaran
    $insert_payment = mysqli_query($koneksi, "INSERT INTO pembayaran (orders_id, metode_pembayaran, bukti_pembayaran) VALUES ('$order_id', '$metode', '$bukti_transfer')");
    if (!$insert_payment) {
        die('Gagal menyimpan data pembayaran: ' . mysqli_error($koneksi));
    }

    header("Location: orders.php?id=$order_id");
    exit;
}

$produk_checkout = [];
$total_checkout = 0;

if (isset($_GET['produk_id'])) {
    $produk_id = intval($_GET['produk_id']);
    $jumlah = isset($_GET['jumlah']) ? intval($_GET['jumlah']) : 1;
    $result = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = '$produk_id'");
    if (mysqli_num_rows($result) > 0) {
        $produk = mysqli_fetch_assoc($result);
        $produk['jumlah'] = $jumlah;
        $produk_checkout[] = $produk;
        $total_checkout = $produk['harga'] * $jumlah;
    }
} else {
    $keranjang = mysqli_query($koneksi, "SELECT keranjang.jumlah, produk.* FROM keranjang JOIN produk ON keranjang.produk_id = produk.id WHERE keranjang.user_id = '$user_id'");
    while ($row = mysqli_fetch_assoc($keranjang)) {
        $produk_checkout[] = $row;
        $total_checkout += $row['harga'] * $row['jumlah'];
    }
}

$total_checkout += 10000;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Booknest</title>
    <link rel="stylesheet" href="css/checkout.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-grid">
            <!-- Form Bagian Kiri -->
            <div class="checkout-form">
                <h2><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h2>
                
                <form id="checkoutForm" action="orders.php" method="POST" enctype="multipart/form-data">
                <?php if (isset($_GET['produk_id'])): ?>
                    <input type="hidden" name="produk_id" value="<?php echo $_GET['produk_id']; ?>">
                    <input type="hidden" name="jumlah" value="<?php echo isset($_GET['jumlah']) ? $_GET['jumlah'] : 1; ?>">
                <?php endif; ?>

                
                    
                    <div class="form-group">
                        <label for="alamat_pengiriman">Alamat Lengkap</label>
                        <textarea id="alamat_pengiriman" name="alamat_pengiriman" rows="3" required placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02"></textarea>
                    </div>
               
                    <h2><i class="fas fa-credit-card"></i> Metode Pembayaran</h2>
                    
                    <div class="payment-methods">
                        <div class="payment-option" data-method="bca">
                            <input type="radio" id="bca" name="metode_pembayaran" value="bca" required>
                            <label for="bca">
                                <img src="images/bca.png" alt="BCA">
                                <span>Bank BCA</span>
                            </label>
                        </div>
                        
                        <div class="payment-option" data-method="dana">
                            <input type="radio" id="dana" name="metode_pembayaran" value="dana">
                            <label for="dana">
                                <img src="images/dana.png" alt="DANA">
                                <span>DANA</span>
                            </label>
                        </div>
                        
                        <div class="payment-option" data-method="ovo">
                            <input type="radio" id="ovo" name="metode_pembayaran" value="ovo">
                            <label for="ovo">
                                <img src="images/ovo.jpg" alt="OVO">
                                <span>OVO</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="proof">Upload Bukti Pembayaran</label>
                        <div class="file-upload">
                            <input type="file" id="proof" name="proof" accept="bukti/*" required>
                            <label for="proof" class="upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Pilih file atau drop disini</span>
                                <span class="file-name">Format: JPG, PNG, JPEG (max 2MB)</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="keranjang.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Keranjang</a>
                        <button type="submit" name="buat_pesanan" class="btn-submit"><i class="fas fa-shopping-bag"></i> Buat Pesanan</button>
                    </div>
                </form>
            </div>
            
            <!-- Ringkasan Bagian Kanan -->
            <div class="order-summary">
                <h2><i class="fas fa-shopping-cart"></i> Ringkasan Pesanan</h2>
                
                <div class="product-list">
                    <?php foreach ($produk_checkout as $produk): ?>
                        <div class="product-item">
                            <img src="img/<?php echo $produk['gambar']; ?>" alt="<?php echo $produk['nama_buku']; ?>">
                            <div class="product-details">
                                <h4><?php echo $produk['nama_buku']; ?></h4>
                                <div class="product-meta">
                                    <span class="harga">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></span>
                                    <span class="jumlah">x<?php echo $produk['jumlah']; ?></span>
                                    <span class="total">Rp <?php echo number_format($produk['harga'] * $produk['jumlah'], 0, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-details">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>Rp <?php echo number_format($total_checkout, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Ongkos Kirim</span>
                        <p><strong>Rp.10.000</strong></p>
                    </div>
                    <div class="summary-row total">
                        <span>Total Pembayaran</span>
                        <span>Rp <?php echo number_format($total_checkout, 0, ',', '.'); ?></span>
                    </div>
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
                    <p><i class="fas fa-info-circle"></i> Total pembayaran: <strong>Rp. <?php echo number_format($total_checkout, 0, ',', '.'); ?></strong></p>
                    <p><i class="fas fa-clock"></i> Batas pembayaran: <strong>24 jam</strong> setelah pesanan dibuat</p>
                    <p><i class="fas fa-upload"></i> Jangan lupa upload bukti transfer</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/checkout.js"></script>
</body>
</html>