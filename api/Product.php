<?php
abstract class Product {
    
    /* Attributes */

    private $id;
    private $SKU;
    private $name;
    private $active;
    private $price;
    private $type;

    /* Constructor */

    public function __construct(array $data) {
        $this->setId($data['id'] ?? null);
        $this->setSKU($data['SKU'] ?? '');
        $this->setName($data['name'] ?? '');
        $this->setPrice($data['price'] ?? 0);
        $this->setActive($data['active'] ?? 1);
        $this->setType($data['typeID'] ?? '');
    }


    /* Setters */

    public function setId($id) {
        return $this->id = $id;
    }

    public function setSKU($SKU) {
        return $this->SKU = $SKU;
    }

    public function setName($name) {
        return $this->name = $name;
    }

    public function setPrice($price) {
        return $this->price = $price;
    }

    public function setActive($active) {
        return $this->active = $active;
    }

    public function setType($type){
        return $this->type = $type; 
    }

    /* Getters */

    public function getId() {
        return $this->id;
    }

    public function getSKU() {
        return $this->SKU;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getActive() {
        return $this->active;
    }

    public function getType(){
        return $this->type; 
    }

    /* Helpers */


    /* Operations */

}

?>
