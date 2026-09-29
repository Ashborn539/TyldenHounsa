<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/bd.php';

function redirectToLogin(string $message): void
{
    header('Location: ../../connexion.php?error=' . rawurlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToLogin('Veuillez utiliser le formulaire de connexion.');
}

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
        'SELECT nom, prenom, mdp
         FROM `user`
         WHERE mail = :email
         LIMIT 1'
    );
    $requete->execute(['email' => $email]);
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    redirectToLogin('Une erreur est survenue. Veuillez réessayer.');
}

if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mdp'])) {
    redirectToLogin('Adresse e-mail ou mot de passe incorrect.');
}

session_regenerate_id(true);
$_SESSION['nom'] = $utilisateur['nom'];
$_SESSION['prenom'] = $utilisateur['prenom'];

header('Location: ../../index.php');
exit;
