<?php
require_once __DIR__ . "/../includes/auth_guard.php";
if ($_SESSION["user"]["role"] !== "admin") die("Access denied");
require_once __DIR__ . "/../config/db.php";

$rows = $pdo->query("
  SELECT m.eq_id, e.name AS equipment_name,
         m.lto_id, u.name AS lto_name,
         m.trac_id, i.prediction,
         m.cost
  FROM Maintenance m
  LEFT JOIN Equipments e ON e.serial_no = m.eq_id
  LEFT JOIN User u ON u.id = m.lto_id
  LEFT JOIN Issues i ON i.trac_id = m.trac_id
  ORDER BY m.trac_id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Maintenance Logs</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="topbar">
    <h2>Maintenance Logs</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Equipment</th><th>LTO</th><th>Issue</th><th>Cost</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r["eq_id"]) ?> - <?= htmlspecialchars($r["equipment_name"] ?? "") ?></td>
          <td><?= htmlspecialchars($r["lto_id"]) ?> - <?= htmlspecialchars($r["lto_name"] ?? "") ?></td>
          <td><?= htmlspecialchars($r["trac_id"]) ?> - <?= htmlspecialchars($r["prediction"] ?? "") ?></td>
          <td><?= htmlspecialchars($r["cost"]) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (count($rows)===0): ?>
        <tr><td colspan="4">No maintenance records found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
