<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="/index.js" defer></script>
</head>

<body>
    <form action="addProduct.php" method="post">
        <label for="productName">Produktname</label>
        <input type="text" name="productName" id="productName">
        <label for="productDescription">Beschreibung</label>
        <input type="text" name="productDescription" id="productDescription">
        <label for="productPrice">Preis</label>
        <input type="text" name="productPrice" id="productPrice">
        <button type="submit">Speichern</button>
    </form>
</body>

</html>