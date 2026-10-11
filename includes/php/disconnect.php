<?php
session_start();
require_once ('./csrf.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	exit('Méthode non autorisée.');
}

requireValidCsrfToken();

$_SESSION = [];
session_destroy();

header('Location: ../../index.php');
exit;
