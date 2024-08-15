<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="/index.js" defer></script>
</head>

<body>

    <?php
    require_once '../dataBase.php';

    $db = new DatabaseConnection();

    $shoppingCartProducts = $db->getShoppingCartProducts();

    foreach ($shoppingCartProducts as $product): ?>
        <div><?php echo $product["productName"] ?></div>
        <div><?php echo $product["description"] ?></div>
        <div><?php echo $product["price"] ?>€</div>

    <?php endforeach; ?>

</body>

</html>