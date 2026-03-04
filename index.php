<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/utils/helpers.php';
require_once __DIR__ . '/utils/Conexao.php';

spl_autoload_register(function (string $classe): void {
    $pastas = [
        __DIR__ . '/classes/controllers/' . $classe . '.php',
        __DIR__ . '/classes/models/' . $classe . '.php',
    ];

    foreach ($pastas as $arquivo) {
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});

$rotas = require __DIR__ . '/routes.php';
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$caminho = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

if ($base !== '' && $base !== '/' && str_starts_with($caminho, $base)) {
    $caminho = substr($caminho, strlen($base));
}

$caminho = $caminho === '' ? '/' : $caminho;

foreach ($rotas as $rota) {
    [$metodoRota, $padrao, $controller, $acao, $perfis] = array_pad($rota, 5, null);

    if ($metodo !== $metodoRota) {
        continue;
    }

    $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([0-9]+)', $padrao);
    $regex = '#^' . $regex . '$#';

    if (!preg_match($regex, $caminho, $matches)) {
        continue;
    }

    array_shift($matches);
    $matches = array_map(
        static fn($valor) => (is_string($valor) && ctype_digit($valor)) ? (int) $valor : $valor,
        $matches
    );

    if (is_array($perfis) && !empty($perfis)) {
        exigirPerfil($perfis);
    }

    $instancia = new $controller();
    $instancia->$acao(...$matches);
    exit;
}

http_response_code(404);
echo '404 - Página não encontrada';
