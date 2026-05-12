<?php
require_once __DIR__ . '/model.php';

class Task extends Model {
    private $id;
    private $title;
    private $priority;
    private $status;

    public function __construct($id = null, $title = "", $priority = "Normal", $status = "active") {
        parent::__construct();
        $this->id = $id;
        $this->title = $title;
        $this->priority = $priority;
        $this->status = $status;

    }

    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getPriority() { return $this->priority; }
    public function getStatus() { return $this->status; }

    public function setTitle($title) { $this->title = $title; }
    public function setPriority($priority) { $this->priority = $priority; }
    public function setStatus($status) { $this->status = $status; }

    public function create() {
        $query = "INSERT INTO tasks (title, priority, status) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $this->title, $this->priority, $this->status);
        
        if ($stmt->execute()) {
            $this->id = $this->db->insert_id;
            return true;
        }
        return false;
    }

    public function complete() {
        $this->status = 'completed';
        $query = "UPDATE tasks SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $this->status, $this->id);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM tasks WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }

    public static function getTasksByStatus($status) {
        $db = Connect::getInstance()->getConnection();
        $query = "SELECT * FROM tasks WHERE status = ? ORDER BY id DESC";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = new Task(
                $row['id'],
                $row['title'],
                $row['priority'],
                $row['status']

            );
        }
        return $tasks;
    }

    public static function findById($id) {
        $db = Connect::getInstance()->getConnection();
        $query = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new Task(
                $row['id'],
                $row['title'],
                $row['priority'],
                $row['status'],

            );
        }
        return null;
    }
}
