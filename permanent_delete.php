<?php
require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: trash.php');
    exit;
}

$id = (int)$_GET['id'];

// Get image filename to delete from disk
$stmt = $pdo->prepare("SELECT image FROM tasks WHERE id = ?");
$stmt->execute([$id]);
$image = $stmt->fetchColumn();

if ($image && file_exists("uploads/" . $image)) {
    unlink("uploads/" . $image);
}

// Delete record from DB
$stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->execute([$id]);

header('Location: trash.php');
exit;
