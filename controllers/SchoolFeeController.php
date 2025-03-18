<?php
/**
 * School Fee Controller
 */
require_once __DIR__ . '/../models/SchoolFee.php';
require_once __DIR__ . '/../services/CalculationService.php';

class SchoolFeeController {
    private $schoolFeeModel;
    private $calculationService;
    private $calculationResults;

    public function __construct() {
        $this->schoolFeeModel = new SchoolFee();
        $this->calculationService = new CalculationService();
        $this->calculationResults = null;
    }

    public function index() {
        // Display school fee view
        include_once __DIR__ . '/../modules/school-fee-simulator/index.php';
    }

    public function projections() {
        // Handle the logic for school fee projections
        $data = $this->schoolFeeModel->getProjections();
        include_once '../views/school-fee/projections.php';
    }

    public function simulate() {
        // Simulate school fee payments based on user input
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = $_POST['school_fee_data'];
            $simulationResult = $this->schoolFeeModel->simulateFees($inputData);
            include_once '../views/school-fee/projections.php';
        } else {
            // Redirect to the index if not a POST request
            header('Location: index.php');
        }
    }

    public function calculateFees($data) {
        // Validate inputs
        if (!$this->validateFeeInputs($data)) {
            return false;
        }
        
        // Get current age from birthdate
        $birthdate = new DateTime($data['child_birthdate']);
        $today = new DateTime();
        $age = $birthdate->diff($today)->y;
        
        // Get education levels and calculate years remaining
        $currentLevel = $data['current_level'];
        $expectedGraduationLevel = $data['expected_graduation_level'];
        
        // French education system levels with typical ages and durations
        $educationLevels = [
            'maternelle' => ['start_age' => 3, 'end_age' => 5, 'duration' => 3],
            'primaire' => ['start_age' => 6, 'end_age' => 10, 'duration' => 5],
            'college' => ['start_age' => 11, 'end_age' => 14, 'duration' => 4],
            'lycee' => ['start_age' => 15, 'end_age' => 17, 'duration' => 3],
            'superieur' => ['start_age' => 18, 'end_age' => 22, 'duration' => 5]
        ];
        
        // Calculate years remaining in education
        $yearsRemaining = 0;
        $currentLevelFound = false;
        $yearlyProjections = [];
        
        $annualTuition = str_replace(' ', '', $data['annual_tuition']);
        $additionalExpenses = str_replace(' ', '', $data['additional_expenses'] ?? 0);
        $inflationRate = $data['inflation_rate'] / 100;
        
        // Total cost calculation
        $totalCost = 0;
        $currentYear = date('Y');
        $currentLevelIndex = $this->getLevelIndex($currentLevel);
        $targetLevelIndex = $this->getLevelIndex($expectedGraduationLevel);
        
        // Calculate years remaining and prepare yearly projections
        for ($levelIndex = $currentLevelIndex; $levelIndex <= $targetLevelIndex; $levelIndex++) {
            $levelKey = $this->getLevelKey($levelIndex);
            if (!$levelKey) continue;
            
            $level = $educationLevels[$levelKey];
            $levelDuration = $level['duration'];
            
            // If this is the current level, calculate remaining years in this level
            if ($levelIndex == $currentLevelIndex) {
                // Calculate approximate position in current level based on age
                $expectedAgeInLevel = $level['start_age'];
                $yearsInCurrentLevel = max(0, min($levelDuration, $levelDuration - ($age - $expectedAgeInLevel)));
                $startFromYear = $levelDuration - $yearsInCurrentLevel;
            } else {
                $yearsInCurrentLevel = $levelDuration;
                $startFromYear = 0;
            }
            
            // Calculate costs for each year in this level
            for ($year = $startFromYear; $year < $levelDuration; $year++) {
                $inflationFactor = pow(1 + $inflationRate, $yearsRemaining);
                $yearTuition = $annualTuition * $inflationFactor;
                $yearAdditional = $additionalExpenses * $inflationFactor;
                $yearTotal = $yearTuition + $yearAdditional;
                
                $totalCost += $yearTotal;
                
                $yearlyProjections[] = [
                    'year' => (int)$currentYear + $yearsRemaining,
                    'age' => $age + $yearsRemaining,
                    'level' => $levelKey,
                    'tuition' => $yearTuition,
                    'additional' => $yearAdditional,
                    'total' => $yearTotal
                ];
                
                $yearsRemaining++;
            }
        }
        
        // Calculate average annual cost
        $averageAnnualCost = $yearsRemaining > 0 ? $totalCost / $yearsRemaining : 0;
        
        // Store calculation results
        $this->calculationResults = [
            'totalCost' => $totalCost,
            'yearsRemaining' => $yearsRemaining,
            'averageAnnualCost' => $averageAnnualCost,
            'yearlyProjections' => $yearlyProjections
        ];
        
        return true;
    }
    
    private function getLevelIndex($levelKey) {
        $levels = ['maternelle', 'primaire', 'college', 'lycee', 'superieur'];
        return array_search($levelKey, $levels);
    }
    
    private function getLevelKey($index) {
        $levels = ['maternelle', 'primaire', 'college', 'lycee', 'superieur'];
        return isset($levels[$index]) ? $levels[$index] : false;
    }
    
    public function saveChild($data) {
        // Clean and prepare data
        $childData = [
            'name' => $data['child_name'],
            'birthdate' => $data['child_birthdate'],
            'current_level' => $data['current_level'],
            'school_name' => $data['school_name'],
            'annual_tuition' => str_replace(' ', '', $data['annual_tuition']),
            'additional_expenses' => str_replace(' ', '', $data['additional_expenses'] ?? 0),
            'inflation_rate' => $data['inflation_rate'],
            'expected_graduation_level' => $data['expected_graduation_level']
        ];
        
        return $this->schoolFeeModel->saveChildProfile($childData);
    }
    
    public function deleteChild($childId) {
        return $this->schoolFeeModel->deleteChildProfile($childId);
    }
    
    public function getSavedChildren() {
        return $this->schoolFeeModel->getChildProfiles();
    }
    
    public function getCalculationResults() {
        return $this->calculationResults;
    }
    
    private function validateFeeInputs($data) {
        // Validate required fields
        $requiredFields = ['child_name', 'child_birthdate', 'current_level', 'annual_tuition', 'inflation_rate', 'expected_graduation_level'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        
        // Validate numeric fields
        $numericFields = ['annual_tuition', 'inflation_rate'];
        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $value = str_replace(' ', '', $data[$field]);
                if (!is_numeric($value)) {
                    return false;
                }
            }
        }
        
        // Validate birthdate (must be in the past)
        $birthdate = new DateTime($data['child_birthdate']);
        $today = new DateTime();
        if ($birthdate > $today) {
            return false;
        }
        
        return true;
    }
}
?>