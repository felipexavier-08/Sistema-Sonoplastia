<?php

declare(strict_types=1);

class ExecucaoController extends BaseController
{
    public function hoje(): void
    {
        $dataHoje = date('Y-m-d');

        $cultoModel = new Culto();
        $participanteModel = new ParticipanteCulto();
        $musicaModel = new Musica();

        $execucaoModel = new Execucao();
        $culto = $cultoModel->buscarCultoDoDia($dataHoje);
        if (!$culto) {
            $this->render('execucao/hoje', [
                'culto' => null,
                'participantes' => [],
                'musicasPorCategoria' => [
                    'regencia_inicio' => [],
                    'louvor_pe' => [],
                    'especial' => [],
                ],
                'dataHoje' => $dataHoje,
                'cultoConcluido' => false,
            ]);
            return;
        }

        $participantes = $participanteModel->listarPorCulto((int) $culto['id']);
        $musicas = $musicaModel->listarPorCultoComExecucao((int) $culto['id']);

        $musicasPorCategoria = [
            'regencia_inicio' => [],
            'louvor_pe' => [],
            'especial' => [],
        ];

        foreach ($musicas as $musica) {
            $musicasPorCategoria[$musica['categoria']][] = $musica;
        }

        $cultoConcluido = $execucaoModel->buscarHistoricoPorCultoId((int) $culto['id']) !== null;

        $this->render('execucao/hoje', [
            'culto' => $culto,
            'participantes' => $participantes,
            'musicasPorCategoria' => $musicasPorCategoria,
            'dataHoje' => $dataHoje,
            'cultoConcluido' => $cultoConcluido,
        ]);
    }

    public function salvarStatus(int $id): void
    {
        $concluido = isset($_POST['concluido']) && $_POST['concluido'] === '1';
        $observacao = trim($_POST['observacao'] ?? '');

        $musicaModel = new Musica();
        $musica = $musicaModel->buscarPorId($id);
        if (!$musica) {
            http_response_code(404);
            echo 'Música não encontrada.';
            return;
        }

        $execucaoModel = new Execucao();
        if ($execucaoModel->buscarHistoricoPorCultoId((int) $musica['culto_id'])) {
            setFlash('warning', 'Este culto ja foi finalizado. Nao e possivel alterar a execucao.');
            redirect('/execucao/hoje');
        }

        $execucaoModel->salvarStatus($id, $concluido, $observacao);

        setFlash('success', 'Execução da música atualizada.');
        redirect('/execucao/hoje');
    }

    public function concluirCulto(int $id): void
    {
        $cultoModel = new Culto();
        $culto = $cultoModel->buscarPorId($id);

        if (!$culto) {
            http_response_code(404);
            echo 'Culto não encontrado.';
            return;
        }

        $musicaModel = new Musica();
        $execucaoModel = new Execucao();
        $participanteModel = new ParticipanteCulto();

        if ($execucaoModel->buscarHistoricoPorCultoId($id)) {
            setFlash('warning', 'Este culto ja foi marcado como concluido.');
            redirect('/execucao/hoje');
        }

        $musicas = $musicaModel->listarPorCultoComExecucao($id);
        $totalMusicas = count($musicas);
        if ($totalMusicas === 0) {
            setFlash('warning', 'Nao e possivel concluir um culto sem musicas cadastradas.');
            redirect('/execucao/hoje');
        }

        $concluidas = 0;
        foreach ($musicas as $musica) {
            if ((int) ($musica['concluido'] ?? 0) === 1) {
                $concluidas++;
            }
        }

        $percentual = (int) round(($concluidas / $totalMusicas) * 100);
        if ($percentual < 100) {
            setFlash('warning', 'O culto so pode ser concluido com progresso em 100%.');
            redirect('/execucao/hoje');
        }

        $participantes = $participanteModel->listarPorCulto($id);
        $execucaoModel->salvarHistoricoCulto($culto, $participantes, $musicas);

        setFlash('success', 'Culto finalizado!');
        redirect('/execucao/hoje');
    }

    public function anteriores(): void
    {
        $execucaoModel = new Execucao();
        $cultos = $execucaoModel->listarHistoricoCultos();

        $this->render('execucao/anteriores', [
            'cultos' => $cultos,
        ]);
    }

    public function showAnterior(int $id): void
    {
        $execucaoModel = new Execucao();
        $dados = $execucaoModel->buscarHistoricoCompleto($id);

        if (!$dados) {
            http_response_code(404);
            echo 'Culto concluido nao encontrado.';
            return;
        }

        $musicasPorCategoria = [
            'regencia_inicio' => [],
            'louvor_pe' => [],
            'especial' => [],
        ];

        foreach ($dados['musicas'] as $musica) {
            $categoria = (string) ($musica['categoria'] ?? '');
            if (!isset($musicasPorCategoria[$categoria])) {
                $musicasPorCategoria[$categoria] = [];
            }
            $musicasPorCategoria[$categoria][] = $musica;
        }

        $this->render('execucao/anterior_show', [
            'culto' => $dados['culto'],
            'participantes' => $dados['participantes'],
            'musicasPorCategoria' => $musicasPorCategoria,
        ]);
    }

    public function limparAnterior(int $id): void
    {
        $execucaoModel = new Execucao();
        $excluiu = $execucaoModel->limparHistorico($id);

        if (!$excluiu) {
            setFlash('warning', 'Culto anterior nao encontrado.');
            redirect('/execucao/anteriores');
        }

        setFlash('success', 'Culto anterior removido da lista.');
        redirect('/execucao/anteriores');
    }
}
