<?php

declare(strict_types=1);

class Musica
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Conexao::getInstancia();
    }

    public function criar(
        int $cultoId,
        string $categoria,
        string $nomeMusica,
        ?string $origem,
        ?string $cantor,
        ?string $linkYoutube,
        int $ordem
    ): void {
        $sql = 'INSERT INTO musicas (culto_id, categoria, nome_musica, origem, cantor, link_youtube, ordem)
                VALUES (:culto_id, :categoria, :nome_musica, :origem, :cantor, :link_youtube, :ordem)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
        $stmt->bindValue(':categoria', $categoria);
        $stmt->bindValue(':nome_musica', $nomeMusica);
        $stmt->bindValue(':origem', $origem);
        $stmt->bindValue(':cantor', $cantor);
        $stmt->bindValue(':link_youtube', $linkYoutube);
        $stmt->bindValue(':ordem', $ordem, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function listarPorCulto(int $cultoId): array
    {
        $sql = 'SELECT id, culto_id, categoria, nome_musica, origem, cantor, link_youtube, ordem
                FROM musicas
                WHERE culto_id = :culto_id
                ORDER BY FIELD(categoria, "regencia_inicio", "louvor_pe", "especial"), ordem ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function listarPorCultoComExecucao(int $cultoId): array
    {
        $sql = 'SELECT m.id, m.culto_id, m.categoria, m.nome_musica, m.origem, m.cantor, m.link_youtube, m.ordem,
                       COALESCE(e.concluido, 0) AS concluido,
                       e.observacao,
                       e.data_execucao
                FROM musicas m
                LEFT JOIN execucao e ON e.id = (
                    SELECT e2.id
                    FROM execucao e2
                    WHERE e2.musica_id = m.id
                    ORDER BY e2.id DESC
                    LIMIT 1
                )
                WHERE m.culto_id = :culto_id
                ORDER BY FIELD(m.categoria, "regencia_inicio", "louvor_pe", "especial"), m.ordem ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = 'SELECT id, culto_id, categoria, nome_musica, origem, cantor, link_youtube, ordem
                FROM musicas
                WHERE id = :id
                LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $musica = $stmt->fetch();
        return $musica ?: null;
    }

    public function excluir(int $id, int $cultoId): bool
    {
        $sql = 'DELETE FROM musicas WHERE id = :id AND culto_id = :culto_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function proximaOrdemPorCultoECategoria(int $cultoId, string $categoria): int
    {
        $sql = 'SELECT COALESCE(MAX(ordem), 0) + 1 AS proxima_ordem
                FROM musicas
                WHERE culto_id = :culto_id AND categoria = :categoria';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
        $stmt->bindValue(':categoria', $categoria);
        $stmt->execute();

        $resultado = $stmt->fetch();
        return (int) ($resultado['proxima_ordem'] ?? 1);
    }

    public function listarIdsPorCulto(int $cultoId): array
    {
        $sql = 'SELECT id FROM musicas WHERE culto_id = :culto_id ORDER BY id ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(static fn(array $linha): int => (int) $linha['id'], $stmt->fetchAll());
    }
}
