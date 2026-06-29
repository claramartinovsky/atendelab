<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    // Retorna o ID do usuário logado na sessão.
    private function usuarioResponsavel(): int
    {
        if (isset($_SESSION['usuario']['id'])) {
            return (int) $_SESSION['usuario']['id'];
        }

        http_response_code(401);
        echo json_encode(['erro' => 'Usuário não autenticado.']);
        exit;
    }

    public function listar(): void
    {
        $stmt = $this->pdo->query(
            "SELECT
                a.id,
                p.nome AS pessoa,
                t.nome AS tipo,
                u.nome AS responsavel,
                a.data_atendimento,
                a.horario_atendimento,
                a.descricao,
                a.status
            FROM atendimentos a
            LEFT JOIN pessoas p
                ON p.id = a.pessoa_id
            LEFT JOIN tipos_atendimentos t
                ON t.id = a.tipo_atendimento_id
            LEFT JOIN usuarios u
                ON u.id = a.usuario_id
            ORDER BY a.id DESC"
        );

        $this->json($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function visualizar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            "SELECT
                a.id,
                a.pessoa_id,
                a.tipo_atendimento_id,
                a.usuario_id,
                p.nome AS pessoa,
                t.nome AS tipo,
                u.nome AS responsavel,
                a.data_atendimento,
                a.horario_atendimento,
                a.descricao,
                a.status
            FROM atendimentos a
            LEFT JOIN pessoas p
                ON p.id = a.pessoa_id
            LEFT JOIN tipos_atendimentos t
                ON t.id = a.tipo_atendimento_id
            LEFT JOIN usuarios u
                ON u.id = a.usuario_id
            WHERE a.id = :id"
        );

        $stmt->execute(['id' => $id]);

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            $this->json(['erro' => 'Atendimento não encontrado.'], 404);
            return;
        }

        $this->json($atendimento);
    }

    public function criar(): void
    {
        $pessoaId             = filter_var($_POST['pessoa_id']             ?? null, FILTER_VALIDATE_INT);
        $tipoAtendimentoId    = filter_var($_POST['tipo_atendimento_id']   ?? null, FILTER_VALIDATE_INT);
        $descricao            = trim($_POST['descricao']                   ?? '');
        $dataAtendimento      = trim($_POST['data_atendimento']            ?? '');
        $horarioAtendimento   = trim($_POST['horario_atendimento']         ?? '');

        // Responsável sempre vem da sessão.
        $usuarioId = $this->usuarioResponsavel();

        if (!$pessoaId || !$tipoAtendimentoId || $descricao === '' || $dataAtendimento === '') {
            $this->json(['erro' => 'Pessoa, tipo, data e descrição são obrigatórios.'], 422);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO atendimentos
                 (pessoa_id, tipo_atendimento_id, usuario_id,
                  descricao, data_atendimento, horario_atendimento, status)
                 VALUES
                 (:pessoa_id, :tipo_atendimento_id, :usuario_id,
                  :descricao, :data_atendimento, :horario_atendimento, 'aberto')"
            );

            $stmt->execute([
                'pessoa_id'           => $pessoaId,
                'tipo_atendimento_id' => $tipoAtendimentoId,
                'usuario_id'          => $usuarioId,
                'descricao'           => $descricao,
                'data_atendimento'    => $dataAtendimento,
                'horario_atendimento' => $horarioAtendimento,
            ]);

            $this->json(['mensagem' => 'Atendimento registrado com sucesso.'], 201);

        } catch (PDOException $e) {
            $this->json(['erro' => 'Não foi possível registrar o atendimento.'], 400);
        }
    }

    public function alterarStatus(): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';

        if (!$id || $status === '') {
            $this->json(['erro' => 'ID e status são obrigatórios.'], 422);
            return;
        }

        if (!in_array($status, ['aberto', 'encerrado'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        try {

            $stmt = $this->pdo->prepare(
                "UPDATE atendimentos
                SET status = :status
                WHERE id = :id"
            );

            $stmt->execute([
                'status' => $status,
                'id' => $id
            ]);

            $this->json([
                'mensagem' => 'Status atualizado com sucesso.'
            ]);

        } catch (PDOException $e) {
            $this->json([
                'erro' => 'Não foi possível atualizar o status.'
            ], 400);
        }
    }

    // Retorna combos de pessoas e tipos ativos para o formulário.
    public function opcoesFormulario(): void
    {
        $pessoas = $this->pdo
            ->query("SELECT id, nome FROM pessoas WHERE status = 'ativo' ORDER BY nome")
            ->fetchAll(PDO::FETCH_ASSOC);

        $tipos = $this->pdo
            ->query("SELECT id, nome FROM tipos_atendimentos WHERE status = 'ativo' ORDER BY nome")
            ->fetchAll(PDO::FETCH_ASSOC);

        $this->json(compact('pessoas', 'tipos'));
    }
}