<?php
require_once '../dataBase.php';

function routeHandler(IShoppingCartManager $scm)
{
    $productId = $_POST['product-id'];
    $scm->addToShoppingCart($productId);

    header('Location: http://localhost');
    exit();
}

routeHandler(new DatabaseConnection());
