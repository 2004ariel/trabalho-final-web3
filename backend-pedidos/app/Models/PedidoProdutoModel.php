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
     * Consumo por produto: quantidade vendida no período, com estoque e
     * disponibilidade. Usa LEFT JOIN a partir de produtos para que produtos
     * sem vendas também apareçam (quantidade_vendida = 0).
     *
     * @param string|null $categoria filtra por produtos.tipo
     * @param string|null $inicio    data inicial (YYYY-MM-DD) ou null = sem limite
     * @param string|null $fim       data final (YYYY-MM-DD) ou null = sem limite
     */
    public function consumoPorProduto(?string $categoria, ?string $inicio, ?string $fim): array
    {
        // Condições de data aplicadas no JOIN (não no WHERE) para preservar
        // produtos sem vendas no período.
        $cond = "pp.id_produto = produtos.id AND pe.status != 'cancelado'";
        $cond .= " AND pe.deleted_at IS NULL";

        $db = db_connect();

        $builder = $db->table('produtos')
            ->select('produtos.id, produtos.nome, produtos.tipo, produtos.disponivel, produtos.estoque, COALESCE(SUM(pp.quantidade), 0) AS quantidade_vendida', false)
            ->join('pedido_produtos pp', 'pp.id_produto = produtos.id', 'left')
            ->join('pedidos pe', 'pe.id = pp.id_pedido', 'left');

        if ($inicio !== null && $inicio !== '') {
            $builder->where('(DATE(pe.created_at) >= ' . $db->escape($inicio) . ' OR pe.id IS NULL)', null, false);
        }
        if ($fim !== null && $fim !== '') {
            $builder->where('(DATE(pe.created_at) <= ' . $db->escape($fim) . ' OR pe.id IS NULL)', null, false);
        }
        // Só conta itens de pedidos não cancelados
        $builder->groupStart()
                ->where('pe.status !=', 'cancelado')
                ->orWhere('pe.id', null)
                ->groupEnd();

        if ($categoria !== null && $categoria !== '') {
            $builder->where('produtos.tipo', $categoria);
        }

        return $builder->groupBy('produtos.id')
                       ->orderBy('produtos.nome', 'ASC')
                       ->get()
                       ->getResultArray();
    }
}
