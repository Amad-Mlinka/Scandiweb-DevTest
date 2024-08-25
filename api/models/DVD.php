<?php

class DVD extends Product {

    /* Attributes */

    private $size;
    private $unit = "MB";

    /* Constructor */

    public function __construct($data = null) {

        if($data != null){
            $size = isset($data['size']) ? $data['size'] : null;
            $unit = isset($data['unit']) ? $data['unit'] : "MB";
    
            parent::__construct($data);
            $this->setSize($size);
            $this->setUnit($unit);
        }       
    }


    /* Setters */

    public function setSize($size) {
        $this->size = $size;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
    }


    /* Getters */

    public function getSize() {
        return $this->size;
    }

    public function getUnit() {
        return $this->unit;
    }

    /* Helpers */

    public function toArray() {
        $array = parent::toArray();
        $array['size'] = $this->size;
        $array['unit'] = $this->unit;
        return $array;
    }

    protected function validate(): array {
        $errors = [];
        if (empty($this->getSKU())) $errors[] = 'SKU is required';
        if (empty($this->getName())) $errors[] = 'Name is required';
        if (!is_numeric($this->getPrice()) || $this->getPrice() <= 0) $errors[] = 'Price must be a positive number';
        if (empty($this->getSize()) || !is_numeric($this->getSize())) $errors[] = 'Size is required and must be numeric';
        if (empty($this->unit)) $errors[] = 'Unit is required';

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

        $this->setSize($value);

        
        return $this;
    }

    protected function saveSpecificAttributes($productId) {
        $db = Database::getInstance()->getConnection();

        try {
            $sql = "INSERT INTO product_details (product_id, attribute, value) VALUES (:product_id, :attribute, :value)";
            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':product_id' => $productId,
                ':attribute' => 'size',
                ':value' => $this->size
            ]);

            $stmt->execute([
            ':product_id' => $productId,
            ':attribute' => 'typeID',
            ':value' => $this->getType() 
            ]);

        } catch (Exception $e) {
            throw new Exception("Error saving DVD details: " . $e->getMessage());
        }
    }

    public function save() {
        $productId = $this->saveProduct();
        $this->saveSpecificAttributes($productId);
    }
    
}
?>
