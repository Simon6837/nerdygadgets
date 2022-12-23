<?php
//clear the session and redirect to login page
session_start();
session_destroy();
header('location: Login.php');
?>