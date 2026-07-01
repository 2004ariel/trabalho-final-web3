<?= $this->include('templates/header') ?>

<?php
    $acao = $ehEdicao
        ? site_url('usuarios/atualizar/' . $usuario['id'])
        : site_url('admin/usuarios/salvar');

    $valNome  = old('nome',  $usuario['nome']  ?? '');
    $valEmail = old('email', $usuario['email'] ?? '');
    $valTipo  = old('tipo',  $usuario['tipo']  ?? 'usuario');

    // Superadmin volta para a listagem; usuário comum volta para produtos.
    $voltar = ! empty($podeTipo) ? site_url('admin/usuarios') : site_url('produtos');
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="h3 mb-4"><?= esc($titulo) ?></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="<?= $acao ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label" for="nome">Nome</label>
                        <input type="text" name="nome" id="nome" class="form-control"
                               value="<?= esc($valNome) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="email" name="email" id="email" class="form-control"
                               value="<?= esc($valEmail) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" class="form-control"
                               <?= $ehEdicao ? 'placeholder="Deixe em branco para manter"' : 'required' ?>>
                    </div>

                    <?php if (! empty($podeTipo)): ?>
                        <div class="mb-3">
                            <label class="form-label" for="tipo">Tipo</label>
                            <select name="tipo" id="tipo" class="form-select">
                                <?php foreach ($tipos as $t): ?>
                                    <option value="<?= esc($t) ?>" <?= $valTipo === $t ? 'selected' : '' ?>>
                                        <?= esc(ucfirst($t)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="tipo" value="<?= esc($valTipo) ?>">
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">Salvar</button>
                        <a href="<?= $voltar ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
