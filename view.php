<?php
require 'config.php';
require 'functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT tasks.*, categories.name AS category_name 
    FROM tasks 
    JOIN categories ON tasks.category_id = categories.id 
    WHERE tasks.id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$task = $stmt->fetch();

if (!$task) {
    echo "<div class='container mt-5 alert alert-danger'>Task not found or already deleted.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Task - <?= htmlspecialchars($task['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .task-card {
            max-width: 700px;
            margin: 50px auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 12px;
            background-color: #fff;
            overflow: hidden;
        }
        .task-image-wrapper {
            width: 100%;
            aspect-ratio: 4/3;
            overflow: hidden;
            /* border: 3px solid red; */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .task-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }
        .task-card .card-body {
            padding: 25px;
        }
        .btn-group {
            margin-top: 25px;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="task-card card">
    <?php if ($task['image'] && file_exists("uploads/" . $task['image'])): ?>
        <div class="task-image-wrapper">
            <img src="uploads/<?= htmlspecialchars($task['image']) ?>" alt="Task Image">
        </div>
    <?php endif; ?>
    <div class="card-body">
        <h3 class="card-title mb-3"><?= htmlspecialchars($task['title']) ?></h3>

        <p class="mb-2"><strong>📂 Category:</strong> <?= htmlspecialchars($task['category_name']) ?></p>
        <p class="mb-2"><strong>📅 Due Date:</strong> <?= htmlspecialchars($task['due_date']) ?></p>
        <hr>
        <p class="mb-0"><strong>📝 Description:</strong></p>
        <p class="text-muted"><?= nl2br(htmlspecialchars($task['description'])) ?></p>

        <div class="btn-group">
            <a href="index.php" class="btn btn-back">⬅ Back</a>
            <!-- <a href="add_edit.php?id=<?= $task['id'] ?>" class="btn btn-edit">✏️ Edit</a>
            <a href="delete.php?id=<?= $task['id'] ?>" onclick="return confirm('Are you sure to delete this task?');" class="btn btn-delete">🗑 Delete</a> -->
        </div>
    </div>
</div>

</body>
</html>
