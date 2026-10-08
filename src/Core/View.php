<?php

declare(strict_types=1);

namespace App\Core;

use App\Security\Csrf;
use RuntimeException;

/**
 * Moteur de vues minimal : rend un fichier PHP de src/Views à l'intérieur d'un layout.
 *
 * Toutes les sorties dynamiques doivent passer par {@see View::escape()}.
 */
final class View
{
    /** Répertoire des vues. */
    private static string $viewsPath = '';

    /** Nom de l'application, transmis au layout. */
    private static string $appName = '';

    /**
     * Définit le répertoire des vues et le nom de l'application.
     */
    public static function configure(string $viewsPath, string $appName): void
    {
        self::$viewsPath = rtrim($viewsPath, '/\\');
        self::$appName = $appName;
    }

    /**
     * Rend une vue puis l'insère dans le layout (variables $content et $appName).
     *
     * @param string $view Nom de la vue, sans extension (ex. « trips/form »)
     * @param array<string, mixed> $data Variables disponibles dans la vue et le layout
     * @param string $layout Nom du layout
     * @throws RuntimeException Si la vue ou le layout est introuvable
     */
    public static function render(string $view, array $data = [], string $layout = 'layout'): string
    {
        $content = self::renderFile($view, $data);

        return self::renderFile($layout, $data + ['content' => $content, 'appName' => self::$appName]);
    }

    /**
     * Échappe une valeur pour l'affichage HTML (protection XSS).
     */
    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Champ caché contenant le jeton CSRF, à inclure dans chaque formulaire POST.
     */
    public static function csrfField(): string
    {
        return '<input type="hidden" name="_csrf" value="' . self::escape(Csrf::token()) . '">';
    }

    /**
     * Exécute un fichier de vue et retourne son rendu.
     *
     * @param array<string, mixed> $data
     * @throws RuntimeException Si le fichier est introuvable
     */
    private static function renderFile(string $view, array $data): string
    {
        $file = self::$viewsPath . '/' . $view . '.php';

        if (!is_file($file)) {
            throw new RuntimeException("Vue introuvable : {$view}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}
