<?php

declare(strict_types=1);

namespace App\Security;

use App\Models\User;
use App\Repositories\UserRepository;
use PDOException;

/**
 * Authentification : connexion, utilisateur courant et déconnexion, basées sur la session PHP.
 */
final class Auth
{
    /** Clé de session contenant l'identifiant de l'utilisateur connecté. */
    private const KEY = 'user_id';

    /** Cache de l'utilisateur courant pour la durée de la requête. */
    private static ?User $user = null;

    /**
     * Tente une connexion par email et mot de passe ; ouvre la session en cas de succès.
     *
     * @return bool Vrai si les identifiants sont corrects
     * @throws PDOException Si la base est injoignable
     */
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

    /**
     * Utilisateur connecté, ou null (anonyme, session obsolète ou base indisponible).
     */
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

    /**
     * Indique si un utilisateur est connecté.
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Ferme la session : efface les données et renouvelle l'identifiant de session.
     */
    public static function logout(): void
    {
        self::$user = null;
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
