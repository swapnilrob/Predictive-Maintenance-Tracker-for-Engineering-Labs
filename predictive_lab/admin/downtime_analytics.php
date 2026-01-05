<?php
require_once __DIR__ . "/../includes/auth_guard.php";
if ($_SESSION["user"]["role"] !== "admin") die("Access denied");
require_once __DIR__ . "/../config/db.php";

$rows = $pdo->query("
  SELECT d.serial_no,
         e.name,
         COUNT(*) AS incidents,
         ROUND(SUM(TIMESTAMPDIFF(MINUTE, d.failure_time, d.restoration_time))/60, 2) AS downtime_hours,
         MAX(d.failure_time) AS last_failure
  FROM Downtime_Log d
  LEFT JOIN Equipments e ON e.serial_no = d.serial_no
  GROUP BY d.serial_no, e.name
  ORDER BY downtime_hours DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Downtime Analytics</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="topbar">
    <h2>Downtime Analytics</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Serial</th><th>Name</th><th>Incidents</th><th>Total Downtime (hrs)</th><th>Last Failure</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r["serial_no"]) ?></td>
          <td><?= htmlspecialchars($r["name"] ?? "") ?></td>
          <td><?= htmlspecialchars($r["incidents"]) ?></td>
          <td><?= htmlspecialchars($r["downtime_hours"] ?? 0) ?></td>
          <td><?= htmlspecialchars($r["last_failure"] ?? "") ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (count($rows)===0): ?>
        <tr><td colspan="5">No downtime logs found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
