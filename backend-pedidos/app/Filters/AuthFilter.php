<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Exige usuário autenticado. Caso contrário, redireciona ao login.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('usuario')) {
            return redirect()->to('/login')
                             ->with('erro', 'Faça login para continuar.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada
    }
}
