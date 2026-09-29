<?php
require_once './includes/php/bd.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$article = null;

if ($id !== false && $id !== null) {
    $bdd = getBD();
    $requete = $bdd->prepare(
        'SELECT id_art, nom, quantite, prix, url_photo, description
        FROM articles
        WHERE id_art = ?'
    );
    $requete->execute([$id]);
    $article = $requete->fetch();
} // else article reste null

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

        <?php if (isset($article)): // si id et articles sont pas vide ?> 
            <div class="product-detail-image">
                <img src="<?php echo $urlPhoto; ?>" alt="<?php echo $nom; ?>">
            </div>
            <div class="product-detail-info">
                <p class="product-id">Référence : PC-<?php echo $id; ?></p>
                <h2><?php echo $nom; ?></h2>
                <p class="description"><?php echo $description; ?></p>
                <p class="quantity">Quantité disponible : <?php echo $quantite; ?></p>
                <strong class="price"><?php echo $prix; ?> €</strong>
                <a class="back-to-products" href="./index.php">Retour aux produits</a>
            </div>

        <?php else: // si id et article sont vide?>
            <div class="product-detail-info">
                <h2>Article introuvable</h2>
                <p class="description">L'article demandé n'existe pas ou l'identifiant est invalide.</p>
                <a class="back-to-products" href="./index.php">Retour aux produits</a>
            </div>
        <?php endif; ?>
    
    </main>
</body>

</html>
