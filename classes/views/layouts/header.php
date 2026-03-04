<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Sonoplastia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --azul-marinho: #091a3d;
            --azul-marinho-2: #12357a;
            --azul-marinho-3: #2f69d9;
            --preto: #0b1224;
            --branco: #ffffff;
            --gelo: #f4f8ff;
            --linha: #d9e4fb;
            --ok: #1f8f6b;
            --texto-suave: #5e6d8f;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Lexend', sans-serif;
            color: var(--preto);
            background: #f5f9ff;
            min-height: 100vh;
        }

        .app-nav {
            background: var(--azul-marinho);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 20px rgba(9, 26, 61, 0.22);
        }

        .app-nav .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .page-wrap {
            max-width: 1150px;
        }

        .glass-card {
            background: #ffffff;
            border: 1px solid var(--linha);
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(18, 53, 122, 0.08);
        }

        .glass-card .card-header {
            background: #ffffff;
            border-bottom: 1px solid var(--linha);
            font-weight: 600;
            font-size: 0.98rem;
            padding: 14px 18px;
        }

        .glass-card .card-body {
            padding: 1.2rem 1.2rem;
        }

        .page-title {
            color: var(--azul-marinho);
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(18, 53, 122, 0.22);
            background: rgba(47, 105, 217, 0.1);
            color: #163f8f;
        }

        .profile-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.2px;
            text-transform: capitalize;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.16);
            color: #f9fbff;
        }

        .btn {
            border-radius: 12px;
            transition: all .2s ease;
            box-shadow: 0 2px 8px rgba(9, 26, 61, 0.08);
        }

        .btn-primary {
            background: var(--azul-marinho);
            border-color: var(--azul-marinho);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: #0c2455;
            border-color: #0c2455;
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border-color: #ccd9f4;
            color: var(--azul-marinho);
            background: #f8fbff;
        }

        .btn-outline-primary:hover {
            background: #eaf1ff;
            color: #0d2b65;
            border-color: #b5c8ef;
        }

        .btn-logout {
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            background: transparent;
            color: #fff;
            font-weight: 500;
            font-size: 0.84rem;
            box-shadow: none;
        }

        .btn-logout:hover,
        .btn-logout:focus {
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.9);
        }

        .item-soft {
            border: 1px solid var(--linha);
            border-radius: 12px;
            background: #fff;
            transition: background-color .2s ease, border-color .2s ease;
        }

        .categoria-title {
            color: var(--azul-marinho);
            font-weight: 700;
            font-size: 0.95rem;
        }

        .mobile-stack > * + * {
            margin-top: 10px;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInSimple .35s ease forwards;
        }

        @keyframes fadeInSimple {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-hover-bg: #f6f9ff;
        }

        .table thead th {
            background: #eef4ff;
            color: var(--azul-marinho);
            font-weight: 600;
            border-bottom: 1px solid var(--linha);
            padding: 14px 12px;
        }

        .table tbody td {
            padding: 14px 12px;
            color: #1d2d57;
        }

        .table tbody tr {
            transition: background-color .2s ease;
        }

        .table tbody tr:hover {
            background: #f6f9ff;
        }

        .progress {
            border-radius: 999px;
            overflow: hidden;
            background: #e6eeff;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border-color: #cad5ee;
            padding: .62rem .8rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #87a9eb;
            box-shadow: 0 0 0 .2rem rgba(47, 105, 217, 0.16);
        }

        .text-muted {
            color: var(--texto-suave) !important;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 96px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.88rem;
            line-height: 1;
            font-weight: 500;
            border: 1px solid transparent;
            letter-spacing: 0.1px;
        }

        .status-pill--success {
            color: #16b159;
            background: #cdeedb;
            border-color: #9dddb7;
        }

        .status-pill--muted {
            color: #6d7688;
            background: #e8ebf1;
            border-color: #dce1ea;
        }

        .status-pill--warning {
            color: #9f6b00;
            background: #f8ecc8;
            border-color: #f1d998;
        }

        .status-pill--edited {
            color: #b76500;
            background: #ffe7c7;
            border-color: #ffc98a;
        }

        .btn-open-culto {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 44px;
            padding: 0 14px;
            border-radius: 15px;
            border: 1px solid #d4dbe8;
            background: #f3f5f9;
            color: #1a2234;
            font-weight: 500;
            font-size: 0.88rem;
            text-decoration: none;
            box-shadow: none;
        }

        .btn-open-culto:hover,
        .btn-open-culto:focus {
            color: #0f1a2f;
            background: #eceff5;
            border-color: #c7d1e1;
            transform: translateY(-1px);
        }

        .btn-open-culto svg {
            width: 18px;
            height: 18px;
            stroke: #1d2536;
            stroke-width: 2;
            fill: none;
        }

        .btn-delete-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            background: transparent;
            color: #ff2f2f;
            border-radius: 10px;
            box-shadow: none;
            padding: 0;
        }

        .btn-delete-icon:hover,
        .btn-delete-icon:focus {
            color: #ea1e1e;
            background: rgba(255, 47, 47, 0.08);
        }

        .btn-delete-icon svg {
            width: 17px;
            height: 17px;
            fill: currentColor;
        }

        @media (max-width: 768px) {
            .page-wrap {
                padding-left: 8px;
                padding-right: 8px;
            }

            .glass-card {
                border-radius: 14px;
            }

            .mobile-full {
                width: 100%;
            }

            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                border: 1px solid var(--linha);
                border-radius: 12px;
                margin-bottom: 10px;
                padding: 8px;
                background: #fff;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                border: 0;
                padding: 6px;
                font-size: 14px;
            }

            .table tbody td::before {
                content: attr(data-label);
                color: #4a5d88;
                font-weight: 600;
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark app-nav mb-4" aria-label="Navegação principal">
        <div class="container page-wrap">
            <a class="navbar-brand" href="<?= e(url('/')) ?>">Sistema de Sonoplastia</a>

            <div class="d-flex align-items-center gap-2 text-white mobile-stack">
                <?php if (!empty($usuario)): ?>
                    <?php
                    $tipoUsuario = (string) ($usuario['tipo'] ?? '');
                    $nomeUsuario = (string) ($usuario['nome'] ?? '');
                    $tipoLabel = $tipoUsuario === 'diretora' ? 'Diretora de Musica' : ($tipoUsuario === 'sonoplasta' ? 'Sonoplasta' : $tipoUsuario);
                    $nomeExibicao = strtolower(trim($nomeUsuario)) === 'diretora geral' ? 'Diretora de Musica' : $nomeUsuario;
                    ?>
                    <span class="profile-badge"><?= e($tipoLabel) ?></span>
                    <small><?= e($nomeExibicao) ?></small>
                    <a class="btn btn-sm btn-logout mobile-full" href="<?= e(url('/logout')) ?>">Sair</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<main class="container page-wrap pb-4">
    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= e($flash['tipo']) ?> shadow-sm fade-up" role="alert">
            <?= e($flash['mensagem']) ?>
        </div>
    <?php endif; ?>
