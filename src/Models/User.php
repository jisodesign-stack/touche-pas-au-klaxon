<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function fullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
