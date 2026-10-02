<?php
session_start();
require_once ('./includes/php/bd.php');

// La suppression est traitée côté serveur afin de rester cohérente avec la session.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vider_panier'])) {
    unset($_SESSION['panier']);
    header('Location: panier.php');
    exit;
}

$panier = $_SESSION['panier'] ?? [];
$articles = [];
$total = 0.0;

// Les détails et les prix sont toujours relus depuis la base, jamais depuis la session.
if ($panier !== []) {
    $bdd = getBD();
    $requete = $bdd->prepare(
        'SELECT id_art, nom, quantite, prix
         FROM articles
         WHERE id_art = ?'
    );

    foreach ($panier as $id => $quantitePanier) {
        $requete->execute([(int) $id]);
        $article = $requete->fetch();
        if (!$article) {
            continue;
        }

        // Évite d'afficher une quantité supérieure au stock courant.
        $quantite = min((int) $quantitePanier, (int) $article['quantite']);
        if ($quantite < 1) {
            continue;
        }

        $sousTotal = $quantite * (float) $article['prix'];
        $article['quantite_panier'] = $quantite;
        $article['sous_total'] = $sousTotal;
        $articles[] = $article;
        $total += $sousTotal;
    }
}
?>
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
        <?php if ($articles === []): ?>
            <p class="cart-empty">Votre panier est vide.</p>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($articles as $article): ?>
                    <div class="cart-item">
                        <div>
                            <h2><?php echo htmlspecialchars($article['nom'], ENT_QUOTES, 'UTF-8'); ?></h2>
                            <p>Quantité : <?php echo $article['quantite_panier']; ?></p>
                        </div>
                        <strong><?php echo number_format($article['sous_total'], 2, ',', ' '); ?> €</strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="cart-total"><strong>Total : <?php echo number_format($total, 2, ',', ' '); ?> €</strong></p>
        <?php endif; ?>
        <div class="cart-actions">
            <a class="back-to-products" href="./index.php">Continuer mes achats</a>
            <?php if ($articles !== []): ?>
                <a class="back-to-products" href="./acheter.php">Valider la commande</a>
                <form action="panier.php" method="post">
                    <button class="clear-cart" type="submit" name="vider_panier">Vider le panier</button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <script src="./assets/js/panier.js"></script>
</body>

</html>
