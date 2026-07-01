<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table            = 'pedidos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = ['status', 'totem'];

    // Datas
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Lista pedidos pendentes (novo / em_preparo) com os itens já agregados,
     * ordenados pelo mais antigo primeiro (fila da cozinha).
     *
     * Retorna cada pedido com uma coluna extra "itens" contendo o resumo
     * "2x X-Burguer, 1x Coca-Cola".
     */
    public function pendentesComItens(): array
    {
        $pedidos = $this->whereIn('status', ['novo', 'em_preparo'])
                        ->orderBy('created_at', 'ASC')
                        ->findAll();

        if ($pedidos === []) {
            return [];
        }

        $itensModel = new PedidoProdutoModel();

        foreach ($pedidos as &$pedido) {
            $itens = $itensModel->itensDoPedido($pedido['id']);

            $resumo = [];
            foreach ($itens as $item) {
                $resumo[] = $item['quantidade'] . 'x ' . $item['nome'];
            }

            $pedido['itens']        = $itens;
            $pedido['itens_resumo'] = implode(', ', $resumo);
        }

        return $pedidos;
    }
}
