<?php
require_once __DIR__ . "/../includes/auth_guard.php";
$role = $_SESSION["user"]["role"];
if ($role !== "admin" && $role !== "lto") die("Access denied");
require_once __DIR__ . "/../config/db.php";

$rows = $pdo->query("
  SELECT e.serial_no, e.name, e.status, e.machine_health, e.availability, e.room_no,
         (SELECT MAX(failure_time) FROM Downtime_Log d WHERE d.serial_no = e.serial_no) AS last_failure
  FROM Equipments e
  WHERE LOWER(e.machine_health) IN ('fair','poor')
     OR LOWER(e.availability) IN ('unavailable')
     OR LOWER(e.status) NOT IN ('active')
  ORDER BY last_failure DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Repair Queue</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="topbar">
    <h2>Repair Queue (Admin/LTO)</h2>
    <a class="btn" href="../<?= $role === 'admin' ? 'admin/dashboard.php' : 'lto/dashboard.php' ?>">Back</a>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Serial</th><th>Name</th><th>Status</th><th>Health</th><th>Availability</th><th>Room</th><th>Last Failure</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r["serial_no"]) ?></td>
          <td><?= htmlspecialchars($r["name"]) ?></td>
          <td><?= htmlspecialchars($r["status"]) ?></td>
          <td><?= htmlspecialchars($r["machine_health"]) ?></td>
          <td><?= htmlspecialchars($r["availability"]) ?></td>
          <td><?= htmlspecialchars($r["room_no"]) ?></td>
          <td><?= htmlspecialchars($r["last_failure"] ?? "") ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if(count($rows)===0): ?>
        <tr><td colspan="7">No equipment currently flagged for repair.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
