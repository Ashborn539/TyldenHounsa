<?php
session_start();
require_once ('./includes/php/bd.php');

$erreur = '';
$commandeEnregistree = false;
$idClient = (int) ($_SESSION['id_client'] ?? 0);
$panier = $_SESSION['panier'] ?? [];

// Une commande doit être rattachée à un compte identifié.
if ($idClient === 0) {
    header('Location: connexion.php');
    exit;
}

if ($panier === []) {
    header('Location: panier.php');
    exit;
}

try {
    // Les lignes de stock sont verrouillées jusqu'à la fin de la transaction.
    $bdd = getBD();
    $bdd->beginTransaction();

    $articleRequete = $bdd->prepare(
        'SELECT id_art, quantite
         FROM articles
         WHERE id_art = :id_art
         FOR UPDATE'
    );
    $commandeRequete = $bdd->prepare(
        'INSERT INTO Commandes (id_art, id_client, quantite, envoi)
         VALUES (:id_art, :id_client, :quantite, FALSE)'
    );
    $stockRequete = $bdd->prepare(
        'UPDATE articles
         SET quantite = quantite - :quantite
         WHERE id_art = :id_art'
    );

    foreach ($panier as $idArt => $quantitePanier) {
        $idArt = (int) $idArt;
        $quantite = filter_var($quantitePanier, FILTER_VALIDATE_INT);
        if ($idArt < 1 || $quantite === false || $quantite < 1) {
            throw new RuntimeException('Le panier contient une quantité invalide.');
        }

        $articleRequete->execute(['id_art' => $idArt]);
        $article = $articleRequete->fetch();
        // Le stock peut avoir changé depuis l'ajout au panier.
        if (!$article || $quantite > (int) $article['quantite']) {
            throw new RuntimeException('Le stock disponible a changé pour un article.');
        }

        $commandeRequete->execute([
            'id_art' => $idArt,
            'id_client' => $idClient,
            'quantite' => $quantite,
        ]);
        $stockRequete->execute([
            'id_art' => $idArt,
            'quantite' => $quantite,
        ]);
    }

    // Le panier n'est supprimé qu'après la validation des opérations SQL.
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
    <?php else: ?>
        <p class="form-error"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="formulaire-link"><a href="./panier.php">Retour au panier</a></p>
    <?php endif; ?>
</body>
</html>
