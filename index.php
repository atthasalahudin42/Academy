<?php
session_start();

if(isset($_SESSION['user_id'])){
    header("Location: templates/dashboard/index.php");
    exit();
}

header("Location: templates/auth/login.php");
exit();
