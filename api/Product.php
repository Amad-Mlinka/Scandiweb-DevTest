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

    public function __construct(array $data) {
        $this->setId($data['id'] ?? null);
        $this->setSKU($data['SKU'] ?? '');
        $this->setName($data['name'] ?? '');
        $this->setPrice($data['price'] ?? 0);
        $this->setActive($data['active'] ?? 1);
        $this->setType($data['typeID'] ?? '');
    }


    /* Abstract methods */

    abstract protected function saveSpecificAttributes($productId);
    abstract protected function fetchSpecificAttributes($productId);
    abstract protected function validate(): array;


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

    public static function skuExists($sku) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM product WHERE sku = :sku");
        $stmt->execute(['sku' => $sku]);
        return $stmt->fetchColumn() > 0;
    }
    

    /* Operations */

    public static function fetchAll() {
        $db = Database::getInstance()->getConnection();
        $allProducts = [];
        $response = new Response();
    
        try {
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

    public static function massHardDelete(array $productIds) {
        $response = new Response();
    
        try {
            if (empty($productIds)) {
                $response->setSuccess(false);
                $response->setMessage("No product IDs provided for deletion.");

                return json_encode($response);
            }
    
            $db = Database::getInstance()->getConnection();
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

    public static function massSoftDelete(array $productIds) {
        $response = new Response();

        try {
            if (empty($productIds)) {
                $response->setSuccess(false);
                $response->setMessage("No product IDs provided for deletion.");

                return json_encode($response);
            }

            $db = Database::getInstance()->getConnection();
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

    public function saveProduct() {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
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

    public static function saveToDatabase($data){
        $response = new Response();

        try{

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
