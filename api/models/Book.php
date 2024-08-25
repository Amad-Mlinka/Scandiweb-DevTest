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


    /* Operations */
}
?>
