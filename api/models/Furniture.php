<?php

class Furniture extends Product {

    /* Attributes */

    private $dimensions;
    private $unit = "CM";

    /* Constructor */

    public function __construct($data = null) {

        if($data != null){
            $dimensions = isset($data['dimensions']) ? $data['dimensions'] : null;
            $unit = isset($data['unit']) ? $data['unit'] : "CM";
    
            parent::__construct($data);
            $this->setDimensions($dimensions);
            $this->setUnit($unit);
        }
       
    }

    /* Setters */

    public function setDimensions($dimensions) {
        $this->dimensions = $dimensions;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
    }

    /* Getters */

    public function getDimensions() {
        return $this->dimensions;
    }  

    public function getUnit() {
        return $this->unit;
    }

    /* Helpers */
    
    public function toArray() {
        $array = parent::toArray();
        $array['dimensions'] = $this->dimensions;
        return $array;
    }

    protected function validate(): array {
        $errors = [];

        if (empty($this->getSKU())) $errors[] = 'SKU is required';
        if (empty($this->getName())) $errors[] = 'Name is required';
        if (!is_numeric($this->getPrice()) || $this->getPrice() <= 0) $errors[] = 'Price must be a positive number';
        if (empty($this->getDimensions())) $errors[] = 'Dimensions are required';
        if (empty($this->getUnit())) $errors[] = 'Material is required';

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

        $this->setDimensions($value);
        

        return $this;
    }

    protected function saveSpecificAttributes($productId) {
        $db = Database::getInstance()->getConnection();

        try {
            $sql = "INSERT INTO product_details (product_id, attribute, value) VALUES (:product_id, :attribute, :value)";
            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':product_id' => $productId,
                ':attribute' => 'dimensions',
                ':value' => $this->getDimensions()
            ]);

            $stmt->execute([
                ':product_id' => $productId,
                ':attribute' => 'typeID',
                ':value' => $this->getType() 
            ]);
        } catch (Exception $e) {
            throw new Exception("Error saving furniture details: " . $e->getMessage());
        }
    }

    public function save() {
        $productId = $this->saveProduct();
        $this->saveSpecificAttributes($productId);
    }
}
?>
