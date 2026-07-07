<?= $this->include('templates/header') ?>

<?php
    $ehEdicao = isset($produto) && $produto !== null;

    $acao = $ehEdicao
        ? site_url('admin/produtos/atualizar/' . $produto['id'])
        : site_url('admin/produtos/salvar');

    $valNome      = old('nome', $produto['nome'] ?? '');
    $valPreco     = old('preco', $produto['preco'] ?? '');
    $valTipo      = old('tipo', $produto['tipo'] ?? '');
    $valDescricao = old('descricao', $produto['descricao'] ?? '');
    $valEstoque   = old('estoque', $produto['estoque'] ?? 0);
    $valDisponivel = old('disponivel', $produto['disponivel'] ?? 1);

    // Categorias disponíveis (as mesmas já usadas no cardápio).
    $categorias = ['Lanches', 'Bebidas', 'Porções'];
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
                        <label class="form-label" for="preco">Preço</label>
                        <input type="text" name="preco" id="preco" class="form-control"
                               value="<?= esc($valPreco) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="tipo">Categoria</label>
                        <select name="tipo" id="tipo" class="form-select">
                            <option value="" <?= $valTipo === '' ? 'selected' : '' ?>>Selecione a categoria</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= esc($cat) ?>" <?= $valTipo === $cat ? 'selected' : '' ?>>
                                    <?= esc($cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="descricao">Descrição</label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="3"><?= esc($valDescricao) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="estoque">Estoque</label>
                        <input type="number" name="estoque" id="estoque" class="form-control"
                               value="<?= esc($valEstoque) ?>" min="0" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="disponivel" id="disponivel" class="form-check-input"
                               value="1" <?= (int) $valDisponivel === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="disponivel">Disponível para venda no totem</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">Salvar</button>
                        <a href="<?= site_url('produtos') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
