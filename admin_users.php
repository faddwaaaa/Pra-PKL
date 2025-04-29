<?php

include 'koneksi.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:loginregister.php');
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($koneksi, "DELETE FROM `user` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_users.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">

   <h1 class="title"> user accounts </h1>

   <div class="box-container">
      <table border="1">
        <tr>
            <th>User Id</th>
            <th>Username</th>
            <th>Email</th>
            <th>User Type</th>
            <th>Aksi</th>
        </tr>
    

        <?php 
        $select_users = mysqli_query($koneksi, "SELECT * FROM `user`") or die('query failed');
        while($user = mysqli_fetch_assoc($select_users)) : 
        ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['nama'] ?></td>
            <td><?= $user['email'] ?></td>
            <td style="color: <?= $user['user_type'] == 'admin' ? 'var(--orange)' : 'blue'; ?>"><?= $user['user_type']; ?></td>
            <td>
            <a href="admin_users.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('hapus user ini?');" class="delete-btn">Hapus</a>
            </td>
        </tr>
    <?php endwhile ?>
        
   </div>

</section>









<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>