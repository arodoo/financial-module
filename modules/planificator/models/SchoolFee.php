<?php
require_once __DIR__ . '/../config/database.php';

class SchoolFee {
    private $feeStructure;
    private $paymentHistory;
    private $conn;
    private $table_children = 'school_fee_children';

    public function __construct() {
        // Initialize original properties
        $this->feeStructure = [];
        $this->paymentHistory = [];

        // Initialize database connection
        $this->conn = getDbConnection();
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
    
    public function saveChildProfile($data) {
        // Insert new child profile
        $query = "INSERT INTO " . $this->table_children . "
                (name, birthdate, current_level, school_name, annual_tuition, 
                additional_expenses, inflation_rate, expected_graduation_level)
            VALUES
                (:name, :birthdate, :current_level, :school_name, :annual_tuition,
                :additional_expenses, :inflation_rate, :expected_graduation_level)";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['birthdate'] = htmlspecialchars(strip_tags($data['birthdate']));
        $data['current_level'] = htmlspecialchars(strip_tags($data['current_level']));
        $data['school_name'] = htmlspecialchars(strip_tags($data['school_name']));
        $data['annual_tuition'] = (float)$data['annual_tuition'];
        $data['additional_expenses'] = (float)$data['additional_expenses'];
        $data['inflation_rate'] = (float)$data['inflation_rate'];
        $data['expected_graduation_level'] = htmlspecialchars(strip_tags($data['expected_graduation_level']));
        
        // Bind parameters
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':birthdate', $data['birthdate']);
        $stmt->bindParam(':current_level', $data['current_level']);
        $stmt->bindParam(':school_name', $data['school_name']);
        $stmt->bindParam(':annual_tuition', $data['annual_tuition']);
        $stmt->bindParam(':additional_expenses', $data['additional_expenses']);
        $stmt->bindParam(':inflation_rate', $data['inflation_rate']);
        $stmt->bindParam(':expected_graduation_level', $data['expected_graduation_level']);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    
    public function getChildProfiles() {
        $query = "SELECT * FROM " . $this->table_children . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function getChildProfile($id) {
        $query = "SELECT * FROM " . $this->table_children . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function deleteChildProfile($id) {
        $query = "DELETE FROM " . $this->table_children . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $id = htmlspecialchars(strip_tags($id));
        
        // Bind parameter
        $stmt->bindParam(':id', $id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    
    public function updateChildProfile($id, $data) {
        $query = "UPDATE " . $this->table_children . " SET
                name = :name,
                birthdate = :birthdate,
                current_level = :current_level,
                school_name = :school_name,
                annual_tuition = :annual_tuition,
                additional_expenses = :additional_expenses,
                inflation_rate = :inflation_rate,
                expected_graduation_level = :expected_graduation_level
            WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $data['name'] = htmlspecialchars(strip_tags($data['name']));
        $data['birthdate'] = htmlspecialchars(strip_tags($data['birthdate']));
        $data['current_level'] = htmlspecialchars(strip_tags($data['current_level']));
        $data['school_name'] = htmlspecialchars(strip_tags($data['school_name']));
        $data['annual_tuition'] = (float)$data['annual_tuition'];
        $data['additional_expenses'] = (float)$data['additional_expenses'];
        $data['inflation_rate'] = (float)$data['inflation_rate'];
        $data['expected_graduation_level'] = htmlspecialchars(strip_tags($data['expected_graduation_level']));
        $id = htmlspecialchars(strip_tags($id));
        
        // Bind parameters
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':birthdate', $data['birthdate']);
        $stmt->bindParam(':current_level', $data['current_level']);
        $stmt->bindParam(':school_name', $data['school_name']);
        $stmt->bindParam(':annual_tuition', $data['annual_tuition']);
        $stmt->bindParam(':additional_expenses', $data['additional_expenses']);
        $stmt->bindParam(':inflation_rate', $data['inflation_rate']);
        $stmt->bindParam(':expected_graduation_level', $data['expected_graduation_level']);
        $stmt->bindParam(':id', $id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
}
?>