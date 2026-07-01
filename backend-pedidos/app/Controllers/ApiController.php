<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;

/**
 * API REST consumida pelo cliente-pedidos (totem).
 *
 * Endpoints:
 *   GET  /api/status    → verifica se a API está no ar
 *   GET  /api/produtos  → lista de produtos
 *   POST /api/checkout  → registra o pedido (header apiKey obrigatório)
 */
class ApiController extends BaseController
{
    // Mesma chave usada pelo cliente (js/api.js) e pelo mock server.
    private const API_KEY = 'D41D8CD98F00B204E9800998ECF8427E';

    /**
     * GET /api/status
     */
    public function status()
    {
        return $this->json(['status' => 'ok', 'mensagem' => 'Api funcionando']);
    }

    /**
     * GET /api/produtos — todos os produtos cadastrados.
     */
    public function produtos()
    {
        $produtoModel = new ProdutoModel();

        return $this->json($produtoModel->orderBy('nome', 'ASC')->findAll());
    }

    /**
     * POST /api/checkout — cria o pedido e seus itens.
     * Body: { status, totem_id, totem_nome, produtos: [{ id_produto, quantidade, preco_unitario }] }
     */
    public function checkout()
    {
        // Valida a chave de API enviada no header.
        if ($this->request->getHeaderLine('apiKey') !== self::API_KEY) {
            return $this->json([
                'status'  => false,
                'message' => 'apiKey ausente ou inválida.',
            ], 401);
        }

        $dados = $this->request->getJSON(true);
        $itens = $dados['produtos'] ?? [];

        if (empty($itens)) {
            return $this->json([
                'status'  => false,
                'message' => 'Pedido sem produtos.',
            ], 400);
        }

        // Cria o pedido (o totem_id é guardado na coluna totem).
        $pedidoModel = new PedidoModel();
        $idPedido = $pedidoModel->insert([
            'status' => $dados['status'] ?? 'novo',
            'totem'  => $dados['totem_id'] ?? null,
        ]);

        // Grava cada item do pedido.
        $itemModel = new PedidoProdutoModel();
        foreach ($itens as $item) {
            $itemModel->insert([
                'id_pedido'      => $idPedido,
                'id_produto'     => $item['id_produto'],
                'quantidade'     => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
            ]);
        }

        return $this->json([
            'status'    => true,
            'message'   => 'Pedido cadastrado com sucesso.',
            'id_pedido' => $idPedido,
        ]);
    }

    /**
     * Responde ao preflight (OPTIONS) que o navegador envia antes do POST.
     */
    public function preflight()
    {
        return $this->json([]);
    }

    /**
     * Resposta JSON com CORS liberado (o cliente roda em outra origem).
     */
    private function json($dados, int $codigo = 200)
    {
        return $this->response
            ->setStatusCode($codigo)
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, apiKey')
            ->setJSON($dados);
    }
}
