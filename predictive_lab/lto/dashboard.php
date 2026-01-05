<?php
require_once __DIR__ . "/../includes/auth_guard.php";

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "lto") {
  die("Access denied");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>LTO Dashboard</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="grid">
  <a class="card" href="downtime_add.php">Add Downtime Log</a>
  <a class="card" href="maintenance_add.php">Add Maintenance Record</a>

  <a class="card" href="../admin/repair_queue.php">Repair Queue</a>
  <a class="card" href="../admin/prediction_dashboard.php">Maintenance Prediction</a>
</div>



  <div class="grid">
    <a class="card" href="downtime_add.php">Add Downtime Log</a>
    <a class="card" href="maintenance_add.php">Add Maintenance Record</a>
  </div>
</body>
</html>
