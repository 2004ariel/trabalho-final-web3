<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProdutos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nome'       => ['type' => 'VARCHAR', 'constraint' => 120],
            'preco'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'tipo'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'descricao'  => ['type' => 'TEXT', 'null' => true],
            'disponivel' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'estoque'    => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('produtos');
    }

    public function down()
    {
        $this->forge->dropTable('produtos');
    }
}
