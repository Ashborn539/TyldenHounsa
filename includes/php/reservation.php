<?php

declare(strict_types=1);

// Modifiez ces deux valeurs pour ajuster les durees sans changer la logique metier.
const CART_RESERVATION_DURATION_SECONDS = 15 * 60;
const LAST_STOCK_RESERVATION_DURATION_SECONDS = 5 * 60;

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
            $restoreStockQuery->execute([
                'quantite' => (int) $reservation['quantite'],
                'id_art' => (int) $reservation['id_art'],
            ]);
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

function reserveArticle(PDO $bdd, int $idClient, int $idArticle, int $quantity): int
{
    if ($idClient < 1 || $idArticle < 0 || $quantity < 1) {
        throw new InvalidArgumentException('Les donnees de reservation sont invalides.');
    }

    $articleQuery = $bdd->prepare(
        'SELECT id_art, quantite
         FROM articles
         WHERE id_art = :id_art
         FOR UPDATE'
    );
    $articleQuery->execute(['id_art' => $idArticle]);
    $article = $articleQuery->fetch();

    if (!$article || $quantity > (int) $article['quantite']) {
        throw new RuntimeException('Le stock disponible est insuffisant.');
    }

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
    $duration = $remainingStock === 0
        ? LAST_STOCK_RESERVATION_DURATION_SECONDS
        : CART_RESERVATION_DURATION_SECONDS;
    $expiration = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
        ->modify('+' . $duration . ' seconds')
        ->format('Y-m-d H:i:s');

    if ($existingReservation) {
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
    } else {
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

function cancelClientReservations(PDO $bdd, int $idClient): void
{
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
        $restoreStockQuery->execute([
            'quantite' => (int) $reservation['quantite'],
            'id_art' => (int) $reservation['id_art'],
        ]);
        $cancelQuery->execute([
            'id_reservation' => (int) $reservation['id_reservation'],
        ]);
    }
}

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
