<?php

class DVD extends Product {

    /* Attributes */

    private $size;
    private $unit = "MB";

    /* Constructor */

    /**
     * Constructor for the DVD class
     * @param array|null $data Optional associative array with initial data
     */

    public function __construct($data = null) {
        if ($data != null) {
            $size = isset($data['size']) ? $data['size'] : null;
            $unit = isset($data['unit']) ? $data['unit'] : "MB";

            parent::__construct($data);
            $this->setSize($size);
            $this->setUnit($unit);
        }
    }

    /* Setters */

    /**
     * Sets the size attribute of the object
     * @param int $size
     * @return void
     */

    public function setSize($size) {
        $this->size = $size;
    }

    /**
     * Sets the unit attribute of the object
     * @param string $unit
     * @return void
     */

    public function setUnit($unit) {
        $this->unit = $unit;
    }

    /* Getters */

    /**
     * Gets the size attribute of the object
     * @return int
     */

    public function getSize() {
        return $this->size;
    }

    /**
     * Gets the unit attribute of the object
     * @return string
     */

    public function getUnit() {
        return $this->unit;
    }

    /* Helpers */

    /**
     * Converts the object to an associative array
     * @return array
     */

    public function toArray() {
        $array = parent::toArray();
        $array['size'] = $this->size;
        $array['unit'] = $this->unit;
        return $array;
    }

    /**
     * Validates the DVD object attributes
     * @return array Associative array of validation errors
     */

    protected function validate(): array {
        $errors = [];

        if (empty($this->getSKU())) $errors['sku'] = 'SKU is required';
        if (empty($this->getName())) $errors['name'] = 'Name is required';
        if (empty($this->getPrice())) {
            $errors['price'] = 'Price is required';
        } elseif (!is_numeric($this->getPrice())) {
            $errors['price'] = 'Price must be numeric';
        } elseif($this->getPrice() <= 0){
            $errors['price'] = 'Price must be a positive number';
        } 
        if (empty($this->getSize())) {
            $errors['size'] = 'Size is required';
        } elseif (!is_numeric($this->getSize())) {
            $errors['size'] = 'Size is required and must be numeric';
        }
        if (empty($this->getUnit())) $errors['unit'] = 'Unit is required';

        return $errors;
    }

    /* Operations */

    /**
     * Fetches specific attributes for the DVD object from the database
     * @param int $productId
     * @return DVD
     * @throws Exception if there is an error fetching the details
     */

    protected function fetchSpecificAttributes($productId) {
        try {
            $db = Database::getInstance()->getConnection();

            $sqlFetchAttributes = "SELECT attribute, value FROM product_details WHERE product_id = :productId AND attribute != 'typeID'";
            $stmt = $db->prepare($sqlFetchAttributes);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $detail = $stmt->fetch(PDO::FETCH_ASSOC);

            $value = $detail['value'];

            $this->setSize($value);

            return $this;
        } catch (Exception $e) {
            throw new Exception("Error fetching DVD details: " . $e->getMessage());
        }
    }

    /**
     * Saves specific attributes for the DVD object to the database
     * @param int $productId
     * @return void
     * @throws Exception if there is an error saving the details
     */

    protected function saveSpecificAttributes($productId) {
        try {
            $db = Database::getInstance()->getConnection();

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

    /**
     * Saves the DVD object, including specific attributes, to the database
     * @return void
     */
    
    public function save() {
        $productId = $this->saveProduct();
        $this->saveSpecificAttributes($productId);
    }
}
?>
