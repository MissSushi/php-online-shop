<?php

require_once "./controller/ItemsController.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = $_SERVER["REQUEST_URI"];
$requestData = json_decode(file_get_contents('php://input'), true);

// Entfernen von Query-Parametern
$requestUri = explode('?', $requestUri)[0];

// Immer `/` am Ende einfügen.
if (!str_ends_with($requestUri, '/')) $requestUri .= '/';

// Routing
if ($requestMethod === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit(0);
}

if (preg_match('/^\/api\/categories\/(\d+)\/$/', $requestUri, $matches)) {
    $categoryId = $matches[1];
    handleCategory($requestMethod, $categoryId, $requestData);
} elseif ($requestUri === '/api/categories/') {
    handleCategories($requestMethod, $requestData);
} elseif (preg_match('/^\/api\/products\/(\d+)\/$/', $requestUri, $matches)) {
    $itemId = $matches[1];
    handleProduct($requestMethod, $itemId, $requestData);
} elseif ($requestUri === '/api/products/') {
    handleProducts($requestMethod, $requestData);
} elseif (preg_match('/^\/api\/images\/(\d+)\/$/', $requestUri, $matches)) {
    $itemId = $matches[1];
    handleFile($requestMethod, $itemId);
    echo $requestData;
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "Endpoint not found"]);
}

function handleCategory(string $method, int $categoryId, mixed $category)
{
    $controller = new ItemsController();

    switch ($method) {
        case "GET":
            break;
        case "PUT":
            break;
        case "DELETE":
            break;
    }
}

function handleCategories(string $method, mixed $category)
{
    $controller = new ItemsController();

    switch ($method) {
        case "POST":
            $controller->createCategory($category);
            break;
    }
}

function handleFile(string $method, int $fileId)
{
    $controller = new ItemsController();

    switch ($method) {
        case "GET":
            $controller->readFile($fileId);
            break;
    }
}

function handleProducts(string $method, mixed $item)
{
    $controller = new ItemsController();


    switch ($method) {
        case "GET":
            if (isset($_GET['limit']) && isset($_GET['page']) && isset($_GET['sortBy']) && isset($_GET['filterBy']) && isset($_GET['search'])) {
                $limit = intval($_GET['limit']);
                $page = intval($_GET['page']);
                $sort = strval($_GET['sortBy']);
                $filter = strval($_GET['filterBy']);
                $search = strval($_GET['search']);

                $offset = ($page - 1) * $limit;
            } else {
                var_dump($_GET);
                echo "Fehler: limit, sortBy oder page fehlen.";
                header("HTTP/1.1 400 Bad Request");
                die;
            }

            $controller->readAllProducts($offset, $limit, $sort, $filter, $search);
            break;
        case "POST":
            $controller->createProduct($item);
            break;
        default:
            header("HTTP/1.1 405 Method not allowed");
            echo json_encode(["message" => "Method not allowed."]);
            break;
    }
}

function handleProduct(string $method, int $id, mixed $item)
{
    $controller = new ItemsController();

    switch ($method) {
        case "GET":
            $controller->readProduct($id);
            break;
        case "PUT":
            $controller->updateProduct($id, $item);
            break;
        case "DELETE":
            $controller->deleteProduct($id);
            break;
        default:
            header("HTTP/1.1 405 Method not allowed");
            echo json_encode(["message" => "Method not allowed."]);
            break;
    }
}
