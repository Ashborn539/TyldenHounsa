<?php
session_start();
require_once ('./includes/php/bd.php');
require_once ('./includes/php/csrf.php');
require_once ('./includes/php/reservation.php');

# Suppression de la reservation quand le panier est vidé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vider_panier'])) {
    requireValidCsrfToken();
    $idClient = (int) ($_SESSION['id_client'] ?? 0);

    if ($idClient > 0) {
        $bdd = getBD();
        $bdd->beginTransaction();
        try {
            cleanupExpiredReservations($bdd);
            cancelClientReservations($bdd, $idClient);
            $bdd->commit();
        } catch (Throwable $exception) {
            if ($bdd->inTransaction()) {
                $bdd->rollBack();
            }
            error_log($exception->getMessage());
        }
    }

    unset($_SESSION['panier']);
    header('Location: panier.php');
    exit;
}

$idClient = (int) ($_SESSION['id_client'] ?? 0);
$csrfToken = csrfToken();
$articles = [];
$total = 0.0;

# Parcours des articles réservé et donc dans le panier et cacule du temps restant
if ($idClient > 0) {
    $bdd = getBD();
    cleanupExpiredReservations($bdd);
    $reservations = fetchActiveReservations($bdd, $idClient);

    foreach ($reservations as $reservation) {
        # la date d'expiration est convertis et rendus constante
        $expiration = new DateTimeImmutable(
            (string) $reservation['date_expiration'],
            new DateTimeZone('UTC')
        );
        # Calcule du temps restant
        $remainingSeconds = max(0, $expiration->getTimestamp() - time());
        $quantite = (int) $reservation['quantite'];
        $sousTotal = $quantite * (float) $reservation['prix'];
        $reservation['quantite_panier'] = $quantite;
        $reservation['sous_total'] = $sousTotal;
        $reservation['secondes_restantes'] = $remainingSeconds;
        $articles[] = $reservation;
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
                            <p>Quantité réservée : <?php echo $article['quantite_panier']; ?></p>
                            <p class="reservation-countdown">
                                Réservation valable encore <?php echo (int) $article['secondes_restantes']; ?> seconde(s).
                            </p>
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
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <button class="clear-cart" type="submit" name="vider_panier">Vider le panier</button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <script src="./assets/js/panier.js"></script>
</body>

</html>
