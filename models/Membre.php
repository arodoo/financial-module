<?php
require_once __DIR__ . '/../config/database.php';

class Membre {
    private $conn;
    
    public function __construct() {
        $this->conn = getDbConnection();
    }
    
    public function getMembre($id) {
        $stmt = $this->conn->prepare("SELECT * FROM membres WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllMembres() {
        $stmt = $this->conn->prepare("SELECT * FROM membres ORDER BY nom, prenom");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
