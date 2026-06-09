<?php

namespace App\Repositories;

use App\Models\Caixa;
use App\Models\CaixaOperacao;
use PDO;

class CaixaRepository
{
    private static bool $schemaChecked = false;

    public function __construct(private PDO $pdo)
    {
        $this->ensureSchema();
    }

    public function findAbertoByUsuario(int $usuarioId): ?Caixa
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.*, u.nome AS usuario_nome
             FROM caixas c
             JOIN usuarios u ON u.id = c.usuario_id
             WHERE c.usuario_id = ? AND c.status = 'aberto'
             ORDER BY c.aberto_em DESC
             LIMIT 1"
        );
        $stmt->execute([$usuarioId]);
        $row = $stmt->fetch();
        return $row ? Caixa::fromArray($row) : null;
    }

    public function findById(int $id): ?Caixa
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.*, u.nome AS usuario_nome
             FROM caixas c
             JOIN usuarios u ON u.id = c.usuario_id
             WHERE c.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? Caixa::fromArray($row) : null;
    }

    public function abrir(int $usuarioId, float $fundoInicial, string $observacao): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO caixas (usuario_id, fundo_inicial, observacao_abertura, status)
             VALUES (?,?,?,'aberto')"
        );
        $stmt->execute([$usuarioId, $fundoInicial, $observacao]);
        return (int) $this->pdo->lastInsertId();
    }

    public function registrarOperacao(int $caixaId, int $usuarioId, string $tipo, float $valor, string $observacao): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO caixa_operacoes (caixa_id, usuario_id, tipo, valor, observacao)
             VALUES (?,?,?,?,?)'
        );
        $stmt->execute([$caixaId, $usuarioId, $tipo, $valor, $observacao]);
        return (int) $this->pdo->lastInsertId();
    }

    public function fechar(int $caixaId, float $valorFechamento, string $observacao): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE caixas
             SET status = 'fechado', valor_fechamento = ?, observacao_fechamento = ?, fechado_em = NOW()
             WHERE id = ? AND status = 'aberto'"
        );
        $stmt->execute([$valorFechamento, $observacao, $caixaId]);
    }

    /** @return CaixaOperacao[] */
    public function operacoes(int $caixaId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM caixa_operacoes WHERE caixa_id = ? ORDER BY criado_em DESC, id DESC');
        $stmt->execute([$caixaId]);
        return array_map([CaixaOperacao::class, 'fromArray'], $stmt->fetchAll());
    }

    /**
     * @return array{
     *   vendas_total: float,
     *   vendas_dinheiro: float,
     *   vendas_por_forma: array<int, array{forma_pagamento: string, total: float, quantidade: int}>,
     *   sangrias: float,
     *   reforcos: float,
     *   saldo_esperado: float
     * }
     */
    public function resumo(Caixa $caixa): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT vp.forma_pagamento, COUNT(DISTINCT v.id) AS quantidade, COALESCE(SUM(vp.valor),0) AS total
             FROM venda_pagamentos vp
             JOIN vendas v ON v.id = vp.venda_id
             WHERE v.caixa_id = ? AND v.status = 'concluida'
             GROUP BY vp.forma_pagamento
             ORDER BY vp.forma_pagamento"
        );
        $stmt->execute([$caixa->id]);
        $vendasPorForma = array_map(
            fn (array $row): array => [
                'forma_pagamento' => (string) $row['forma_pagamento'],
                'total' => (float) $row['total'],
                'quantidade' => (int) $row['quantidade'],
            ],
            $stmt->fetchAll()
        );

        $vendasTotal = 0.0;
        $vendasDinheiro = 0.0;
        foreach ($vendasPorForma as $row) {
            $vendasTotal += $row['total'];
            if ($row['forma_pagamento'] === 'dinheiro' || str_starts_with($row['forma_pagamento'], 'dinheiro_')) {
                $vendasDinheiro += $row['total'];
            }
        }

        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(troco),0)
             FROM vendas
             WHERE caixa_id = ? AND status = 'concluida'"
        );
        $stmt->execute([$caixa->id]);
        $vendasDinheiro = max($vendasDinheiro - (float) $stmt->fetchColumn(), 0);

        $stmt = $this->pdo->prepare(
            "SELECT tipo, COALESCE(SUM(valor),0) AS total
             FROM caixa_operacoes
             WHERE caixa_id = ?
             GROUP BY tipo"
        );
        $stmt->execute([$caixa->id]);

        $sangrias = 0.0;
        $reforcos = 0.0;
        foreach ($stmt->fetchAll() as $row) {
            if ($row['tipo'] === 'sangria') {
                $sangrias = (float) $row['total'];
            }
            if ($row['tipo'] === 'reforco') {
                $reforcos = (float) $row['total'];
            }
        }

        return [
            'vendas_total' => $vendasTotal,
            'vendas_dinheiro' => $vendasDinheiro,
            'vendas_por_forma' => $vendasPorForma,
            'sangrias' => $sangrias,
            'reforcos' => $reforcos,
            'saldo_esperado' => $caixa->fundoInicial + $vendasDinheiro + $reforcos - $sangrias,
        ];
    }

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS caixas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            fundo_inicial DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            valor_fechamento DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            status ENUM('aberto','fechado') NOT NULL DEFAULT 'aberto',
            observacao_abertura TEXT NULL,
            observacao_fechamento TEXT NULL,
            aberto_em DATETIME DEFAULT CURRENT_TIMESTAMP,
            fechado_em DATETIME NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        )");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS caixa_operacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            caixa_id INT NOT NULL,
            usuario_id INT NOT NULL,
            tipo ENUM('sangria','reforco') NOT NULL,
            valor DECIMAL(10,2) NOT NULL,
            observacao TEXT NULL,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (caixa_id) REFERENCES caixas(id),
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        )");

        $column = $this->pdo->query("SHOW COLUMNS FROM vendas LIKE 'caixa_id'")->fetch();
        if (!$column) {
            $this->pdo->exec('ALTER TABLE vendas ADD COLUMN caixa_id INT NULL AFTER empresa_id');
        }

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS venda_pagamentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            venda_id INT NOT NULL,
            forma_pagamento VARCHAR(80) NOT NULL,
            valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (venda_id) REFERENCES vendas(id)
        )");

        $this->pdo->exec(
            "INSERT INTO venda_pagamentos (venda_id, forma_pagamento, valor)
             SELECT v.id, v.forma_pagamento, v.total
             FROM vendas v
             LEFT JOIN venda_pagamentos vp ON vp.venda_id = v.id
             WHERE vp.id IS NULL"
        );
    }
}
