<?php

declare(strict_types=1);

/**
 * Génère un token CSRF et le conserve parmi les deux tokens actifs de la session.
 *
 * @return string Token CSRF hexadécimal.
 * @throws Exception Si le générateur aléatoire sécurisé échoue.
 */
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

/**
 * Vérifie le token CSRF transmis par une requête POST.
 * Interrompt la requête avec HTTP 403 si le token est absent ou invalide.
 *
 * @return void
 */
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