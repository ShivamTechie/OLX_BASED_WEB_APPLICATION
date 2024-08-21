<?php
session_start(); // Start the session

// Unset the specific session variable
if (isset($_SESSION['full_name'])) {
  unset($_SESSION['full_name']);
}

// Redirect to admin-login.php
header('Location: admin-login');
exit();
