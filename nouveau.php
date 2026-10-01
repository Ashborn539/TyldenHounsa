<?php
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="description">
    <meta name="author" content="author">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Frames & cores - Sign Up</title>
</head>
<body class="formulaire">

    <h1>Créer un compte</h1>
    <?php if ($error !== ''): ?>
        <p class="form-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="./includes/php/submit_formulaire.php" method="post">
        <p>
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="n" required>
        </p>
        <p>
            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="p" required>
        </p>
        <p>
            <label for="adresse">Adresse:</label>
            <input type="text" id="adresse" name="adr" required>
        </p>
        <p> 
            <label for="num">Téléphone:</label>
            <input type="text" id="num" name="num" required>
        </p>
        <p>
            <label for="mail">Email:</label>
            <input type="email" id="mail" name="mail" required>
        </p>
        <p>
            <label for="mdp1">Mot de passe:</label>
            <input type="password" id="mdp1" name="mdp1" required>
        </p>
        <p>
            <label for="mdp2">Confirmer le mot de passe:</label>
            <input type="password" id="mdp2" name="mdp2" required>
        </p>

        <button type="submit" class="btn">Confirmer</button>
    </form>
</body>
</html>