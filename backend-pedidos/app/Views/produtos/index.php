<?= $this->include('templates/header') ?>

<?php
    $tipo    = session()->get('usuario')['tipo'] ?? null;
    $ehAdmin = in_array($tipo, ['admin', 'superadmin'], true);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Produtos</h1>
    <?php if ($ehAdmin): ?>
        <a href="<?= site_url('admin/produtos/criar') ?>" class="btn btn-success">+ Novo produto</a>
    <?php endif; ?>
</div>

<div class="table-responsive">
    <table class="table table-striped align-middle bg-white">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Categoria</th>
                <th class="text-end">Preço</th>
                <th class="text-center">Disponível</th>
                <th class="text-end">Estoque</th>
                <?php if ($ehAdmin): ?>
                    <th class="text-end">Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produtos)): ?>
                <tr><td colspan="6" class="text-muted text-center">Nenhum produto cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= esc($p['nome']) ?></td>
                        <td><?= esc($p['tipo'] ?? '—') ?></td>
                        <td class="text-end">R$ <?= number_format((float) $p['preco'], 2, ',', '.') ?></td>
                        <td class="text-center"><?= ((int) $p['disponivel'] === 1) ? '✓' : '✗' ?></td>
                        <td class="text-end"><?= esc($p['estoque'] ?? 0) ?></td>
                        <?php if ($ehAdmin): ?>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1 justify-content-end">
                                    <a href="<?= site_url('admin/produtos/editar/' . $p['id']) ?>"
                                       class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form method="post" action="<?= site_url('admin/produtos/excluir/' . $p['id']) ?>"
                                          onsubmit="return confirm('Excluir este produto? Esta ação não pode ser desfeita.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->include('templates/footer') ?>
