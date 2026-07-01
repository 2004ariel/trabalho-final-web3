<?= $this->include('templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Usuários</h1>
    <a href="<?= site_url('admin/usuarios/criar') ?>" class="btn btn-success">+ Novo usuário</a>
</div>

<div class="table-responsive">
    <table class="table table-striped align-middle bg-white">
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Tipo</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr><td colspan="5" class="text-muted text-center">Nenhum usuário cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                    <?php $bloqueado = (int) $u['bloqueado'] === 1; ?>
                    <tr>
                        <td><?= esc($u['nome']) ?></td>
                        <td><?= esc($u['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= esc($u['tipo']) ?></span></td>
                        <td>
                            <?php if ($bloqueado): ?>
                                <span class="badge bg-danger">Bloqueado</span>
                            <?php else: ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('usuarios/editar/' . $u['id']) ?>"
                               class="btn btn-sm btn-outline-primary">Editar</a>

                            <a href="<?= site_url('admin/usuarios/bloquear/' . $u['id']) ?>"
                               class="btn btn-sm <?= $bloqueado ? 'btn-outline-success' : 'btn-outline-danger' ?>"
                               onclick="return confirm('<?= $bloqueado ? 'Desbloquear' : 'Bloquear' ?> <?= esc($u['nome'], 'js') ?>?');">
                                <?= $bloqueado ? 'Desbloquear' : 'Bloquear' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->include('templates/footer') ?>
