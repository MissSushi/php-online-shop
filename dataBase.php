<?php
interface IProductManager
{
    public function addProduct(string $name, string $description, int $price, int $status);
    public function getProducts(int $offset, int $limit, string $sort, string $filter, string $search);
    public function deleteProduct(int $productId);
    public function getProduct(int $productId);
    public function editProduct(int $productId, string $name, string $description, int $price, int $status);
}

// interface IShoppingCartManager
// {
//     public function addToShoppingCart(int $productId);
//     public function getShoppingCartCount();
//     public function getShoppingCartProducts();
// }

class DatabaseConnection implements IProductManager
{
    private PDO $conn;

    public function __construct()
    {
        // $servername = 'localhost'; // Hostname oder IP-Adresse des MySQL-Servers
        // $username = 'root';        // Benutzername für die MySQL-Datenbank
        // $password = '';            // Passwort für die MySQL-Datenbank
        // $dbname = 'online-shop'; // Name der MySQL-Datenbank
        $this->conn = new PDO("sqlite:./datenbank.db");
        // PDO Fehler-Modus auf Exception setzen
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function addProduct(string $description, string $name, int $price, int $status)
    {
        $statement = $this->conn->prepare("INSERT INTO products (description, productName, price, status) VALUES (:description, :productName, :price, :status)");
        $statement->bindParam(':description', $description);
        $statement->bindParam(':productName', $name);
        $statement->bindParam(':price', $price);
        $statement->bindParam(':status', $status);

        $statement->execute();

        $last_id = $this->conn->lastInsertId();
        return $last_id;
    }

    public function getCount(string $sort, string $filter, string $search)
    {
        $validSortColumns = [
            'id' => 'id',
            'name' => 'productName',
            'price' => 'price',
        ];

        if (!array_key_exists($sort, $validSortColumns)) {
            $sort = 'id';
        }

        if ($filter == "active") {
            $statement = $this->conn->prepare("SELECT COUNT(*) AS count FROM products WHERE status = 1 AND (id = :search OR productName LIKE :searchPattern)");
        } elseif ($filter == "inactive") {
            $statement = $this->conn->prepare("SELECT COUNT(*) AS count FROM products WHERE status = 0 AND (id = :search OR productName LIKE :searchPattern)");
        } else {
            $statement = $this->conn->prepare("SELECT COUNT(*) AS count FROM products WHERE (id = :search OR productName LIKE :searchPattern)");
        }
        $statement->bindValue(':searchPattern', "%$search%", PDO::PARAM_STR);
        $statement->bindValue(':search', $search, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['count'];
    }

    public function getProducts(int $offset, int $limit, string $sort, string $filter, string $search)
    {
        $validSortColumns = [
            'id' => 'id',
            'name' => 'productName',
            'price' => 'price',
        ];

        if (!array_key_exists($sort, $validSortColumns)) {
            $sort = 'id';
        }

        if ($filter == "active") {
            $statement = $this->conn->prepare("SELECT productName, description, price, id, status FROM products WHERE status = 1 AND (id = :search OR productName LIKE :searchPattern) ORDER BY " . $validSortColumns[$sort] . " LIMIT :limit OFFSET :offset");
        } elseif ($filter == "inactive") {
            $statement = $this->conn->prepare("SELECT productName, description, price, id, status FROM products WHERE status = 0 AND (id = :search OR productName LIKE :searchPattern) ORDER BY " . $validSortColumns[$sort] . " LIMIT :limit OFFSET :offset");
        } else {
            $statement = $this->conn->prepare("SELECT productName, description, price, id, status FROM products WHERE (id = :search OR productName LIKE :searchPattern) ORDER BY " . $validSortColumns[$sort] . " LIMIT :limit OFFSET :offset");
        }
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->bindValue(':searchPattern', "%$search%", PDO::PARAM_STR);
        $statement->bindValue(':search', $search, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getProduct(int $productId)
    {
        $statement = $this->conn->prepare("SELECT productName, description, price, id, status FROM products WHERE id = :id");
        $statement->bindParam(':id', $productId);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    public function deleteProduct(int $productId)
    {
        $statement = $this->conn->prepare("DELETE FROM products WHERE id = :id");
        $statement->bindParam(':id', $productId);
        $statement->execute();
    }

    public function editProduct(int $productId, string $name, string $description, int $price, int $status)
    {
        $statement = $this->conn->prepare("UPDATE products SET productName = :productName, description = :description, price = :price, status = :status WHERE id = :id");
        $statement->bindParam(':id', $productId);
        $statement->bindParam(':productName', $name);
        $statement->bindParam(':description', $description);
        $statement->bindParam(':price', $price);
        $statement->bindParam(':status', $status);

        $statement->execute();
    }

    // public function addToShoppingCart(int $productId)
    // {
    //     $shoppingCartDataStatement = $this->conn->prepare('SELECT quantity FROM shoppingcart WHERE productId = :productId');
    //     $shoppingCartDataStatement->bindParam(':productId', $productId);
    //     $shoppingCartDataStatement->execute();
    //     $shoppingCartData = $shoppingCartDataStatement->fetchAll(PDO::FETCH_ASSOC);

    //     if (!empty($shoppingCartData)) {
    //         $newQuantity = $shoppingCartData[0]['quantity'] + 1;

    //         $statement = $this->conn->prepare('UPDATE shoppingcart SET quantity = :quantity WHERE productId = :productId');
    //         $statement->bindParam(':productId', $productId);
    //         $statement->bindParam(':quantity', $newQuantity);
    //         $statement->execute();
    //     } else {
    //         $statement = $this->conn->prepare("INSERT INTO shoppingcart (productId, quantity) VALUES (:productId, 1)");
    //         $statement->bindParam(':productId', $productId);
    //         $statement->execute();
    //     }
    // }

    // public function getShoppingCartCount()
    // {
    //     $statement = $this->conn->prepare('SELECT COUNT(productId) AS count FROM shoppingcart');
    //     $statement->execute();
    //     $count = $statement->fetchAll(PDO::FETCH_ASSOC);
    //     return $count[0]['count'];
    // }

    // public function getShoppingCartProducts()
    // {
    //     $statement = $this->conn->prepare('SELECT p.description, p.productName, p.price, s.quantity FROM shoppingcart s LEFT JOIN products p 
    //     ON s.productId = p.id ORDER BY s.productId');
    //     $statement->execute();
    //     $shoppingCartEntries = $statement->fetchAll(PDO::FETCH_ASSOC);
    //     return $shoppingCartEntries;
    // }
}
