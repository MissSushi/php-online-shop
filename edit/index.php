<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <script src="/index.js" defer></script> -->
</head>

<?php

require_once "../dataBase.php";

$db = new DatabaseConnection();

$id = (int)$_GET["id"];

$product = $db->getProduct($id);

?>

<body>
    <form action="editProduct.php" method="post">
        <input type="hidden" name="productId" value="<?php echo $product["id"] ?>">
        <label for="editName">Produktname bearbeiten</label>
        <input type="text" name="editName" id="editName" value="<?php echo $product["productName"] ?>">

        <label for="editDescription">Produtbeschreibung bearbeiten</label>
        <input type="text" name="editDescription" id="editDescription" value="<?php echo $product["description"] ?>">

        <label for="editPrice">Preis bearbeiten</label>
        <input type="text" name="editPrice" id="editPrice" value="<?php echo $product["price"] ?>">
        <button type="submit">Speichern</button>
    </form>
</body>

</html>