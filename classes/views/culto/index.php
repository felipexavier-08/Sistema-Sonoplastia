<header class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 fade-up">
    <div>
        <h4 class="mb-1 page-title">Cultos cadastrados</h4>
        <small class="text-muted">Cultos de domingo, quarta e sabado do mes atual sao gerados automaticamente.</small>
    </div>
    <a class="btn btn-primary" href="<?= e(url('/cultos/criar')) ?>">Novo culto</a>
</header>

<section class="card glass-card border-0 fade-up" aria-label="Lista de cultos">
    <div class="card-body">
        <?php if (empty($cultos)): ?>
            <article class="text-center py-4">
                <p class="mb-1 fw-semibold">Nenhum culto cadastrado</p>
                <p class="text-muted mb-0">Comece criando o primeiro culto da agenda.</p>
            </article>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="chip">Total: <?= (int) count($cultos) ?></span>
            </div>
            <div class="table-responsive" role="region" aria-label="Tabela de cultos">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cultos as $culto): ?>
                        <?php
                        $totalParticipantes = (int) ($culto['total_participantes'] ?? 0);
                        $totalRegentes = (int) ($culto['total_regentes'] ?? 0);
                        $totalMusicas = (int) ($culto['total_musicas'] ?? 0);
                        $totalConcluido = (int) ($culto['total_concluido'] ?? 0);
                        $tipoCultoNormal = strtolower(trim((string) ($culto['tipo_culto'] ?? ''))) === 'culto normal';
                        $dataCulto = DateTimeImmutable::createFromFormat('Y-m-d', (string) ($culto['data_culto'] ?? ''));
                        $ehMesAtual = $dataCulto && $dataCulto->format('Y-m') === (new DateTimeImmutable('today'))->format('Y-m');
                        $diaSemana = $dataCulto ? (int) $dataCulto->format('w') : -1;
                        $ehDiaPadrao = in_array($diaSemana, [0, 3, 6], true);
                        $ehCultoAutomaticoMesAtual = $tipoCultoNormal && $ehMesAtual && $ehDiaPadrao;

                        $statusTexto = 'Nao editado';
                        $statusClasse = 'status-pill--muted';

                        if ($totalConcluido > 0) {
                            $statusTexto = 'Completo';
                            $statusClasse = 'status-pill--success';
                        } elseif ($totalParticipantes > 0 || $totalRegentes > 0 || $totalMusicas > 0) {
                            $statusTexto = 'Editado';
                            $statusClasse = 'status-pill--edited';
                        } else {
                            $statusTexto = 'Nao editado';
                            $statusClasse = 'status-pill--muted';
                        }

                        $mensagemConfirmacao = $ehCultoAutomaticoMesAtual
                            ? 'Deseja limpar este culto normal? A data sera mantida e apenas musicas/participantes serao removidos.'
                            : 'Deseja excluir este culto? Esta acao remove o dia da agenda.';

                        $tituloBotao = $ehCultoAutomaticoMesAtual ? 'Limpar culto' : 'Excluir culto';
                        ?>
                        <tr>
                            <td data-label="#"><?= (int) $culto['id'] ?></td>
                            <td data-label="Data"><?= e(date('d/m/Y', strtotime($culto['data_culto']))) ?></td>
                            <td data-label="Tipo"><?= e($culto['tipo_culto'] ?? '-') ?></td>
                            <td data-label="Status"><span class="status-pill <?= e($statusClasse) ?>"><?= e($statusTexto) ?></span></td>
                            <td data-label="Ações" class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn-open-culto" href="<?= e(url('/cultos/' . (int) $culto['id'])) ?>">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M14 4h6v6" />
                                            <path d="M20 4l-9 9" />
                                            <path d="M20 13v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5" />
                                        </svg>
                                        Abrir culto
                                    </a>
                                    <form method="post" action="<?= e(url('/cultos/' . (int) $culto['id'] . '/excluir')) ?>" onsubmit="return confirm('<?= e($mensagemConfirmacao) ?>');">
                                        <button type="submit" class="btn-delete-icon" title="<?= e($tituloBotao) ?>" aria-label="<?= e($tituloBotao) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" aria-hidden="true">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0A.5.5 0 0 1 8.5 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1 0-2H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11z"/>
                                            </svg>
                                        </button>
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
