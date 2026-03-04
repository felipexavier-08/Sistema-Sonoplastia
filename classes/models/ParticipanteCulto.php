<?php

declare(strict_types=1);

class ParticipanteCulto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Conexao::getInstancia();
    }

    public function listarPorCulto(int $cultoId): array
    {
        $sql = 'SELECT id, culto_id, nome, funcao FROM participantes_culto WHERE culto_id = :culto_id ORDER BY id ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function criar(int $cultoId, string $nome, string $funcao): void
    {
        $sql = 'INSERT INTO participantes_culto (culto_id, nome, funcao) VALUES (:culto_id, :nome, :funcao)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':culto_id', $cultoId, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':funcao', $funcao);
        $stmt->execute();
    }
}
