<?php
require_once __DIR__ . "/../includes/auth_guard.php";
require_once __DIR__ . "/../config/db.php";
if ($_SESSION["user"]["role"] !== "admin") { die("Access denied"); }

$msg = "";
$error = "";

// Load labs for dropdown (if you inserted Lab rows later)
$labs = [];
try {
  $labs = $pdo->query("SELECT room_no FROM Lab ORDER BY room_no")->fetchAll();
} catch (Exception $e) { }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $serial_no = (int)$_POST["serial_no"];
    $name = trim($_POST["name"]);
    $status = trim($_POST["status"]);
    $location = trim($_POST["location"]);
    $machine_health = trim($_POST["machine_health"]);
    $manufacturer = trim($_POST["manufacturer"]);
    $install_date = $_POST["install_date"];
    $availability = trim($_POST["availability"]);
    $room_no = trim($_POST["room_no"]);

    $stmt = $pdo->prepare("
      INSERT INTO Equipments
      (serial_no, name, status, location, machine_health, manufacturer, install_date, availability, room_no)
      VALUES (?,?,?,?,?,?,?,?,?)
    ");
    $stmt->execute([$serial_no,$name,$status,$location,$machine_health,$manufacturer,$install_date,$availability,$room_no]);

    $msg = "Equipment added successfully!";
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Equipment</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="topbar">
    <h2>Add Equipment</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="POST" class="card">
    <label>Serial No</label>
    <input name="serial_no" required>

    <label>Name</label>
    <input name="name" required>

    <label>Status</label>
    <input name="status" placeholder="Operational / Faulty" required>

    <label>Location</label>
    <input name="location" placeholder="Lab corner / Rack..." required>

    <label>Machine Health</label>
    <input name="machine_health" placeholder="Good / Fair / Poor" required>

    <label>Manufacturer</label>
    <input name="manufacturer" required>

    <label>Install Date</label>
    <input name="install_date" type="date" required>

    <label>Availability</label>
    <input name="availability" placeholder="Available / In Use" required>

    <label>Room No</label>
    <?php if (count($labs) > 0): ?>
      <select name="room_no" required>
        <?php foreach ($labs as $l): ?>
          <option value="<?= htmlspecialchars($l["room_no"]) ?>"><?= htmlspecialchars($l["room_no"]) ?></option>
        <?php endforeach; ?>
      </select>
    <?php else: ?>
      <input name="room_no" placeholder="e.g., 301" required>
      <p class="hint">No Lab rows found yet. Insert Lab dataset later or manually type a room number that exists.</p>
    <?php endif; ?>

    <button type="submit">Add</button>
  </form>
</body>
</html>
