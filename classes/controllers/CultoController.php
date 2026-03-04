<?php

declare(strict_types=1);

class CultoController extends BaseController
{
    public function index(): void
    {
        $cultoModel = new Culto();
        $cultoModel->garantirCultosDoMesAtual();
        $cultos = $cultoModel->listarTodos();

        $this->render('culto/index', [
            'cultos' => $cultos,
        ]);
    }

    public function create(): void
    {
        $this->render('culto/create');
    }

    public function store(): void
    {
        $dataCultoInput = trim($_POST['data_culto'] ?? '');
        $tipoCulto = trim($_POST['tipo_culto'] ?? '');

        if ($dataCultoInput === '') {
            setFlash('warning', 'Informe a data do culto.');
            redirect('/cultos/criar');
        }

        $dataCulto = $this->normalizarDataCulto($dataCultoInput);
        if ($dataCulto === null) {
            setFlash('warning', 'Data invalida. Use o formato dd/mm/aaaa.');
            redirect('/cultos/criar');
        }

        if ($tipoCulto === '') {
            $tipoCulto = 'culto normal';
        }

        $cultoModel = new Culto();
        $cultoModel->garantirCultosDoMesAtual();

        if ($cultoModel->existePorData($dataCulto)) {
            setFlash('warning', 'Ja existe um culto cadastrado para esta data.');
            redirect('/cultos/criar');
        }

        $cultoId = $cultoModel->criar($dataCulto, $tipoCulto);

        setFlash('success', 'Culto criado com sucesso.');
        redirect('/cultos/' . $cultoId);
    }

    public function show(int $id): void
    {
        $cultoModel = new Culto();
        $participanteModel = new ParticipanteCulto();
        $musicaModel = new Musica();

        $culto = $cultoModel->buscarPorId($id);
        if (!$culto) {
            http_response_code(404);
            echo 'Culto não encontrado.';
            return;
        }

        $participantes = $participanteModel->listarPorCulto($id);
        $musicas = $musicaModel->listarPorCulto($id);

        $musicasPorCategoria = [
            'regencia_inicio' => [],
            'louvor_pe' => [],
            'especial' => [],
        ];

        foreach ($musicas as $musica) {
            $musicasPorCategoria[$musica['categoria']][] = $musica;
        }

        $this->render('culto/show', [
            'culto' => $culto,
            'participantes' => $participantes,
            'musicasPorCategoria' => $musicasPorCategoria,
        ]);
    }

    public function storeParticipante(int $id): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $funcao = trim($_POST['funcao'] ?? '');

        if ($nome === '' || !in_array($funcao, ['regente', 'especial'], true)) {
            setFlash('warning', 'Preencha os dados do participante corretamente.');
            redirect('/cultos/' . $id);
        }

        $cultoModel = new Culto();
        if (!$cultoModel->buscarPorId($id)) {
            http_response_code(404);
            echo 'Culto não encontrado.';
            return;
        }

        $participanteModel = new ParticipanteCulto();
        $participanteModel->criar($id, $nome, $funcao);

        setFlash('success', 'Participante cadastrado.');
        redirect('/cultos/' . $id);
    }

    public function destroy(int $id): void
    {
        $cultoModel = new Culto();
        $culto = $cultoModel->buscarPorId($id);

        if (!$culto) {
            setFlash('warning', 'Culto não encontrado.');
            redirect('/cultos');
        }

        if ($this->ehCultoAutomaticoMesAtual($culto)) {
            $cultoModel->limparPlanejamento($id);
            setFlash('success', 'Culto normal limpo com sucesso. A data foi mantida na agenda.');
            redirect('/cultos');
        }

        $cultoModel->excluir($id);
        setFlash('success', 'Culto excluido com sucesso.');
        redirect('/cultos');
    }

    private function normalizarDataCulto(string $data): ?string
    {
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data) === 1) {
            $date = DateTime::createFromFormat('d/m/Y', $data);
            if ($date && $date->format('d/m/Y') === $data) {
                return $date->format('Y-m-d');
            }

            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) === 1) {
            $date = DateTime::createFromFormat('Y-m-d', $data);
            if ($date && $date->format('Y-m-d') === $data) {
                return $data;
            }
        }

        return null;
    }

    private function ehCultoAutomaticoMesAtual(array $culto): bool
    {
        $tipoCulto = strtolower(trim((string) ($culto['tipo_culto'] ?? '')));
        if ($tipoCulto !== 'culto normal') {
            return false;
        }

        $dataCulto = (string) ($culto['data_culto'] ?? '');
        $data = DateTimeImmutable::createFromFormat('Y-m-d', $dataCulto);
        if (!$data || $data->format('Y-m-d') !== $dataCulto) {
            return false;
        }

        $mesAtual = (new DateTimeImmutable('today'))->format('Y-m');
        $diaSemana = (int) $data->format('w');

        return $data->format('Y-m') === $mesAtual && in_array($diaSemana, [0, 3, 6], true);
    }
}
