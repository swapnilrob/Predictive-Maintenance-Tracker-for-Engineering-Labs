<?php
require_once __DIR__ . "/../includes/auth_guard.php";
if ($_SESSION["user"]["role"] !== "admin") die("Access denied");
require_once __DIR__ . "/../config/db.php";

$error = "";
$rows = [];

try {
  $rows = $pdo->query("
    SELECT s.serial_no,
           e.name AS equipment_name,
           s.part_name,
           s.quantity,
           s.min_quantity,
           s.reorder_flag
    FROM spare_inventory s
    LEFT JOIN equipments e ON e.serial_no = s.serial_no
    ORDER BY s.reorder_flag DESC, s.quantity ASC
  ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Spare Inventory</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">

  <div class="topbar">
    <h2>Spare Inventory</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <?php if ($error): ?>
    <div class="alert">DB Error: <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <table class="table">
    <thead>
      <tr>
        <th>Serial</th>
        <th>Equipment</th>
        <th>Part Name</th>
        <th>Quantity</th>
        <th>Min Qty</th>
        <th>Reorder Flag</th>
        <th>Alert</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($rows)): ?>
        <tr><td colspan="7">No spare inventory records found.</td></tr>
      <?php else: ?>
        <?php foreach ($rows as $r):
          $alert = "";
          if ((int)$r["reorder_flag"] === 1 || (int)$r["quantity"] <= (int)$r["min_quantity"]) {
            $alert = "LOW STOCK / REORDER";
          }
        ?>
          <tr>
            <td><?= htmlspecialchars($r["serial_no"]) ?></td>
            <td><?= htmlspecialchars($r["equipment_name"] ?? "") ?></td>
            <td><?= htmlspecialchars($r["part_name"]) ?></td>
            <td><?= htmlspecialchars($r["quantity"]) ?></td>
            <td><?= htmlspecialchars($r["min_quantity"]) ?></td>
            <td><?= htmlspecialchars($r["reorder_flag"]) ?></td>
            <td><?= htmlspecialchars($alert) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>
