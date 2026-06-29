<?php

class DashboardController
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

    public function resumo(): void
    {
        // Totais gerais
        $totalPessoas = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM pessoas")
            ->fetchColumn();

        $totalTipos = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM tipos_atendimentos")
            ->fetchColumn();

        $totalAtendimentos = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM atendimentos")
            ->fetchColumn();

        // Últimos 5 atendimentos com joins para exibir nomes
        $stmt = $this->pdo->query(
            "SELECT a.id,
                    p.nome                AS pessoa,
                    t.nome                AS tipo,
                    u.nome                AS responsavel,
                    a.data_atendimento,
                    a.horario_atendimento,
                    a.status
             FROM atendimentos a
             LEFT JOIN pessoas             p ON p.id = a.pessoa_id
             LEFT JOIN tipos_atendimentos  t ON t.id = a.tipo_atendimento_id
             LEFT JOIN usuarios            u ON u.id = a.usuario_id
             ORDER BY a.id DESC
             LIMIT 5"
        );
        $atendimentos_recentes = $stmt->fetchAll();

        $this->json([
            'indicadores' => [
                'total_pessoas'      => $totalPessoas,
                'total_tipos'        => $totalTipos,
                'total_atendimentos' => $totalAtendimentos,
            ],
            'atendimentos_recentes' => $atendimentos_recentes,
        ]);
    }
}