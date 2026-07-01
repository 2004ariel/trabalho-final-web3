<?php

namespace App\Controllers;

use App\Models\ProdutoModel;

class ProdutoController extends BaseController
{
    /**
     * Listagem de produtos — landing pós-login (protegida por 'auth').
     */
    public function index()
    {
        $produtoModel = new ProdutoModel();

        return view('produtos/index', [
            'titulo'   => 'Produtos',
            'produtos' => $produtoModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }
}
