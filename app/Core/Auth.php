<?php

namespace App\Core;
use App\Core\Message;
class Auth
{
    public static function user()
    {
        $session = new Session();
        return $session->auth ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user->role ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Message::warning("warning", "Você precisa fazer login.");
            redirect("/entrar");
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();

        if (self::role() !== $role) {
            Message::error("Você não tem autorização de acesso a essa página.");
            redirect("/entrar");
        }
    }
}