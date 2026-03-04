<?php

declare(strict_types=1);

class Execucao
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Conexao::getInstancia();
    }

    private function garantirTabelasHistorico(): void
    {
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS cultos_concluidos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                culto_id INT NOT NULL,
                data_culto DATE NOT NULL,
                tipo_culto VARCHAR(100) DEFAULT NULL,
                concluido_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_cultos_concluidos_culto_id (culto_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS cultos_concluidos_participantes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                culto_concluido_id INT NOT NULL,
                nome VARCHAR(100) NOT NULL,
                funcao VARCHAR(20) NOT NULL,
                CONSTRAINT fk_cultos_concluidos_participantes
                    FOREIGN KEY (culto_concluido_id) REFERENCES cultos_concluidos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS cultos_concluidos_musicas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                culto_concluido_id INT NOT NULL,
                categoria VARCHAR(30) NOT NULL,
                nome_musica VARCHAR(150) NOT NULL,
                origem VARCHAR(50) DEFAULT NULL,
                cantor VARCHAR(150) DEFAULT NULL,
                link_youtube TEXT DEFAULT NULL,
                ordem INT NOT NULL,
                concluido TINYINT(1) NOT NULL DEFAULT 0,
                observacao TEXT DEFAULT NULL,
                CONSTRAINT fk_cultos_concluidos_musicas
                    FOREIGN KEY (culto_concluido_id) REFERENCES cultos_concluidos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function buscarUltimaPorMusica(int $musicaId): ?array
    {
        $sql = 'SELECT id, musica_id, concluido, observacao, data_execucao
                FROM execucao
                WHERE musica_id = :musica_id
                ORDER BY id DESC
                LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':musica_id', $musicaId, PDO::PARAM_INT);
        $stmt->execute();

        $execucao = $stmt->fetch();
        return $execucao ?: null;
    }

    public function salvarStatus(int $musicaId, bool $concluido, ?string $observacao): void
    {
        $ultima = $this->buscarUltimaPorMusica($musicaId);

        if ($ultima) {
            $sql = 'UPDATE execucao
                    SET concluido = :concluido, observacao = :observacao, data_execucao = CURRENT_TIMESTAMP
                    WHERE id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':concluido', $concluido ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':observacao', $observacao !== '' ? $observacao : null);
            $stmt->bindValue(':id', (int) $ultima['id'], PDO::PARAM_INT);
            $stmt->execute();
            return;
        }

        $sql = 'INSERT INTO execucao (musica_id, concluido, observacao) VALUES (:musica_id, :concluido, :observacao)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':musica_id', $musicaId, PDO::PARAM_INT);
        $stmt->bindValue(':concluido', $concluido ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':observacao', $observacao !== '' ? $observacao : null);
        $stmt->execute();
    }

    public function buscarHistoricoPorCultoId(int $cultoId): ?array
    {
        $this->garantirTabelasHistorico();

        $sql = 'SELECT id, culto_id, data_culto, tipo_culto, concluido_em
                FROM cultos_concluidos
                WHERE culto_id = :culto_id
                LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
        $stmt->execute();

        $item = $stmt->fetch();
        return $item ?: null;
    }

    public function salvarHistoricoCulto(array $culto, array $participantes, array $musicas): int
    {
        $this->garantirTabelasHistorico();

        $cultoId = (int) ($culto['id'] ?? 0);
        $existente = $this->buscarHistoricoPorCultoId($cultoId);
        if ($existente) {
            return (int) $existente['id'];
        }

        $this->db->beginTransaction();

        try {
            $sqlCulto = 'INSERT INTO cultos_concluidos (culto_id, data_culto, tipo_culto)
                         VALUES (:culto_id, :data_culto, :tipo_culto)';
            $stmtCulto = $this->db->prepare($sqlCulto);
            $stmtCulto->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
            $stmtCulto->bindValue(':data_culto', (string) ($culto['data_culto'] ?? ''));
            $stmtCulto->bindValue(':tipo_culto', (string) ($culto['tipo_culto'] ?? 'culto normal'));
            $stmtCulto->execute();

            $historicoId = (int) $this->db->lastInsertId();

            if (!empty($participantes)) {
                $sqlParticipante = 'INSERT INTO cultos_concluidos_participantes (culto_concluido_id, nome, funcao)
                                    VALUES (:culto_concluido_id, :nome, :funcao)';
                $stmtParticipante = $this->db->prepare($sqlParticipante);

                foreach ($participantes as $participante) {
                    $stmtParticipante->bindValue(':culto_concluido_id', $historicoId, PDO::PARAM_INT);
                    $stmtParticipante->bindValue(':nome', (string) ($participante['nome'] ?? ''));
                    $stmtParticipante->bindValue(':funcao', (string) ($participante['funcao'] ?? ''));
                    $stmtParticipante->execute();
                }
            }

            if (!empty($musicas)) {
                $sqlMusica = 'INSERT INTO cultos_concluidos_musicas
                            (culto_concluido_id, categoria, nome_musica, origem, cantor, link_youtube, ordem, concluido, observacao)
                            VALUES
                            (:culto_concluido_id, :categoria, :nome_musica, :origem, :cantor, :link_youtube, :ordem, :concluido, :observacao)';
                $stmtMusica = $this->db->prepare($sqlMusica);

                foreach ($musicas as $musica) {
                    $stmtMusica->bindValue(':culto_concluido_id', $historicoId, PDO::PARAM_INT);
                    $stmtMusica->bindValue(':categoria', (string) ($musica['categoria'] ?? ''));
                    $stmtMusica->bindValue(':nome_musica', (string) ($musica['nome_musica'] ?? ''));
                    $stmtMusica->bindValue(':origem', ($musica['origem'] ?? null) !== '' ? $musica['origem'] : null);
                    $stmtMusica->bindValue(':cantor', ($musica['cantor'] ?? null) !== '' ? $musica['cantor'] : null);
                    $stmtMusica->bindValue(':link_youtube', ($musica['link_youtube'] ?? null) !== '' ? $musica['link_youtube'] : null);
                    $stmtMusica->bindValue(':ordem', (int) ($musica['ordem'] ?? 0), PDO::PARAM_INT);
                    $stmtMusica->bindValue(':concluido', (int) ($musica['concluido'] ?? 0), PDO::PARAM_INT);
                    $stmtMusica->bindValue(':observacao', ($musica['observacao'] ?? null) !== '' ? $musica['observacao'] : null);
                    $stmtMusica->execute();
                }
            }

            $this->db->commit();
            return $historicoId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function listarHistoricoCultos(): array
    {
        $this->garantirTabelasHistorico();

        $sql = 'SELECT c.id, c.culto_id, c.data_culto, c.tipo_culto, c.concluido_em,
                       (SELECT COUNT(*) FROM cultos_concluidos_participantes p WHERE p.culto_concluido_id = c.id) AS total_participantes,
                       (SELECT COUNT(*) FROM cultos_concluidos_participantes p WHERE p.culto_concluido_id = c.id AND p.funcao = "regente") AS total_regentes,
                       (SELECT COUNT(*) FROM cultos_concluidos_musicas m WHERE m.culto_concluido_id = c.id) AS total_musicas
                FROM cultos_concluidos c
                ORDER BY c.data_culto DESC, c.id DESC';
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function buscarHistoricoCompleto(int $id): ?array
    {
        $this->garantirTabelasHistorico();

        $sqlCulto = 'SELECT id, culto_id, data_culto, tipo_culto, concluido_em
                     FROM cultos_concluidos
                     WHERE id = :id
                     LIMIT 1';
        $stmtCulto = $this->db->prepare($sqlCulto);
        $stmtCulto->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtCulto->execute();

        $culto = $stmtCulto->fetch();
        if (!$culto) {
            return null;
        }

        $sqlParticipantes = 'SELECT nome, funcao
                             FROM cultos_concluidos_participantes
                             WHERE culto_concluido_id = :culto_concluido_id
                             ORDER BY id ASC';
        $stmtParticipantes = $this->db->prepare($sqlParticipantes);
        $stmtParticipantes->bindValue(':culto_concluido_id', $id, PDO::PARAM_INT);
        $stmtParticipantes->execute();
        $participantes = $stmtParticipantes->fetchAll();

        $sqlMusicas = 'SELECT categoria, nome_musica, origem, cantor, link_youtube, ordem, concluido, observacao
                       FROM cultos_concluidos_musicas
                       WHERE culto_concluido_id = :culto_concluido_id
                       ORDER BY categoria ASC, ordem ASC, id ASC';
        $stmtMusicas = $this->db->prepare($sqlMusicas);
        $stmtMusicas->bindValue(':culto_concluido_id', $id, PDO::PARAM_INT);
        $stmtMusicas->execute();
        $musicas = $stmtMusicas->fetchAll();

        return [
            'culto' => $culto,
            'participantes' => $participantes,
            'musicas' => $musicas,
        ];
    }

    public function limparHistorico(int $id): bool
    {
        $this->garantirTabelasHistorico();

        $sql = 'DELETE FROM cultos_concluidos WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
