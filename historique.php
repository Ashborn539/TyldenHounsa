<?php
session_start();
require_once ('./includes/php/bd.php');

$idClient = (int) ($_SESSION['id_client'] ?? 0);
// L'historique est strictement limité au client actuellement connecté.
if ($idClient === 0) {
    header('Location: connexion.php');
    exit;
}

$bdd = getBD();
$requete = $bdd->prepare(
    'SELECT c.id_commande, c.quantite, c.envoi, a.nom, a.prix
     FROM Commandes c
     INNER JOIN articles a ON a.id_art = c.id_art
     WHERE c.id_client = :id_client
     ORDER BY c.id_commande DESC'
);
// La jointure fournit les informations produit associées à chaque commande.
$requete->execute(['id_client' => $idClient]);
$commandes = $requete->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Historique des commandes">
    <title>Historique des commandes - Frames &amp; cores</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body class="formulaire">
    <h1>Historique des commandes</h1>
    <main class="order-history">
        <?php if ($commandes === []): ?>
            <p class="cart-empty">Vous n'avez pas encore passé de commande.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Commande</th>
                            <th>Article</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>État d'envoi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $commande): ?>
                            <tr>
                                <td><?php echo (int) $commande['id_commande']; ?></td>
                                <td><?php echo htmlspecialchars($commande['nom'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int) $commande['quantite']; ?></td>
                                <td><?php echo number_format((float) $commande['prix'] * (int) $commande['quantite'], 2, ',', ' '); ?> €</td>
                                <td><?php echo (int) $commande['envoi'] === 1 ? 'Envoyée' : 'En attente'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <p class="formulaire-link"><a href="./compte.php">Retour au compte</a></p>
    </main>
</body>
</html>
