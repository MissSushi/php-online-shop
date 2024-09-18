<?php
require_once "../dataBase.php";

interface IRestController
{
    public function createProduct(mixed $item);
    public function createCategory(mixed $category);
    public function readAllProducts(int $offset, int $limit, string $sort, string $filter, string $search);
    public function readProduct(int $id);
    public function readFile(int $fileId);
    public function updateProduct(int $id, mixed $item);
    public function deleteProduct(int $id);
}
class ItemsController implements IRestController
{
    private DatabaseConnection $db;

    public function __construct()
    {
        $this->db = new DatabaseConnection();
    }

    public function uuidGenerator($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function readFile(int $fileId)
    {
        $filePath = $this->db->getFilePath($fileId);
        if ($filePath === null) {
            http_response_code(404);
        }

        $mimeType = mime_content_type($filePath);
        header("Content-Type: " . $mimeType);
        echo file_get_contents($filePath);
    }

    public function createCategory(mixed $category)
    {
        $description = $category["description"];
        $file = $category["image"];
        $name = $category["category"];
        $status = $category["status"];
        $fileName = $this->uuidGenerator();

        $fileId = $this->db->addFile($file, $fileName);
        $lastId = $this->db->addCategory($name, $status, $description, $fileId);
        echo json_encode([
            'id' => (int)$lastId
        ]);
    }

    public function createProduct(mixed $item)
    {

        $description = $item["description"];
        $price = $item["price"];
        $name = $item["name"];
        $status = $item["status"];

        $lastId = $this->db->addProduct($description, $name, $price, $status);
        echo json_encode([
            'id' => (int)$lastId
        ]);
    }

    public function readAllProducts(int $offset, int $limit, string $sort, string $filter, string $search)
    {
        header('Content-Type: application/json');
        $products = $this->db->getProducts($offset, $limit, $sort, $filter, $search);
        $countProducts = $this->db->getCount($sort, $filter, $search);
        $countPages = ceil($countProducts / $limit);
        echo json_encode([
            'count' => $countProducts,
            'countPages' => $countPages,
            'products' => $products
        ]);
    }

    public function readProduct(int $id)
    {
        header('Content-Type: application/json');
        $result = $this->db->getProduct($id);
        if ($result === null) {
            http_response_code(404);
        }
        echo json_encode($result);
    }

    public function updateProduct(int $id, mixed $item)
    {
        header('Content-Type: application/json');

        $description = $item["description"];
        $price = $item["price"];
        $name = $item["name"];
        $status = $item["status"];

        $this->db->editProduct($id, $name, $description, $price, $status);
    }

    public function deleteProduct(int $id)
    {
        header('Content-Type: application/json');
        $this->db->deleteProduct($id);
    }
}
