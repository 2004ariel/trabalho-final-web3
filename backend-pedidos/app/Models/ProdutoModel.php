<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table            = 'produtos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = ['nome', 'preco', 'tipo', 'descricao', 'disponivel', 'estoque'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Lista de categorias distintas (campo tipo) para filtros.
     */
    public function categorias(): array
    {
        $linhas = $this->select('tipo')
                       ->where('tipo IS NOT NULL')
                       ->where('tipo !=', '')
                       ->distinct()
                       ->orderBy('tipo', 'ASC')
                       ->findAll();

        return array_column($linhas, 'tipo');
    }
}
