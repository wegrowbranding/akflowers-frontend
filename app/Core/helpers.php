<?php

function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
}

function isLoggedIn(): bool {
    return !empty($_SESSION['token']);
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        redirect('auth/login');
    }
}

function requireGuest(): void {
    if (isLoggedIn()) {
        redirect('dashboard');
    }
}

function token(): string {
    return $_SESSION['token'] ?? '';
}

function authUser(): array {
    return $_SESSION['user'] ?? [];
}

function userId(): int {
    return (int)($_SESSION['user']['id'] ?? 0);
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function old(string $key, string $default = ''): string {
    return e($_SESSION['old'][$key] ?? $default);
}

function setOld(array $data): void {
    $_SESSION['old'] = $data;
}

function clearOld(): void {
    unset($_SESSION['old']);
}
