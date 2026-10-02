<?php
session_start();
require_once ('./includes/php/bd.php');

$id = filter_input(INPUT_POST, 'id_art', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_UNSAFE_RAW);
$article = null;

// Une action d'ajout nécessite une quantité entière strictement positive.
if ($action === 'add_to_cart') {
    $requestedQuantity = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT);
    if ($requestedQuantity === false || $requestedQuantity === null || $requestedQuantity < 1) {
        header('Location: article.php');
        exit;
    }
}

if ($id !== false && $id !== null) {
    $bdd = getBD();
    $requete = $bdd->prepare(
        'SELECT id_art, nom, quantite, prix, url_photo, description
        FROM articles
        WHERE id_art = ?'
    );
    $requete->execute([$id]);
    $article = $requete->fetch();
} // Un identifiant absent ou invalide laisse la fiche vide.

// Le stock est contrôlé avant d'enregistrer la quantité dans la session.
if ($article && $action === 'add_to_cart') {
    if ($requestedQuantity > (int) $article['quantite']) {
        $requestedQuantity = (int) $article['quantite'];
    }

    if ($requestedQuantity > 0) {
        $_SESSION['panier'] ??= [];
        $currentQuantity = (int) ($_SESSION['panier'][$id] ?? 0);
        $_SESSION['panier'][$id] = min($currentQuantity + $requestedQuantity, (int) $article['quantite']);
    }

    header('Location: panier.php');
    exit;
}

if ($article) {
    $id = (int) $article['id_art'];
    $nom = htmlspecialchars($article['nom'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($article['description'], ENT_QUOTES, 'UTF-8');
    $urlPhoto = htmlspecialchars($article['url_photo'], ENT_QUOTES, 'UTF-8');
    $quantite = (int) $article['quantite'];
    $prix = number_format((float) $article['prix'], 2, ',', ' ');
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Description du produit">
    <title><?php echo $article ? $nom : 'Article introuvable'; ?> - Frames &amp; cores</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <h1 id="title">Fiche produit</h1>

    <main class="product-detail">

        <?php if ($article): // Rend la fiche uniquement après une recherche réussie. ?>
            <div class="product-detail-image">
                <img src="<?php echo $urlPhoto; ?>" alt="<?php echo $nom; ?>">
            </div>
            <div class="product-detail-info">
                <p class="product-id">Référence : PC-<?php echo $id; ?></p>
                <h2><?php echo $nom; ?></h2>
                <p class="description"><?php echo $description; ?></p>
                <?php $stockClass = $quantite === 0 ? 'stock-not-ok' : 'stock-ok'; ?>
                <p class="quantity <?php echo $stockClass; ?>">Quantité disponible : <?php echo $quantite; ?></p>
                <strong class="price"><?php echo $prix; ?> €</strong>
                <?php if ($quantite !== 0): // Si l'article est disponible  ?>
                    <form class="add-to-cart-form" action="article.php" method="post">
                        <input type="hidden" name="id_art" value="<?php echo $id; ?>">
                        <input type="hidden" name="action" value="add_to_cart">
                        <label for="quantite">Quantité :</label>
                        <input type="number" id="quantite" name="quantite" min="1" max="<?php echo $quantite; ?>" value="1" required>
                        <button class="btn-add-cart" type="submit">Ajouter au panier</button>
                    </form>
                <?php endif; ?>
                <a class="back-to-products" href="./index.php">Retour aux produits</a>
            </div>

        <?php else: // Un résultat absent produit une réponse lisible plutôt qu'une erreur PHP. ?>
            <div class="product-detail-info">
                <h2>Article introuvable</h2>
                <p class="description">L'article demandé n'existe pas ou l'identifiant est invalide.</p>
                <a class="back-to-products" href="./index.php">Retour aux produits</a>
            </div>
        <?php endif; ?>
    
    </main>
</body>

</html>
