<?php
require 'config.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: trash.php');
    exit;
}

// Set deleted_at to NULL to restore
$stmt = $pdo->prepare("UPDATE tasks SET deleted_at = NULL WHERE id = ?");
$stmt->execute([$id]);

header('Location: trash.php');
exit;
