<?php

declare(strict_types=1);

abstract class BaseController
{
    protected function render(string $view, array $dados = []): void
    {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException('View não encontrada: ' . $view);
        }

        extract($dados, EXTR_SKIP);
        $flash = getFlash();
        $usuario = usuarioLogado();

        require __DIR__ . '/../views/layouts/header.php';
        require $viewFile;
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
