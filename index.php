<?php
require 'config.php';
require 'functions.php';

// Get categories for filter and display
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

$filter_cat_id = $_GET['category'] ?? '';

if ($filter_cat_id && is_numeric($filter_cat_id)) {
    $stmt = $pdo->prepare("SELECT tasks.*, categories.name as category_name 
        FROM tasks 
        JOIN categories ON tasks.category_id = categories.id 
        WHERE tasks.category_id = ? AND tasks.deleted_at IS NULL
        ORDER BY due_date ASC");
    $stmt->execute([$filter_cat_id]);
} else {
    $stmt = $pdo->query("SELECT tasks.*, categories.name as category_name 
        FROM tasks 
        JOIN categories ON tasks.category_id = categories.id 
        WHERE tasks.deleted_at IS NULL
        ORDER BY due_date ASC");
}

$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
    <h1>🗂 Task Manager</h1>

    <div class="mb-3">
        <a href="add_edit.php" class="btn btn-success">➕ Add New Task</a>
        <a href="trash.php" class="btn btn-warning">🗑 View Trash</a>
        <a href="index.php" class="btn btn-info">📋 All Tasks</a>
        <a href="Category.php" class="btn btn-secondary">🗂 Categories</a>
    </div>

    <form method="GET" class="mb-4">
        <label for="category" class="form-label">Filter by Category:</label>
        <select name="category" id="category" class="form-select" onchange="this.form.submit()">
            <option value="">-- All Categories --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($filter_cat_id == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if (count($tasks) === 0): ?>
        <div class="alert alert-info">No tasks found.</div>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>📌 Title</th>
                    <th>📝 Description</th>
                    <th>📅 Due Date</th>
                    <th>📂 Category</th>
                    <th>🖼 Image</th>
                    <th>⚙️ Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars(shorten($task['description'], 80)) ?></td>
                    <td><?= htmlspecialchars($task['due_date']) ?></td>
                    <td><?= htmlspecialchars($task['category_name']) ?></td>
                    <td>
                        <?php if ($task['image'] && file_exists(__DIR__ . '/uploads/' . $task['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($task['image']) ?>" alt="Task Image" class="thumb" />
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="view.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-info">👁 View</a>
                        <a href="add_edit.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <a href="delete.php?id=<?= $task['id'] ?>" onclick="return confirm('Are you sure?');" class="btn btn-sm btn-danger">🗑 Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
