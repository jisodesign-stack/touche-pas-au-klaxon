<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Message flash : message stocké en session, affiché une seule fois à la page suivante.
 */
final class Flash
{
    /** Clé de session. */
    private const KEY = '_flash';

    /**
     * Enregistre un message pour la prochaine page affichée.
     *
     * @param string $type Type d'alerte Bootstrap (success, danger, warning…)
     */
    public static function set(string $type, string $message): void
    {
        $_SESSION[self::KEY] = ['type' => $type, 'message' => $message];
    }

    /**
     * Lit puis supprime le message en attente.
     *
     * @return array{type: string, message: string}|null Null s'il n'y a pas de message
     */
    public static function pull(): ?array
    {
        $flash = $_SESSION[self::KEY] ?? null;
        unset($_SESSION[self::KEY]);

        return is_array($flash) && isset($flash['type'], $flash['message']) ? $flash : null;
    }
}
