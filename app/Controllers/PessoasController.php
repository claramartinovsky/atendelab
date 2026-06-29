<?php

class PessoasController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // Método auxiliar para padronizar respostas JSON.
    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    public function listar(): void
    {
        $sql = "SELECT id, nome, documento, telefone, email,
                    status, criado_em, atualizado_em
                FROM pessoas
                ORDER BY nome";

        $pessoas = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $this->json($pessoas);
    }

    public function buscar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, nome, documento, telefone, email,
                    status, criado_em, atualizado_em
             FROM pessoas
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            $this->json(['erro' => 'Pessoa não encontrada.'], 404);
            return;
        }

        $this->json($pessoa);
    }

    public function criar(): void
    {
        $nome        = trim($_POST['nome']        ?? '');
        $documento   = trim($_POST['documento']   ?? '');
        $telefone    = trim($_POST['telefone']    ?? '');
        $email       = trim($_POST['email']       ?? '');
        $status      = $_POST['status']           ?? 'ativo';
        $criado_em      = trim($_POST['criado_em']        ?? '');
        $atualizado_em  = trim($_POST['atualizado_em']    ?? '');

        // Validações obrigatórias.
        if ($nome === '' || $documento === '' || $email === '') {
            $this->json(['erro' => 'Nome, documento e e-mail são obrigatórios.'], 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['erro' => 'E-mail inválido.'], 422);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO pessoas
                 (nome, documento, telefone, email,
                    status, criado_em, atualizado_em)
                 VALUES
                 (:nome, :documento, :telefone, :email,
                  :status, :criado_em, :atualizado_em)'
            );

            $stmt->execute(compact(
                'nome', 'documento', 'telefone', 'email', 'status', 
                'criado_em', 'atualizado_em'
            ));

            $this->json(['mensagem' => 'Pessoa cadastrada com sucesso.'], 201);

        } catch (PDOException $e) {
            $this->json(['erro' => 'Não foi possível cadastrar a pessoa.'], 400);
        }
    }

    public function atualizar(): void
    {
        $id          = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $nome        = trim($_POST['nome']        ?? '');
        $documento   = trim($_POST['documento']   ?? '');
        $telefone    = trim($_POST['telefone']    ?? '');
        $email       = trim($_POST['email']       ?? '');
        $status      = $_POST['status']           ?? 'ativo';
        $criado_em      = trim($_POST['criado_em']        ?? '');
        $atualizado_em  = trim($_POST['atualizado_em']    ?? '');


        // Validações obrigatórias.
        if (!$id || $nome === '' || $documento === '' || $email === '') {
            $this->json(['erro' => 'Dados obrigatórios ausentes.'], 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['erro' => 'E-mail inválido.'], 422);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE pessoas
                 SET nome = :nome,
                     documento = :documento,
                     telefone = :telefone,
                     email = :email,
                     status = :status,
                     criado_em = :criado_em,
                     atualizado_em = :atualizado_em
                 WHERE id = :id'
            );

            $stmt->execute(compact(
                'id', 'nome', 'documento', 'telefone', 'email',
                 'status', 'criado_em', 'atualizado_em'
            ));

            $this->json(['mensagem' => 'Pessoa atualizada com sucesso.']);

        } catch (PDOException $e) {
            $this->json(['erro' => 'Não foi possível atualizar a pessoa.'], 400);
        }
    }

    public function inativar(): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE pessoas SET status = 'inativo' WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        $this->json(['mensagem' => 'Pessoa inativada com sucesso.']);
    }
}