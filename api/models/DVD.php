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
}
?>
