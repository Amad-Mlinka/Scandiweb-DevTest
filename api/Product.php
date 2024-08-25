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

    /* Operations */

    public static function fetchAll() {
        $db = Database::getInstance()->getConnection();
        $allProducts = [];
        
        $sqlFetchProducts = "SELECT * FROM product WHERE active = 1";
        $fetchedProducts = $db->query($sqlFetchProducts)->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$fetchedProducts) {
            throw new Exception("Error loading products.");
        }
        
        $sqlFetchProductTypes = "SELECT id, title FROM product_type";
        $fetchedProductTypes = $db->query($sqlFetchProductTypes)->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$fetchedProductTypes) {
            throw new Exception("Error loading product types.");
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
                    throw new Exception("Product class $productClass does not exist.");
                }
            }
        }       
        return json_encode($allProducts);
    }

    public static function massHardDelete(array $productIds) {
        if (empty($productIds)) {
            throw new Exception("No product IDs provided for deletion.");
        }

        $db = Database::getInstance()->getConnection();
        $ids = rtrim(str_repeat('?,', count($productIds)), ',');
        $sql = "DELETE FROM product WHERE id IN ($ids)";
        $stmt = $db->prepare($sql);

        if (!$stmt->execute($productIds)) {
            throw new Exception("Error deleting products.");
        }

        return true;
    }

    public static function massSoftDelete(array $productIds) {
        if (empty($productIds)) {
            throw new Exception("No product IDs provided for deletion.");
        }

        $db = Database::getInstance()->getConnection();
        $ids = rtrim(str_repeat('?,', count($productIds)), ',');
        $sql = "UPDATE product SET active = 0 WHERE id IN ($ids)";
        $stmt = $db->prepare($sql);

        if (!$stmt->execute($productIds)) {
            throw new Exception("Error performing soft delete on products.");
        }

        return true;
    }
}

?>
