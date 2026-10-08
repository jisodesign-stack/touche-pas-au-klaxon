<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    private static string $viewsPath = '';
    private static string $appName = '';

    public static function configure(string $viewsPath, string $appName): void
    {
        self::$viewsPath = rtrim($viewsPath, '/\\');
        self::$appName = $appName;
    }

    /** @param array<string, mixed> $data */
    public static function render(string $view, array $data = [], string $layout = 'layout'): string
    {
        $content = self::renderFile($view, $data);

        return self::renderFile($layout, $data + ['content' => $content, 'appName' => self::$appName]);
    }

    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** @param array<string, mixed> $data */
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
