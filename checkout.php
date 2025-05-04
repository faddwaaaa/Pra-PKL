<?php
include 'koneksi.php';
session_start();

$user_id = $_SESSION['user_id'];

// Cek apakah form submit
if (isset($_POST['buat_pesanan'])) {
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $metode = $_POST['metode'];
    $tanggal = date('Y-m-d');

    $total_harga = 0;

    if (isset($_POST['produk_id'])) {
        $produk_id = intval($_POST['produk_id']);
        $jumlah = intval($_POST['jumlah']);
        $produk_query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = '$produk_id'");
        $produk = mysqli_fetch_assoc($produk_query);
        $total_harga = $produk['harga'] * $jumlah;
    } else {
        $keranjang_query = mysqli_query($koneksi, "SELECT keranjang.*, produk.harga FROM keranjang JOIN produk ON keranjang.produk_id = produk.id WHERE user_id = '$user_id'");
        while ($row = mysqli_fetch_assoc($keranjang_query)) {
            $total_harga += $row['harga'] * $row['jumlah'];
        }
    }

    $total_harga += 10000; // Ongkir tetap

    mysqli_query($koneksi, "INSERT INTO orders (user_id, alamat_pengiriman, total_harga, tanggal_pesanan, status) VALUES ('$user_id', '$alamat', '$total_harga', '$tanggal', 'pending')");
    $order_id = mysqli_insert_id($koneksi);

    if (isset($_POST['produk_id'])) {
        mysqli_query($koneksi, "INSERT INTO detail_pesanan (orders_id, produk_id, jumlah) VALUES ('$order_id', '$produk_id', '$jumlah')");
    } else {
        $produk_query = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE user_id = '$user_id'");
        while ($row = mysqli_fetch_assoc($produk_query)) {
            mysqli_query($koneksi, "INSERT INTO detail_pesanan (orders_id, produk_id, jumlah) VALUES ('$order_id', '{$row['produk_id']}', '{$row['jumlah']}')");
        }
        mysqli_query($koneksi, "DELETE FROM keranjang WHERE user_id = '$user_id'");
    }

    mysqli_query($koneksi, "INSERT INTO pembayaran (orders_id, metode_pembayaran) VALUES ('$order_id', '$metode')");

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
    <title>Checkout - Booknest</title>
    <link rel="stylesheet" href="css/checkout.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="js/checkout.js" defer></script>
    <script>
        function showRekening() {
            const metode = document.getElementById("metode").value;
            const popup = document.getElementById("popup-rekening");
            const judul = document.getElementById("judul-metode");
            const text = document.getElementById("rekening-text");
            let nomorVirtual = "";

            switch (metode) {
                case "DANA": 
                    judul.textContent = "Dana";
                    nomorVirtual = "0812-0967-2345 (a.n Booknest)";
                    break;
                case "BCA": 
                    judul.textContent = "BCA";
                    nomorVirtual = "1234567890 (a.n Booknest)";
                    break;
                case "OVO": 
                    judul.textContent = "OVO";
                    nomorVirtual = "0813-0987-6574 (a.n Booknest)";
                    break;
                case "COD": 
                    judul.textContent = "Cash On Delivery";
                    nomorVirtual = "Bayar di tempat saat barang sampai";
                    break;
            }

            text.textContent = nomorVirtual;
            popup.style.display = "block";
        }

        function tutupPopup() {
            document.getElementById("popup-rekening").style.display = "none";
        }
    </script>
</head>
<body>
<?php include 'header.php'; ?>

<div class="heading">
    <h3>Checkout</h3>
    <p> <a href="home.php">home</a> / Checkout </p>
</div>

<section class="checkout-section">
    <form method="post" enctype="multipart/form-data">
        <h2>Konfirmasi Pesanan</h2>

        <div class="form-group">
            <label>Alamat:</label>
            <input type="text" name="alamat" placeholder="Alamat lengkap" required>
        </div>

        <div class="form-group">
            <label>Produk:</label>
            <div class="produk-container">
                <?php foreach($produk_checkout as $produk): ?>
                    <div class="produk-item">
                        <img src="img/<?= $produk['gambar'] ?>" alt="<?= $produk['nama_buku'] ?>">
                        <p><?= $produk['nama_buku'] ?></p>
                        <p class="harga">Rp.<?= number_format($produk['harga'], 0, ',', '.') ?> x <?= $produk['jumlah'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="subtotal-info">
            <p><strong>Ongkir:</strong> Rp.10.000</p>
            <p><strong>SubTotal:</strong> Rp.<?= number_format($total_checkout, 0, ',', '.') ?></p>
        </div>

        <div class="form-group">
            <label>Metode Pembayaran:</label>
            <select name="metode" id="metode" onchange="showRekening()" required>
                <option value="">Pilih</option>
                <option value="DANA">DANA</option>
                <option value="BCA">BCA</option>
                <option value="OVO">OVO</option>
                <option value="COD">Cash On Delivery</option>
            </select>
        </div>

        <div class="form-group">
            <label>Upload Bukti Pembayaran:</label>
            <input type="file" name="bukti" accept="image/*" required>
        </div>

        <?php if (isset($_GET['produk_id'])): ?>
            <input type="hidden" name="produk_id" value="<?= intval($_GET['produk_id']) ?>">
            <input type="hidden" name="jumlah" value="<?= intval($_GET['jumlah']) ?>">
        <?php endif; ?>

        <button type="submit" name="buat_pesanan">Buat Pesanan</button>
    </form>

    <!-- Popup Rekening -->
    <div id="popup-rekening" style="display:none;">
        <h3 id="judul-metode">Metode</h3>
        <p id="rekening-text">Nomor virtual muncul di sini</p>
        <button onclick="document.getElementById('popup-rekening').style.display='none'">OKE</button>
    </div>
</section>


<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>