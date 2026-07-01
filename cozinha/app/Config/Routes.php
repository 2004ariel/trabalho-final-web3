<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/',                       'CozinhaController::index');
$routes->get('cozinha',                 'CozinhaController::index');
$routes->get('cozinha/detalhes/(:num)', 'CozinhaController::detalhes/$1');
$routes->post('cozinha/status/(:num)',  'CozinhaController::atualizarStatus/$1');
