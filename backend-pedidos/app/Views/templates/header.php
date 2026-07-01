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
    <title><?= esc($titulo ?? 'Backend de Pedidos') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= site_url('produtos') ?>">🍔 Backend Pedidos</a>

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
