<?php
require_once __DIR__ . "/../includes/auth_guard.php";
$role = $_SESSION["user"]["role"];
if ($role !== "admin" && $role !== "lto") die("Access denied");

require_once __DIR__ . "/../config/db.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$rows = $pdo->query("
  SELECT e.serial_no, e.name, e.machine_health,
         COUNT(d.tracking_id) AS incidents,
         ROUND(IFNULL(SUM(TIMESTAMPDIFF(MINUTE, d.failure_time, d.restoration_time))/60, 0), 2) AS downtime_hours,
         MAX(d.failure_time) AS last_failure
  FROM Equipments e
  LEFT JOIN Downtime_Log d ON d.serial_no = e.serial_no
  GROUP BY e.serial_no, e.name, e.machine_health
  ORDER BY downtime_hours DESC
")->fetchAll();

function riskLabel($incidents, $hours, $health) {
  $score = 0;
  if ($incidents >= 2) $score += 2;
  if ($hours >= 5) $score += 2;

  $h = strtolower($health);
  if ($h === "fair") $score += 1;
  if ($h === "poor") $score += 3;

  if ($score >= 5) return "HIGH";
  if ($score >= 3) return "MEDIUM";
  return "LOW";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Maintenance Prediction</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="topbar">
    <h2>Maintenance Prediction</h2>
    <a class="btn" href="<?= $role === 'admin' ? 'dashboard.php' : '../lto/dashboard.php' ?>">Back</a>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Serial</th><th>Name</th><th>Health</th><th>Incidents</th><th>Downtime(hrs)</th><th>Last Failure</th><th>Risk</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($rows)): ?>
        <tr><td colspan="7">No data found.</td></tr>
      <?php else: ?>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r["serial_no"]) ?></td>
            <td><?= htmlspecialchars($r["name"]) ?></td>
            <td><?= htmlspecialchars($r["machine_health"]) ?></td>
            <td><?= htmlspecialchars($r["incidents"]) ?></td>
            <td><?= htmlspecialchars($r["downtime_hours"]) ?></td>
            <td><?= htmlspecialchars($r["last_failure"] ?? "") ?></td>
            <td><?= htmlspecialchars(riskLabel((int)$r["incidents"], (float)$r["downtime_hours"], $r["machine_health"])) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
