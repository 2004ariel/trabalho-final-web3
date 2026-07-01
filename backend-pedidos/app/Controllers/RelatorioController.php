<?php

namespace App\Controllers;

use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Models\ProdutoModel;

class RelatorioController extends BaseController
{
    /**
     * Painel de vendas: total por dia no intervalo + gráfico de linha.
     * GET: ?inicio=YYYY-MM-DD&fim=YYYY-MM-DD (padrão: últimos 7 dias)
     */
    public function vendas()
    {
        // ---- Intervalo (padrão: últimos 7 dias) -------------------------
        $hoje = date('Y-m-d');
        $fim    = $this->request->getGet('fim')    ?: $hoje;
        $inicio = $this->request->getGet('inicio') ?: date('Y-m-d', strtotime('-6 days', strtotime($fim)));

        // Garante inicio <= fim
        if ($inicio > $fim) {
            [$inicio, $fim] = [$fim, $inicio];
        }

        $pedidoModel = new PedidoModel();
        $linhas      = $pedidoModel->vendasPorDia($inicio, $fim);

        // Mapa data => total (para preencher dias sem venda com 0)
        $mapa = [];
        foreach ($linhas as $l) {
            $mapa[$l['data']] = (float) $l['total'];
        }

        // Série completa de dias do intervalo (ASC)
        $serie = [];
        $cursor = strtotime($inicio);
        $limite = strtotime($fim);
        while ($cursor <= $limite) {
            $d = date('Y-m-d', $cursor);
            $serie[] = ['data' => $d, 'total' => $mapa[$d] ?? 0.0];
            $cursor = strtotime('+1 day', $cursor);
        }

        $numDias = count($serie);

        // ---- Dados para o gráfico (Chart.js) ----------------------------
        $dadosGrafico = [
            'labels'  => array_map(static fn ($r) => date('d/m', strtotime($r['data'])), $serie),
            'valores' => array_map(static fn ($r) => round($r['total'], 2), $serie),
        ];

        // ---- Paginação CI4 nativa sobre a tabela ------------------------
        $perPageOpcoes = [10, 25, 50];
        $perPage = (int) ($this->request->getGet('perPage') ?: 10);
        if (! in_array($perPage, $perPageOpcoes, true)) {
            $perPage = 10;
        }
        $page  = (int) ($this->request->getGet('page') ?: 1);
        $total = count($serie);

        // Tabela mais recente primeiro
        $serieDesc = array_reverse($serie);
        $linhasPagina = array_slice($serieDesc, ($page - 1) * $perPage, $perPage);

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        return view('admin/relatorios/vendas', [
            'titulo'        => 'Painel de vendas',
            'inicio'        => $inicio,
            'fim'           => $fim,
            'numDias'       => $numDias,
            'linhas'        => $linhasPagina,
            'perPage'       => $perPage,
            'perPageOpcoes' => $perPageOpcoes,
            'pager'         => $pager,
            'dadosGrafico'  => $dadosGrafico,
        ]);
    }

    /**
     * Consumo por produto: quantidade vendida no período + estoque.
     * GET: ?categoria=&inicio=&fim=&periodo=7dias|sempre (padrão: desde sempre)
     */
    public function consumo()
    {
        $categoria = $this->request->getGet('categoria') ?: null;
        $periodo   = $this->request->getGet('periodo') ?: 'sempre';
        $inicio    = $this->request->getGet('inicio') ?: null;
        $fim       = $this->request->getGet('fim') ?: null;

        // Resolve o período em datas concretas
        if ($periodo === '7dias') {
            $fim    = date('Y-m-d');
            $inicio = date('Y-m-d', strtotime('-6 days'));
        } elseif ($periodo === 'sempre') {
            $inicio = null;
            $fim    = null;
        }
        // periodo === 'intervalo' usa inicio/fim como vieram

        $itensModel   = new PedidoProdutoModel();
        $produtoModel = new ProdutoModel();

        $produtos = $itensModel->consumoPorProduto($categoria, $inicio, $fim);

        return view('admin/relatorios/consumo', [
            'titulo'     => 'Consumo por produto',
            'produtos'   => $produtos,
            'categorias' => $produtoModel->categorias(),
            'filtros'    => [
                'categoria' => $categoria,
                'periodo'   => $periodo,
                'inicio'    => $inicio,
                'fim'       => $fim,
            ],
        ]);
    }
}
