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

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Total de vendas por dia no intervalo informado.
     * Soma (preco_unitario * quantidade) dos itens, ignorando pedidos cancelados.
     *
     * @return array<int, array{data:string,total:float}> ordenado por data ASC
     */
    public function vendasPorDia(string $inicio, string $fim): array
    {
        return $this->select('DATE(pedidos.created_at) AS data, SUM(pp.preco_unitario * pp.quantidade) AS total', false)
                    ->join('pedido_produtos pp', 'pp.id_pedido = pedidos.id')
                    ->where('pedidos.status !=', 'cancelado')
                    ->where('DATE(pedidos.created_at) >=', $inicio)
                    ->where('DATE(pedidos.created_at) <=', $fim)
                    ->groupBy('DATE(pedidos.created_at)')
                    ->orderBy('data', 'ASC')
                    ->findAll();
    }
}
