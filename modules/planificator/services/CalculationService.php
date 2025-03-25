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
    
    /**
     * Projects school fees over time taking inflation into account
     * 
     * @param float $annualFee Base annual tuition fee
     * @param float $additionalExpenses Additional annual expenses
     * @param float $inflationRate Annual inflation rate as decimal (e.g., 0.02 for 2%)
     * @param int $years Number of years to project
     * @return array Projected fees for each year
     */
    public function projectSchoolFees($annualFee, $additionalExpenses, $inflationRate, $years) {
        $projections = [];
        
        for ($i = 0; $i < $years; $i++) {
            $inflationFactor = pow(1 + $inflationRate, $i);
            $yearTuition = $annualFee * $inflationFactor;
            $yearAdditional = $additionalExpenses * $inflationFactor;
            
            $projections[] = [
                'year' => date('Y') + $i,
                'tuition' => round($yearTuition, 2),
                'additional' => round($yearAdditional, 2),
                'total' => round($yearTuition + $yearAdditional, 2)
            ];
        }
        
        return $projections;
    }
    
    /**
     * Calculate total projected school fees over a period
     * 
     * @param float $annualFee Base annual tuition fee
     * @param float $additionalExpenses Additional annual expenses
     * @param float $inflationRate Annual inflation rate as decimal
     * @param int $years Number of years
     * @return float Total projected cost
     */
    public function calculateTotalSchoolFees($annualFee, $additionalExpenses, $inflationRate, $years) {
        $projections = $this->projectSchoolFees($annualFee, $additionalExpenses, $inflationRate, $years);
        $total = 0;
        
        foreach ($projections as $projection) {
            $total += $projection['total'];
        }
        
        return round($total, 2);
    }
    
    /**
     * Calculate remaining years in education based on current and target levels
     * 
     * @param string $currentLevel Current education level
     * @param string $targetLevel Target education level
     * @param int $age Current age
     * @return int Estimated years remaining
     */
    public function calculateRemainingEducationYears($currentLevel, $targetLevel, $age) {
        // French education system levels with typical ages and durations
        $educationLevels = [
            'maternelle' => ['start_age' => 3, 'end_age' => 5, 'duration' => 3],
            'primaire' => ['start_age' => 6, 'end_age' => 10, 'duration' => 5],
            'college' => ['start_age' => 11, 'end_age' => 14, 'duration' => 4],
            'lycee' => ['start_age' => 15, 'end_age' => 17, 'duration' => 3],
            'superieur' => ['start_age' => 18, 'end_age' => 22, 'duration' => 5]
        ];
        
        $levelOrder = ['maternelle', 'primaire', 'college', 'lycee', 'superieur'];
        $currentIndex = array_search($currentLevel, $levelOrder);
        $targetIndex = array_search($targetLevel, $levelOrder);
        
        if ($currentIndex === false || $targetIndex === false || $targetIndex < $currentIndex) {
            return 0;
        }
        
        $years = 0;
        
        // Calculate remaining years in current level
        $currentLevelData = $educationLevels[$currentLevel];
        $expectedAgeInCurrentLevel = $currentLevelData['start_age'];
        $yearsIntoCurrentLevel = $age - $expectedAgeInCurrentLevel;
        $yearsIntoCurrentLevel = max(0, min($yearsIntoCurrentLevel, $currentLevelData['duration']));
        $remainingInCurrentLevel = $currentLevelData['duration'] - $yearsIntoCurrentLevel;
        $years += $remainingInCurrentLevel;
        
        // Add years for subsequent levels until target
        for ($i = $currentIndex + 1; $i <= $targetIndex; $i++) {
            $levelKey = $levelOrder[$i];
            $years += $educationLevels[$levelKey]['duration'];
        }
        
        return $years;
    }

    public function simulateSchoolFees($annualFee, $years) {
        return $annualFee * $years;
    }
}
?>