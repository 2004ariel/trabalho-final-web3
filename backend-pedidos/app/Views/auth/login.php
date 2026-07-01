<?= $this->include('templates/header') ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h1 class="h4 mb-4 text-center">Entrar</h1>

                <form method="post" action="<?= site_url('login') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="email" name="email" id="email" class="form-control"
                               value="<?= esc(old('email')) ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
