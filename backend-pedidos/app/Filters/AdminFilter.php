<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    /**
     * Controle de acesso por papel.
     *
     * Uso nas rotas:
     *   'filter' => 'admin:admin'       → admin OU superadmin
     *   'filter' => 'admin:superadmin'  → somente superadmin
     *
     * Sem argumento, exige pelo menos admin.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $usuario = session()->get('usuario');

        if (! $usuario) {
            return redirect()->to('/login')
                             ->with('erro', 'Faça login para continuar.');
        }

        $tipo  = $usuario['tipo'] ?? 'usuario';
        $exige = $arguments[0] ?? 'admin';

        if ($exige === 'superadmin') {
            $permitido = $tipo === 'superadmin';
        } else { // 'admin'
            $permitido = in_array($tipo, ['admin', 'superadmin'], true);
        }

        if (! $permitido) {
            return redirect()->to('/produtos')
                             ->with('erro', 'Acesso restrito. Você não tem permissão para esta área.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada
    }
}
