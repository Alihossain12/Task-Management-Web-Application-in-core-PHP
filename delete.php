<?php
// require 'config.php';

// $id = $_GET['id'] ?? null;
// if (!$id || !is_numeric($id)) {
//     header('Location: index.php');
//     exit;
// }

// // Fetch image filename to delete from filesystem
// $stmt = $pdo->prepare("SELECT image FROM tasks WHERE id = ?");
// $stmt->execute([$id]);
// $task = $stmt->fetch();

// if ($task) {
//     if ($task['image'] && file_exists(__DIR__ . '/uploads/' . $task['image'])) {
//         unlink(__DIR__ . '/uploads/' . $task['image']);
//     }

//     $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
//     $stmt->execute([$id]);
// }

// header('Location: index.php');
// exit;






require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("UPDATE tasks SET deleted_at = NOW() WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;


