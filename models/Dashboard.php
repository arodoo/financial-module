<?php
class Dashboard {
    private $totalIncome;
    private $totalExpenses;
    private $netSavings;
    private $assets;
    private $loans;

    public function __construct() {
        $this->totalIncome = 0;
        $this->totalExpenses = 0;
        $this->netSavings = 0;
        $this->assets = [];
        $this->loans = [];
    }

    public function calculateNetSavings() {
        $this->netSavings = $this->totalIncome - $this->totalExpenses;
        return $this->netSavings;
    }

    public function addIncome($amount) {
        $this->totalIncome += $amount;
    }

    public function addExpense($amount) {
        $this->totalExpenses += $amount;
    }

    public function addAsset($asset) {
        $this->assets[] = $asset;
    }

    public function addLoan($loan) {
        $this->loans[] = $loan;
    }

    public function getSummary() {
        return [
            'totalIncome' => $this->totalIncome,
            'totalExpenses' => $this->totalExpenses,
            'netSavings' => $this->calculateNetSavings(),
            'assets' => $this->assets,
            'loans' => $this->loans,
        ];
    }
}
?>