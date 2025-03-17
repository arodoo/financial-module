<?php
require_once __DIR__ . '/../config/database.php';

class Dashboard {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
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

    public function getTotalExpense($startDate = null, $endDate = null) {
        global $id_oo;
        
        $sql = "SELECT SUM(amount) as total FROM expense_transactions WHERE membre_id = :membre_id";
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

    public function getRecentTransactions($limit = 5) {
        global $id_oo;
        
        // Get recent income transactions
        $incomeQuery = "
            SELECT 
                i.id, 
                i.amount, 
                i.description, 
                i.transaction_date, 
                c.name as category,
                'income' as type
            FROM income_transactions i
            JOIN income_categories c ON i.category_id = c.id
            WHERE i.membre_id = :membre_id
            ORDER BY i.transaction_date DESC
            LIMIT :limit
        ";
        
        $incomeStmt = $this->conn->prepare($incomeQuery);
        $incomeStmt->bindValue(':membre_id', $id_oo, PDO::PARAM_INT);
        $incomeStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $incomeStmt->execute();
        $incomeTransactions = $incomeStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get recent expense transactions
        $expenseQuery = "
            SELECT 
                e.id, 
                e.amount, 
                e.description, 
                e.transaction_date, 
                c.name as category,
                'expense' as type
            FROM expense_transactions e
            JOIN expense_categories c ON e.category_id = c.id
            WHERE e.membre_id = :membre_id
            ORDER BY e.transaction_date DESC
            LIMIT :limit
        ";
        
        $expenseStmt = $this->conn->prepare($expenseQuery);
        $expenseStmt->bindValue(':membre_id', $id_oo, PDO::PARAM_INT);
        $expenseStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $expenseStmt->execute();
        $expenseTransactions = $expenseStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Combine and sort by date (newest first)
        $allTransactions = array_merge($incomeTransactions, $expenseTransactions);
        usort($allTransactions, function($a, $b) {
            return strtotime($b['transaction_date']) - strtotime($a['transaction_date']);
        });
        
        // Return only the specified limit
        return array_slice($allTransactions, 0, $limit);
    }

    public function getCategoryTotals($type = 'expense') {
        global $id_oo;
        
        if ($type == 'income') {
            $query = "
                SELECT c.name as category, SUM(t.amount) as total
                FROM income_transactions t
                JOIN income_categories c ON t.category_id = c.id
                WHERE t.membre_id = :membre_id
                GROUP BY t.category_id
                ORDER BY total DESC
            ";
        } else {
            $query = "
                SELECT c.name as category, SUM(t.amount) as total
                FROM expense_transactions t
                JOIN expense_categories c ON t.category_id = c.id
                WHERE t.membre_id = :membre_id
                GROUP BY t.category_id
                ORDER BY total DESC
            ";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':membre_id', $id_oo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>