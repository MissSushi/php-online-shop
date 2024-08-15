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
    require_once 'dataBase.php';

    $db = new DatabaseConnection();

    $count = $db->getShoppingCartCount();

    ?>

    <a href="/add/">Produkt hinzufügen</a>
    <a href="./shopping-cart">Warenkorb </a><span>(<?php echo $count ?>)</span>

    <?php
    require_once 'dataBase.php';

    $db = new DatabaseConnection();

    $products = $db->getProducts();

    foreach ($products as $product): ?>
        <div><?php echo $product["productName"] ?></div>
        <div><?php echo $product["description"] ?></div>
        <div><?php echo $product["price"] ?>€</div>
        <form action="/deleteProduct.php" method="post" class="delete">
            <input type="hidden" name="productId" value="<?php echo $product["id"] ?>">
            <button type="submit">Löschen</button>
        </form>
        <a href="edit/?id=<?php echo $product["id"] ?>">Bearbeiten</a>

        <form action="./shopping-cart/addToCart.php" method="post">
            <input type="hidden" name="product-id" value="<?php echo $product["id"] ?>">
            <button type="submit" name="addToCart">In den Warenkorb</button>
        </form>
    <?php endforeach; ?>


</body>

</html>