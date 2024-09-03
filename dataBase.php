<?php


interface IProductManager
{
    public function addProduct(string $name, string $description, int $price);
    public function getProducts();
    public function deleteProduct(int $productId);
    public function getProduct(int $productId);
    public function editProduct(int $productId, string $name, string $description, int $price);
}

interface IShoppingCartManager
{
    public function addToShoppingCart(int $productId);
    public function getShoppingCartCount();
    public function getShoppingCartProducts();
}

class DatabaseConnection implements IProductManager, IShoppingCartManager
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

    public function addProduct(string $description, string $name, int $price)
    {
        $statement = $this->conn->prepare("INSERT INTO products (description, productName, price) VALUES (:description, :productName, :price)");
        $statement->bindParam(':description', $description);
        $statement->bindParam(':productName', $name);
        $statement->bindParam(':price', $price);

        $statement->execute();

        $last_id = $this->conn->lastInsertId();
        return $last_id;
    }

    public function getProducts()
    {
        $statement = $this->conn->prepare("SELECT productName, description, price, id FROM products");
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getProduct(int $productId)
    {
        $statement = $this->conn->prepare("SELECT productName, description, price, id FROM products WHERE id = :id");
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

    public function editProduct(int $productId, string $name, string $description, int $price)
    {
        $statement = $this->conn->prepare("UPDATE products SET productName = :productName, description = :description, price = :price WHERE id = :id");
        $statement->bindParam(':id', $productId);
        $statement->bindParam(':productName', $name);
        $statement->bindParam(':description', $description);
        $statement->bindParam(':price', $price);

        $statement->execute();
    }

    public function addToShoppingCart(int $productId)
    {
        $shoppingCartDataStatement = $this->conn->prepare('SELECT quantity FROM shoppingcart WHERE productId = :productId');
        $shoppingCartDataStatement->bindParam(':productId', $productId);
        $shoppingCartDataStatement->execute();
        $shoppingCartData = $shoppingCartDataStatement->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($shoppingCartData)) {
            $newQuantity = $shoppingCartData[0]['quantity'] + 1;

            $statement = $this->conn->prepare('UPDATE shoppingcart SET quantity = :quantity WHERE productId = :productId');
            $statement->bindParam(':productId', $productId);
            $statement->bindParam(':quantity', $newQuantity);
            $statement->execute();
        } else {
            $statement = $this->conn->prepare("INSERT INTO shoppingcart (productId, quantity) VALUES (:productId, 1)");
            $statement->bindParam(':productId', $productId);
            $statement->execute();
        }
    }

    public function getShoppingCartCount()
    {
        $statement = $this->conn->prepare('SELECT COUNT(productId) AS count FROM shoppingcart');
        $statement->execute();
        $count = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $count[0]['count'];
    }

    public function getShoppingCartProducts()
    {
        $statement = $this->conn->prepare('SELECT p.description, p.productName, p.price, s.quantity FROM shoppingcart s LEFT JOIN products p 
        ON s.productId = p.id ORDER BY s.productId');
        $statement->execute();
        $shoppingCartEntries = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $shoppingCartEntries;
    }
}
