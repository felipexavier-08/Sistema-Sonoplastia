<header class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 fade-up">
    <div>
        <h4 class="mb-1 page-title">Cultos anteriores</h4>
        <small class="text-muted">Historico de cultos concluidos pela sonoplastia.</small>
    </div>
    <a href="<?= e(url('/execucao/hoje')) ?>" class="btn btn-outline-primary">Voltar para hoje</a>
</header>

<section class="card glass-card border-0 fade-up" aria-label="Histórico de cultos">
    <div class="card-body">
        <?php if (empty($cultos)): ?>
            <article class="text-center py-4">
                <p class="mb-1 fw-semibold">Nenhum culto concluido ainda</p>
                <p class="text-muted mb-0">Quando um culto for marcado como concluido, ele aparecera aqui.</p>
            </article>
        <?php else: ?>
            <div class="table-responsive" role="region" aria-label="Tabela de cultos anteriores">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Concluido em</th>
                        <th>Músicas</th>
                        <th>Regentes</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cultos as $culto): ?>
                        <tr>
                            <td data-label="Data"><?= e(date('d/m/Y', strtotime($culto['data_culto']))) ?></td>
                            <td data-label="Tipo"><?= e($culto['tipo_culto'] ?? '-') ?></td>
                            <td data-label="Concluido em"><?= e(date('d/m/Y H:i', strtotime($culto['concluido_em']))) ?></td>
                            <td data-label="Músicas"><?= (int) ($culto['total_musicas'] ?? 0) ?></td>
                            <td data-label="Regentes"><?= (int) ($culto['total_regentes'] ?? 0) ?></td>
                            <td data-label="Ações" class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(url('/execucao/anteriores/' . (int) $culto['id'])) ?>">Ver dados</a>
                                    <form method="post" action="<?= e(url('/execucao/anteriores/' . (int) $culto['id'] . '/limpar')) ?>" onsubmit="return confirm('Deseja remover este culto da lista de anteriores?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Limpar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
