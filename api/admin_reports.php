<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../middleware/admin.php");

header("Content-Type: application/json");
$db = (new Database())->getConnection();
adminOnly();

$users = $db->query("SELECT COUNT(*) as total_users FROM users")->fetch(PDO::FETCH_ASSOC);
$jobs = $db->query("SELECT COUNT(*) as total_jobs FROM jobs")->fetch(PDO::FETCH_ASSOC);
$apps = $db->query("SELECT COUNT(*) as total_apps FROM applications")->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "users" => (int)$users["total_users"],
    "jobs" => (int)$jobs["total_jobs"],
    "applications" => (int)$apps["total_apps"]
]);