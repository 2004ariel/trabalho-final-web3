<?= $this->include('templates/header') ?>

<h1 class="h3 mb-4">Produtos</h1>

<div class="table-responsive">
    <table class="table table-striped align-middle bg-white">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Categoria</th>
                <th class="text-end">Preço</th>
                <th class="text-center">Disponível</th>
                <th class="text-end">Estoque</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produtos)): ?>
                <tr><td colspan="5" class="text-muted text-center">Nenhum produto cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= esc($p['nome']) ?></td>
                        <td><?= esc($p['tipo'] ?? '—') ?></td>
                        <td class="text-end">R$ <?= number_format((float) $p['preco'], 2, ',', '.') ?></td>
                        <td class="text-center"><?= ((int) $p['disponivel'] === 1) ? '✓' : '✗' ?></td>
                        <td class="text-end"><?= esc($p['estoque'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->include('templates/footer') ?>
