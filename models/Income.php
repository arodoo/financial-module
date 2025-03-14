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
        $sql = "SELECT t.*, c.name as category_name 
                FROM income_transactions t 
                JOIN income_categories c ON t.category_id = c.id
                WHERE 1=1";
        $params = [];

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
        $stmt = $this->conn->prepare(
            "INSERT INTO income_transactions (category_id, amount, description, transaction_date) 
             VALUES (:category_id, :amount, :description, :transaction_date)"
        );
        
        return $stmt->execute([
            ':category_id' => $categoryId,
            ':amount' => $amount,
            ':description' => $description,
            ':transaction_date' => $transactionDate
        ]);
    }

    public function getTotalIncome($startDate = null, $endDate = null) {
        $sql = "SELECT SUM(amount) as total FROM income_transactions WHERE 1=1";
        $params = [];

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
}
?>