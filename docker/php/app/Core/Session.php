<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = config('app', 'session');
        session_name($config['name']);
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        ]);
        session_start();
        self::ageOldInput();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    /** Input del formulario disponible solo en el siguiente request (como flash). */
    public static function flashOld(array $data): void
    {
        $_SESSION['_old_flash'] = $data;
    }

    public static function clearOld(): void
    {
        unset($_SESSION['_old'], $_SESSION['_old_flash']);
    }

    private static function ageOldInput(): void
    {
        if (array_key_exists('_old_flash', $_SESSION)) {
            $_SESSION['_old'] = $_SESSION['_old_flash'];
            unset($_SESSION['_old_flash']);
            return;
        }

        unset($_SESSION['_old']);
    }
}
