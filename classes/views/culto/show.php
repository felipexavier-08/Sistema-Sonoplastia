<?php
$origensLabel = [
    'hinario_novo' => 'Hinário novo',
    'hinario_antigo' => 'Hinário antigo',
    'cd_jovem' => 'CD Jovem',
    'adoradores_5' => 'Adoradores 5',
    'adoradores_3' => 'Adoradores 3',
    'adoradores_2' => 'Adoradores 2',
];
$funcoesLabel = [
    'regente' => 'Regente',
    'especial' => 'Louvor Especial',
];

$totalMusicas = count($musicasPorCategoria['regencia_inicio']) + count($musicasPorCategoria['louvor_pe']) + count($musicasPorCategoria['especial']);
?>

<header class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 fade-up">
    <div>
        <h4 class="mb-1 page-title">Culto de <?= e(date('d/m/Y', strtotime($culto['data_culto']))) ?></h4>
        <small class="text-muted">Tipo: <?= e($culto['tipo_culto'] ?? '-') ?></small>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <span class="chip">Participantes: <?= (int) count($participantes) ?></span>
        <span class="chip">Músicas: <?= (int) $totalMusicas ?></span>
    </div>
</header>

<section class="row g-3 mb-4" aria-label="Participantes">
    <section class="col-12 col-lg-5 fade-up" aria-labelledby="titulo-add-participante">
        <div class="card glass-card border-0 h-100">
            <h2 class="card-header h5 mb-0" id="titulo-add-participante">Adicionar participante</h2>
            <div class="card-body">
                <form method="post" action="<?= e(url('/cultos/' . (int) $culto['id'] . '/participantes/salvar')) ?>" class="row g-2">
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="participanteNome">Nome</label>
                        <input type="text" name="nome" id="participanteNome" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="participanteFuncao">Função</label>
                        <select name="funcao" id="participanteFuncao" class="form-select" required>
                            <option value="regente">Regente</option>
                            <option value="especial">Louvor Especial</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">Cadastrar participante</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="col-12 col-lg-7 fade-up" aria-labelledby="titulo-participantes-culto">
        <div class="card glass-card border-0 h-100">
            <h2 class="card-header h5 mb-0" id="titulo-participantes-culto">Participantes do culto</h2>
            <div class="card-body">
                <?php if (empty($participantes)): ?>
                    <p class="text-muted mb-0">Nenhum participante cadastrado.</p>
                <?php else: ?>
                    <ul class="list-unstyled d-flex flex-column gap-2 m-0">
                        <?php foreach ($participantes as $participante): ?>
                            <li class="item-soft p-2 d-flex justify-content-between align-items-center">
                                <span><?= e($participante['nome']) ?></span>
                                <span class="chip"><?= e($funcoesLabel[$participante['funcao'] ?? ''] ?? ($participante['funcao'] ?? '-')) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </section>
</section>

<section class="card glass-card border-0 mb-4 fade-up" aria-labelledby="titulo-add-musica">
    <h2 class="card-header h5 mb-0" id="titulo-add-musica">Adicionar música</h2>
    <div class="card-body">
        <form method="post" action="<?= e(url('/cultos/' . (int) $culto['id'] . '/musicas/salvar')) ?>" class="row g-2" id="formAdicionarMusica">
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label fw-semibold" for="categoriaMusica">Categoria</label>
                <select name="categoria" class="form-select" id="categoriaMusica" required>
                    <option value="regencia_inicio">Regência início</option>
                    <option value="louvor_pe">Hino Inicial</option>
                    <option value="especial">Louvor Especial</option>
                </select>
            </div>
            <div class="col-12 col-md-8 col-lg-3">
                <label class="form-label fw-semibold" for="nomeMusica">Nome da música</label>
                <input type="text" name="nome_musica" id="nomeMusica" class="form-control" required>
            </div>
            <div class="col-12 col-md-6 col-lg-2" id="grupoOrigem">
                <label class="form-label fw-semibold" for="origemMusica">Origem</label>
                <select name="origem" id="origemMusica" class="form-select">
                    <option value="hinario_novo">Hinário novo</option>
                    <option value="hinario_antigo">Hinário antigo</option>
                    <option value="cd_jovem">CD Jovem</option>
                    <option value="adoradores_5">Adoradores 5</option>
                    <option value="adoradores_3">Adoradores 3</option>
                    <option value="adoradores_2">Adoradores 2</option>
                </select>
                <small class="text-muted">Apenas para regência.</small>
            </div>
            <div class="col-12 col-md-6 col-lg-2 d-none" id="grupoCantor">
                <label class="form-label fw-semibold" for="inputCantor">Cantor (Louvor Especial)</label>
                <input type="text" name="cantor" id="inputCantor" class="form-control" placeholder="Informe o cantor">
            </div>
            <div class="col-12 col-md-6 col-lg-2">
                <label class="form-label fw-semibold" for="ordemMusica">Ordem</label>
                <input type="number" name="ordem" id="ordemMusica" min="1" class="form-control" placeholder="Auto">
                <small class="text-muted">Ex.: 1 (primeira música), 2 (segunda).</small>
            </div>
            <div class="col-12 col-lg-10 d-none" id="grupoLinkYoutube">
                <label class="form-label fw-semibold" for="linkYoutube">Link YouTube (Louvor Especial)</label>
                <input type="url" name="link_youtube" id="linkYoutube" class="form-control" placeholder="https://...">
            </div>
            <div class="col-12 col-lg-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Adicionar</button>
            </div>
        </form>
    </div>
</section>

<?php
$titulos = [
    'regencia_inicio' => 'Regência Início',
    'louvor_pe' => 'Hino Inicial',
    'especial' => 'Louvor Especial',
];
?>

<section class="accordion fade-up" id="musicasAccordion" aria-label="Músicas por categoria">
    <?php $i = 0; foreach ($musicasPorCategoria as $categoria => $musicas): ?>
        <?php $i++; ?>
        <article class="accordion-item mb-3 border-0 glass-card overflow-hidden">
            <h2 class="accordion-header">
                <button class="accordion-button <?= $i === 1 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#cat<?= $i ?>">
                    <span class="categoria-title"><?= e($titulos[$categoria]) ?></span>
                    <span class="ms-2 chip"><?= (int) count($musicas) ?> músicas</span>
                </button>
            </h2>
            <div id="cat<?= $i ?>" class="accordion-collapse collapse <?= $i === 1 ? 'show' : '' ?>" data-bs-parent="#musicasAccordion">
                <div class="accordion-body">
                    <?php if (empty($musicas)): ?>
                        <p class="text-muted mb-0">Sem músicas nesta categoria.</p>
                    <?php else: ?>
                        <ol class="list-unstyled d-flex flex-column gap-2 m-0">
                            <?php foreach ($musicas as $musica): ?>
                                <li class="item-soft p-3">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <div class="fw-semibold"><?= e($musica['ordem'] . '. ' . $musica['nome_musica']) ?></div>
                                            <?php if ($categoria !== 'especial'): ?>
                                                <small class="text-muted">Origem: <?= e($origensLabel[$musica['origem'] ?? ''] ?? '-') ?></small>
                                            <?php else: ?>
                                                <small class="text-muted d-block">Cantor: <?= e($musica['cantor'] ?? '-') ?></small>
                                                <?php if (!empty($musica['link_youtube'])): ?>
                                                    <a href="<?= e($musica['link_youtube']) ?>" target="_blank" class="small">Abrir no YouTube</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <form method="post" action="<?= e(url('/cultos/' . (int) $culto['id'] . '/musicas/' . (int) $musica['id'] . '/excluir')) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja realmente excluir esta música?');">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<nav class="mt-3 fade-up" aria-label="Navegação do culto">
    <a href="<?= e(url('/cultos')) ?>" class="btn btn-outline-primary">Voltar para lista</a>
</nav>

<script>
    (function () {
        var selectCategoria = document.getElementById('categoriaMusica');
        if (!selectCategoria) {
            return;
        }

        var grupoOrigem = document.getElementById('grupoOrigem');
        var grupoCantor = document.getElementById('grupoCantor');
        var grupoLinkYoutube = document.getElementById('grupoLinkYoutube');
        var inputCantor = document.getElementById('inputCantor');

        function atualizarCamposPorCategoria() {
            var isEspecial = selectCategoria.value === 'especial';

            if (grupoOrigem) {
                grupoOrigem.classList.toggle('d-none', isEspecial);
            }

            if (grupoCantor) {
                grupoCantor.classList.toggle('d-none', !isEspecial);
            }

            if (grupoLinkYoutube) {
                grupoLinkYoutube.classList.toggle('d-none', !isEspecial);
            }

            if (inputCantor) {
                inputCantor.required = isEspecial;
            }
        }

        selectCategoria.addEventListener('change', atualizarCamposPorCategoria);
        atualizarCamposPorCategoria();
    })();
</script>
