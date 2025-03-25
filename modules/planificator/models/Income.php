<?php
require_once __DIR__ . '/../config/database.php';

class Income {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }

    public function getAllCategories() {
        $stmt = $this->conn->prepare("SELECT * FROM income_categories ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncomeTransactions($startDate = null, $endDate = null, $categoryId = null) {
        global $id_oo;
        
        $sql = "SELECT t.*, c.name as category_name 
                FROM income_transactions t 
                JOIN income_categories c ON t.category_id = c.id
                WHERE t.membre_id = :membre_id";
        $params = [':membre_id' => $id_oo];

        if ($startDate) {
            $sql .= " AND t.transaction_date >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND t.transaction_date <= :end_date";
            $params[':end_date'] = $endDate;
        }

        if ($categoryId) {
            $sql .= " AND t.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $sql .= " ORDER BY t.transaction_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addIncome($categoryId, $amount, $description, $transactionDate) {
        global $id_oo;
        
        $stmt = $this->conn->prepare(
            "INSERT INTO income_transactions (membre_id, category_id, amount, description, transaction_date) 
             VALUES (:membre_id, :category_id, :amount, :description, :transaction_date)"
        );
        
        return $stmt->execute([
            ':membre_id' => $id_oo,
            ':category_id' => $categoryId,
            ':amount' => $amount,
            ':description' => $description,
            ':transaction_date' => $transactionDate
        ]);
    }

    public function getTotalIncome($startDate = null, $endDate = null) {
        global $id_oo;
        
        $sql = "SELECT SUM(amount) as total FROM income_transactions WHERE membre_id = :membre_id";
        $params = [':membre_id' => $id_oo];

        if ($startDate) {
            $sql .= " AND transaction_date >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND transaction_date <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getTransactionById($id) {
        global $id_oo;
        
        $stmt = $this->conn->prepare(
            "SELECT t.*, c.name as category_name 
             FROM income_transactions t 
             JOIN income_categories c ON t.category_id = c.id
             WHERE t.id = :id AND t.membre_id = :membre_id"
        );
        
        $stmt->execute([
            ':id' => $id,
            ':membre_id' => $id_oo
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateIncome($id, $categoryId, $amount, $description, $transactionDate) {
        global $id_oo;
        
        $stmt = $this->conn->prepare(
            "UPDATE income_transactions 
             SET category_id = :category_id, 
                 amount = :amount, 
                 description = :description, 
                 transaction_date = :transaction_date 
             WHERE id = :id AND membre_id = :membre_id"
        );
        
        return $stmt->execute([
            ':id' => $id,
            ':membre_id' => $id_oo,
            ':category_id' => $categoryId,
            ':amount' => $amount,
            ':description' => $description,
            ':transaction_date' => $transactionDate
        ]);
    }

    public function deleteIncome($id) {
        global $id_oo;
        
        $stmt = $this->conn->prepare(
            "DELETE FROM income_transactions 
             WHERE id = :id AND membre_id = :membre_id"
        );
        
        return $stmt->execute([
            ':id' => $id,
            ':membre_id' => $id_oo
        ]);
    }
}
?>