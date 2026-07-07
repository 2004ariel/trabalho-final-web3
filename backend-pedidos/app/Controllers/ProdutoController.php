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

    /**
     * Formulário de cadastro de novo produto (rota protegida por admin:admin).
     */
    public function criar()
    {
        return view('produtos/form', [
            'titulo'  => 'Novo produto',
            'produto' => null,
        ]);
    }

    /**
     * Salva um novo produto (POST, admin:admin).
     */
    public function salvar()
    {
        $regras = [
            'nome'     => 'required|min_length[3]',
            'preco'    => 'required|decimal',
            'estoque'  => 'required|integer|greater_than_equal_to[0]',
        ];

        if (! $this->validate($regras)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $produtoModel = new ProdutoModel();
        $produtoModel->insert([
            'nome'       => $this->request->getPost('nome'),
            'preco'      => $this->request->getPost('preco'),
            'tipo'       => $this->request->getPost('tipo'),
            'descricao'  => $this->request->getPost('descricao'),
            'estoque'    => $this->request->getPost('estoque'),
            'disponivel' => $this->request->getPost('disponivel') ? 1 : 0,
        ]);

        return redirect()->to('/produtos')
                         ->with('sucesso', 'Produto cadastrado com sucesso.');
    }

    /**
     * Formulário de edição de produto (rota protegida por admin:admin).
     */
    public function editar($id)
    {
        $produtoModel = new ProdutoModel();
        $produto      = $produtoModel->find($id);

        if ($produto === null) {
            return redirect()->to('/produtos')
                             ->with('erro', 'Produto não encontrado.');
        }

        return view('produtos/form', [
            'titulo'  => 'Editar produto',
            'produto' => $produto,
        ]);
    }

    /**
     * Atualiza produto, incluindo reposição de estoque (POST, admin:admin).
     */
    public function atualizar($id)
    {
        $produtoModel = new ProdutoModel();
        $produto      = $produtoModel->find($id);

        if ($produto === null) {
            return redirect()->to('/produtos')
                             ->with('erro', 'Produto não encontrado.');
        }

        $regras = [
            'nome'     => 'required|min_length[3]',
            'preco'    => 'required|decimal',
            'estoque'  => 'required|integer|greater_than_equal_to[0]',
        ];

        if (! $this->validate($regras)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $produtoModel->update($id, [
            'nome'       => $this->request->getPost('nome'),
            'preco'      => $this->request->getPost('preco'),
            'tipo'       => $this->request->getPost('tipo'),
            'descricao'  => $this->request->getPost('descricao'),
            'estoque'    => $this->request->getPost('estoque'),
            'disponivel' => $this->request->getPost('disponivel') ? 1 : 0,
        ]);

        return redirect()->to('/produtos')
                         ->with('sucesso', 'Produto atualizado com sucesso.');
    }

    /**
     * Exclui um produto (POST, admin:admin).
     */
    public function excluir($id)
    {
        $produtoModel = new ProdutoModel();
        $produto      = $produtoModel->find($id);

        if ($produto === null) {
            return redirect()->to('/produtos')
                             ->with('erro', 'Produto não encontrado.');
        }

        $produtoModel->delete($id);

        return redirect()->to('/produtos')
                         ->with('sucesso', 'Produto excluído com sucesso.');
    }
}
