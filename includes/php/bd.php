<?php
function getBD()
{
    $bdd = new PDO('mysql:host=localhost;dbname=computer_database;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $bdd;
}
