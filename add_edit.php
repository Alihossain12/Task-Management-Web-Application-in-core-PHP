<?php
require 'config.php';
require 'functions.php';

$errors = [];
$id = $_GET['id'] ?? null;

$title = '';
$description = '';
$due_date = '';
$category_id = '';
$current_image = null;

if ($id) {
    // Fetch existing task data for editing
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch();
    if (!$task) {
        die("Task not found.");
    }
    $title = $task['title'];
    $description = $task['description'];
    $due_date = $task['due_date'];
    $category_id = $task['category_id'];
    $current_image = $task['image'];
}

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
    $category_id = $_POST['category'] ?? '';

    // Validate inputs
    if (!$title) $errors['title'] = "Title is required.";
    if (!$description) $errors['description'] = "Description is required.";
    if (!$due_date) $errors['due_date'] = "Due date is required.";
    elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due_date)) $errors['due_date'] = "Due date format invalid.";
    if (!$category_id || !is_numeric($category_id)) $errors['category'] = "Category is required.";

    // Validate image upload
    $imageError = validateImage($_FILES['image'] ?? []);
    if ($imageError) $errors['image'] = $imageError;

    if (empty($errors)) {
        // Handle image upload if new image uploaded
        $image_filename = $current_image; // default to current image on edit
        if (!empty($_FILES['image']['name'])) {
            // Delete old image file if exists and editing
            if ($current_image && file_exists(__DIR__ . '/uploads/' . $current_image)) {
                unlink(__DIR__ . '/uploads/' . $current_image);
            }
            $uploaded = uploadImage($_FILES['image']);
            if ($uploaded === false) {
                $errors['image'] = "Failed to upload image.";
            } else {
                $image_filename = $uploaded;
            }
        }

        if (empty($errors)) {
            if ($id) {
                // Update existing task
                $stmt = $pdo->prepare("UPDATE tasks SET title=?, description=?, due_date=?, category_id=?, image=? WHERE id=?");
                $stmt->execute([$title, $description, $due_date, $category_id, $image_filename, $id]);
                header("Location: index.php");
                exit;
            } else {
                // Insert new task
                $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, category_id, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $due_date, $category_id, $image_filename]);
                header("Location: index.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title><?= $id ? 'Edit' : 'Add' ?> Task</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
<div class="container mt-4">
    <h1><?= $id ? 'Edit' : 'Add New' ?> Task</h1>
    <a href="index.php" class="btn btn-secondary mb-3">Back to Task List</a>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Task Title *</label>
            <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description *</label>
            <textarea id="description" name="description" class="form-control" rows="4" required><?= htmlspecialchars($description) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="due_date" class="form-label">Due Date *</label>
            <input type="date" id="due_date" name="due_date" class="form-control" value="<?= htmlspecialchars($due_date) ?>" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category *</label>
            <select name="category" id="category" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $category_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Task Image (JPG/PNG, max 2MB)</label>
            <input type="file" id="image" name="image" class="form-control" accept=".jpg,.jpeg,.png">
            <?php if ($current_image && file_exists(__DIR__ . '/uploads/' . $current_image)): ?>
                <img src="uploads/<?= htmlspecialchars($current_image) ?>" alt="Current Image" style="max-width:120px; margin-top:10px;">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-success"><?= $id ? 'Update' : 'Add' ?> Task</button>
    </form>
</div>
</body>
</html>
