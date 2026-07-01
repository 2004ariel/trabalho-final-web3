<?= $this->include('templates/header') ?>

<?php
    // Emoji por categoria (tipo do produto)
    $emojiPorTipo = static function (?string $tipo): string {
        $t = mb_strtolower((string) $tipo);
        if (str_contains($t, 'lanche'))  return '🍔';
        if (str_contains($t, 'bebida'))  return '🥤';
        if (str_contains($t, 'sobrem'))  return '🍰';
        if (str_contains($t, 'porç') || str_contains($t, 'porc')) return '🍟';
        return '🍽️';
    };
?>

<h1 class="h3 mb-4">Consumo por produto</h1>

<!-- Filtros -->
<form method="get" action="<?= site_url('admin/relatorios/consumo') ?>" class="row g-2 align-items-end mb-4">
    <input type="hidden" name="periodo" id="periodo" value="<?= esc($filtros['periodo']) ?>">

    <div class="col-auto">
        <label class="form-label small">Categoria</label>
        <select name="categoria" class="form-select" onchange="this.form.submit()">
            <option value="">Todas as categorias</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= esc($cat) ?>" <?= $filtros['categoria'] === $cat ? 'selected' : '' ?>>
                    <?= esc($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-auto">
        <label class="form-label small">Início</label>
        <input type="date" name="inicio" class="form-control" value="<?= esc($filtros['inicio']) ?>">
    </div>
    <div class="col-auto">
        <label class="form-label small">Fim</label>
        <input type="date" name="fim" class="form-control" value="<?= esc($filtros['fim']) ?>">
    </div>

    <div class="col-auto">
        <button type="submit" class="btn btn-dark"
                onclick="document.getElementById('periodo').value='intervalo'">
            Intervalo de datas
        </button>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-primary"
                onclick="document.getElementById('periodo').value='7dias'">
            Últimos 7 dias
        </button>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-primary"
                onclick="document.getElementById('periodo').value='sempre'">
            Desde sempre
        </button>
    </div>
    <div class="col-auto">
        <a href="<?= site_url('admin/relatorios/consumo') ?>" class="btn btn-outline-secondary" title="Limpar filtros">↺</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
            <div class="d-flex align-items-center gap-1">
                <span class="text-muted small">Mostrando</span>
                <select id="porPagina" class="form-select form-select-sm w-auto">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-muted small">por página</span>
            </div>
            <input type="text" id="filtroNome" class="form-control form-control-sm w-auto"
                   placeholder="Filtrar por nome...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle" id="tabelaConsumo">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th class="text-center">Disponível</th>
                        <th class="text-end">Estoque</th>
                        <th class="text-end">Qtd vendida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produtos)): ?>
                        <tr><td colspan="5" class="text-muted text-center">Nenhum produto encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($produtos as $p): ?>
                            <tr data-nome="<?= esc(mb_strtolower($p['nome']), 'attr') ?>">
                                <td><?= $emojiPorTipo($p['tipo']) ?> <?= esc($p['nome']) ?></td>
                                <td><?= esc($p['tipo'] ?? '—') ?></td>
                                <td class="text-center">
                                    <?= ((int) $p['disponivel'] === 1)
                                        ? '<span class="text-success">✓</span>'
                                        : '<span class="text-danger">✗</span>' ?>
                                </td>
                                <td class="text-end"><?= esc($p['estoque'] ?? 0) ?></td>
                                <td class="text-end"><?= esc($p['quantidade_vendida']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <nav class="d-flex justify-content-center">
            <ul class="pagination pagination-sm mb-0" id="paginacaoConsumo"></ul>
        </nav>
    </div>
</div>

<script>
(function () {
    const tabela    = document.getElementById('tabelaConsumo');
    const filtro    = document.getElementById('filtroNome');
    const porPagina = document.getElementById('porPagina');
    const paginacao = document.getElementById('paginacaoConsumo');
    const linhas    = Array.from(tabela.querySelectorAll('tbody tr')).filter(tr => tr.hasAttribute('data-nome'));
    let pagina = 1;

    function filtradas() {
        const termo = filtro.value.toLowerCase();
        return linhas.filter(tr => tr.getAttribute('data-nome').includes(termo));
    }

    function render() {
        const visiveis = filtradas();
        const porPag   = parseInt(porPagina.value, 10);
        const totalPag = Math.max(1, Math.ceil(visiveis.length / porPag));
        if (pagina > totalPag) pagina = totalPag;

        // Esconde todas; mostra só as da página atual
        linhas.forEach(tr => tr.style.display = 'none');
        const inicio = (pagina - 1) * porPag;
        visiveis.slice(inicio, inicio + porPag).forEach(tr => tr.style.display = '');

        // Botões de paginação
        paginacao.innerHTML = '';
        for (let i = 1; i <= totalPag; i++) {
            const li = document.createElement('li');
            li.className = 'page-item' + (i === pagina ? ' active' : '');
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.textContent = i;
            a.addEventListener('click', function (e) { e.preventDefault(); pagina = i; render(); });
            li.appendChild(a);
            paginacao.appendChild(li);
        }
    }

    filtro.addEventListener('input', function () { pagina = 1; render(); });
    porPagina.addEventListener('change', function () { pagina = 1; render(); });
    render();
})();
</script>

<?= $this->include('templates/footer') ?>
