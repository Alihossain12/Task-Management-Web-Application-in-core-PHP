<?php
class Task {
    private $conn;

    public $title;
    public $description;
    public $due_date;
    public $category_id;
    public $image;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO tasks (title, description, due_date, category_id, image)
                  VALUES (:title, :description, :due_date, :category_id, :image)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':due_date' => $this->due_date,
            ':category_id' => $this->category_id,
            ':image' => $this->image
        ]);
    }

    public function getAll($categoryId = null, $includeDeleted = false) {
        $query = "SELECT tasks.*, categories.name as category_name FROM tasks
                  JOIN categories ON tasks.category_id = categories.id";

        $conditions = [];
        if (!$includeDeleted) {
            $conditions[] = "tasks.deleted = 0";
        }
        if ($categoryId) {
            $conditions[] = "tasks.category_id = :category_id";
        }

        if ($conditions) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);

        if ($categoryId) {
            $stmt->bindParam(':category_id', $categoryId);
        }

        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id) {
        $query = "UPDATE tasks SET title = :title, description = :description, due_date = :due_date, category_id = :category_id, image = :image WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':due_date' => $this->due_date,
            ':category_id' => $this->category_id,
            ':image' => $this->image,
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function softDelete($id) {
        $stmt = $this->conn->prepare("UPDATE tasks SET deleted = 1 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function restore($id) {
        $stmt = $this->conn->prepare("UPDATE tasks SET deleted = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}