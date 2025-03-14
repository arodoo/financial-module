<?php
class Asset {
    private $id;
    private $name;
    private $value;
    private $acquisitionDate;
    private $category;

    public function __construct($id, $name, $value, $acquisitionDate, $category) {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->acquisitionDate = $acquisitionDate;
        $this->category = $category;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getValue() {
        return $this->value;
    }

    public function getAcquisitionDate() {
        return $this->acquisitionDate;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setAcquisitionDate($acquisitionDate) {
        $this->acquisitionDate = $acquisitionDate;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'acquisitionDate' => $this->acquisitionDate,
            'category' => $this->category,
        ];
    }
}
?>