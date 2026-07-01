<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// ---------------------------------------------------------------------
// API REST (consumida pelo cliente-pedidos / totem)
// ---------------------------------------------------------------------
$routes->get('api/status',    'ApiController::status');
$routes->get('api/produtos',  'ApiController::produtos');
$routes->post('api/checkout', 'ApiController::checkout');
$routes->options('api/(:any)', 'ApiController::preflight'); // preflight CORS

// ---------------------------------------------------------------------
// Autenticação
// ---------------------------------------------------------------------
$routes->get('login',   'UsuarioController::login');
$routes->post('login',  'UsuarioController::tentarLogin');
$routes->get('logout',  'UsuarioController::logout');

// ---------------------------------------------------------------------
// Área autenticada
// ---------------------------------------------------------------------
$routes->get('produtos', 'ProdutoController::index', ['filter' => 'auth']);

// Edição do próprio cadastro (qualquer usuário logado; regra fina no controller)
$routes->get('usuarios/editar/(:num)',     'UsuarioController::editar/$1',    ['filter' => 'auth']);
$routes->post('usuarios/atualizar/(:num)', 'UsuarioController::atualizar/$1', ['filter' => 'auth']);

// ---------------------------------------------------------------------
// Parte 3 — Controle de usuários (somente superadmin)
// ---------------------------------------------------------------------
$routes->group('admin', ['filter' => 'admin:superadmin'], function ($routes) {
    $routes->get('usuarios',                 'UsuarioController::index');
    $routes->get('usuarios/criar',           'UsuarioController::criar');
    $routes->post('usuarios/salvar',         'UsuarioController::salvar');
    $routes->get('usuarios/bloquear/(:num)', 'UsuarioController::bloquear/$1');
});

// ---------------------------------------------------------------------
// Parte 3 — Painel de vendas (admin ou superadmin)
// ---------------------------------------------------------------------
$routes->group('admin', ['filter' => 'admin:admin'], function ($routes) {
    $routes->get('relatorios/vendas',  'RelatorioController::vendas');
    $routes->get('relatorios/consumo', 'RelatorioController::consumo');
});
