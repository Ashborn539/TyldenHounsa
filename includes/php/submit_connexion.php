<?php
declare(strict_types=1);

session_start();
require_once ('./bd.php');

function redirectToLogin(string $message): void
{
    header('Location: ../../connexion.php?error=' . rawurlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToLogin('Veuillez utiliser le formulaire de connexion.');
}

// Les données d'authentification sont validées avant toute requête SQL.
$email = trim((string) ($_POST['mail'] ?? ''));
$motDePasse = (string) ($_POST['mdp'] ?? '');

if ($email === '' || $motDePasse === '') {
    redirectToLogin('L’adresse e-mail et le mot de passe sont obligatoires.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectToLogin('L’adresse e-mail est invalide.');
}

try {
    $bdd = getBD();
    $requete = $bdd->prepare(
        'SELECT id_user, nom, prenom, mdp
         FROM `user`
         WHERE mail = :email
         LIMIT 1'
    );
    $requete->execute(['email' => $email]);
    $utilisateur = $requete->fetch();
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    redirectToLogin('Une erreur est survenue. Veuillez réessayer.');
}

if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mdp'])) {
    redirectToLogin('Adresse e-mail ou mot de passe incorrect.');
}

session_regenerate_id(true);
// L'identifiant client sert à rattacher le panier validé et l'historique au compte.
$_SESSION['id_client'] = (int) $utilisateur['id_user'];
$_SESSION['nom'] = $utilisateur['nom'];
$_SESSION['prenom'] = $utilisateur['prenom'];

header('Location: ../../index.php');
exit;
