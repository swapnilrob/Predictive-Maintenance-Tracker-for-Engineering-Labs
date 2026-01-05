<?php
require_once __DIR__ . "/../config/db.php";
header("Content-Type: application/json; charset=utf-8");

try {
  $stmt = $pdo->query("
    SELECT serial_no, name, machine_health, availability, status, room_no, location
    FROM Equipments
    ORDER BY serial_no
  ");
  echo json_encode($stmt->fetchAll());
} catch (Exception $e) {
  echo json_encode(["error" => $e->getMessage()]);
}
