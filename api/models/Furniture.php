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


    /* Operations */
    
}
?>
