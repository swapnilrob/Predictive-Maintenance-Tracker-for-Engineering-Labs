<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . "/../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"] ?? "");
  $id    = trim($_POST["id"] ?? "");

  $stmt = $pdo->prepare("SELECT id, email, name, phone FROM User WHERE id = ? AND email = ?");
  $stmt->execute([$id, $email]);
  $u = $stmt->fetch();

  if (!$u) {
    $error = "Invalid ID or Email";
  } else {
    // Detect role based on which role-table contains the id
    $role = "student";

    $checks = [
      "admin"   => "SELECT id FROM Admin WHERE id=?",
      "lto"     => "SELECT id FROM LTO WHERE id=?",
      "faculty" => "SELECT id FROM Faculty WHERE id=?",
      "student" => "SELECT id FROM Student WHERE id=?",
    ];

    foreach ($checks as $r => $q) {
      $st = $pdo->prepare($q);
      $st->execute([$u["id"]]);
      if ($st->fetch()) { $role = $r; break; }
    }

    $_SESSION["user"] = [
      "id"    => $u["id"],
      "name"  => $u["name"],
      "email" => $u["email"],
      "role"  => $role
    ];

    // Redirect by role
    if ($role === "admin") header("Location: ../admin/dashboard.php");
    elseif ($role === "lto") header("Location: ../lto/dashboard.php");
    else header("Location: ../user/dashboard.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <h2>Login</h2>

  <?php if ($error): ?>
    <div class="alert"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" class="card">
    <label>User ID</label>
    <input name="id" required>

    <label>Email</label>
    <input name="email" type="email" required>

    <button type="submit">Login</button>
  </form>

  <p class="hint">Example Admin: ID 11100001, Email rahim.admin@bracu.ac.bd</p>
</body>
</html>
