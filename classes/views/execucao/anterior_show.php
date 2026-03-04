<?php
$origensLabel = [
    'hinario_novo' => 'Hinario novo',
    'hinario_antigo' => 'Hinario antigo',
    'cd_jovem' => 'CD Jovem',
    'adoradores_5' => 'Adoradores 5',
    'adoradores_3' => 'Adoradores 3',
    'adoradores_2' => 'Adoradores 2',
];
$funcoesLabel = [
    'regente' => 'Regente',
    'especial' => 'Louvor Especial',
];

$titulos = [
    'regencia_inicio' => 'Regencia Inicio',
    'louvor_pe' => 'Hino Inicial',
    'especial' => 'Louvor Especial',
];
?>

<header class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 fade-up">
    <div>
        <h4 class="mb-1 page-title">Dados do culto concluido</h4>
        <small class="text-muted">Data: <?= e(date('d/m/Y', strtotime($culto['data_culto']))) ?> | Concluido em: <?= e(date('d/m/Y H:i', strtotime($culto['concluido_em']))) ?></small>
    </div>
    <a href="<?= e(url('/execucao/anteriores')) ?>" class="btn btn-outline-primary">Voltar para cultos anteriores</a>
</header>

<section class="card glass-card border-0 mb-3 fade-up" aria-label="Resumo do culto">
    <div class="card-body">
        <div><strong>Tipo:</strong> <?= e($culto['tipo_culto'] ?? '-') ?></div>
    </div>
</section>

<section class="card glass-card border-0 mb-4 fade-up" aria-labelledby="titulo-participantes-anterior">
    <h2 class="card-header h5 mb-0" id="titulo-participantes-anterior">Participantes</h2>
    <div class="card-body">
        <?php if (empty($participantes)): ?>
            <p class="text-muted mb-0">Sem participantes registrados.</p>
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

<section class="row g-3 fade-up" aria-label="Músicas do culto">
    <?php foreach ($musicasPorCategoria as $categoria => $musicas): ?>
        <section class="col-12 col-lg-4">
            <article class="card glass-card border-0 h-100">
                <h2 class="card-header d-flex justify-content-between align-items-center h5 mb-0">
                    <span><?= e($titulos[$categoria] ?? ucfirst($categoria)) ?></span>
                    <span class="chip"><?= (int) count($musicas) ?></span>
                </h2>
                <div class="card-body">
                    <?php if (empty($musicas)): ?>
                        <p class="text-muted mb-0">Sem musicas.</p>
                    <?php else: ?>
                        <ol class="list-unstyled d-flex flex-column gap-3 m-0">
                            <?php foreach ($musicas as $musica): ?>
                                <li class="item-soft p-3">
                                    <?php $observacaoMusica = trim((string) ($musica['observacao'] ?? '')); ?>
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                        <div class="fw-semibold"><?= e((string) ($musica['ordem'] ?? '-') . '. ' . (string) ($musica['nome_musica'] ?? '-')) ?></div>
                                        <span class="badge <?= ((int) ($musica['concluido'] ?? 0) === 1) ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                            <?= ((int) ($musica['concluido'] ?? 0) === 1) ? 'Concluida' : 'Pendente' ?>
                                        </span>
                                    </div>

                                    <?php if ($categoria !== 'especial'): ?>
                                        <div class="small text-muted mb-1">Origem: <?= e($origensLabel[$musica['origem'] ?? ''] ?? '-') ?></div>
                                    <?php else: ?>
                                        <div class="small text-muted mb-1">Cantor: <?= e($musica['cantor'] ?? '-') ?></div>
                                    <?php endif; ?>

                                    <div class="small"><strong>Observacao:</strong> <?= $observacaoMusica !== '' ? e($observacaoMusica) : '-' ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </div>
            </article>
        </section>
    <?php endforeach; ?>
</section>
