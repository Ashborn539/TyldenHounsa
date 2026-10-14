<?php
session_start();
require_once ('./includes/php/bd.php');
require_once ('./includes/php/csrf.php');
require_once ('./includes/php/reservation.php');

$erreur = '';
$commandeEnregistree = false;
$idClient = (int) ($_SESSION['id_client'] ?? 0);

# Une commande doit être rattachée à un compte identifié.
if ($idClient === 0) {
    header('Location: connexion.php');
    exit;
}

# recuperation des resa actives du client
$bdd = getBD();
cleanupExpiredReservations($bdd);
$reservations = fetchActiveReservations($bdd, $idClient);

if ($reservations === []) {
    header('Location: panier.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrfToken();

try {
    # verouillage des lignes de stock jusqu'à la fin de la transaction.
    $bdd->beginTransaction();
    cleanupExpiredReservations($bdd);
    $reservations = lockActiveReservations($bdd, $idClient);
    if ($reservations === []) {
        throw new RuntimeException('Les réservations du panier ont expiré.');
    }

    $commandeRequete = $bdd->prepare(
        'INSERT INTO Commandes (id_art, id_client, quantite, envoi)
        VALUES (:id_art, :id_client, :quantite, FALSE)'
    );
    $confirmationRequete = $bdd->prepare(
        "UPDATE reservations
        SET statut = 'confirmee'
        WHERE id_reservation = :id_reservation AND statut = 'active'"
    );

    foreach ($reservations as $reservation) {
        $idArt = (int) $reservation['id_art'];
        $quantite = (int) $reservation['quantite'];

        $commandeRequete->execute([
            'id_art' => $idArt,
            'id_client' => $idClient,
            'quantite' => $quantite,
        ]);
        $confirmationRequete->execute([
            'id_reservation' => (int) $reservation['id_reservation'],
        ]);
    }

    # Le panier n'est supprimé qu'après la validation des opérations SQL.
    $bdd->commit();
    unset($_SESSION['panier']);
    $commandeEnregistree = true;
} catch (Throwable $exception) {
    if (isset($bdd) && $bdd->inTransaction()) {
        $bdd->rollBack();
    }
    error_log($exception->getMessage());
    $erreur = 'La commande n’a pas pu être enregistrée. Veuillez réessayer.';
}
}

$csrfToken = csrfToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Validation de la commande">
    <title>Commande - Frames &amp; cores</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body class="formulaire">
    <h1>Commande</h1>
    <?php if ($commandeEnregistree): ?>
        <p class="form-success">Votre commande a bien été enregistrée.</p>
        <p class="formulaire-link"><a href="./index.php">Retour à l'accueil</a></p>
    <?php elseif ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
        <p>Confirmez votre commande pour la valider.</p>
        <form action="acheter.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn">Confirmer la commande</button>
        </form>
        <p class="formulaire-link"><a href="./panier.php">Retour au panier</a></p>
    <?php else: ?>
        <p class="form-error"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="formulaire-link"><a href="./panier.php">Retour au panier</a></p>
    <?php endif; ?>
</body>
</html>
