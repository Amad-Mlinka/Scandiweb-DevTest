<?php
require_once '../models/Response.php';

abstract class Product {

    /* Attributes */
    private $id;
    private $SKU;
    private $name;
    private $active;
    private $price;
    private $type;

    /* Constructor */
    /**
     * Initializes the product object with provided data.
     * @param array $data An associative array containing product details.
     */
    public function __construct(array $data) {
        $this->setId($data['id'] ?? null);
        $this->setSKU($data['SKU'] ?? '');
        $this->setName($data['name'] ?? '');
        $this->setPrice($data['price'] ?? 0);
        $this->setActive($data['active'] ?? 1);
        $this->setType($data['typeID'] ?? '');
    }

    /* Abstract methods */
    /**
     * Saves specific attributes for the product object to the database.
     * @param int $productId The ID of the product to update.
     * @return void
     * @throws Exception if there is an error saving the details.
     */
    abstract protected function saveSpecificAttributes($productId);

    /**
     * Fetches specific attributes for the product object from the database.
     * @param int $productId The ID of the product to fetch.
     * @return void
     */
    abstract protected function fetchSpecificAttributes($productId);

    /**
     * Validates the product data.
     * @return array An array of validation errors, if any.
     */
    abstract protected function validate(): array;

    /* Setters */

    public function setId($id) {
        $this->id = $id;
    }

    public function setSKU($SKU) {
        $this->SKU = $SKU;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    public function setType($type) {
        $this->type = $type;
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

    public function getType() {
        return $this->type;
    }

    /* Helpers */

    /**
     * Converts the product object to an associative array.
     * @return array An associative array representation of the product.
     */

    public function toArray() {
        return [
            'id' => $this->getId(),
            'SKU' => $this->getSKU(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'active' => $this->getActive(),
            'type' => $this->getType(),
        ];
    }

    /**
     * Creates an instance of a product subclass based on the type ID.
     * @param int $typeID The ID of the product type.
     * @return Product A new product instance of the appropriate type.
     * @throws Exception if the product type or class does not exist.
     */

    public static function getProductInstanceByTypeID($typeID) {
        $response = new Response();
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT title FROM product_type WHERE id = :typeID";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':typeID', $typeID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$result) {
            throw new Exception("Product type with ID $typeID not found.");
        }
    
        $typeName = ucfirst($result['title']);
    
        if (class_exists($typeName)) {
            return new $typeName(['typeID' => $typeID]);
        } else {
            throw new Exception("Product class $typeName does not exist.");
        }
    }

    /**
     * Checks if a SKU already exists in the database.
     * @param string $sku The SKU to check.
     * @return bool True if the SKU exists, false otherwise.
     */

    public static function skuExists($sku) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM product WHERE sku = :sku");
        $stmt->execute(['sku' => $sku]);

        return $stmt->fetchColumn() > 0;
    }

    /* Operations */
    /**
     * Fetches all active products from the database.
     * @return string JSON-encoded response containing product data or an error message.
     */

    public static function fetchAll() {
        $allProducts = [];
        $response = new Response();

        try {
            $db = Database::getInstance()->getConnection();
            $sqlFetchProducts = "SELECT * FROM product WHERE active = 1";
            $fetchedProducts = $db->query($sqlFetchProducts)->fetchAll(PDO::FETCH_ASSOC);
    
            if (!$fetchedProducts) {
                $response->setSuccess(true);
                $response->setMessage("No products found");
                return json_encode($response);
            }
    
            $sqlFetchProductTypes = "SELECT id, title FROM product_type";
            $fetchedProductTypes = $db->query($sqlFetchProductTypes)->fetchAll(PDO::FETCH_ASSOC);
    
            if (!$fetchedProductTypes) {
                $response->setSuccess(false);
                $response->setError("Error loading product types.");
                return json_encode($response);
            }
    
            $productTypes = [];
            foreach ($fetchedProductTypes as $type) {
                $productTypes[$type['id']] = $type['title'];
            }
    
            foreach ($fetchedProducts as $product) {
                $productId = $product['id'];
    
                $sqlFetchProductDetails = "SELECT attribute, value FROM product_details WHERE product_id = :productId";
                $stmtDetails = $db->prepare($sqlFetchProductDetails);
                $stmtDetails->bindParam(':productId', $productId, PDO::PARAM_INT);
                $stmtDetails->execute();
                $fetchedProductDetails = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);
    
                $typeName = null;
                foreach ($fetchedProductDetails as $detail) {
                    if ($detail['attribute'] === 'typeID') {
                        $typeId = $detail['value'];
                        $typeName = $productTypes[$typeId];
                        break;
                    }
                }
    
                if ($typeName) {
                    $productClass = ucfirst($typeName);
                    if (class_exists($productClass)) {
                        $data = [
                            'id' => $productId, 
                            'SKU' => $product['SKU'], 
                            'name' => $product['name'], 
                            'price' => $product['price'],
                            'active' => $product['active'],
                            'typeID' => $detail['value'],
                        ];

                        $productInstance = new $productClass($data);
                        $productInstance->fetchSpecificAttributes($productId);
    
                        $allProducts[] = $productInstance->toArray();
                    } else {
                        $response->setSuccess(false);
                        $response->setError("Product class $productClass does not exist.");
                        return json_encode($response);
                    }
                }
            }
    
            $response->setSuccess(true);
            $response->setData($allProducts);
            $response->setMessage("Products fetched successfully.");
        } catch (Exception $e) {
            $response->setSuccess(false);
            $response->setError($e->getMessage());
        }
    
        return json_encode($response);
    }

    /**
     * Retrieves all product types from the database.
     * @return string JSON-encoded response containing product type data or an error message.
     */

    public static function getTypes() {
        $response = new Response();

        try {
            $db = Database::getInstance()->getConnection();
    
            $sql = "SELECT id, title, input_HTML FROM product_type";
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if ($result) {
                $response->setSuccess(true);
                $response->setData($result);
                $response->setMessage("Product types fetched successfully.");
            } else {
                $response->setSuccess(false);
                $response->setMessage("No product types found.");
            }
        } catch (Exception $e) {
            $response->setSuccess(false);
            $response->setMessage("An error occurred: " . $e->getMessage());
            $response->setError($e->getMessage());
        }
    
        return json_encode($response);
    }

    /**
     * Deletes multiple products from the database permanently.
     * @param array $productIds An array of product IDs to delete.
     * @return string JSON-encoded response indicating success or failure.
     */

    public static function massHardDelete(array $productIds) {
        $response = new Response();

        try {
            $db = Database::getInstance()->getConnection();

            if (empty($productIds)) {
                $response->setSuccess(false);
                $response->setMessage("No product IDs provided for deletion.");

                return json_encode($response);
            }

            $ids = rtrim(str_repeat('?,', count($productIds)), ',');
            $sql = "DELETE FROM product WHERE id IN ($ids)";
            $stmt = $db->prepare($sql);
    
            if (!$stmt->execute($productIds)) {
                $response->setSuccess(false);
                $response->setMessage("Error deleting products.");

                return json_encode($response);
            }
    
            $response->setSuccess(true);
            $response->setMessage("Products deleted successfully.");


        } catch (Exception $e) {
            $response->setSuccess(false);
            $response->setMessage("An error occurred: " . $e->getMessage());
            $response->setError($e->getMessage());
        }
        return json_encode($response);
    }


    /**
     * Performs a soft delete on multiple products by setting their active status to 0.
     * @param array $productIds An array of product IDs to be soft-deleted.
     * @return string JSON-encoded response indicating success or failure of the operation.
     */

    public static function massSoftDelete(array $productIds) {
        $response = new Response();

        try {
            $db = Database::getInstance()->getConnection();

            if (empty($productIds)) {
                $response->setSuccess(false);
                $response->setMessage("No product IDs provided for deletion.");

                return json_encode($response);
            }

            $ids = rtrim(str_repeat('?,', count($productIds)), ',');
            $sql = "UPDATE product SET active = 0 WHERE id IN ($ids)";
            $stmt = $db->prepare($sql);

            if (!$stmt->execute($productIds)) {
                $response->setSuccess(false);
                $response->setMessage("Error performing soft delete on products.");

                return json_encode($response);
            }

            $response->setSuccess(true);
            $response->setMessage("Products soft-deleted successfully.");


        } catch (Exception $e) {
            $response->setSuccess(false);
            $response->setMessage("An error occurred: " . $e->getMessage());
            $response->setError($e->getMessage());
        }

        return json_encode($response);

    }

    /**
     * Fetches the HTML input format for a product type from the database.
     * @param int $typeId The ID of the product type to fetch HTML for.
     * @return string JSON-encoded response with the HTML input or an error message.
     */

    public static function getHtmlForType($typeId) {
        $response = new Response();

        try{        
            $db = Database::getInstance()->getConnection();

            $sql = "SELECT input_HTML FROM product_type WHERE id = :typeId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':typeId', $typeId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $response->setSuccess(true);
                $response->setMessage("Fetched input HTML");
                $response->setData($result['input_HTML']);
            } else {
                $response->setSuccess(false);
                $response->setMessage("Error fetching input html");
            }
        } catch (Exception $e) {
            $response->setSuccess(false);
            $response->setMessage("An error occurred: " . $e->getMessage());
            $response->setError($e->getMessage());
        }
       
        return json_encode($response);

    }

    /**
     * Saves the current product object to the database.
     * Begins a transaction, inserts the product data, and commits the transaction.
     * @return int The ID of the newly created product.
     * @throws Exception if there is an error during the transaction or saving process.
     */

    public function saveProduct() {       

        try {
            $db = Database::getInstance()->getConnection();

            $db->beginTransaction();

            $sql = "INSERT INTO product (SKU, name, price, active) VALUES (:SKU, :name, :price, :active)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':SKU' => $this->getSKU(),
                ':name' => $this->getName(),
                ':price' => $this->getPrice(),
                ':active' => $this->getActive()
            ]);

            $productId = $db->lastInsertId();
            $this->setId($productId);

            $db->commit();

            return $productId;

        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception("Error saving product: " . $e->getMessage());
        }
    }


    /**
     * Saves a new product to the database, creating the appropriate product type instance.
     * Validates the product data before saving.
     * @param array $data An associative array containing product data.
     * @return Response JSON-encoded response with success or error information.
     */
    
    public static function saveToDatabase($data){
        $response = new Response();


        try{
            $db = Database::getInstance()->getConnection();

            if(self::skuExists($data['sku'])){

                $response->setSuccess(false);
                $response->setMessage('SKU already exists');
                $response->setError('A product with this SKU already exists in the database.');

            }else{

                $productType = self::getProductInstanceByTypeID($data['type']);
                $productData = [
                    'SKU' => $data['sku'],
                    'name' => $data['name'],
                    'price' => $data['price'],
                    'active' => 1,
                    'typeID' => $data['type'],
                    $data['attributeName'] => $data['attributeValue']
                ];
        
                $product = new $productType($productData);
                $errors = $product->validate();
                if(!empty($errors)){
                    $response->setSuccess(false);
                    $response->setMessage('Validation Error');
                    $response->setError($errors);

                    return $response;  
                }
                $product->save();

                $response->setSuccess(true);
                $response->setMessage('Product succsessfully added');
                $response->setData($product->toArray());

            }      
        }catch(Exception $e){
            $response->setSuccess(false);
            $response->setError($e->getMessage());
        }

        return $response;        
    }
}

?>
