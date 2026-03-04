<?php

declare(strict_types=1);

class Culto
{
    private PDO $db;
    private const DIAS_PADRAO_CULTO = [0, 3, 6]; // domingo, quarta, sabado
    private ?bool $tabelaHistoricoExiste = null;

    public function __construct()
    {
        $this->db = Conexao::getInstancia();
    }

    public function listarTodos(): array
    {
        $statusConcluidoSql = $this->tabelaExiste('cultos_concluidos')
            ? '(SELECT COUNT(*) FROM cultos_concluidos cc WHERE cc.culto_id = c.id) AS total_concluido'
            : '0 AS total_concluido';

        $sql = "SELECT c.id, c.data_culto, c.tipo_culto, c.criado_em,
                       (SELECT COUNT(*) FROM participantes_culto pc WHERE pc.culto_id = c.id) AS total_participantes,
                       (SELECT COUNT(*) FROM participantes_culto pc WHERE pc.culto_id = c.id AND pc.funcao = 'regente') AS total_regentes,
                       (SELECT COUNT(*) FROM musicas m WHERE m.culto_id = c.id) AS total_musicas,
                       {$statusConcluidoSql}
                FROM cultos c
                ORDER BY c.data_culto ASC, c.id ASC";
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    private function tabelaExiste(string $nomeTabela): bool
    {
        if ($nomeTabela === 'cultos_concluidos' && $this->tabelaHistoricoExiste !== null) {
            return $this->tabelaHistoricoExiste;
        }

        $sql = 'SELECT COUNT(*)
                FROM information_schema.tables
                WHERE table_schema = DATABASE()
                  AND table_name = :table_name';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':table_name', $nomeTabela);
        $stmt->execute();

        $existe = ((int) $stmt->fetchColumn()) > 0;
        if ($nomeTabela === 'cultos_concluidos') {
            $this->tabelaHistoricoExiste = $existe;
        }

        return $existe;
    }

    public function criar(string $dataCulto, string $tipoCulto): int
    {
        $sql = 'INSERT INTO cultos (data_culto, tipo_culto) VALUES (:data_culto, :tipo_culto)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':data_culto', $dataCulto);
        $stmt->bindValue(':tipo_culto', $tipoCulto !== '' ? $tipoCulto : 'culto normal');
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    public function existePorData(string $dataCulto): bool
    {
        $sql = 'SELECT id FROM cultos WHERE data_culto = :data_culto LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':data_culto', $dataCulto);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    public function garantirCultosDoMesAtual(): void
    {
        $inicioMes = new DateTimeImmutable('first day of this month');
        $fimMes = new DateTimeImmutable('last day of this month');

        for ($dia = $inicioMes; $dia <= $fimMes; $dia = $dia->modify('+1 day')) {
            $diaSemana = (int) $dia->format('w');
            if (!in_array($diaSemana, self::DIAS_PADRAO_CULTO, true)) {
                continue;
            }

            $dataCulto = $dia->format('Y-m-d');
            if ($this->existePorData($dataCulto)) {
                continue;
            }

            $this->criar($dataCulto, 'culto normal');
        }
    }

    public function excluir(int $id): bool
    {
        $sql = 'DELETE FROM cultos WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function limparPlanejamento(int $id): void
    {
        $this->db->beginTransaction();

        try {
            $sqlParticipantes = 'DELETE FROM participantes_culto WHERE culto_id = :culto_id';
            $stmtParticipantes = $this->db->prepare($sqlParticipantes);
            $stmtParticipantes->bindValue(':culto_id', $id, PDO::PARAM_INT);
            $stmtParticipantes->execute();

            // Ao remover as musicas, a tabela execucao e limpa via ON DELETE CASCADE.
            $sqlMusicas = 'DELETE FROM musicas WHERE culto_id = :culto_id';
            $stmtMusicas = $this->db->prepare($sqlMusicas);
            $stmtMusicas->bindValue(':culto_id', $id, PDO::PARAM_INT);
            $stmtMusicas->execute();

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = 'SELECT id, data_culto, tipo_culto, criado_em FROM cultos WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $culto = $stmt->fetch();
        return $culto ?: null;
    }

    public function buscarCultoDoDia(string $data): ?array
    {
        $sql = 'SELECT id, data_culto, tipo_culto, criado_em FROM cultos WHERE data_culto = :data ORDER BY id DESC LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':data', $data);
        $stmt->execute();

        $culto = $stmt->fetch();
        return $culto ?: null;
    }
}
