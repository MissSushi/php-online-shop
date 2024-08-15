<?php
require_once 'dataBase.php';


function routeHandler(IProductManager $db)
{
    $productId = $_POST['productId'];
    $db->deleteProduct((int)$productId);

    header('Location: http://localhost');
    exit();
}

routeHandler(new DatabaseConnection());
