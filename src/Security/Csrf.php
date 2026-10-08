<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Protection CSRF : un jeton aléatoire par session, à inclure dans chaque formulaire POST.
 */
final class Csrf
{
    /** Clé de session du jeton. */
    private const KEY = '_csrf_token';

    /**
     * Retourne le jeton de la session (généré à la première demande).
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    /**
     * Compare, en temps constant, le jeton soumis à celui de la session.
     *
     * @param mixed $submitted Valeur reçue du formulaire
     */
    public static function isValid(mixed $submitted): bool
    {
        return is_string($submitted)
            && isset($_SESSION[self::KEY])
            && is_string($_SESSION[self::KEY])
            && hash_equals($_SESSION[self::KEY], $submitted);
    }
}
