<?php

class AuthController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function exibirLogin(): void
    {
        $baseUrl   = '/atendelab/public/';
        $erroLogin = $_SESSION['erro_login'] ?? null;
        $mensagem  = $_SESSION['mensagem']   ?? null;

        unset($_SESSION['erro_login'], $_SESSION['mensagem']);

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function entrar(): void
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha']      ?? '';

        if ($email === '' || $senha === '') {
            $_SESSION['erro_login'] = 'Preencha e-mail e senha.';
            header('Location: /atendelab/public/?controller=auth&action=login');
            exit;
        }

        $stmt = $this->pdo->prepare(
            "SELECT id, nome, email, senha, perfil, status
             FROM usuarios
             WHERE email = :email
               AND status = 'ativo'
             LIMIT 1"
        );
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {

            $_SESSION['erro_login'] = 'E-mail ou senha inválidos.';     
            header('Location: /atendelab/public/?controller=auth&action=login');
            exit;
        }

        // Salva dados na sessão sem a senha.
        $_SESSION['usuario'] = [
            'id'     => $usuario['id'],
            'nome'   => $usuario['nome'],
            'email'  => $usuario['email'],
            'perfil' => $usuario['perfil'],
        ];

        header('Location: /atendelab/public/?controller=auth&action=dashboard');
        exit;
    }

    public function dashboard(): void
    {
        $baseUrl  = '/atendelab/public/';
        $usuario  = $_SESSION['usuario'] ?? [];

        require __DIR__ . '/../Views/dashboard/index.php';
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /atendelab/public/?controller=auth&action=login');
        exit;
    }
}