<?php
require_once '../dataBase.php';

function routeHandler(IProductManager $db)
{
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productPrice = $_POST['productPrice'];

    $db->addProduct($productDescription, $productName, $productPrice);


    header('Location: http://localhost');
    exit();
}

routeHandler(new DatabaseConnection());
