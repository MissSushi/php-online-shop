<?php
require_once '../dataBase.php';


function routeHandler(IProductManager $db)
{
    $productId = $_POST['productId'];
    $name = $_POST['editName'];
    $description = $_POST['editDescription'];
    $price = $_POST['editPrice'];

    $db->editProduct((int)$productId, $name, $description, $price);

    header('Location: http://localhost');
    exit();
}

routeHandler(new DatabaseConnection());
