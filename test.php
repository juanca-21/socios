<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
ini_set('display_errors', 1);
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
echo $_SESSION['user_id'];
echo $_SESSION['email'];
echo $_SESSION['rol'];

?>