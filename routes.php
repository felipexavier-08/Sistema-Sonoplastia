<?php

declare(strict_types=1);

return [
    ['GET', '/', 'AuthController', 'home'],

    ['GET', '/login', 'AuthController', 'loginForm'],
    ['POST', '/login', 'AuthController', 'login'],
    ['GET', '/logout', 'AuthController', 'logout'],

    ['GET', '/cultos', 'CultoController', 'index', ['diretora']],
    ['GET', '/cultos/criar', 'CultoController', 'create', ['diretora']],
    ['POST', '/cultos/salvar', 'CultoController', 'store', ['diretora']],
    ['POST', '/cultos/{id}/excluir', 'CultoController', 'destroy', ['diretora']],
    ['GET', '/cultos/{id}', 'CultoController', 'show', ['diretora']],
    ['POST', '/cultos/{id}/participantes/salvar', 'CultoController', 'storeParticipante', ['diretora']],
    ['POST', '/cultos/{id}/musicas/salvar', 'MusicaController', 'store', ['diretora']],
    ['POST', '/cultos/{cultoId}/musicas/{id}/excluir', 'MusicaController', 'destroy', ['diretora']],

    ['GET', '/execucao/hoje', 'ExecucaoController', 'hoje', ['sonoplasta']],
    ['POST', '/execucao/musica/{id}/status', 'ExecucaoController', 'salvarStatus', ['sonoplasta']],
    ['POST', '/execucao/culto/{id}/concluir', 'ExecucaoController', 'concluirCulto', ['sonoplasta']],
    ['GET', '/execucao/anteriores', 'ExecucaoController', 'anteriores', ['sonoplasta']],
    ['GET', '/execucao/anteriores/{id}', 'ExecucaoController', 'showAnterior', ['sonoplasta']],
    ['POST', '/execucao/anteriores/{id}/limpar', 'ExecucaoController', 'limparAnterior', ['sonoplasta']],
];
