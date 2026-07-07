<?php
$usuario = session()->get('usuario');
$tipo    = $usuario['tipo'] ?? null;
$ehAdmin      = in_array($tipo, ['admin', 'superadmin'], true);
$ehSuperadmin = $tipo === 'superadmin';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'Salsicha Lanches — Admin') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Tema Salsicha Lanches — Máquina do Mistério (Scooby-Doo) */
        :root {
            --sl-green: #5bbf3a;   --sl-green-dark: #3f9526;
            --sl-orange: #f6911e;  --sl-orange-dark: #dd760a;
            --sl-brown: #6b4423;   --sl-cream: #fdf7e9;   --sl-ink: #2a2118;
        }
        body { background-color: var(--sl-cream) !important; font-family: "Nunito", system-ui, sans-serif; color: var(--sl-ink); }
        h1, h2, h3, h4, h5, .navbar-brand, .h3, .h4, .h5 { font-family: "Fredoka", "Nunito", sans-serif; }
        .sl-navbar { background: linear-gradient(90deg, var(--sl-green), var(--sl-green-dark)) !important; border-bottom: 4px solid var(--sl-orange); }
        .sl-navbar .navbar-brand { display: inline-flex; align-items: center; gap: .5rem; letter-spacing: .3px; }
        .sl-logo { width: 2.1rem; height: 2.1rem; filter: drop-shadow(0 1px 1px rgba(0,0,0,.15)); }
        .btn-dark { background: var(--sl-ink); border-color: var(--sl-ink); }
        .btn-dark:hover, .btn-dark:focus { background: #000; border-color: #000; }
        .card { border-radius: .85rem; }
        footer { background: transparent; color: var(--sl-brown) !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sl-navbar mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= site_url('produtos') ?>">
            <svg class="sl-logo" viewBox="0 0 64 64" role="img" aria-label="Salsicha Lanches" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="58" height="58" rx="17" fill="#5bbf3a" stroke="#3f9526" stroke-width="3"/><g transform="rotate(-18 32 33)"><rect x="12" y="30" width="40" height="13" rx="6.5" fill="#f2b45f"/><rect x="15" y="27" width="34" height="10" rx="5" fill="#c34a2c"/><rect x="12" y="23" width="40" height="10" rx="5" fill="#f7cf8a"/><path d="M17 28 q4.5 -3.5 9 0 t9 0 t9 0" fill="none" stroke="#ffd23f" stroke-width="2.6" stroke-linecap="round"/></g><circle cx="45" cy="45" r="9.5" fill="#bfeee9" fill-opacity="0.5" stroke="#0f8f88" stroke-width="3"/><line x1="52" y1="52" x2="58" y2="58" stroke="#0f8f88" stroke-width="4" stroke-linecap="round"/></svg>
            Salsicha Lanches <span class="fw-normal opacity-75">· Admin</span>
        </a>

        <?php if ($usuario): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('produtos') ?>">Produtos</a>
                    </li>
                    <?php if ($ehSuperadmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/usuarios') ?>">Usuários</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($ehAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/relatorios/vendas') ?>">Vendas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/relatorios/consumo') ?>">Consumo</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('usuarios/editar/' . $usuario['id']) ?>">
                            <?= esc($usuario['nome']) ?>
                            <span class="badge bg-secondary"><?= esc($tipo) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="<?= site_url('logout') ?>">Sair</a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>

<main class="container pb-5">

    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('sucesso')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('erro')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $erro): ?>
                    <li><?= esc($erro) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
