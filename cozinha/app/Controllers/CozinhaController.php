<?php

namespace App\Controllers;

use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;

class CozinhaController extends BaseController
{
    /**
     * Transições de status permitidas a partir da cozinha.
     */
    private array $statusValidos = ['novo', 'em_preparo', 'finalizado', 'cancelado'];

    /**
     * Painel da cozinha: pedidos pendentes (novo / em_preparo),
     * mais antigos primeiro. A view faz auto-refresh a cada 30s.
     */
    public function index()
    {
        $pedidoModel = new PedidoModel();

        $dados = [
            'titulo'      => 'Cozinha — Pedidos',
            'pedidos'     => $pedidoModel->pendentesComItens(),
            'autoRefresh' => 30, // segundos (meta refresh no header)
        ];

        return view('cozinha/index', $dados);
    }

    /**
     * Detalhes de um pedido específico, com os itens e botões de ação.
     */
    public function detalhes($id)
    {
        $pedidoModel = new PedidoModel();
        $itensModel  = new PedidoProdutoModel();

        $pedido = $pedidoModel->find($id);

        if ($pedido === null) {
            return redirect()->to('/cozinha')
                             ->with('erro', 'Pedido #' . esc($id) . ' não encontrado.');
        }

        $dados = [
            'titulo' => 'Pedido #' . $pedido['id'],
            'pedido' => $pedido,
            'itens'  => $itensModel->itensDoPedido((int) $id),
        ];

        return view('cozinha/detalhes', $dados);
    }

    /**
     * Atualiza o status do pedido (POST) e volta para o painel.
     */
    public function atualizarStatus($id)
    {
        $pedidoModel = new PedidoModel();

        $pedido = $pedidoModel->find($id);
        if ($pedido === null) {
            return redirect()->to('/cozinha')
                             ->with('erro', 'Pedido #' . esc($id) . ' não encontrado.');
        }

        $novoStatus = $this->request->getPost('status');

        if (! in_array($novoStatus, $this->statusValidos, true)) {
            return redirect()->to('/cozinha')
                             ->with('erro', 'Status inválido.');
        }

        $pedidoModel->update($id, ['status' => $novoStatus]);

        $rotulos = [
            'novo'       => 'marcado como novo',
            'em_preparo' => 'em preparo',
            'finalizado' => 'finalizado',
            'cancelado'  => 'cancelado',
        ];

        return redirect()->to('/cozinha')
                         ->with('sucesso', 'Pedido #' . esc($id) . ' ' . $rotulos[$novoStatus] . '.');
    }
}
