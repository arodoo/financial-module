<?php
require_once __DIR__ . '/../config/database.php';

class Asset {
    private $conn;
    
    public function __construct() {
        $this->conn = getDbConnection();
    }

    /**
     * Get all assets for the current user
     */
    public function getAllAssets() {
        global $id_oo;
        
        $query = "SELECT a.*, c.name as category_name 
                  FROM assets a
                  JOIN asset_categories c ON a.category_id = c.id
                  WHERE a.membre_id = :membre_id
                  ORDER BY a.name";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':membre_id', $id_oo, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total value of all assets
     */
    public function getTotalAssetValue() {
        global $id_oo;
        
        $query = "SELECT SUM(current_value) as total 
                 FROM assets 
                 WHERE membre_id = :membre_id";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':membre_id', $id_oo, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }

    /**
     * Get a single asset by ID
     */
    public function getAsset($assetId) {
        global $id_oo;
        
        $query = "SELECT a.*, c.name as category_name
                  FROM assets a
                  JOIN asset_categories c ON a.category_id = c.id
                  WHERE a.id = :asset_id AND a.membre_id = :membre_id";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':asset_id', $assetId, PDO::PARAM_INT);
        $stmt->bindValue(':membre_id', $id_oo, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get asset value history for a specific asset
     */
    public function getAssetValueHistory($assetId) {
        $query = "SELECT * FROM asset_value_history
                  WHERE asset_id = :asset_id
                  ORDER BY valuation_date DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':asset_id', $assetId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Add a new asset
     */
    public function addAsset($data) {
        global $id_oo;
        
        $query = "INSERT INTO assets 
                 (membre_id, category_id, name, description, purchase_value, current_value, 
                 purchase_date, last_valuation_date, location, notes) 
                 VALUES 
                 (:membre_id, :category_id, :name, :description, :purchase_value, :current_value, 
                 :purchase_date, :last_valuation_date, :location, :notes)";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $params = [
            ':membre_id' => $id_oo,
            ':category_id' => $data['category_id'],
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':purchase_value' => $data['purchase_value'],
            ':current_value' => $data['current_value'],
            ':purchase_date' => $data['purchase_date'],
            ':last_valuation_date' => $data['last_valuation_date'] ?? date('Y-m-d'),
            ':location' => $data['location'] ?? null,
            ':notes' => $data['notes'] ?? null
        ];
        
        if ($stmt->execute($params)) {
            $assetId = $this->conn->lastInsertId();
            
            // Also record the initial value in the value history table
            $this->addValueHistoryRecord($assetId, $data['current_value'], $data['last_valuation_date'] ?? date('Y-m-d'));
            
            return $assetId;
        }
        
        return false;
    }
    
    /**
     * Update an existing asset
     */
    public function updateAsset($assetId, $data) {
        global $id_oo;
        
        // Get current asset to check for value changes
        $currentAsset = $this->getAsset($assetId);
        
        $query = "UPDATE assets SET 
                 category_id = :category_id,
                 name = :name,
                 description = :description,
                 purchase_value = :purchase_value,
                 current_value = :current_value,
                 purchase_date = :purchase_date,
                 last_valuation_date = :last_valuation_date,
                 location = :location,
                 notes = :notes
                 WHERE id = :asset_id AND membre_id = :membre_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $params = [
            ':asset_id' => $assetId,
            ':membre_id' => $id_oo,
            ':category_id' => $data['category_id'],
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':purchase_value' => $data['purchase_value'],
            ':current_value' => $data['current_value'],
            ':purchase_date' => $data['purchase_date'],
            ':last_valuation_date' => $data['last_valuation_date'] ?? date('Y-m-d'),
            ':location' => $data['location'] ?? null,
            ':notes' => $data['notes'] ?? null
        ];
        
        $result = $stmt->execute($params);
        
        // If value has changed, add a new history record
        if ($result && $currentAsset && $currentAsset['current_value'] != $data['current_value']) {
            $this->addValueHistoryRecord($assetId, $data['current_value'], $data['last_valuation_date'] ?? date('Y-m-d'));
        }
        
        return $result;
    }
    
    /**
     * Delete an asset
     */
    public function deleteAsset($assetId) {
        global $id_oo;
        
        $query = "DELETE FROM assets 
                 WHERE id = :asset_id AND membre_id = :membre_id";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':asset_id', $assetId, PDO::PARAM_INT);
        $stmt->bindValue(':membre_id', $id_oo, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Add a value history record
     */
    public function addValueHistoryRecord($assetId, $value, $valuationDate, $notes = null) {
        $query = "INSERT INTO asset_value_history 
                 (asset_id, valuation_date, value, notes) 
                 VALUES 
                 (:asset_id, :valuation_date, :value, :notes)";
                 
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':asset_id' => $assetId,
            ':valuation_date' => $valuationDate,
            ':value' => $value,
            ':notes' => $notes
        ]);
    }
    
    /**
     * Get all asset categories
     */
    public function getAllCategories() {
        $query = "SELECT * FROM asset_categories ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get assets summary by category
     */
    public function getAssetsByCategory() {
        global $id_oo;
        
        $query = "SELECT 
                    c.name as category, 
                    COUNT(a.id) as count, 
                    SUM(a.current_value) as total_value
                  FROM 
                    assets a
                  JOIN 
                    asset_categories c ON a.category_id = c.id
                  WHERE 
                    a.membre_id = :membre_id
                  GROUP BY 
                    a.category_id
                  ORDER BY 
                    total_value DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':membre_id', $id_oo, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>