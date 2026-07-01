<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoProdutoModel extends Model
{
    protected $table            = 'pedido_produtos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = ['id_pedido', 'id_produto', 'quantidade', 'preco_unitario'];

    protected $useTimestamps = false;

    /**
     * Itens de um pedido com o nome do produto (JOIN com produtos).
     */
    public function itensDoPedido(int $idPedido): array
    {
        return $this->select('pedido_produtos.*, produtos.nome, produtos.tipo')
                    ->join('produtos', 'produtos.id = pedido_produtos.id_produto', 'left')
                    ->where('pedido_produtos.id_pedido', $idPedido)
                    ->orderBy('produtos.nome', 'ASC')
                    ->findAll();
    }
}
