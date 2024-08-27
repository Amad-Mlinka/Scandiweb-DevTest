<?php

class Book extends Product {

    /* Attributes */

    private $weight;
    private $unit = "KG";

    /* Constructor */

    /**
     * Constructor for the Book class.
     * @param array|null $data An associative array of book attributes to initialize the object.
     */

    public function __construct($data = null) {
        if ($data != null) {
            $weight = isset($data['weight']) ? $data['weight'] : null;
            $unit = isset($data['unit']) ? $data['unit'] : "KG";
    
            parent::__construct($data);

            $this->setWeight($weight);
            $this->setUnit($unit);
        }
    }

    /* Setters */

    /**
     * Sets the weight of the book.
     * @param float $weight The weight of the book.
     */
    
    public function setWeight($weight) {
        $this->weight = $weight;
    }

    /**
     * Sets the unit of measurement for the book's weight.
     * @param string $unit The unit of measurement.
     */

    public function setUnit($unit) {
        $this->unit = $unit;
    }

    /* Getters */

    /**
     * Gets the weight of the book.
     * @return float The weight of the book.
     */

    public function getWeight() {
        return $this->weight;
    }    

    /**
     * Gets the unit of measurement for the book's weight.
     * @return string The unit of measurement.
     */

    public function getUnit() {
        return $this->unit;
    }

    /* Helpers */

    /**
     * Converts the book object to an associative array.
     * @return array The associative array representation of the book object.
     */

    public function toArray() {
        $array = parent::toArray();
        $array['weight'] = $this->weight;

        return $array;
    }

    /**
     * Validates the book's attributes.
     * @return array An associative array of validation errors, if any.
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
        if (empty($this->getWeight())) {
            $errors['weight'] = 'Weight is required';
        } elseif (!is_numeric($this->getWeight())) {
            $errors['weight'] = 'Weight must be numeric';
        }
        if (empty($this->getUnit())) $errors['unit'] = 'Unit is required';

        return $errors;
    }

    /* Operations */

    /**
     * Fetches specific attributes for the book from the database.
     * @param int $productId The ID of the product to fetch attributes for.
     * @return Book The current instance with the fetched attributes.
     * @throws Exception if there is an error fetching the details.
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
    
            $this->setWeight($value);
    
            return $this;
        } catch (Exception $e) {
            throw new Exception("Error fetching book details: " . $e->getMessage());
        }
    }

    /**
     * Saves specific attributes of the book to the database.
     * @param int $productId The ID of the product to save attributes for.
     * @return void
     * @throws Exception if there is an error saving the details.
     */

    protected function saveSpecificAttributes($productId) {
        try {
            $db = Database::getInstance()->getConnection();

            $sql = "INSERT INTO product_details (product_id, attribute, value) VALUES (:product_id, :attribute, :value)";
            $stmt = $db->prepare($sql);

            $stmt->execute([
                ':product_id' => $productId,
                ':attribute' => 'weight',
                ':value' => $this->getWeight()
            ]);

            $stmt->execute([
                ':product_id' => $productId,
                ':attribute' => 'typeID',
                ':value' => $this->getType() 
            ]);

        } catch (Exception $e) {
            throw new Exception("Error saving book details: " . $e->getMessage());
        }
    }

    /**
     * Saves the book to the database and its specific attributes.
     * @return void
     */
    
    public function save() {
        $productId = $this->saveProduct();
        $this->saveSpecificAttributes($productId);
    }
}
?>
