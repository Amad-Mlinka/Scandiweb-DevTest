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

}
?>
