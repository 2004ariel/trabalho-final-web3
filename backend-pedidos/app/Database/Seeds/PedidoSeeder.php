<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PedidoSeeder extends Seeder
{
    public function run()
    {
        // Evita duplicar se já houver pedidos
        if ($this->db->table('pedidos')->countAllResults() > 0) {
            return;
        }

        // Produtos disponíveis (id, preco)
        $produtos = $this->db->table('produtos')->select('id, preco')->get()->getResultArray();
        if ($produtos === []) {
            return;
        }

        $statuses = ['finalizado', 'finalizado', 'finalizado', 'em_preparo', 'cancelado'];

        // Gera pedidos para os últimos 7 dias
        for ($d = 6; $d >= 0; $d--) {
            $qtdPedidos = random_int(1, 3);

            for ($n = 0; $n < $qtdPedidos; $n++) {
                $data = date('Y-m-d H:i:s', strtotime("-{$d} days " . random_int(8, 20) . ' hours'));

                $this->db->table('pedidos')->insert([
                    'status'     => $statuses[array_rand($statuses)],
                    'totem'      => 'T' . random_int(1, 5),
                    'created_at' => $data,
                    'updated_at' => $data,
                    'deleted_at' => null,
                ]);
                $idPedido = $this->db->insertID();

                // 1 a 3 itens por pedido
                $itens = random_int(1, 3);
                for ($i = 0; $i < $itens; $i++) {
                    $produto = $produtos[array_rand($produtos)];
                    $this->db->table('pedido_produtos')->insert([
                        'id_pedido'      => $idPedido,
                        'id_produto'     => $produto['id'],
                        'quantidade'     => random_int(1, 4),
                        'preco_unitario' => $produto['preco'],
                    ]);
                }
            }
        }
    }
}
