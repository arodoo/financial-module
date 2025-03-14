<?php
class CalculationService {
    public function calculateTotalIncome(array $incomes) {
        return array_sum($incomes);
    }

    public function calculateTotalExpenses(array $expenses) {
        return array_sum($expenses);
    }

    public function calculateNetIncome(array $incomes, array $expenses) {
        return $this->calculateTotalIncome($incomes) - $this->calculateTotalExpenses($expenses);
    }

    public function simulateLoanPayment($principal, $annualInterestRate, $years) {
        $monthlyInterestRate = $annualInterestRate / 12 / 100;
        $numberOfPayments = $years * 12;
        $monthlyPayment = ($principal * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$numberOfPayments));
        return round($monthlyPayment, 2);
    }

    public function simulateSchoolFees($annualFee, $years) {
        return $annualFee * $years;
    }
}
?>