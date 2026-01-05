<?php
require_once __DIR__ . "/../includes/auth_guard.php";
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
  die("Access denied");
}
require_once __DIR__ . "/../config/db.php";

$eq = [];
$error = "";

try {
  $stmt = $pdo->query("
    SELECT serial_no, name, machine_health, availability, status, room_no
    FROM Equipments
    ORDER BY serial_no
  ");
  $eq = $stmt->fetchAll();
} catch (Exception $e) {
  $error = $e->getMessage();
  $eq = []; // ensure it's always an array
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">

  <div class="topbar">
    <h2>Admin Dashboard</h2>
    <div>
      <a class="btn" href="equipment_add.php">Add Equipment</a>
      <a class="btn" href="maintenance_log.php">Maintenance Logs</a>
      <a class="btn" href="downtime_analytics.php">Downtime Analytics</a>
      <a class="btn" href="spare_inventory.php">Spare Inventory</a>
      <a class="btn" href="repair_queue.php">Repair Queue</a>
      <a class="btn" href="prediction_dashboard.php">Maintenance Prediction</a>
      <a class="btn" href="../auth/logout.php">Logout</a>
    </div>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert">DB Error: <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <h3>Equipment List</h3>

  <table class="table">
    <thead>
      <tr>
        <th>Serial</th><th>Name</th><th>Health</th><th>Availability</th><th>Status</th><th>Room</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($eq)): ?>
        <tr><td colspan="6">No equipment found.</td></tr>
      <?php else: ?>
        <?php foreach ($eq as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e["serial_no"]) ?></td>
            <td><?= htmlspecialchars($e["name"]) ?></td>
            <td><?= htmlspecialchars($e["machine_health"]) ?></td>
            <td><?= htmlspecialchars($e["availability"]) ?></td>
            <td><?= htmlspecialchars($e["status"]) ?></td>
            <td><?= htmlspecialchars($e["room_no"]) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>
