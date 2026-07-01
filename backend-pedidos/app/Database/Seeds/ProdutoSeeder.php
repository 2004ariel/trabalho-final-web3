<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run()
    {
        $agora = date('Y-m-d H:i:s');

        $produtos = [
            ['nome' => 'X-Burguer', 'preco' => 18.90, 'tipo' => 'Lanches', 'disponivel' => 1, 'estoque' => 40],
            ['nome' => 'X-Salada',  'preco' => 21.50, 'tipo' => 'Lanches', 'disponivel' => 1, 'estoque' => 25],
            ['nome' => 'Batata Frita', 'preco' => 12.00, 'tipo' => 'Porções', 'disponivel' => 1, 'estoque' => 30],
            ['nome' => 'Coca-Cola', 'preco' => 7.00,  'tipo' => 'Bebidas', 'disponivel' => 1, 'estoque' => 60],
            ['nome' => 'Suco de Laranja', 'preco' => 9.00, 'tipo' => 'Bebidas', 'disponivel' => 0, 'estoque' => 0],
        ];

        foreach ($produtos as $p) {
            $existe = $this->db->table('produtos')->where('nome', $p['nome'])->countAllResults();
            if ($existe > 0) {
                continue;
            }
            $this->db->table('produtos')->insert(array_merge($p, [
                'descricao'  => '',
                'created_at' => $agora,
                'updated_at' => $agora,
            ]));
        }
    }
}
