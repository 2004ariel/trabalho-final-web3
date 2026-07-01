<?= $this->include('templates/header') ?>

<h1 class="h3 mb-4">Painel de vendas</h1>

<!-- Filtros de intervalo -->
<form method="get" action="<?= site_url('admin/relatorios/vendas') ?>" class="row g-2 align-items-end mb-4">
    <div class="col-auto">
        <label class="form-label" for="inicio">Início</label>
        <input type="date" name="inicio" id="inicio" class="form-control" value="<?= esc($inicio) ?>">
    </div>
    <div class="col-auto">
        <label class="form-label" for="fim">Fim</label>
        <input type="date" name="fim" id="fim" class="form-control" value="<?= esc($fim) ?>">
    </div>
    <input type="hidden" name="perPage" value="<?= esc($perPage) ?>">
    <div class="col-auto">
        <button type="submit" class="btn btn-dark">Filtrar</button>
    </div>
    <div class="col-auto">
        <a href="<?= site_url('admin/relatorios/vendas') ?>" class="btn btn-outline-secondary">Últimos 7 dias</a>
    </div>
</form>

<div class="row g-4">
    <!-- Esquerda: tabela -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                    <form method="get" action="<?= site_url('admin/relatorios/vendas') ?>" class="d-flex align-items-center gap-1 m-0">
                        <input type="hidden" name="inicio" value="<?= esc($inicio) ?>">
                        <input type="hidden" name="fim" value="<?= esc($fim) ?>">
                        <span class="text-muted small">Mostrando</span>
                        <select name="perPage" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            <?php foreach ($perPageOpcoes as $opt): ?>
                                <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="text-muted small">por página</span>
                    </form>
                    <input type="text" id="filtroTabela" class="form-control form-control-sm w-auto"
                           placeholder="Filtrar...">
                </div>

                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-3" id="tabelaVendas">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th class="text-end">Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($linhas)): ?>
                                <tr><td colspan="2" class="text-muted text-center">Sem vendas no período.</td></tr>
                            <?php else: ?>
                                <?php foreach ($linhas as $linha): ?>
                                    <tr>
                                        <td><?= esc(date('d/m/Y', strtotime($linha['data']))) ?></td>
                                        <td class="text-end">R$ <?= number_format((float) $linha['total'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?= $pager->links('default', 'default_full') ?>
            </div>
        </div>
    </div>

    <!-- Direita: gráfico -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Gráfico de vendas dos últimos <?= (int) $numDias ?> dias</h2>
                <canvas id="graficoVendas" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    // Filtro client-side da tabela (por data)
    document.getElementById('filtroTabela').addEventListener('input', function () {
        const termo = this.value.toLowerCase();
        document.querySelectorAll('#tabelaVendas tbody tr').forEach(function (tr) {
            tr.style.display = tr.textContent.toLowerCase().includes(termo) ? '' : 'none';
        });
    });

    // Gráfico de linha
    const dadosGrafico = <?= json_encode($dadosGrafico) ?>;
    new Chart(document.getElementById('graficoVendas'), {
        type: 'line',
        data: {
            labels: dadosGrafico.labels,
            datasets: [{
                label: 'Vendas (R$)',
                data: dadosGrafico.valores,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (valor) {
                            return 'R$ ' + Number(valor).toLocaleString('pt-BR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return 'R$ ' + Number(ctx.parsed.y).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                        }
                    }
                }
            }
        }
    });
</script>

<?= $this->include('templates/footer') ?>
