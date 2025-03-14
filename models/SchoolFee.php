<?php
class SchoolFee {
    private $feeStructure;
    private $paymentHistory;

    public function __construct() {
        $this->feeStructure = [];
        $this->paymentHistory = [];
    }

    public function setFeeStructure($year, $amount) {
        $this->feeStructure[$year] = $amount;
    }

    public function getFeeStructure() {
        return $this->feeStructure;
    }

    public function addPayment($year, $amount, $date) {
        $this->paymentHistory[] = [
            'year' => $year,
            'amount' => $amount,
            'date' => $date
        ];
    }

    public function getPaymentHistory() {
        return $this->paymentHistory;
    }

    public function simulateFees($years) {
        $simulation = [];
        foreach ($years as $year) {
            $amount = isset($this->feeStructure[$year]) ? $this->feeStructure[$year] : 0;
            $simulation[$year] = $amount;
        }
        return $simulation;
    }
}
?>