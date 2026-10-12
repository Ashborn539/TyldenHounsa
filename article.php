<?php
session_start();
require_once ('./includes/php/bd.php');
require_once ('./includes/php/csrf.php');
require_once ('./includes/php/reservation.php');

$id = filter_input(INPUT_POST, 'id_art', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_UNSAFE_RAW);
$article = null;

// Toutes les requêtes POST de cette page proviennent d'un formulaire protégé.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrfToken();
}

// Recherche commune à la consultation et à l'ajout au panier.
if ($id !== false && $id !== null) {
    $bdd = getBD();
    cleanupExpiredReservations($bdd);
    $requete = $bdd->prepare(
        'SELECT id_art, nom, quantite, prix, url_photo, description
        FROM articles
        WHERE id_art = ?'
    );
    $requete->execute([$id]);
    $article = $requete->fetch();
} // Un identifiant absent ou invalide laisse la fiche vide.

// Les contrôles de quantité et de stock ne concernent que l'ajout au panier.
if (is_array($article) && $action === 'add_to_cart') {
    $idClient = (int) ($_SESSION['id_client'] ?? 0);
    if ($idClient === 0) {
        header('Location: connexion.php');
        exit;
    }

    $requestedQuantity = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT);
    if ($requestedQuantity === false || $requestedQuantity === null || $requestedQuantity < 1) {
        header('Location: article.php');
        exit;
    }

    try {
        $bdd->beginTransaction();
        cleanupExpiredReservations($bdd);
        $reservedQuantity = reserveArticle($bdd, $idClient, (int) $id, $requestedQuantity);
        $bdd->commit();

        $_SESSION['panier'] ??= [];
        $_SESSION['panier'][$id] = $reservedQuantity;
    } catch (Throwable $exception) {
        if (isset($bdd) && $bdd->inTransaction()) {
            $bdd->rollBack();
        }
        error_log($exception->getMessage());
        header('Location: article.php');
        exit;
    }

    header('Location: panier.php');
    exit;
}

if (is_array($article)) {
    $csrfToken = csrfToken();
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
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
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
