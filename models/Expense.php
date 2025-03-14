<?php
class Expense {
    private $id;
    private $description;
    private $amount;
    private $date;

    public function __construct($id, $description, $amount, $date) {
        $this->id = $id;
        $this->description = $description;
        $this->amount = $amount;
        $this->date = $date;
    }

    public function getId() {
        return $this->id;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public static function fetchAllExpenses($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM expenses");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addExpense($pdo, $description, $amount, $date) {
        $stmt = $pdo->prepare("INSERT INTO expenses (description, amount, date) VALUES (:description, :amount, :date)");
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':date', $date);
        return $stmt->execute();
    }

    public static function deleteExpense($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>