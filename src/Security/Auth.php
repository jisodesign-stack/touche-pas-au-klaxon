<?php

declare(strict_types=1);

namespace App\Security;

use App\Models\User;
use App\Repositories\UserRepository;
use PDOException;

final class Auth
{
    private const KEY = 'user_id';

    private static ?User $user = null;

    public static function attempt(string $email, string $password): bool
    {
        $user = (new UserRepository())->findByEmail(strtolower(trim($email)));

        // Vérification factice si l'utilisateur n'existe pas, pour garder un temps de réponse constant.
        $hash = $user->motDePasse ?? '$2y$10$y3aTj55d3OH/gIEEMjPFlu2AqEedYq89gXw0DhSHaMIBoZNxLX/0O';

        if (!password_verify($password, $hash) || $user === null) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION[self::KEY] = $user->id;
        self::$user = $user;

        return true;
    }

    public static function user(): ?User
    {
        if (self::$user === null && isset($_SESSION[self::KEY])) {
            try {
                self::$user = (new UserRepository())->findById((int) $_SESSION[self::KEY]);
            } catch (PDOException $e) {
                // Base indisponible : on traite la requête comme anonyme sans fermer la session.
                error_log($e->getMessage());

                return null;
            }

            if (self::$user === null) {
                unset($_SESSION[self::KEY]);
            }
        }

        return self::$user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        self::$user = null;
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
