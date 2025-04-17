<?php
session_start();

require_once 'config.php';
require_once '../dashboard/dashboard.php';

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');


    exit;

}


$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">💰 BudgetManager</div>
            <ul class="nav-links">
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
    </nav>



    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> BudgetManager. All rights reserved.Firdaous Srifi</p>
   </footer>
    
</body>
</html>