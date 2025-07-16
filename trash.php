<?php
require 'config.php';

// Fetch soft-deleted tasks
$stmt = $pdo->query("SELECT tasks.*, categories.name AS category_name 
                     FROM tasks 
                     JOIN categories ON tasks.category_id = categories.id
                     WHERE deleted_at IS NOT NULL
                     ORDER BY deleted_at DESC");
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Trash - Deleted Tasks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
    img.thumb {
        max-width: 80px;
        max-height: 60px;
        object-fit: cover;
    }
</style>
</head>
<body>
<div class="container mt-4">
    <h1>Trash - Deleted Tasks</h1>
    <a href="index.php" class="btn btn-secondary mb-3">Back to Task List</a>

    <?php if (count($tasks) === 0): ?>
        <div class="alert alert-info">Trash is empty.</div>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Category</th>
                    <th>Deleted At</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars(substr($task['description'], 0, 80)) ?>...</td>
                    <td><?= htmlspecialchars($task['due_date']) ?></td>
                    <td><?= htmlspecialchars($task['category_name']) ?></td>
                    <td><?= htmlspecialchars($task['deleted_at']) ?></td>
                    <td>
                        <?php if ($task['image'] && file_exists(__DIR__ . '/uploads/' . $task['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($task['image']) ?>" alt="Task Image" class="thumb" />
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="restore.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-success"
                           onclick="return confirm('Restore this task?');">Restore</a>
                        <a href="permanent_delete.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Permanently delete this task? This action cannot be undone.');">Delete Permanently</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
