<?php

class Book extends Product {

    /* Attributes */

    private $weight;
    private $unit = "KG";

    /* Constructor */

    public function __construct($data = null) {

        if($data != null){
            $weight = isset($data['weight']) ? $data['weight'] : null;
            $unit = isset($data['unit']) ? $data['unit'] : "KG";
    
            parent::__construct($data);
            $this->setWeight($weight);
            $this->setUnit($unit);
        }
       
    }

    /* Setters */

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
    }

    /* Getters */

    public function getWeight() {
        return $this->weight;
    }    

    public function getUnit() {
        return $this->unit;
    }

    /* Helpers */

    public function toArray() {
        $array = parent::toArray();
        $array['weight'] = $this->weight;
        return $array;
    }

    protected function validate(): array {
        $errors = [];

        if (empty($this->getSKU())) $errors[] = 'SKU is required';
        if (empty($this->getName())) $errors[] = 'Name is required';
        if (!is_numeric($this->getPrice()) || $this->getPrice() <= 0) $errors[] = 'Price must be a positive number';
        if (empty($this->getWeight()) || !is_numeric($this->getWeight())) $errors[] = 'Size is required and must be numeric';
        if (empty($this->getUnit())) $errors[] = 'Unit is required';

        return $errors;
    }

    /* Operations */

    protected function fetchSpecificAttributes($productId) {
        $db = Database::getInstance()->getConnection();
        $sqlFetchAttributes = "SELECT attribute, value FROM product_details WHERE product_id = :productId AND attribute != 'typeID'";
        $stmt = $db->prepare($sqlFetchAttributes);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $detail = $stmt->fetch(PDO::FETCH_ASSOC);

        $value = $detail['value'];

        $this->setWeight($value);

        return $this;
    }

    protected function saveSpecificAttributes($productId) {
        $db = Database::getInstance()->getConnection();

        try {
            $sql = "INSERT INTO product_details (product_id, attribute, value) VALUES (:product_id, :attribute, :value)";
            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':product_id' => $productId,
                ':attribute' => 'weight',
                ':value' => $this->getWeight()
            ]);

            $stmt->execute([
                ':product_id' => $productId,
                ':attribute' => 'typeID',
                ':value' => $this->getType() 
            ]);

        } catch (Exception $e) {
            throw new Exception("Error saving book details: " . $e->getMessage());
        }
    }

    public function save() {
        $productId = $this->saveProduct();
        $this->saveSpecificAttributes($productId);
    }
}
?>
