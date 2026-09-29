<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panier de produits">
    <title>Panier - Frames &amp; cores</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body class="cart-page">
    <h1 id="title">Mon panier</h1>

    <main class="cart-container">
        <p class="cart-empty">Votre panier est vide.</p>
        <div class="cart-items"></div>
        <div class="cart-actions">
            <a class="back-to-products" href="./index.php">Continuer mes achats</a>
            <button class="clear-cart" type="button">Vider le panier</button>
        </div>
    </main>

    <script src="./assets/js/panier.js"></script>
</body>

</html>