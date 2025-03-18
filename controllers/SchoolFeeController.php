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

    /**
     * Process actions based on request
     */
    public function processRequest() {
        global $id_oo; // Used for user identification if needed
        
        // Check for form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Calculate fees
            if (isset($_POST['calculate_fees'])) {
                $this->calculateFees($_POST);
                // No redirect, just stay on the page with results
            }
            
            // Add new child
            if (isset($_POST['save_child'])) {
                $this->saveChild($_POST);
                header('Location: ?action=school-fee&success=child_saved');
                exit;
            }
            
            // Update child
            if (isset($_POST['update_child'])) {
                $childId = intval($_POST['child_id']);
                $this->updateChild($childId, $_POST);
                header('Location: ?action=school-fee&success=child_updated');
                exit;
            }
        }
        
        // Handle child deletion via GET
        if (isset($_GET['delete_child'])) {
            $childId = intval($_GET['delete_child']);
            $this->deleteChild($childId);
            header('Location: ?action=school-fee&success=child_deleted');
            exit;
        }
    }
    
    /**
     * Get data needed for the school fee simulator module
     */
    public function getViewData() {
        $viewData = [];
        
        // Get children profiles
        $viewData['children'] = $this->getSavedChildren();
        
        // Check if we need to load an existing child
        if (isset($_GET['child_id'])) {
            $childId = intval($_GET['child_id']);
            $viewData['selectedChild'] = $this->schoolFeeModel->getChildProfile($childId);
        }
        
        // Check if we need to edit a child
        if (isset($_GET['edit_child'])) {
            $childId = intval($_GET['edit_child']);
            $viewData['editChild'] = $this->schoolFeeModel->getChildProfile($childId);
        }
        
        // Check if we need to view child details
        if (isset($_GET['view_child'])) {
            $childId = intval($_GET['view_child']);
            $viewData['viewChild'] = $this->schoolFeeModel->getChildProfile($childId);
            
            // Pre-calculate the projections for this child
            $childData = $viewData['viewChild'];
            if ($childData) {
                $this->calculateFees($childData);
                $viewData['calculationResults'] = $this->calculationResults;
            }
        }
        
        // Add calculation results if available
        if (!isset($viewData['calculationResults']) && $this->calculationResults) {
            $viewData['calculationResults'] = $this->calculationResults;
        }
        
        return $viewData;
    }

    public function index() {
        // Process any form submissions or actions
        $this->processRequest();
        
        // Get data for the view
        $viewData = $this->getViewData();
        
        // Extract variables for use in the view
        extract($viewData);
        
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
            header('Location: ?action=school-fee');
        }
    }

    /**
     * Calculate fees based on provided data
     */
    public function calculateFees($data) {
        // Validate inputs
        if (!$this->validateFeeInputs($data)) {
            return false;
        }
        
        // Support both child_birthdate and birthdate fields (for form data vs DB record)
        $birthdateField = isset($data['child_birthdate']) ? 'child_birthdate' : 'birthdate';
        $nameField = isset($data['child_name']) ? 'child_name' : 'name';
        
        // Get current age from birthdate
        $birthdate = new DateTime($data[$birthdateField]);
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
        
        // Make sure numeric values are properly formatted
        $annualTuition = is_numeric($data['annual_tuition']) ? 
            $data['annual_tuition'] : 
            str_replace([' ', ','], ['', '.'], $data['annual_tuition']);
            
        $additionalExpenses = isset($data['additional_expenses']) ?
            (is_numeric($data['additional_expenses']) ? 
                $data['additional_expenses'] : 
                str_replace([' ', ','], ['', '.'], $data['additional_expenses'])) : 0;
                
        $inflationRate = is_numeric($data['inflation_rate']) ? 
            $data['inflation_rate'] / 100 : 
            floatval($data['inflation_rate']) / 100;
        
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
    
    /**
     * Update an existing child profile
     */
    public function updateChild($childId, $data) {
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
        
        return $this->schoolFeeModel->updateChildProfile($childId, $childData);
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
    
    /**
     * Validate fee input data
     */
    private function validateFeeInputs($data) {
        // Check for required fields with support for both form data and DB record format
        $nameField = isset($data['child_name']) ? 'child_name' : 'name';
        $birthdateField = isset($data['child_birthdate']) ? 'child_birthdate' : 'birthdate';
        
        $requiredFields = [$nameField, $birthdateField, 'current_level', 'annual_tuition', 'inflation_rate', 'expected_graduation_level'];
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
        $birthdate = new DateTime($data[$birthdateField]);
        $today = new DateTime();
        if ($birthdate > $today) {
            return false;
        }
        
        return true;
    }
}
?>