<?php
session_start();
require_once ('./includes/php/csrf.php');

if (empty($_SESSION['nom']) || empty($_SESSION['prenom'])) {
    header('Location: ./connexion.php');
    exit;
}

/**
 * Normalise un nom pour son affichage avec une première lettre en majuscule.
 *
 * @param string $name Nom à normaliser.
 * @return string Nom normalisé.
 */
function formatUserName(string $name): string
{
    $name = mb_strtolower(trim($name), 'UTF-8');
    return mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8')
        . mb_substr($name, 1, null, 'UTF-8');
}

$nomUtilisateur = htmlspecialchars(formatUserName((string) ($_SESSION['nom'] ?? '')), ENT_QUOTES, 'UTF-8');
$prenomUtilisateur = htmlspecialchars(formatUserName((string) ($_SESSION['prenom'] ?? '')), ENT_QUOTES, 'UTF-8');
$csrfToken = csrfToken();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Votre compte utilisateur">
    <title>Mon compte - Frames &amp; cores</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body class="formulaire">
    <h1>Mon compte</h1>
    <p>Bienvenue <?php echo $prenomUtilisateur . ' ' . $nomUtilisateur; ?>.</p>
    <p><a class="back-btn-account" href="./index.php">Retour à l'accueil</a></p>
    <p><a class="back-btn-account" href="./historique.php">Historique des commandes</a></p>
    <form class="disconnect-form" action="./includes/php/disconnect.php" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="disconnect-btn">Se deconncter</button>
    </form>
</body>
</html>