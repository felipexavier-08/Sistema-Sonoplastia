<section class="row justify-content-center" aria-labelledby="titulo-novo-culto">
    <section class="col-12 col-lg-7 fade-up">
        <article class="card glass-card border-0">
            <h1 class="card-header d-flex justify-content-between align-items-center h5 mb-0" id="titulo-novo-culto">
                <span>Novo culto</span>
                <span class="chip">Diretora de Musica</span>
            </h1>
            <div class="card-body">
                <p class="text-muted mb-3">Use este cadastro para cultos especiais fora da agenda automatica (domingo, quarta e sabado).</p>
                <form method="post" action="<?= e(url('/cultos/salvar')) ?>" class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="dataCulto">Data do culto</label>
                        <input
                            type="text"
                            name="data_culto"
                            id="dataCulto"
                            class="form-control form-control-lg"
                            placeholder="dd/mm/aaaa"
                            inputmode="numeric"
                            maxlength="10"
                            pattern="\d{2}/\d{2}/\d{4}"
                            required
                        >
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="tipoCulto">Tipo do culto</label>
                        <input type="text" name="tipo_culto" id="tipoCulto" class="form-control form-control-lg" value="culto normal" placeholder="Ex.: Culto Jovem">
                        <small class="text-muted">Opcional. Se vazio, será salvo como "culto normal".</small>
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                        <button type="submit" class="btn btn-primary">Salvar culto</button>
                        <a href="<?= e(url('/cultos')) ?>" class="btn btn-outline-primary">Voltar</a>
                    </div>
                </form>
            </div>
        </article>
    </section>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var inputData = document.getElementById('dataCulto');
    if (!inputData) {
        return;
    }

    inputData.addEventListener('input', function (event) {
        var valor = event.target.value.replace(/\D/g, '').slice(0, 8);

        if (valor.length > 4) {
            valor = valor.slice(0, 2) + '/' + valor.slice(2, 4) + '/' + valor.slice(4);
        } else if (valor.length > 2) {
            valor = valor.slice(0, 2) + '/' + valor.slice(2);
        }

        event.target.value = valor;
    });
});
</script>
