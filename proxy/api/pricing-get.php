<?php
require_once "cors.php";
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';

/**
 * 1. Check if the table has any rows
 */
$check = $conn->query("SELECT COUNT(*) AS count FROM ProxyPricing");
$rowCount = $check->fetch_assoc()['count'];

/**
 * 2. If empty → Insert initial defaults
 */
if ($rowCount == 0) {
    $conn->query("INSERT INTO ProxyPricing (type, country, costPerWeek, costPerMonth) VALUES
        ('IPv6', 'Canada', 0.04, 0.16),
        ('IPv4', 'Australia', 0.35, 1.50),
        ('Shared', 'France', 0.10, 0.40)
    ");
}

/**
 * 3. Now fetch all
 */
$sql = "SELECT * FROM ProxyPricing ORDER BY id ASC";
$res = $conn->query($sql);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = [
        "id" => (string)$row["id"],
        "type" => $row["type"],
        "country" => $row["country"],
        "costPerWeek" => (float)$row["costPerWeek"],
        "costPerMonth" => (float)$row["costPerMonth"],
    ];
}

echo json_encode($data);
?>
