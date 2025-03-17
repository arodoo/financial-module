<?php
require_once __DIR__ . '/../models/Loan.php';
require_once __DIR__ . '/../models/Membre.php';

/**
 * Loan Controller
 * Handles loan simulation calculations and management
 */
class LoanController {
    private $loanModel;
    private $membreModel;
    private $calculationResults = null;

    public function __construct() {
        $this->loanModel = new Loan();
        $this->membreModel = new Membre();
    }

    /**
     * Calculate loan parameters based on input
     */
    public function calculateLoan($data) {
        $loanAmount = floatval(str_replace([' ', ','], ['', '.'], $data['loan_amount']));
        $interestRate = floatval($data['interest_rate']);
        $loanTermMonths = intval($data['loan_term']);
        
        // Validations
        if ($loanAmount <= 0 || $interestRate <= 0 || $loanTermMonths <= 0) {
            return false;
        }
        
        // Monthly interest rate (annual rate / 12 months / 100 to convert from percentage)
        $monthlyRate = ($interestRate / 100) / 12;
        
        // Monthly payment calculation using the formula:
        // M = P * (r * (1 + r)^n) / ((1 + r)^n - 1)
        $monthlyPayment = $loanAmount * 
                        ($monthlyRate * pow(1 + $monthlyRate, $loanTermMonths)) / 
                        (pow(1 + $monthlyRate, $loanTermMonths) - 1);
        
        // Total payment over the loan term
        $totalPayment = $monthlyPayment * $loanTermMonths;
        
        // Total interest paid
        $totalInterest = $totalPayment - $loanAmount;
        
        // Store results for later use
        $this->calculationResults = [
            'monthlyPayment' => $monthlyPayment,
            'totalPayment' => $totalPayment,
            'totalInterest' => $totalInterest,
            'loanTermMonths' => $loanTermMonths
        ];
        
        return $this->calculationResults;
    }
    
    /**
     * Get the loan calculation results
     */
    public function getCalculationResults() {
        return $this->calculationResults;
    }
    
    /**
     * Save a loan to the database
     */
    public function saveLoan($data) {
        global $id_oo;
        
        $loanData = [
            'membre_id' => $id_oo,
            'name' => $data['loan_name'],
            'amount' => floatval($data['loan_amount']),
            'interest_rate' => floatval($data['interest_rate']),
            'term' => intval($data['loan_term']),
            'monthly_payment' => floatval($data['monthly_payment']),
            'start_date' => $data['start_date'] ?? date('Y-m-d'),
            'asset_id' => !empty($data['asset_id']) ? intval($data['asset_id']) : null
        ];
        
        return $this->loanModel->saveLoan($loanData);
    }
    
    /**
     * Get all saved loans for the current member
     */
    public function getSavedLoans() {
        global $id_oo;
        return $this->loanModel->getLoansByMember($id_oo);
    }
    
    /**
     * Get a specific loan by ID
     */
    public function getLoan($loanId) {
        global $id_oo;
        return $this->loanModel->getLoan($loanId, $id_oo);
    }
    
    /**
     * Delete a loan
     */
    public function deleteLoan($loanId) {
        global $id_oo;
        return $this->loanModel->deleteLoan($loanId, $id_oo);
    }
    
    /**
     * Generate amortization schedule
     */
    public function generateAmortizationSchedule($loanAmount, $interestRate, $loanTerm) {
        $schedule = [];
        $balance = $loanAmount;
        $monthlyRate = ($interestRate / 100) / 12;
        
        // Calculate monthly payment
        $totalPayments = $loanTerm * 12;
        $monthlyPayment = $loanAmount * 
                        ($monthlyRate * pow(1 + $monthlyRate, $totalPayments)) / 
                        (pow(1 + $monthlyRate, $totalPayments) - 1);
        
        for ($month = 1; $month <= $totalPayments; $month++) {
            $interestPayment = $balance * $monthlyRate;
            $principalPayment = $monthlyPayment - $interestPayment;
            
            $balance -= $principalPayment;
            if ($balance < 0) $balance = 0;
            
            $schedule[] = [
                'month' => $month,
                'payment' => $monthlyPayment,
                'principal' => $principalPayment,
                'interest' => $interestPayment,
                'balance' => $balance
            ];
            
            if ($balance <= 0) break;
        }
        
        return $schedule;
    }
}
