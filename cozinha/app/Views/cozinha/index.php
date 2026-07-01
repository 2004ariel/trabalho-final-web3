<?= $this->include('templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Pedidos pendentes</h1>
    <span class="badge bg-secondary fs-6"><?= count($pedidos) ?> na fila</span>
</div>

<?php if ($pedidos === []): ?>

    <div class="alert alert-info text-center py-5">
        <p class="display-6 mb-1">✅</p>
        <p class="mb-0">Nenhum pedido pendente no momento.</p>
        <small class="text-muted">A página atualiza sozinha a cada 30 segundos.</small>
    </div>

<?php else: ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($pedidos as $pedido): ?>
            <?php
                $ehNovo = $pedido['status'] === 'novo';
                $badge  = $ehNovo
                    ? ['bg-warning text-dark', 'Novo']
                    : ['bg-primary', 'Em preparo'];
            ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Pedido #<?= esc($pedido['id']) ?></strong>
                        <span class="badge <?= $badge[0] ?>"><?= $badge[1] ?></span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between text-muted small mb-2">
                            <span>Totem: <strong><?= esc($pedido['totem'] ?? '—') ?></strong></span>
                            <span>
                                <?= ! empty($pedido['created_at'])
                                    ? esc(date('d/m H:i', strtotime($pedido['created_at'])))
                                    : '' ?>
                            </span>
                        </div>
                        <p class="card-text mb-0">
                            <?= $pedido['itens_resumo'] !== ''
                                ? esc($pedido['itens_resumo'])
                                : '<span class="text-muted">Sem itens</span>' ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="<?= site_url('cozinha/detalhes/' . $pedido['id']) ?>"
                           class="btn btn-outline-dark w-100">Ver detalhes</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<?= $this->include('templates/footer') ?>
