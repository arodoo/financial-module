<?php
require_once __DIR__ . '/../models/Loan.php';
require_once __DIR__ . '/../models/Membre.php';
require_once __DIR__ . '/../services/CalculationService.php';
require_once __DIR__ . '/../config/database.php'; // Add this line to ensure database.php is included

class LoanController {
    private $loanModel;
    private $calculationService;
    private $calculationResults;
    private $loanDetails;
    private $currentUser;

    public function __construct() {
        global $id_oo;
        
        // Initialize models and services
        $this->loanModel = new Loan();
        $this->calculationService = new CalculationService();
        $this->calculationResults = null;
        $this->loanDetails = null;
        
        // Store current user ID
        $this->currentUser = ['id' => $id_oo];
    }

    /**
     * Process a loan calculation based on form input
     */
    public function calculateLoan($data) {
        $loanAmount = (float)str_replace(' ', '', $data['loan_amount']);
        $interestRate = (float)$data['interest_rate'];
        $loanTerm = (int)$data['loan_term']; // Term is in months in the UI
        
        // Convert interest rate to monthly
        $monthlyInterestRate = ($interestRate / 100) / 12;
        
        // Calculate monthly payment
        $monthlyPayment = 0;
        if ($monthlyInterestRate > 0) {
            $monthlyPayment = $loanAmount * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $loanTerm) / 
                             (pow(1 + $monthlyInterestRate, $loanTerm) - 1);
        } else {
            $monthlyPayment = $loanAmount / $loanTerm;
        }
        
        // Calculate total payment and interest
        $totalPayment = $monthlyPayment * $loanTerm;
        $totalInterest = $totalPayment - $loanAmount;
        
        // Store results
        $this->calculationResults = [
            'monthlyPayment' => $monthlyPayment,
            'totalPayment' => $totalPayment,
            'totalInterest' => $totalInterest,
            'loanAmount' => $loanAmount,
            'interestRate' => $interestRate,
            'loanTerm' => $loanTerm
        ];
        
        return true;
    }
    
    /**
     * Get saved loans for the current user
     */
    public function getSavedLoans() {
        if (!$this->currentUser['id']) return [];
        
        $loans = $this->loanModel->getLoans($this->currentUser['id']);
        
        // Convert term from years to months for UI
        foreach ($loans as &$loan) {
            $loan['term_months'] = $loan['term'] * 12;
        }
        
        return $loans;
    }

    /**
     * Save a new loan
     */
    public function saveLoan($data) {
        if (!$this->currentUser['id']) return false;
        
        $loanData = [
            'membre_id' => $this->currentUser['id'],
            'name' => $data['loan_name'],
            'amount' => str_replace(' ', '', $data['loan_amount']),
            'interest_rate' => (float)$data['interest_rate'],
            'term' => (float)$data['loan_term'] / 12, // Convert months to years for DB
            'monthly_payment' => (float)$data['monthly_payment'],
            'start_date' => $data['start_date'],
            'asset_id' => !empty($data['asset_id']) ? (int)$data['asset_id'] : null
        ];
        
        $loanId = $this->loanModel->saveLoan($loanData);
        
        if ($loanId && !empty($data['asset_id'])) {
            $this->loanModel->updateAssetLoanInfo(
                $data['asset_id'], 
                $loanId, 
                $loanData['amount'], 
                $loanData['monthly_payment']
            );
        }
        
        return $loanId;
    }
    
    /**
     * Update an existing loan
     */
    public function updateLoan($data) {
        if (!$this->currentUser['id']) return false;
        
        $loanId = (int)$data['loan_id'];
        $currentLoan = $this->getLoanById($loanId);
        
        // Prepare data for update
        $loanData = [
            'membre_id' => $this->currentUser['id'],
            'name' => $data['loan_name'],
            'amount' => str_replace(' ', '', $data['loan_amount']),
            'interest_rate' => (float)$data['interest_rate'],
            'term' => (float)$data['loan_term'] / 12, // Convert months to years for DB
            'start_date' => $data['start_date'],
            'asset_id' => !empty($data['asset_id']) ? (int)$data['asset_id'] : null
        ];
        
        // Recalculate monthly payment
        $monthlyInterestRate = ($loanData['interest_rate'] / 100) / 12;
        $termInMonths = $loanData['term'] * 12;
        
        if ($monthlyInterestRate > 0) {
            $loanData['monthly_payment'] = $loanData['amount'] * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $termInMonths) / 
                                          (pow(1 + $monthlyInterestRate, $termInMonths) - 1);
        } else {
            $loanData['monthly_payment'] = $loanData['amount'] / $termInMonths;
        }
        
        $result = $this->loanModel->updateLoan($loanId, $loanData);
        
        // Handle asset relationship updates
        if ($result) {
            // If previously had an asset but now doesn't, or has a different asset, clear the old asset
            if ($currentLoan && isset($currentLoan['asset_id']) && $currentLoan['asset_id'] && 
                (!$loanData['asset_id'] || $currentLoan['asset_id'] != $loanData['asset_id'])) {
                $this->loanModel->clearAssetLoanInfo($currentLoan['asset_id']);
            }
            
            // If has a new asset, update asset info
            if ($loanData['asset_id']) {
                $this->loanModel->updateAssetLoanInfo(
                    $loanData['asset_id'], 
                    $loanId, 
                    $loanData['amount'], 
                    $loanData['monthly_payment']
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Delete a loan
     */
    public function deleteLoan($loanId) {
        if (!$this->currentUser['id']) return false;
        
        $loanId = (int)$loanId;
        $loan = $this->getLoanById($loanId);
        
        // Clear asset relationship if exists
        if ($loan && isset($loan['asset_id']) && $loan['asset_id']) {
            $this->loanModel->clearAssetLoanInfo($loan['asset_id']);
        }
        
        return $this->loanModel->deleteLoan($loanId, $this->currentUser['id']);
    }
    
    /**
     * Get data for the view
     */
    public function getViewData() {
        $data = [];
        
        // Get all saved loans for the current user
        $data['loans'] = $this->getSavedLoans();
        
        // Check if a specific loan is requested to view
        if (isset($_GET['view_loan'])) {
            $loanId = (int)$_GET['view_loan'];
            $loan = $this->getLoanById($loanId);
            
            if ($loan) {
                // Convert term from years to months for UI display
                $loan['term'] = $loan['term'] * 12;
                $data['viewLoan'] = $loan;
            }
        }
        
        // Check if a specific loan is requested to edit
        if (isset($_GET['edit_loan'])) {
            $loanId = (int)$_GET['edit_loan'];
            $loan = $this->getLoanById($loanId);
            
            if ($loan) {
                // Convert term from years to months for UI display
                $loan['term'] = $loan['term'] * 12;
                $data['editLoan'] = $loan;
            }
        }
        
        // Include calculation results if available
        if ($this->calculationResults) {
            $data['calculationResults'] = $this->calculationResults;
        }
        
        return $data;
    }
    
    /**
     * Get loan by ID
     */
    public function getLoanById($id) {
        if (!$this->currentUser['id']) return null;
        
        return $this->loanModel->getLoan($id, $this->currentUser['id']);
    }
    
    /**
     * Calculate loan details for a specific loan
     */
    public function calculateLoanDetails($loan) {
        if (!$loan) return;
        
        $balance = $loan['amount'];
        $term = $loan['term']; // Term is in months here (already converted in getViewData)
        $monthlyRate = ($loan['interest_rate'] / 100) / 12;
        $monthlyPayment = $loan['monthly_payment'];
        
        // Calculate months elapsed since loan start
        $startDate = new DateTime($loan['start_date']);
        $today = new DateTime();
        $monthsElapsed = (($today->format('Y') - $startDate->format('Y')) * 12) + 
                         ($today->format('n') - $startDate->format('n'));
        $monthsElapsed = max(0, min($monthsElapsed, $term));
        
        $principalPaid = 0;
        $interestPaid = 0;
        $currentBalance = $balance;
        
        // Calculate principal and interest paid so far
        for ($i = 0; $i < $monthsElapsed; $i++) {
            $interestForMonth = $currentBalance * $monthlyRate;
            $principalForMonth = $monthlyPayment - $interestForMonth;
            
            $principalPaid += $principalForMonth;
            $interestPaid += $interestForMonth;
            $currentBalance -= $principalForMonth;
            
            if ($currentBalance <= 0) {
                $currentBalance = 0;
                break;
            }
        }
        
        $this->loanDetails = [
            'currentBalance' => $currentBalance,
            'principalPaid' => $principalPaid,
            'interestPaid' => $interestPaid,
            'monthsElapsed' => $monthsElapsed,
            'monthsRemaining' => $term - $monthsElapsed
        ];
    }
    
    /**
     * Get loan details
     */
    public function getLoanDetails() {
        return $this->loanDetails;
    }
    
    /**
     * Get calculation results
     */
    public function getCalculationResults() {
        return $this->calculationResults;
    }
}
?>
