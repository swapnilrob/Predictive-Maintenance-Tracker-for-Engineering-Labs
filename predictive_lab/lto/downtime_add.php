<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
if ($_SESSION["user"]["role"] !== "lto") { die("Access denied"); }

$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $tracking_id = (int)$_POST["tracking_id"];
    $serial_no = (int)$_POST["serial_no"];
    $failure_time = $_POST["failure_time"];
    $restoration_time = $_POST["restoration_time"];
    $severity = trim($_POST["severity"]);
    $failure_type = trim($_POST["failure_type"]);
    $root_cause = trim($_POST["root_cause"]);

    $stmt = $pdo->prepare("
      INSERT INTO Downtime_Log (tracking_id, serial_no, failure_time, restoration_time, severity, failure_type, root_cause)
      VALUES (?,?,?,?,?,?,?)
    ");
    $stmt->execute([$tracking_id,$serial_no,$failure_time,$restoration_time,$severity,$failure_type,$root_cause]);
    $msg = "Downtime log added!";
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Downtime</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="topbar">
    <h2>Add Downtime Log</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="POST" class="card">
    <label>Tracking ID</label>
    <input name="tracking_id" required>

    <label>Equipment Serial No</label>
    <input name="serial_no" required>

    <label>Failure Time</label>
    <input name="failure_time" type="datetime-local" required>

    <label>Restoration Time</label>
    <input name="restoration_time" type="datetime-local" required>

    <label>Severity</label>
    <input name="severity" placeholder="High/Medium/Low" required>

    <label>Failure Type</label>
    <input name="failure_type" placeholder="Overheating / Power Supply..." required>

    <label>Root Cause</label>
    <textarea name="root_cause" required></textarea>

    <button type="submit">Save</button>
  </form>

  <p class="hint">Note: If you haven’t inserted Equipments data yet, serial_no must match an existing equipment row or foreign key will fail.</p>
</body>
</html>
