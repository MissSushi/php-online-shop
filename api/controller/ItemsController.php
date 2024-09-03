<?php
require_once "../dataBase.php";

interface IRestController
{
    public function createItem(mixed $item);
    public function readAll();
    public function readItem(int $id);
    public function updateItem(int $id, mixed $item);
    public function deleteItem(int $id);
}
class ItemsController implements IRestController
{
    private DatabaseConnection $db;

    public function __construct()
    {
        $this->db = new DatabaseConnection();
    }

    public function createItem(mixed $item)
    {

        $description = $item["description"];
        $price = $item["price"];
        $name = $item["name"];

        $lastId = $this->db->addProduct($description, $name, $price);
        echo json_encode([
            'id' => (int)$lastId
        ]);
    }

    public function readAll()
    {
        header('Content-Type: application/json');
        $result = $this->db->getProducts();
        echo json_encode($result);
    }

    public function readItem(int $id)
    {
        header('Content-Type: application/json');
        $result = $this->db->getProduct($id);
        if ($result === null) {
            http_response_code(404);
        }
        echo json_encode($result);
    }

    public function updateItem(int $id, mixed $item)
    {
        header('Content-Type: application/json');

        $description = $item["description"];
        $price = $item["price"];
        $name = $item["name"];

        $this->db->editProduct($id, $name, $description, $price);
    }

    public function deleteItem(int $id)
    {
        header('Content-Type: application/json');
        $this->db->deleteProduct($id);
    }
}
