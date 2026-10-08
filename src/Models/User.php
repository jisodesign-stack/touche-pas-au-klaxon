<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Employé de l'entreprise (données issues du système RH). Objet immuable.
 */
final class User
{
    /**
     * @param int $id Identifiant
     * @param string $nom Nom de famille
     * @param string $prenom Prénom
     * @param string $email Adresse email, utilisée comme identifiant de connexion
     * @param string $telephone Numéro de téléphone
     * @param string $role « utilisateur » ou « admin »
     * @param string $motDePasse Empreinte du mot de passe (password_hash)
     */
    public function __construct(
        public readonly int $id,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $email,
        public readonly string $telephone,
        public readonly string $role,
        public readonly string $motDePasse,
    ) {
    }

    /**
     * Indique si l'utilisateur est administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Prénom et nom, pour l'affichage.
     */
    public function fullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
