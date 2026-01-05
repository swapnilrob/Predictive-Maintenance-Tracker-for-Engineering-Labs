<a class="btn" href="report_issue.php">Report Equipment Issue</a>

<?php
require_once __DIR__ . "/../includes/auth_guard.php";
$user = $_SESSION["user"];
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="container">
  <div class="topbar">
    <h2>Welcome, <?= htmlspecialchars($user["name"]) ?> (<?= htmlspecialchars($user["role"]) ?>)</h2>
    <a class="btn" href="../auth/logout.php">Logout</a>
  </div>

  <h3>Live Equipment Dashboard</h3>
  <p class="hint">Auto refresh every 5 seconds.</p>

  <table class="table" id="eqTable">
    <thead>
      <tr>
        <th>Serial</th>
        <th>Name</th>
        <th>Health</th>
        <th>Availability</th>
        <th>Status</th>
        <th>Room</th>
        <th>Location</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

<script>
async function loadEquipment(){
  const tbody = document.querySelector("#eqTable tbody");
  tbody.innerHTML = `<tr><td colspan="7">Loading...</td></tr>`;

  try {
    const res = await fetch("../api/equipment_status.php");
    const data = await res.json();

    if (data.error) {
      tbody.innerHTML = `<tr><td colspan="7">API Error: ${data.error}</td></tr>`;
      return;
    }

    if (data.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7">No equipment found.</td></tr>`;
      return;
    }

    tbody.innerHTML = "";
    data.forEach(e => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${e.serial_no}</td>
        <td>${e.name}</td>
        <td>${e.machine_health}</td>
        <td>${e.availability}</td>
        <td>${e.status}</td>
        <td>${e.room_no}</td>
        <td>${e.location}</td>
      `;
      tbody.appendChild(tr);
    });

  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="7">Fetch failed: ${err}</td></tr>`;
  }
}

loadEquipment();
setInterval(loadEquipment, 5000);
</script>

</body>
</html>
