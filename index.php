<?php
    session_start();

    function formatUserName(string $name): string
    {
        $name = mb_strtolower(trim($name), 'UTF-8');
        return mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8')
            . mb_substr($name, 1, null, 'UTF-8');
    }

    $nomUtilisateur = htmlspecialchars(formatUserName((string) ($_SESSION['nom'] ?? '')), ENT_QUOTES, 'UTF-8');
    $prenomUtilisateur = htmlspecialchars(formatUserName((string) ($_SESSION['prenom'] ?? '')), ENT_QUOTES, 'UTF-8');
    $compteUrl = $nomUtilisateur !== '' && $prenomUtilisateur !== '' ? 'compte.php' : 'connexion.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="description">
    <title>Frames & cores</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <h1 id="title">Frames & cores</h1>

    <?php if ($nomUtilisateur !== '' && $prenomUtilisateur !== ''): ?>
        <p class="welcome-message">Bienvenue <?php echo $prenomUtilisateur . ' ' . $nomUtilisateur; ?>.</p>
    <?php endif; ?>

    <div class="header-nav">
        <a class="nav-btn" href="index.php" aria-label="Accueil" title="Accueil"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M9.02 2.84004L3.63 7.04004C2.73 7.74004 2 9.23004 2 10.36V17.77C2 20.09 3.89 21.99 6.21 21.99H17.79C20.11 21.99 22 20.09 22 17.78V10.5C22 9.29004 21.19 7.74004 20.2 7.05004L14.02 2.72004C12.62 1.74004 10.37 1.79004 9.02 2.84004Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 17.99V14.99" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
        <a class="nav-btn" href="contact.html" aria-label="Contact" title="Contact"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M9.16006 10.87C9.06006 10.86 8.94006 10.86 8.83006 10.87C6.45006 10.79 4.56006 8.84 4.56006 6.44C4.56006 3.99 6.54006 2 9.00006 2C11.4501 2 13.4401 3.99 13.4401 6.44C13.4301 8.84 11.5401 10.79 9.16006 10.87Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.41 4C18.35 4 19.91 5.57 19.91 7.5C19.91 9.39 18.41 10.93 16.54 11C16.46 10.99 16.37 10.99 16.28 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.15997 14.56C1.73997 16.18 1.73997 18.82 4.15997 20.43C6.90997 22.27 11.42 22.27 14.17 20.43C16.59 18.81 16.59 16.17 14.17 14.56C11.43 12.73 6.91997 12.73 4.15997 14.56Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.3401 20C19.0601 19.85 19.7401 19.56 20.3001 19.13C21.8601 17.96 21.8601 16.03 20.3001 14.86C19.7501 14.44 19.0801 14.16 18.3701 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
        <a class="nav-btn" href="<?php echo $compteUrl; ?>" aria-label="Compte utilisateur" title="Compte utilisateur"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.50998C8.71997 7.69998 10.18 6.22998 12 6.22998C13.81 6.22998 15.28 7.69998 15.28 9.50998C15.27 11.28 13.88 12.72 12.12 12.78Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.74 19.3801C16.96 21.0101 14.6 22.0001 12 22.0001C9.40001 22.0001 7.04001 21.0101 5.26001 19.3801C5.36001 18.4401 5.96001 17.5201 7.03001 16.8001C9.77001 14.9801 14.25 14.9801 16.97 16.8001C18.04 17.5201 18.64 18.4401 18.74 19.3801Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
        <a class="nav-btn" href="panier.php" aria-label="Panier" title="Panier"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M7.5 7.67001V6.70001C7.5 4.45001 9.31 2.24001 11.56 2.03001C14.24 1.77001 16.5 3.88001 16.5 6.51001V7.89001" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.99999 22H15C19.02 22 19.74 20.39 19.95 18.43L20.7 12.43C20.97 9.99 20.27 8 16 8H7.99999C3.72999 8 3.02999 9.99 3.29999 12.43L4.04999 18.43C4.25999 20.39 4.97999 22 8.99999 22Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/><path d="M15.4955 12H15.5045" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.49451 12H8.50349" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
    </div>

    <div class="products-container">
        <!-- Section qui contient les articles -->
        
        <?php require_once('./includes/php/bd.php');
        $bdd = getBD();
        $rep = $bdd -> query(
            "SELECT id_art, nom, quantite, prix, url_photo, description
            FROM articles
            ORDER BY id_art"
        );

        $articles = $rep->fetchAll();

        foreach ($articles as $article):
            $id = (int) $article["id_art"];
            $nom = htmlspecialchars($article['nom'], ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars($article['description'], ENT_QUOTES, 'UTF-8');
            $urlPhoto = htmlspecialchars($article['url_photo'], ENT_QUOTES, 'UTF-8');
            $quantite = (int) $article['quantite'];
            $prix = number_format((float) $article['prix'], 2, ',', ' ');
        ?>
            <div class="card" data-url="article.php?id=<?php echo $id ?>">
                <div class="card-image">
                    <img src="<?php echo $urlPhoto ?>" alt="<?php echo $nom ?>" loading="lazy">
                </div>
                <div class="card-info">
                    <p class="product-id">Référence : PC-<?php echo $id ?></p>
                    <h2><?php echo $nom ?></h2>
                    <p class="description"><?php echo $description ?></p>
                    <p class="quantity">Quantité disponible : <?php echo $quantite ?></p>
                    <strong class="price"><?php echo $prix ?> €</strong>
                    <button class="btn-add-cart" data-name="<?php echo $nom ?>">Ajouter au panier</button>
                </div>
            </div>
        <?php endforeach; ?>

</div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="./assets/js/main.js"></script>
</body>

</html>