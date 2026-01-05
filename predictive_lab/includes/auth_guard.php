<?php
// Always start session safely
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// If not logged in, send to login page
if (!isset($_SESSION["user"])) {
  header("Location: ../auth/login.php");
  exit;
}
