<?php

declare(strict_types=1);

function basePath(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    return ($base === '.' || $base === '/') ? '' : $base;
}

function url(string $path = '/'): string
{
    $path = $path === '' ? '/' : $path;
    $path = str_starts_with($path, '/') ? $path : '/' . $path;

    return basePath() . $path;
}

function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function redirect(string $rota): void
{
    header('Location: ' . url($rota));
    exit;
}

function setFlash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'] = [
        'tipo' => $tipo,
        'mensagem' => $mensagem,
    ];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function usuarioLogado(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function exigirLogin(): void
{
    if (!usuarioLogado()) {
        setFlash('warning', 'Faça login para continuar.');
        redirect('/login');
    }
}

function exigirPerfil(array $perfis): void
{
    exigirLogin();

    $tipo = $_SESSION['usuario']['tipo'] ?? '';
    if (!in_array($tipo, $perfis, true)) {
        setFlash('danger', 'Você não tem permissão para acessar esta área.');
        redirect('/');
    }
}
