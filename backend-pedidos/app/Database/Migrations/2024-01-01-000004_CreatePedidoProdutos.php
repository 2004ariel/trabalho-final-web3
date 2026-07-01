<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePedidoProdutos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'id_pedido'      => ['type' => 'INT', 'unsigned' => true],
            'id_produto'     => ['type' => 'INT', 'unsigned' => true],
            'quantidade'     => ['type' => 'INT', 'default' => 1],
            'preco_unitario' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('id_pedido');
        $this->forge->addKey('id_produto');
        $this->forge->createTable('pedido_produtos');
    }

    public function down()
    {
        $this->forge->dropTable('pedido_produtos');
    }
}
