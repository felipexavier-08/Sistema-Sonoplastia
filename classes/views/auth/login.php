<section class="row justify-content-center align-items-center fade-up" style="min-height: calc(100vh - 140px);" aria-labelledby="titulo-login">
    <section class="col-12 col-md-7 col-lg-5">
        <article class="card glass-card border-0">
            <h1 class="card-header h5 mb-0" id="titulo-login">
                Entrar no sistema
            </h1>
            <div class="card-body p-4 p-md-5">
                <p class="text-muted mb-4">Organize o culto com clareza e acompanhe a execução em tempo real.</p>

                <form method="post" action="<?= e(url('/login')) ?>" class="mobile-stack">
                    <div>
                        <label class="form-label fw-semibold" for="emailLogin">E-mail</label>
                        <input type="email" name="email" id="emailLogin" class="form-control form-control-lg" required>
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="senhaLogin">Senha</label>
                        <input type="password" name="senha" id="senhaLogin" class="form-control form-control-lg" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-2 py-2">Entrar</button>
                </form>
            </div>
        </article>

        <aside class="mt-3 text-center" aria-label="Perfis do sistema">
            <span class="chip">Perfis: Diretora de Musica e Sonoplasta</span>
        </aside>
    </section>
</section>
