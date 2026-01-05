<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
if ($_SESSION["user"]["role"] !== "lto") { die("Access denied"); }

$msg = "";
$error = "";
$lto_id = $_SESSION["user"]["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $eq_id   = (int)$_POST["eq_id"];
    $trac_id = (int)$_POST["trac_id"];
    $cost    = (float)$_POST["cost"];

    $stmt = $pdo->prepare("
      INSERT INTO Maintenance (eq_id, lto_id, trac_id, cost)
      VALUES (?,?,?,?)
    ");
    $stmt->execute([$eq_id, $lto_id, $trac_id, $cost]);
    $msg = "Maintenance record added!";
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Maintenance</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="topbar">
    <h2>Add Maintenance Record</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="POST" class="card">
    <label>Equipment Serial No (eq_id)</label>
    <input name="eq_id" required>

    <label>Issue Tracking ID (trac_id)</label>
    <input name="trac_id" required>

    <label>Cost (BDT)</label>
    <input name="cost" type="number" step="0.01" required>

    <button type="submit">Save</button>
  </form>

  <p class="hint">Note: eq_id must exist in Equipments, lto_id is auto from your login, trac_id must exist in Issues.</p>
</body>
</html>