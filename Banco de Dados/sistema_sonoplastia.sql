-- Criar banco
CREATE DATABASE sistema_sonoplastia;
USE sistema_sonoplastia;

-- ==========================
-- USUÁRIOS (login)
-- ==========================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('diretora', 'sonoplasta') NOT NULL
);

-- ==========================
-- CULTOS (cada dia)
-- ==========================
CREATE TABLE cultos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_culto DATE NOT NULL,
    tipo_culto VARCHAR(100),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- PESSOAS QUE IRÃO LOUVAR NO DIA
-- ==========================
CREATE TABLE participantes_culto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    culto_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    funcao ENUM('regente', 'especial') NOT NULL,
    
    FOREIGN KEY (culto_id) REFERENCES cultos(id) ON DELETE CASCADE
);

-- ==========================
-- MÚSICAS DO CULTO
-- ==========================
CREATE TABLE musicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    culto_id INT NOT NULL,
    
    categoria ENUM('regencia_inicio', 'louvor_pe', 'especial') NOT NULL,
    
    nome_musica VARCHAR(150) NOT NULL,
    
    -- Origem só para regência
    origem ENUM('hinario_novo', 'hinario_antigo', 'cd_jovem', 'adoradores_5', 'adoradores_3', 'adoradores_2') DEFAULT NULL,
    
    -- Para especial
    cantor VARCHAR(150) DEFAULT NULL,
    link_youtube TEXT DEFAULT NULL,
    
    ordem INT NOT NULL,
    
    FOREIGN KEY (culto_id) REFERENCES cultos(id) ON DELETE CASCADE
);

-- ==========================
-- EXECUÇÃO PELO SONOPLASTA
-- ==========================
CREATE TABLE execucao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    musica_id INT NOT NULL,
    concluido BOOLEAN DEFAULT FALSE,
    observacao TEXT,
    data_execucao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (musica_id) REFERENCES musicas(id) ON DELETE CASCADE
);

-- ==========================
-- HISTÓRICO DE CULTOS CONCLUÍDOS (SONOPLASTIA)
-- ==========================
CREATE TABLE cultos_concluidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    culto_id INT NOT NULL UNIQUE,
    data_culto DATE NOT NULL,
    tipo_culto VARCHAR(100),
    concluido_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cultos_concluidos_participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    culto_concluido_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    funcao VARCHAR(20) NOT NULL,

    FOREIGN KEY (culto_concluido_id) REFERENCES cultos_concluidos(id) ON DELETE CASCADE
);

CREATE TABLE cultos_concluidos_musicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    culto_concluido_id INT NOT NULL,
    categoria VARCHAR(30) NOT NULL,
    nome_musica VARCHAR(150) NOT NULL,
    origem VARCHAR(50) DEFAULT NULL,
    cantor VARCHAR(150) DEFAULT NULL,
    link_youtube TEXT DEFAULT NULL,
    ordem INT NOT NULL,
    concluido BOOLEAN DEFAULT FALSE,
    observacao TEXT DEFAULT NULL,

    FOREIGN KEY (culto_concluido_id) REFERENCES cultos_concluidos(id) ON DELETE CASCADE
);

-- ==========================
-- USUÁRIOS INICIAIS (SEED)
-- senha padrão de ambos: password
-- hash bcrypt compatível com password_verify do PHP
-- ==========================
INSERT INTO usuarios (nome, email, senha, tipo) VALUES
('Diretora de Musica', 'diretora@igreja.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'diretora'),
('Sonoplasta Oficial', 'sonoplasta@igreja.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sonoplasta');

-- ==========================
-- MIGRAÇÃO PARA BASE JÁ EXISTENTE
-- Execute apenas se a tabela musicas já estiver criada
-- ==========================
ALTER TABLE musicas
MODIFY COLUMN origem ENUM('hinario_novo', 'hinario_antigo', 'cd_jovem', 'adoradores_5', 'adoradores_3', 'adoradores_2') DEFAULT NULL;
