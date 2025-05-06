<?php
include "koneksi.php";

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
    

<header class="header">


<div class="header-2">
   <div class="flex">

      <a href="home.php" class="logo">Booknest</a>

      <nav class="navbar">
         <a href="home.php">Home</a>
         <a href="about.php">About</a>
         <a href="shop.php">Shop</a>
         <a href="messages.php">Messages</a>
         <a href="orders.php">Orders</a>
      </nav>

      <div class="icons">
         <a href="search_page.php" class="fas fa-search"></a>
         <div id="user-btn" class="fas fa-user"></div>

         <?php
               $select_cart_number = mysqli_query($koneksi, "SELECT * FROM `keranjang` WHERE user_id = '$user_id'") or die('query failed');
               $cart_rows_number = mysqli_num_rows($select_cart_number); 
            ?>
            <a href="keranjang.php" style="position: relative;">
               <i class="fas fa-shopping-cart"></i>
               <span style="
                  font-size: 1.2rem;
                  position: absolute;
                  top: -10px;
                  right: -10px;
                  background: red;
                  color: white;
                  padding: 2px 6px;
                  border-radius: 50%;
               ">
                  <?php echo $cart_rows_number; ?>
               </span>
             </a>
        </div>
    
      <?php if ($user_id){ ?>
      <div class="user-box">
         <p>username : <span><?php echo $_SESSION['user_name']; ?></span></p>
         <p>email : <span><?php echo $_SESSION['user_email']; ?></span></p>
         <a href="logout.php" class="delete-btn">logout</a>
         <div>new <a href="loginregister.php">login</a> | <a href="loginregister.php">register</a></div>
      </div>
      <?php } ?>
    </div>
</div>
</header>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>