<?php

class Furniture extends Product {

    /* Attributes */

    private $dimensions;
    private $unit = "CM";

    /* Constructor */

    /**
     * Constructor for the Furniture class
     * @param array|null $data Optional associative array with initial data
     */

    public function __construct($data = null) {
        if ($data != null) {
            $dimensions = isset($data['dimensions']) ? $data['dimensions'] : null;
            $unit = isset($data['unit']) ? $data['unit'] : "CM";

            parent::__construct($data);
            $this->setDimensions($dimensions);
            $this->setUnit($unit);
        }
    }

    /* Setters */

    /**
     * Sets the dimensions attribute of the object
     * @param string $dimensions
     * @return void
     */

    public function setDimensions($dimensions) {
        $this->dimensions = $dimensions;
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
     * Gets the dimensions attribute of the object
     * @return string
     */

    public function getDimensions() {
        return $this->dimensions;
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
        $array['dimensions'] = $this->dimensions;
        return $array;
    }

    /**
     * Validates the Furniture object attributes
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
        if (empty($this->getDimensions())) {
            $errors['dimensions'] = 'Dimensions are required';
        } else {
            $dimensions = explode('x', $this->getDimensions());
            foreach ($dimensions as $dimension) {
                if (!is_numeric(trim($dimension))) {
                    $errors['dimensions'] = 'Each part of the dimensions must be numeric';
                    break;
                }
            }
        }
        if (empty($this->getUnit())) $errors['unit'] = 'Unit is required';

        return $errors;
    }

    /* Operations */

    /**
     * Fetches specific attributes for the Furniture object from the database
     * @param int $productId
     * @return Furniture
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

            $this->setDimensions($value);

            return $this;
        } catch (Exception $e) {
            throw new Exception("Error fetching furniture details: " . $e->getMessage());
        }
    }

    /**
     * Saves specific attributes for the Furniture object to the database
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

    /**
     * Saves the Furniture object, including specific attributes, to the database
     * @return void
     */
    
    public function save() {
        $productId = $this->saveProduct();
        $this->saveSpecificAttributes($productId);
    }
}
?>
