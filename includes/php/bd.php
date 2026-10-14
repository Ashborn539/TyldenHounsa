<?php

/**
 * Ouvre une connexion PDO vers la base MySQL de l'application.
 * Configure le mode d'erreur et synchronise le fuseau horaire SQL sur UTC.
 *
 * @return PDO Connexion PDO prête à être utilisée.
 * @throws PDOException Si la connexion ou la configuration échoue.
 */
function getBD()
{
    $bdd = new PDO('mysql:host=localhost;dbname=computer_database;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->exec("SET time_zone = '+00:00'");
    return $bdd;
}
