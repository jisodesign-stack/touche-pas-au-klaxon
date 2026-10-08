<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

/**
 * Trajet de covoiturage, avec les noms des agences et les coordonnées de son auteur
 * (la personne à contacter). Objet immuable alimenté par TripRepository.
 */
final class Trip
{
    /**
     * @param int $id Identifiant du trajet
     * @param int $agenceDepartId Identifiant de l'agence de départ
     * @param string $agenceDepart Nom de l'agence de départ
     * @param int $agenceArriveeId Identifiant de l'agence d'arrivée
     * @param string $agenceArrivee Nom de l'agence d'arrivée
     * @param DateTimeImmutable $dateDepart Date et heure de départ
     * @param DateTimeImmutable $dateArrivee Date et heure d'arrivée
     * @param int $placesTotal Nombre total de places
     * @param int $placesDisponibles Nombre de places encore disponibles
     * @param int $auteurId Identifiant de l'utilisateur qui propose le trajet
     * @param string $auteurNom Prénom et nom de l'auteur
     * @param string $auteurTelephone Téléphone de l'auteur
     * @param string $auteurEmail Adresse email de l'auteur
     */
    public function __construct(
        public readonly int $id,
        public readonly int $agenceDepartId,
        public readonly string $agenceDepart,
        public readonly int $agenceArriveeId,
        public readonly string $agenceArrivee,
        public readonly DateTimeImmutable $dateDepart,
        public readonly DateTimeImmutable $dateArrivee,
        public readonly int $placesTotal,
        public readonly int $placesDisponibles,
        public readonly int $auteurId,
        public readonly string $auteurNom,
        public readonly string $auteurTelephone,
        public readonly string $auteurEmail,
    ) {
    }
}
