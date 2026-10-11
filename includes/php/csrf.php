<?php

declare(strict_types=1);

function csrfToken(): string
{
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_tokens'] ??= [];
    array_push($_SESSION['csrf_tokens'], $token);

    if (count($_SESSION['csrf_tokens']) > 2) {
        array_shift($_SESSION['csrf_tokens']);
    }

    return $token;
}

function requireValidCsrfToken(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');
    $tokens = $_SESSION['csrf_tokens'] ?? [];

    foreach ($tokens as $validToken) {
        if (is_string($validToken) && hash_equals($validToken, $token)) {
            return;
        }
    }

    http_response_code(403);
    exit('Requête refusée. Le jeton de sécurité est absent ou expiré.');
}