<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $agora = date('Y-m-d H:i:s');

        $usuarios = [
            ['nome' => 'Super Admin', 'email' => 'super@pedidos.com', 'tipo' => 'superadmin'],
            ['nome' => 'Gerente',     'email' => 'admin@pedidos.com', 'tipo' => 'admin'],
            ['nome' => 'Funcionário', 'email' => 'user@pedidos.com',  'tipo' => 'usuario'],
        ];

        foreach ($usuarios as $u) {
            $existe = $this->db->table('usuarios')->where('email', $u['email'])->countAllResults();
            if ($existe > 0) {
                continue;
            }
            $this->db->table('usuarios')->insert([
                'nome'       => $u['nome'],
                'email'      => $u['email'],
                'senha'      => password_hash('123456', PASSWORD_DEFAULT),
                'tipo'       => $u['tipo'],
                'bloqueado'  => 0,
                'created_at' => $agora,
                'updated_at' => $agora,
            ]);
        }
    }
}
