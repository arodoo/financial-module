<?php
require_once __DIR__ . '/../config/database.php';

class Loan {
    private $conn;
    private $table = 'loans';

    public function __construct() {
        // Initialize database connection
        $this->conn = getDbConnection();
    }
    
    /**
     * Get all loans for a member
     */
    public function getLoans($membre_id) {
        $query = "SELECT * FROM {$this->table} WHERE membre_id = :membre_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':membre_id', $membre_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a loan by ID
     */
    public function getLoan($id, $membre_id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id AND membre_id = :membre_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':membre_id', $membre_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Save a new loan
     */
    public function saveLoan($data) {
        $query = "INSERT INTO {$this->table} 
                 (membre_id, name, amount, interest_rate, term, monthly_payment, start_date, asset_id) 
                 VALUES 
                 (:membre_id, :name, :amount, :interest_rate, :term, :monthly_payment, :start_date, :asset_id)";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $name = htmlspecialchars(strip_tags($data['name']));
        $amount = (float)$data['amount'];
        $interest_rate = (float)$data['interest_rate'];
        $term = (float)$data['term']; // Term in years
        $monthly_payment = (float)$data['monthly_payment'];
        $start_date = $data['start_date'];
        $asset_id = !empty($data['asset_id']) ? (int)$data['asset_id'] : null;
        
        // Bind parameters
        $stmt->bindParam(':membre_id', $data['membre_id']);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':interest_rate', $interest_rate);
        $stmt->bindParam(':term', $term);
        $stmt->bindParam(':monthly_payment', $monthly_payment);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':asset_id', $asset_id);
        
        // Execute query
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update an existing loan
     */
    public function updateLoan($id, $data) {
        $query = "UPDATE {$this->table} SET
                  name = :name,
                  amount = :amount,
                  interest_rate = :interest_rate,
                  term = :term,
                  monthly_payment = :monthly_payment,
                  start_date = :start_date,
                  asset_id = :asset_id
                  WHERE id = :id AND membre_id = :membre_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $name = htmlspecialchars(strip_tags($data['name']));
        $amount = (float)$data['amount'];
        $interest_rate = (float)$data['interest_rate'];
        $term = (float)$data['term']; // Term in years
        $monthly_payment = (float)$data['monthly_payment'];
        $start_date = $data['start_date'];
        $asset_id = !empty($data['asset_id']) ? (int)$data['asset_id'] : null;
        
        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':interest_rate', $interest_rate);
        $stmt->bindParam(':term', $term);
        $stmt->bindParam(':monthly_payment', $monthly_payment);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':asset_id', $asset_id);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':membre_id', $data['membre_id']);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Delete a loan
     */
    public function deleteLoan($id, $membre_id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id AND membre_id = :membre_id";
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':membre_id', $membre_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Update asset with loan information
     */
    public function updateAssetLoanInfo($assetId, $loanId, $loanAmount, $monthlyPayment) {
        $query = "UPDATE assets SET loan_id = :loan_id, loan_amount = :loan_amount, loan_monthly_payment = :monthly_payment WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':loan_id', $loanId);
        $stmt->bindParam(':loan_amount', $loanAmount);
        $stmt->bindParam(':monthly_payment', $monthlyPayment);
        $stmt->bindParam(':id', $assetId);
        
        // Execute query
        return $stmt->execute();
    }
    
    /**
     * Clear loan information from an asset
     */
    public function clearAssetLoanInfo($assetId) {
        $query = "UPDATE assets SET loan_id = NULL, loan_amount = NULL, loan_monthly_payment = NULL WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':id', $assetId);
        
        // Execute query
        return $stmt->execute();
    }
}
?>