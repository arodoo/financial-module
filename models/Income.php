<?php
class Income {
    private $id;
    private $amount;
    private $source;
    private $date;

    public function __construct($id, $amount, $source, $date) {
        $this->id = $id;
        $this->amount = $amount;
        $this->source = $source;
        $this->date = $date;
    }

    public function getId() {
        return $this->id;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getSource() {
        return $this->source;
    }

    public function getDate() {
        return $this->date;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public static function fetchAll($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM incomes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Income');
    }

    public function save($pdo) {
        $stmt = $pdo->prepare("INSERT INTO incomes (amount, source, date) VALUES (:amount, :source, :date)");
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':source', $this->source);
        $stmt->bindParam(':date', $this->date);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    public function delete($pdo) {
        $stmt = $pdo->prepare("DELETE FROM incomes WHERE id = :id");
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }
}
?>