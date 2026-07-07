<?= $this->include('templates/header') ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <div class="text-center mb-3">
                    <svg style="width:3.5rem;height:3.5rem" viewBox="0 0 64 64" role="img" aria-label="Salsicha Lanches" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="58" height="58" rx="17" fill="#5bbf3a" stroke="#3f9526" stroke-width="3"/><g transform="rotate(-18 32 33)"><rect x="12" y="30" width="40" height="13" rx="6.5" fill="#f2b45f"/><rect x="15" y="27" width="34" height="10" rx="5" fill="#c34a2c"/><rect x="12" y="23" width="40" height="10" rx="5" fill="#f7cf8a"/><path d="M17 28 q4.5 -3.5 9 0 t9 0 t9 0" fill="none" stroke="#ffd23f" stroke-width="2.6" stroke-linecap="round"/></g><circle cx="45" cy="45" r="9.5" fill="#bfeee9" fill-opacity="0.5" stroke="#0f8f88" stroke-width="3"/><line x1="52" y1="52" x2="58" y2="58" stroke="#0f8f88" stroke-width="4" stroke-linecap="round"/></svg>
                </div>
                <h1 class="h4 mb-4 text-center">Entrar no painel</h1>

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
