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
    
    // Constants for education system - moved outside methods to save memory
    private static $EDUCATION_LEVELS = [
        'maternelle' => ['start_age' => 3, 'end_age' => 5, 'duration' => 3],
        'primaire' => ['start_age' => 6, 'end_age' => 10, 'duration' => 5],
        'college' => ['start_age' => 11, 'end_age' => 14, 'duration' => 4],
        'lycee' => ['start_age' => 15, 'end_age' => 17, 'duration' => 3],
        'superieur' => ['start_age' => 18, 'end_age' => 22, 'duration' => 5]
    ];
    
    private static $LEVEL_ORDER = ['maternelle', 'primaire', 'college', 'lycee', 'superieur'];

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
     * Optimized with streaming computation and minimal memory usage
     */
    public function calculateFees($data) {
        // Early validation
        if (!$this->validateFeeInputs($data)) {
            return false;
        }

        // Extract essential data only - avoid large variable storage
        $birthdateField = isset($data['child_birthdate']) ? 'child_birthdate' : 'birthdate';
        
        // Convert to timestamp instead of DateTime object to save memory
        $birthdateTimestamp = strtotime($data[$birthdateField]);
        $currentTimestamp = time();
        $age = floor((($currentTimestamp - $birthdateTimestamp) / 31556926)); // Seconds per year
        
        $currentLevel = $data['current_level'];
        $expectedGraduationLevel = $data['expected_graduation_level'];
        
        // Convert strings to numbers immediately
        $annualTuition = $this->cleanNumericValue($data['annual_tuition']);
        $additionalExpenses = isset($data['additional_expenses']) ? 
            $this->cleanNumericValue($data['additional_expenses']) : 0;
        $inflationRate = $this->cleanNumericValue($data['inflation_rate']) / 100;
        
        // Get level indices using static array
        $currentLevelIndex = array_search($currentLevel, self::$LEVEL_ORDER);
        $targetLevelIndex = array_search($expectedGraduationLevel, self::$LEVEL_ORDER);
        
        // Calculate summary metrics first without storing all projections
        $currentYear = (int)date('Y');
        $totalCost = 0;
        $yearsRemaining = 0;
        
        // Small sample of data for display purposes - only store what's needed
        $sampleProjections = [];
        $projectionCount = 0;
        
        // Streaming calculation approach - process each year without storing full array
        for ($levelIndex = $currentLevelIndex; $levelIndex <= $targetLevelIndex; $levelIndex++) {
            $levelKey = self::$LEVEL_ORDER[$levelIndex] ?? null;
            if (!$levelKey || !isset(self::$EDUCATION_LEVELS[$levelKey])) continue;
            
            $level = self::$EDUCATION_LEVELS[$levelKey];
            $levelDuration = $level['duration'];
            
            // Calculate remaining years in current level
            $yearsInCurrentLevel = $levelDuration;
            $startFromYear = 0;
            
            if ($levelIndex == $currentLevelIndex) {
                $expectedAgeInLevel = $level['start_age'];
                $yearsInCurrentLevel = max(0, min($levelDuration, $levelDuration - ($age - $expectedAgeInLevel)));
                $startFromYear = $levelDuration - $yearsInCurrentLevel;
            }
            
            // Stream through years without storing everything
            for ($year = $startFromYear; $year < $levelDuration; $year++) {
                $currentProjectionYear = $currentYear + $yearsRemaining;
                $inflationFactor = pow(1 + $inflationRate, $yearsRemaining);
                $yearTuition = $annualTuition * $inflationFactor;
                $yearAdditional = $additionalExpenses * $inflationFactor;
                $yearTotal = $yearTuition + $yearAdditional;
                
                // Update running totals
                $totalCost += $yearTotal;
                
                // Store only a small sample (first year, middle years, and final years)
                // This reduces memory usage while still providing useful data points
                if ($projectionCount < 5 || $yearsRemaining == 0 || 
                    $levelIndex == $targetLevelIndex || 
                    $projectionCount % 3 == 0) { // Only keep every 3rd projection + key ones
                    
                    $sampleProjections[] = [
                        'year' => $currentProjectionYear,
                        'age' => $age + $yearsRemaining,
                        'level' => $levelKey,
                        'total' => round($yearTotal, 2) // Round to save space, precision not critical
                    ];
                    
                    // Keep sample projections under control
                    if (count($sampleProjections) > 15) {
                        array_shift($sampleProjections);
                    }
                }
                
                $yearsRemaining++;
                $projectionCount++;
            }
        }
        
        // Store only summary results plus small sample
        $averageAnnualCost = $yearsRemaining > 0 ? $totalCost / $yearsRemaining : 0;
        
        $this->calculationResults = [
            'totalCost' => $totalCost,
            'yearsRemaining' => $yearsRemaining,
            'averageAnnualCost' => $averageAnnualCost,
            'sampleYears' => $sampleProjections,
            'currentYear' => $currentYear,
            'projectionCount' => $projectionCount
        ];
        
        // Force garbage collection to free memory
        unset($sampleProjections);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        return true;
    }
    
    /**
     * Efficient numeric cleaning function (string operations are memory intensive)
     */
    private function cleanNumericValue($value) {
        if (is_numeric($value)) {
            return (float)$value;
        }
        $value = trim($value);
        $value = str_replace([' ', ','], ['', '.'], $value);
        return (float)$value;
    }
    
    /**
     * Generator function to calculate projections on-demand without storing all in memory
     * This can be used by views that need to iterate through all projections
     */
    public function streamYearlyProjections($data) {
        // Similar setup to calculateFees but returns generator instead
        if (!$this->validateFeeInputs($data)) {
            return;
        }
        
        $birthdateField = isset($data['child_birthdate']) ? 'child_birthdate' : 'birthdate';
        $birthdateTimestamp = strtotime($data[$birthdateField]);
        $currentTimestamp = time();
        $age = floor((($currentTimestamp - $birthdateTimestamp) / 31556926));
        
        $currentLevel = $data['current_level'];
        $expectedGraduationLevel = $data['expected_graduation_level'];
        
        $annualTuition = $this->cleanNumericValue($data['annual_tuition']);
        $additionalExpenses = isset($data['additional_expenses']) ? 
            $this->cleanNumericValue($data['additional_expenses']) : 0;
        $inflationRate = $this->cleanNumericValue($data['inflation_rate']) / 100;
        
        $currentLevelIndex = array_search($currentLevel, self::$LEVEL_ORDER);
        $targetLevelIndex = array_search($expectedGraduationLevel, self::$LEVEL_ORDER);
        
        $currentYear = (int)date('Y');
        $yearsRemaining = 0;
        
        for ($levelIndex = $currentLevelIndex; $levelIndex <= $targetLevelIndex; $levelIndex++) {
            $levelKey = self::$LEVEL_ORDER[$levelIndex] ?? null;
            if (!$levelKey || !isset(self::$EDUCATION_LEVELS[$levelKey])) continue;
            
            $level = self::$EDUCATION_LEVELS[$levelKey];
            $levelDuration = $level['duration'];
            
            $yearsInCurrentLevel = $levelDuration;
            $startFromYear = 0;
            
            if ($levelIndex == $currentLevelIndex) {
                $expectedAgeInLevel = $level['start_age'];
                $yearsInCurrentLevel = max(0, min($levelDuration, $levelDuration - ($age - $expectedAgeInLevel)));
                $startFromYear = $levelDuration - $yearsInCurrentLevel;
            }
            
            for ($year = $startFromYear; $year < $levelDuration; $year++) {
                $inflationFactor = pow(1 + $inflationRate, $yearsRemaining);
                $yearTuition = $annualTuition * $inflationFactor;
                $yearAdditional = $additionalExpenses * $inflationFactor;
                
                yield [
                    'year' => $currentYear + $yearsRemaining,
                    'age' => $age + $yearsRemaining,
                    'level' => $levelKey,
                    'tuition' => round($yearTuition, 2),
                    'additional' => round($yearAdditional, 2),
                    'total' => round($yearTuition + $yearAdditional, 2)
                ];
                
                $yearsRemaining++;
            }
        }
    }
    
    /**
     * Get projection for a specific year (on demand calculation)
     */
    public function getProjectionForYear($data, $targetYear) {
        foreach ($this->streamYearlyProjections($data) as $projection) {
            if ($projection['year'] == $targetYear) {
                return $projection;
            }
        }
        return null;
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