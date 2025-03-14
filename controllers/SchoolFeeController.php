<?php
/**
 * School Fee Controller
 */
class SchoolFeeController {
    private $schoolFeeModel;

    public function __construct() {
        // Constructor logic
        $this->schoolFeeModel = new SchoolFee();
    }

    public function index() {
        // Display school fee view
        include_once __DIR__ . '/../views/school-fee/index.php';
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
}
?>