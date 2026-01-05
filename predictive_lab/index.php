<?php
session_start();
if (isset($_SESSION["user"])) {
  $role = $_SESSION["user"]["role"];
  if ($role === "admin") header("Location: admin/dashboard.php");
  elseif ($role === "lto") header("Location: lto/dashboard.php");
  else header("Location: user/dashboard.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Predictive Lab Management</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="container">
  <h1>Predictive Maintenance Tracker</h1>
  <p>Login to view equipment dashboard, downtime, maintenance and inventory.</p>
  <a class="btn" href="auth/login.php">Login</a>
</body>
</html>