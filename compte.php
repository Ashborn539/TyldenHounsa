<?php
session_start();

if (empty($_SESSION['nom']) || empty($_SESSION['prenom'])) {
    header('Location: ./connexion.php');
    exit;
}
function formatUserName(string $name): string
{
    $name = mb_strtolower(trim($name), 'UTF-8');
    return mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8')
        . mb_substr($name, 1, null, 'UTF-8');
}

$nomUtilisateur = htmlspecialchars(formatUserName((string) ($_SESSION['nom'] ?? '')), ENT_QUOTES, 'UTF-8');
$prenomUtilisateur = htmlspecialchars(formatUserName((string) ($_SESSION['prenom'] ?? '')), ENT_QUOTES, 'UTF-8');

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
    <form class="disconnect-form" action="./includes/php/disconnect.php" method="post" novalidate>
        <button type="submit" class="disconnect-btn">Se deconncter</button>
    </form>
</body>
</html>