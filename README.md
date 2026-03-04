# Sistema de gerenciamento para Sonoplastia e ministério de música 

Aplicação web em PHP para planejamento e execução de cultos, com dois perfis:

- `diretora de musica`: organiza cultos, participantes e músicas.
- `sonoplasta`: acompanha a execução e finaliza o culto.


Vídeo mostrando o sistema funcionando:

[![Vídeo do Sistema](https://img.youtube.com/vi/fqvPqp8-0Eg/0.jpg)](https://youtu.be/fqvPqp8-0Eg)


## Funcionalidades

### Diretora de Música
- Login no sistema.
- Listagem de cultos.
- Geração automática de cultos do mês atual (domingo, quarta e sábado).
- Criação manual de culto extra.
- Cadastro de participantes por culto.
- Cadastro e remoção de músicas por categoria.
- Exclusão/limpeza de culto conforme regras de negócio.

### Sonoplasta
- Tela de execução do culto do dia.
- Marcação de músicas como concluídas.
- Observações por música.
- Barra de progresso da execução.
- Conclusão do culto apenas com 100% das músicas concluídas.
- Histórico de cultos concluídos.
- Visualização detalhada e limpeza de itens do histórico.

## Stack técnica

- PHP 8+
- MySQL / MariaDB
- PDO
- Bootstrap 5 (CDN)
- Arquitetura MVC simples (Controllers, Models, Views)

## Estrutura do projeto

```text
.
├── index.php
├── routes.php
├── .htaccess
├── classes
│   ├── controllers
│   ├── models
│   └── views
├── utils
│   ├── Conexao.php
│   └── helpers.php
└── Banco de Dados
    └── sistema_sonoplastia.sql
```

## Configuração do ambiente

### 1) Banco de dados

Importar:

- `Banco de Dados/sistema_sonoplastia.sql`

O script cria:
- tabelas do sistema
- usuários iniciais

### 2) Conexão

Arquivo:

- `utils/Conexao.php`

Configuração padrão atual:

- host: `127.0.0.1`
- database: `sistema_sonoplastia`
- user: `root`
- password: *(vazia)*
- charset: `utf8mb4`

Ajustar conforme o servidor interno.

### 3) Servidor web

- Apache com `mod_rewrite` habilitado.
- `.htaccess` já direciona as rotas para `index.php`.

## Execução local

1. Colocar o projeto em diretório servido pelo Apache (ex.: `htdocs`).
2. Iniciar Apache e MySQL.
3. Importar o SQL em `Banco de Dados/sistema_sonoplastia.sql`.
4. Acessar a URL local do projeto.

## Usuários iniciais (ambiente de desenvolvimento)

Senha padrão dos seeds: `password`

- Diretora: `diretora@igreja.com`
- Sonoplasta: `sonoplasta@igreja.com`

> Recomenda-se alterar senhas no primeiro uso em ambiente real.

## Rotas principais

### Públicas
- `GET /`
- `GET /login`
- `POST /login`
- `GET /logout`

### Diretora
- `GET /cultos`
- `GET /cultos/criar`
- `POST /cultos/salvar`
- `POST /cultos/{id}/excluir`
- `GET /cultos/{id}`
- `POST /cultos/{id}/participantes/salvar`
- `POST /cultos/{id}/musicas/salvar`
- `POST /cultos/{cultoId}/musicas/{id}/excluir`

### Sonoplasta
- `GET /execucao/hoje`
- `POST /execucao/musica/{id}/status`
- `POST /execucao/culto/{id}/concluir`
- `GET /execucao/anteriores`
- `GET /execucao/anteriores/{id}`
- `POST /execucao/anteriores/{id}/limpar`

## Regras de negócio

- Categorias internas de música:
  - `regencia_inicio`
  - `hino_inicial`
  - `louvor_especial`
- Rótulos de frontend podem ser alterados sem trocar os valores internos do banco.
- Em `louvor_especial`, cantor é obrigatório.
- Culto só pode ser concluído com progresso de execução em 100%.
- Após conclusão, a execução fica bloqueada para edição.

## Segurança

- Sessão PHP para autenticação.
- Validação de senha com `password_verify`.
- Controle por perfil via `exigirPerfil()`.
- Mensagens de feedback via flash (`setFlash/getFlash`).
