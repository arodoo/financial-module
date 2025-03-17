<?php
require_once __DIR__ . '/../config/database.php';

class Loan {
    private $conn;
    private $loanAmount;
    private $interestRate;
    private $loanTerm;
    private $monthlyPayment;

    public function __construct($loanAmount = null, $interestRate = null, $loanTerm = null) {
        $this->conn = getDbConnection();
        
        if ($loanAmount && $interestRate && $loanTerm) {
            $this->loanAmount = $loanAmount;
            $this->interestRate = $interestRate;
            $this->loanTerm = $loanTerm;
            $this->monthlyPayment = $this->calculateMonthlyPayment();
        }
    }

    private function calculateMonthlyPayment() {
        $monthlyRate = $this->interestRate / 100 / 12;
        $numberOfPayments = $this->loanTerm * 12;
        return ($this->loanAmount * $monthlyRate) / (1 - pow(1 + $monthlyRate, -$numberOfPayments));
    }

    public function getMonthlyPayment() {
        return round($this->monthlyPayment, 2);
    }

    public function getTotalPayment() {
        return round($this->getMonthlyPayment() * $this->loanTerm * 12, 2);
    }

    public function getTotalInterest() {
        return round($this->getTotalPayment() - $this->loanAmount, 2);
    }
    
    /**
     * Save loan information to database
     */
    public function saveLoan($data) {
        $query = "INSERT INTO loans 
                 (membre_id, name, amount, interest_rate, term, monthly_payment, start_date, asset_id, created_at) 
                 VALUES 
                 (:membre_id, :name, :amount, :interest_rate, :term, :monthly_payment, :start_date, :asset_id, NOW())";
                 
        $stmt = $this->conn->prepare($query);
        
        $params = [
            ':membre_id' => $data['membre_id'],
            ':name' => $data['name'],
            ':amount' => $data['amount'],
            ':interest_rate' => $data['interest_rate'],
            ':term' => $data['term'],
            ':monthly_payment' => $data['monthly_payment'],
            ':start_date' => $data['start_date'],
            ':asset_id' => $data['asset_id']
        ];
        
        $result = $stmt->execute($params);
        
        if ($result) {
            // If this loan is linked to an asset, update the asset
            if (!empty($data['asset_id'])) {
                $this->linkLoanToAsset($this->conn->lastInsertId(), $data['asset_id'], $data);
            }
            
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Link a loan to an asset (for mortgages)
     */
    private function linkLoanToAsset($loanId, $assetId, $loanData) {
        // Update the asset with loan information
        $query = "UPDATE assets 
                 SET loan_id = :loan_id, 
                     loan_amount = :loan_amount, 
                     loan_monthly_payment = :monthly_payment 
                 WHERE id = :asset_id";
                 
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':loan_id' => $loanId,
            ':loan_amount' => $loanData['amount'],
            ':monthly_payment' => $loanData['monthly_payment'],
            ':asset_id' => $assetId
        ]);
    }
    
    /**
     * Get loans by member
     */
    public function getLoansByMember($membreId) {
        $query = "SELECT l.*, a.name as asset_name 
                 FROM loans l 
                 LEFT JOIN assets a ON l.asset_id = a.id
                 WHERE l.membre_id = :membre_id 
                 ORDER BY l.created_at DESC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':membre_id', $membreId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get specific loan by ID for a member
     */
    public function getLoan($loanId, $membreId) {
        $query = "SELECT l.*, a.name as asset_name 
                 FROM loans l 
                 LEFT JOIN assets a ON l.asset_id = a.id
                 WHERE l.id = :loan_id AND l.membre_id = :membre_id";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':loan_id', $loanId, PDO::PARAM_INT);
        $stmt->bindValue(':membre_id', $membreId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Delete a loan
     */
    public function deleteLoan($loanId, $membreId) {
        // First, remove any asset associations
        $query = "UPDATE assets SET loan_id = NULL, loan_amount = NULL, loan_monthly_payment = NULL 
                 WHERE loan_id = :loan_id";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':loan_id', $loanId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Then delete the loan
        $query = "DELETE FROM loans WHERE id = :loan_id AND membre_id = :membre_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':loan_id', $loanId, PDO::PARAM_INT);
        $stmt->bindValue(':membre_id', $membreId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>