<?php

declare(strict_types=1);

class AuthController extends BaseController
{
    public function home(): void
    {
        $usuario = usuarioLogado();
        if (!$usuario) {
            redirect('/login');
        }

        if ($usuario['tipo'] === 'diretora') {
            redirect('/cultos');
        }

        redirect('/execucao/hoje');
    }

    public function loginForm(): void
    {
        if (usuarioLogado()) {
            redirect('/');
        }

        $this->render('auth/login');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $senha = (string) ($_POST['senha'] ?? '');

        if ($email === '' || $senha === '') {
            setFlash('warning', 'Preencha e-mail e senha.');
            redirect('/login');
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorEmail($email);

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            setFlash('danger', 'Credenciais inválidas.');
            redirect('/login');
        }

        $_SESSION['usuario'] = [
            'id' => (int) $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'tipo' => $usuario['tipo'],
        ];

        setFlash('success', 'Login realizado com sucesso.');
        redirect('/');
    }

    public function logout(): void
    {
        unset($_SESSION['usuario']);
        setFlash('success', 'Sessão encerrada.');
        redirect('/login');
    }
}
