<?php

declare(strict_types=1);

class MusicaController extends BaseController
{
    public function store(int $id): void
    {
        $categoria = trim($_POST['categoria'] ?? '');
        $nomeMusica = trim($_POST['nome_musica'] ?? '');
        $origem = trim($_POST['origem'] ?? '');
        $cantor = trim($_POST['cantor'] ?? '');
        $linkYoutube = trim($_POST['link_youtube'] ?? '');
        $ordem = (int) ($_POST['ordem'] ?? 0);

        $categoriasValidas = ['regencia_inicio', 'louvor_pe', 'especial'];
        if (!in_array($categoria, $categoriasValidas, true) || $nomeMusica === '') {
            setFlash('warning', 'Categoria e nome da música são obrigatórios.');
            redirect('/cultos/' . $id);
        }

        if ($categoria !== 'especial') {
            $cantor = '';
            $linkYoutube = '';
        }

        if ($categoria === 'especial' && $cantor === '') {
            setFlash('warning', 'Informe o cantor do louvor especial.');
            redirect('/cultos/' . $id);
        }

        if ($categoria === 'especial') {
            $origem = '';
        }

        $origensValidas = [
            'hinario_novo',
            'hinario_antigo',
            'cd_jovem',
            'adoradores_5',
            'adoradores_3',
            'adoradores_2',
        ];
        if ($categoria !== 'especial' && !in_array($origem, $origensValidas, true)) {
            setFlash('warning', 'Origem inválida para a música.');
            redirect('/cultos/' . $id);
        }

        $cultoModel = new Culto();
        if (!$cultoModel->buscarPorId($id)) {
            http_response_code(404);
            echo 'Culto não encontrado.';
            return;
        }

        $musicaModel = new Musica();
        if ($ordem <= 0) {
            $ordem = $musicaModel->proximaOrdemPorCultoECategoria($id, $categoria);
        }

        $musicaModel->criar(
            $id,
            $categoria,
            $nomeMusica,
            $origem !== '' ? $origem : null,
            $cantor !== '' ? $cantor : null,
            $linkYoutube !== '' ? $linkYoutube : null,
            $ordem
        );

        setFlash('success', 'Música cadastrada com sucesso.');
        redirect('/cultos/' . $id);
    }

    public function destroy(int $cultoId, int $id): void
    {
        $cultoModel = new Culto();
        if (!$cultoModel->buscarPorId($cultoId)) {
            http_response_code(404);
            echo 'Culto não encontrado.';
            return;
        }

        $musicaModel = new Musica();
        $excluiu = $musicaModel->excluir($id, $cultoId);

        if (!$excluiu) {
            setFlash('warning', 'Música não encontrada para este culto.');
            redirect('/cultos/' . $cultoId);
        }

        setFlash('success', 'Música excluída com sucesso.');
        redirect('/cultos/' . $cultoId);
    }
}
