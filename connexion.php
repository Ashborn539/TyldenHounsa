<?php
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connexion à votre compte">
    <title>Connexion - Frames &amp; cores</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body class="formulaire">
    <h1>Connexion</h1>

    <?php if ($error !== ''): ?>
        <p class="form-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="./includes/php/submit_connexion.php" method="post">
        <p>
            <label for="mail">Adresse e-mail :</label>
            <input type="email" id="mail" name="mail" autocomplete="email" required>
        </p>
        <p>
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" autocomplete="current-password" required>
        </p>
        <button type="submit" class="btn">Se connecter</button>
    </form>

    <p class="formulaire-link">
        Pas encore de compte ? <a href="./nouveau.php">Créer un compte</a>
    </p>
</body>

</html>
