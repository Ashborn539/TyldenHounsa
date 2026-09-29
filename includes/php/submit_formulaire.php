<?php
session_start();

require_once ('/bd.php');

function redirectToRegistration(string $message): void
{
    header('Location: ../../nouveau.php?error=' . rawurlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToRegistration('Veuillez utiliser le formulaire d’inscription.');
}

$nom = trim((string) ($_POST['n'] ?? ''));
$prenom = trim((string) ($_POST['p'] ?? ''));
$adresse = trim((string) ($_POST['adr'] ?? ''));
$telephone = trim((string) ($_POST['num'] ?? ''));
$email = trim((string) ($_POST['mail'] ?? ''));
$motDePasse = (string) ($_POST['mdp1'] ?? '');
$confirmation = (string) ($_POST['mdp2'] ?? '');

if ($nom === '' || $prenom === '' || $adresse === '' || $telephone === '' || $email === '' || $motDePasse === '' || $confirmation === '') {
    redirectToRegistration('Tous les champs sont obligatoires.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectToRegistration('L’adresse e-mail est invalide.');
}

if (strlen($motDePasse) < 8) {
    redirectToRegistration('Le mot de passe doit contenir au moins 8 caractères.');
}

if ($motDePasse !== $confirmation) {
    redirectToRegistration('Les mots de passe ne correspondent pas.');
}

try {
    $bdd = getBD();
    $requete = $bdd->prepare(
        'INSERT INTO `user` (`nom`, `prenom`, `adr`, `num`, `mail`, `mdp`)
        VALUES (:nom, :prenom, :adresse, :telephone, :email, :mot_de_passe)'
    );

    $requete->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'adresse' => $adresse,
        'telephone' => $telephone,
        'email' => $email,
        'mot_de_passe' => password_hash($motDePasse, PASSWORD_DEFAULT),
    ]);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    redirectToRegistration('Impossible de créer le compte. Vérifiez votre adresse e-mail.');
}

$_SESSION['nom'] = $nom;
$_SESSION['prenom'] = $prenom;

header('Location: ../../index.php');
exit;
