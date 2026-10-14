<?php

declare(strict_types=1);

// Modifiez ces deux valeurs pour ajuster les durees sans changer la logique metier.
const CART_RESERVATION_DURATION_SECONDS = 15 * 60;
const LAST_STOCK_RESERVATION_DURATION_SECONDS = 5 * 60;

/**
 * Recherche les reservations actives arrivees a expiration.
 * Restitue les quantites au stock, marque les reservations comme expirees
 * et gere une transaction si aucune transaction n'est deja ouverte.
 *
 * @return int Nombre de reservations expirees.
 */
function cleanupExpiredReservations(PDO $bdd): int
{
    $startedTransaction = false;

    if (!$bdd->inTransaction()) {
        $bdd->beginTransaction();
        $startedTransaction = true;
    }

    try {
        $reservationQuery = $bdd->query(
            "SELECT id_reservation, id_art, quantite
            FROM reservations
            WHERE statut = 'active' AND date_expiration <= NOW()
            FOR UPDATE"
        );
        $expiredReservations = $reservationQuery->fetchAll();

        $restoreStockQuery = $bdd->prepare(
            'UPDATE articles
            SET quantite = quantite + :quantite
            WHERE id_art = :id_art'
        );

        $expireReservationQuery = $bdd->prepare(
            "UPDATE reservations
            SET statut = 'expiree'
            WHERE id_reservation = :id_reservation AND statut = 'active'"
        );

        foreach ($expiredReservations as $reservation) {
            # Pour chaque reservation expirée on restaure le stock
            $restoreStockQuery->execute([
                'quantite' => (int) $reservation['quantite'],
                'id_art' => (int) $reservation['id_art'],
            ]);
            # On les marques comme expiree
            $expireReservationQuery->execute([
                'id_reservation' => (int) $reservation['id_reservation'],
            ]);
        }

        if ($startedTransaction) {
            $bdd->commit();
        }

        return count($expiredReservations);
    } catch (Throwable $exception) {
        if ($startedTransaction && $bdd->inTransaction()) {
            $bdd->rollBack();
        }
        throw $exception;
    }
}

/**
 * Reserve une quantite d'article pour un client.
 * Verrouille l'article, controle le stock, cree ou met a jour la reservation,
 * puis diminue le stock disponible. La duree depend du stock restant.
 *
 * @return int Quantite totale reservee par le client pour cet article.
 * @throws InvalidArgumentException Si les donnees fournies sont invalides.
 * @throws RuntimeException Si l'article n'existe pas ou si le stock est insuffisant.
 */
function reserveArticle(PDO $bdd, int $idClient, int $idArticle, int $quantity): int
{
    if ($idClient < 1 || $idArticle < 0 || $quantity < 1) {
        # Si les parametres sint invalides
        throw new InvalidArgumentException('Les donnees de reservation sont invalides.');
    }

    # Liste des infos sur l'article a reserver
    # La transaction est ouverte par l'appelant.
    $articleQuery = $bdd->prepare(
        'SELECT id_art, quantite
        FROM articles
        WHERE id_art = :id_art
        FOR UPDATE'
    );
    $articleQuery->execute(['id_art' => $idArticle]);
    $article = $articleQuery->fetch();

    if (!$article || $quantity > (int) $article['quantite']) {
    # Si la quantite demandee depasse celle disponible
    throw new RuntimeException('Le stock disponible est insuffisant.');
    }

    # Liste des reservations active pour le client
    $existingQuery = $bdd->prepare(
        "SELECT id_reservation, quantite
        FROM reservations
        WHERE id_client = :id_client
            AND id_art = :id_art
            AND statut = 'active'
        FOR UPDATE"
    );
    $existingQuery->execute([
        'id_client' => $idClient,
        'id_art' => $idArticle,
    ]);
    $existingReservation = $existingQuery->fetch();

    $remainingStock = (int) $article['quantite'] - $quantity;
    # Si il s'agit du/des derniers exemplaires de l'article ?... sinon :... 
    $duration = $remainingStock === 0
        ? LAST_STOCK_RESERVATION_DURATION_SECONDS
        : CART_RESERVATION_DURATION_SECONDS;
    $expiration = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
        ->modify('+' . $duration . ' seconds')
        ->format('Y-m-d H:i:s');

    if ($existingReservation) {
        # Si il existe deja une reservation active pour cet article, on la maj
        $totalQuantity = (int) $existingReservation['quantite'] + $quantity;
        $reservationQuery = $bdd->prepare(
            'UPDATE reservations
            SET quantite = :quantite,
                date_expiration = :date_expiration
            WHERE id_reservation = :id_reservation'
        );
        $reservationQuery->execute([
            'quantite' => $totalQuantity,
            'date_expiration' => $expiration,
            'id_reservation' => (int) $existingReservation['id_reservation'],
        ]);
    } else { # Sinon on crée la reservation 
        $totalQuantity = $quantity;
        $reservationQuery = $bdd->prepare(
            "INSERT INTO reservations
                (id_client, id_art, quantite, date_reservation, date_expiration, statut)
            VALUES
                (:id_client, :id_art, :quantite, NOW(), :date_expiration, 'active')"
        );
        $reservationQuery->execute([
            'id_client' => $idClient,
            'id_art' => $idArticle,
            'quantite' => $quantity,
            'date_expiration' => $expiration,
        ]);
    }

    # On mets a jour le stock
    $stockQuery = $bdd->prepare(
        'UPDATE articles
        SET quantite = quantite - :quantite
        WHERE id_art = :id_art'
    );
    $stockQuery->execute([
        'quantite' => $quantity,
        'id_art' => $idArticle,
    ]);

    return $totalQuantity;
}

/**
 * Annule toutes les reservations actives d'un client.
 * Restitue chaque quantite au stock et change le statut des reservations
 * concernees en annulee.
 */
function cancelClientReservations(PDO $bdd, int $idClient): void
{
    # Liste des reservations active du client
    $reservationQuery = $bdd->prepare(
        "SELECT id_reservation, id_art, quantite
        FROM reservations
        WHERE id_client = :id_client AND statut = 'active'
        FOR UPDATE"
    );
    $reservationQuery->execute(['id_client' => $idClient]);
    $reservations = $reservationQuery->fetchAll();


    $restoreStockQuery = $bdd->prepare(
        'UPDATE articles
        SET quantite = quantite + :quantite
        WHERE id_art = :id_art'
    );
    $cancelQuery = $bdd->prepare(
        "UPDATE reservations
        SET statut = 'annulee'
        WHERE id_reservation = :id_reservation AND statut = 'active'"
    );

    foreach ($reservations as $reservation) {
        # Pour chaques resa actives on restaure le stock et le marque comme annulée
        $restoreStockQuery->execute([
            'quantite' => (int) $reservation['quantite'],
            'id_art' => (int) $reservation['id_art'],
        ]);
        $cancelQuery->execute([
            'id_reservation' => (int) $reservation['id_reservation'],
        ]);
    }
}

/**
 * Recupere les reservations actives d'un client avec les informations utiles
 * sur les articles associes, notamment leur nom, leur prix et leur stock.
 *
 * @return array Liste des reservations actives du client.
 */
function fetchActiveReservations(PDO $bdd, int $idClient): array
{
    $reservationQuery = $bdd->prepare(
        "SELECT r.id_reservation, r.id_art, r.quantite, r.date_expiration,
            a.nom, a.prix, a.quantite AS stock_disponible
        FROM reservations r
        INNER JOIN articles a ON a.id_art = r.id_art
        WHERE r.id_client = :id_client AND r.statut = 'active'
        ORDER BY r.id_reservation"
    );
    $reservationQuery->execute(['id_client' => $idClient]);

    return $reservationQuery->fetchAll();
}

/**
 * Recupere et verrouille les reservations actives d'un client.
 * Ce verrouillage empeche une modification concurrente pendant une operation
 * qui doit traiter les reservations et leur stock de maniere coherente.
 *
 * @return array Reservations actives verrouillees.
 */
function lockActiveReservations(PDO $bdd, int $idClient): array
{
    $reservationQuery = $bdd->prepare(
        "SELECT id_reservation, id_art, quantite
        FROM reservations
        WHERE id_client = :id_client AND statut = 'active'
        FOR UPDATE"
    );
    $reservationQuery->execute(['id_client' => $idClient]);

    return $reservationQuery->fetchAll();
}
