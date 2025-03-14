<?php
class Loan {
    private $loanAmount;
    private $interestRate;
    private $loanTerm;
    private $monthlyPayment;

    public function __construct($loanAmount, $interestRate, $loanTerm) {
        $this->loanAmount = $loanAmount;
        $this->interestRate = $interestRate;
        $this->loanTerm = $loanTerm;
        $this->monthlyPayment = $this->calculateMonthlyPayment();
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
}
?>