<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";

$role = $_SESSION["user"]["role"];
if ($role !== "student" && $role !== "faculty") die("Access denied");

$msg=""; $err="";

$equipments = $pdo->query("SELECT serial_no, name FROM Equipments ORDER BY serial_no")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $serial = (int)$_POST["serial_no"];
    $desc = trim($_POST["desc"]);

    $trac = (int)date("ymdHis"); // unique tracking id
    $text = "REPORTED by {$role} ({$_SESSION['user']['id']}): Equipment {$serial} - {$desc}";

    $st = $pdo->prepare("INSERT INTO Issues (trac_id, prediction) VALUES (?,?)");
    $st->execute([$trac, $text]);

    $msg = "Issue reported successfully. Tracking ID: $trac";
  } catch (Exception $e) {
    $err = $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Report Issue</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="topbar">
    <h2>Report Equipment Issue</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <?php if($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if($err): ?><div class="alert"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <form method="POST" class="card">
    <label>Select Equipment</label>
    <select name="serial_no" required>
      <?php foreach($equipments as $e): ?>
        <option value="<?= $e["serial_no"] ?>">
          <?= $e["serial_no"] ?> - <?= htmlspecialchars($e["name"]) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Describe the problem</label>
    <textarea name="desc" required></textarea>

    <button type="submit">Submit Report</button>
  </form>
</body>
</html>
