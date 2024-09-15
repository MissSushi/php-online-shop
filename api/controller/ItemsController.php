<?php
require_once "../dataBase.php";

interface IRestController
{
    public function createItem(mixed $item);
    public function readAll(int $offset, int $limit, string $sort, string $filter, string $search);
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
        $status = $item["status"];

        $lastId = $this->db->addProduct($description, $name, $price, $status);
        echo json_encode([
            'id' => (int)$lastId
        ]);
    }

    public function readAll(int $offset, int $limit, string $sort, string $filter, string $search)
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
        $status = $item["status"];

        $this->db->editProduct($id, $name, $description, $price, $status);
    }

    public function deleteItem(int $id)
    {
        header('Content-Type: application/json');
        $this->db->deleteProduct($id);
    }
}
