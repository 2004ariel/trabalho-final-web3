<?= $this->include('templates/header') ?>

<?php
    $rotuloStatus = [
        'novo'       => ['bg-warning text-dark', 'Novo'],
        'em_preparo' => ['bg-primary', 'Em preparo'],
        'finalizado' => ['bg-success', 'Finalizado'],
        'cancelado'  => ['bg-danger', 'Cancelado'],
    ];
    $st = $rotuloStatus[$pedido['status']] ?? ['bg-secondary', $pedido['status']];
?>

<a href="<?= site_url('cozinha') ?>" class="btn btn-link px-0 mb-3">&larr; Voltar</a>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0">
            Pedido #<?= esc($pedido['id']) ?>
            <span class="text-muted fs-6">— Totem <?= esc($pedido['totem'] ?? '—') ?></span>
        </h1>
        <span class="badge <?= $st[0] ?> fs-6"><?= $st[1] ?></span>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">
            <?= ! empty($pedido['created_at'])
                ? esc(date('d/m/Y \à\s H:i', strtotime($pedido['created_at'])))
                : 'Data não informada' ?>
        </p>

        <h2 class="h5">Itens</h2>
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th class="text-end" style="width:140px;">Quantidade</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($itens === []): ?>
                    <tr><td colspan="2" class="text-muted">Nenhum item neste pedido.</td></tr>
                <?php else: ?>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?= esc($item['nome'] ?? ('Produto #' . $item['id_produto'])) ?></td>
                            <td class="text-end"><?= esc($item['quantidade']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <?php if ($pedido['status'] === 'novo'): ?>
                <form method="post" action="<?= site_url('cozinha/status/' . $pedido['id']) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="em_preparo">
                    <button type="submit" class="btn btn-primary">&#9654; Iniciar preparo</button>
                </form>
            <?php endif; ?>

            <?php if ($pedido['status'] === 'em_preparo'): ?>
                <form method="post" action="<?= site_url('cozinha/status/' . $pedido['id']) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="finalizado">
                    <button type="submit" class="btn btn-success">&#9989; Finalizar pedido</button>
                </form>
            <?php endif; ?>

            <form method="post" action="<?= site_url('cozinha/status/' . $pedido['id']) ?>"
                  onsubmit="return confirm('Cancelar o pedido #<?= esc($pedido['id']) ?>?');">
                <?= csrf_field() ?>
                <input type="hidden" name="status" value="cancelado">
                <button type="submit" class="btn btn-danger">&#10005; Cancelar pedido</button>
            </form>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
