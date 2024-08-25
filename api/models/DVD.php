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


    /* Operations */
}
?>
