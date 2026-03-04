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

$totalMusicas = 0;
$concluidas = 0;
foreach ($musicasPorCategoria as $grupo) {
    $totalMusicas += count($grupo);
    foreach ($grupo as $musica) {
        if ((int) ($musica['concluido'] ?? 0) === 1) {
            $concluidas++;
        }
    }
}
$percentual = $totalMusicas > 0 ? (int) round(($concluidas / $totalMusicas) * 100) : 0;
?>

<header class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 fade-up">
    <div>
        <h4 class="mb-1 page-title">Execução do culto</h4>
        <small class="text-muted">Data: <?= e(date('d/m/Y', strtotime($dataHoje))) ?></small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="chip">Progresso: <?= (int) $percentual ?>%</span>
        <a class="btn btn-outline-primary btn-sm" href="<?= e(url('/execucao/anteriores')) ?>">Cultos anteriores</a>
    </div>
</header>

<?php if (!$culto): ?>
    <section class="card glass-card border-0 fade-up" aria-label="Sem culto">
        <div class="card-body text-center py-4">
            <h6 class="mb-1">Nenhum culto encontrado para hoje</h6>
            <p class="text-muted mb-0">Quando a Diretora de Musica criar o culto do dia, ele aparecera aqui.</p>
        </div>
    </section>
<?php else: ?>
    <section class="card glass-card border-0 mb-3 fade-up" aria-labelledby="titulo-resumo-culto">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1" id="titulo-resumo-culto">Resumo do culto</h5>
                    <div><strong>Data:</strong> <?= e(date('d/m/Y', strtotime($culto['data_culto']))) ?></div>
                    <div><strong>Tipo:</strong> <?= e($culto['tipo_culto'] ?? '-') ?></div>
                </div>
                <?php
                $podeConcluir = $percentual === 100 && !$cultoConcluido;
                $mensagemBloqueio = !$cultoConcluido
                    ? 'Para concluir, finalize 100% das musicas.'
                    : 'Este culto ja foi finalizado e salvo em Cultos anteriores.';
                ?>
                <form method="post" action="<?= e(url('/execucao/culto/' . (int) $culto['id'] . '/concluir')) ?>">
                    <button class="btn btn-outline-primary" <?= $podeConcluir ? '' : 'disabled' ?>>Marcar culto como concluido</button>
                </form>
            </div>

            <?php if (!$podeConcluir): ?>
                <div class="small text-muted mb-2"><?= e($mensagemBloqueio) ?></div>
            <?php endif; ?>

            <?php if ($cultoConcluido): ?>
                <div class="alert alert-success py-2 px-3 mb-3">Culto Finalizado!</div>
            <?php endif; ?>

            <div class="progress" style="height: 12px;">
                <div class="progress-bar" role="progressbar" style="width: <?= (int) $percentual ?>%; background: linear-gradient(90deg, #16336f, #1f8f6b);" aria-valuenow="<?= (int) $percentual ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="small text-muted mt-1"><?= (int) $concluidas ?> de <?= (int) $totalMusicas ?> músicas concluídas</div>
        </div>
    </section>

    <section class="card glass-card border-0 mb-4 fade-up" aria-labelledby="titulo-participantes-hoje">
        <h2 class="card-header h5 mb-0" id="titulo-participantes-hoje">Participantes</h2>
        <div class="card-body">
            <?php if (empty($participantes)): ?>
                <p class="text-muted mb-0">Sem participantes cadastrados.</p>
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
    </section>

    <?php
    $titulos = [
        'regencia_inicio' => 'Regência Início',
        'louvor_pe' => 'Hino Inicial',
        'especial' => 'Louvor Especial',
    ];
    ?>

    <section class="row g-3 fade-up" aria-label="Músicas por categoria">
        <?php foreach ($musicasPorCategoria as $categoria => $musicas): ?>
            <section class="col-12 col-lg-4">
                <article class="card glass-card border-0 h-100">
                    <h2 class="card-header d-flex justify-content-between align-items-center h5 mb-0">
                        <span><?= e($titulos[$categoria]) ?></span>
                        <span class="chip"><?= (int) count($musicas) ?></span>
                    </h2>
                    <div class="card-body">
                        <?php if (empty($musicas)): ?>
                            <p class="text-muted mb-0">Sem músicas.</p>
                        <?php else: ?>
                            <ol class="list-unstyled d-flex flex-column gap-3 m-0">
                                <?php foreach ($musicas as $musica): ?>
                                    <li class="item-soft p-3 <?= ((int) $musica['concluido'] === 1) ? 'border-success-subtle' : '' ?>">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                            <div class="fw-semibold"><?= e($musica['ordem'] . '. ' . $musica['nome_musica']) ?></div>
                                            <?php if ((int) $musica['concluido'] === 1): ?>
                                                <span class="badge text-bg-success">Concluída</span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($categoria !== 'especial'): ?>
                                            <div class="small text-muted mb-2">Origem: <?= e($origensLabel[$musica['origem'] ?? ''] ?? '-') ?></div>
                                        <?php else: ?>
                                            <div class="small text-muted">Cantor: <?= e($musica['cantor'] ?? '-') ?></div>
                                            <?php if (!empty($musica['link_youtube'])): ?>
                                                <a href="<?= e($musica['link_youtube']) ?>" target="_blank" class="small d-inline-block mb-2">Abrir YouTube</a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <form method="post" action="<?= e(url('/execucao/musica/' . (int) $musica['id'] . '/status')) ?>" class="mobile-stack">
                                            <div>
                                                <label class="form-label small fw-semibold" for="obs<?= (int) $musica['id'] ?>">Observação</label>
                                                <textarea name="observacao" id="obs<?= (int) $musica['id'] ?>" class="form-control form-control-sm" rows="2"><?= e((string) ($musica['observacao'] ?? '')) ?></textarea>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" name="concluido" id="m<?= (int) $musica['id'] ?>" <?= ((int) $musica['concluido'] === 1) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="m<?= (int) $musica['id'] ?>">Concluída</label>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
                                            </div>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                    </div>
                </article>
            </section>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
