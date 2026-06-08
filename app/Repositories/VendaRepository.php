<?php

namespace App\Repositories;

use App\Models\Venda;
use App\Models\VendaItem;
use PDO;

class VendaRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return Venda[] */
    public function findByPeriodo(string $dataInicio, string $dataFim, int $empresaId = 0): array
    {
        $where = "DATE(v.criado_em) BETWEEN ? AND ?";
        $params = [$dataInicio, $dataFim];
        if ($empresaId > 0) {
            $where .= " AND v.empresa_id = ?";
            $params[] = $empresaId;
        }

        $stmt = $this->pdo->prepare(
            "SELECT v.*, u.nome AS usuario_nome,
                    e.razao_social AS empresa_nome, e.cnpj AS empresa_cnpj,
                    (SELECT COUNT(*) FROM venda_itens vi WHERE vi.venda_id = v.id) AS qtd_itens
             FROM vendas v
             JOIN usuarios u ON u.id = v.usuario_id
             LEFT JOIN empresas e ON e.id = v.empresa_id
             WHERE $where
             ORDER BY v.criado_em DESC"
        );
        $stmt->execute($params);
        return array_map([Venda::class, 'fromArray'], $stmt->fetchAll());
    }

    /** @return Venda[] */
    public function findRecentes(int $limit = 10, int $empresaId = 0): array
    {
        $where = "v.status = 'concluida'";
        $params = [$limit];
        if ($empresaId > 0) {
            $where .= " AND v.empresa_id = ?";
            $params = [$empresaId, $limit];
        }

        $stmt = $this->pdo->prepare(
            "SELECT v.*, u.nome AS usuario_nome,
                    e.razao_social AS empresa_nome, e.cnpj AS empresa_cnpj
             FROM vendas v
             JOIN usuarios u ON u.id = v.usuario_id
             LEFT JOIN empresas e ON e.id = v.empresa_id
             WHERE $where
             ORDER BY v.criado_em DESC
             LIMIT ?"
        );
        $stmt->execute($params);
        return array_map([Venda::class, 'fromArray'], $stmt->fetchAll());
    }

    public function totalHoje(int $empresaId = 0): float
    {
        $where = "DATE(criado_em) = CURDATE() AND status = 'concluida'";
        $params = [];
        if ($empresaId > 0) {
            $where .= " AND empresa_id = ?";
            $params[] = $empresaId;
        }
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(total),0) FROM vendas WHERE $where");
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public function countHoje(int $empresaId = 0): int
    {
        $where = "DATE(criado_em) = CURDATE() AND status = 'concluida'";
        $params = [];
        if ($empresaId > 0) {
            $where .= " AND empresa_id = ?";
            $params[] = $empresaId;
        }
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM vendas WHERE $where");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function save(Venda $venda): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO vendas (usuario_id, empresa_id, total, desconto, forma_pagamento, valor_pago, troco)
                 VALUES (?,?,?,?,?,?,?)'
            );
            $stmt->execute([
                $venda->usuarioId,
                $venda->empresaId > 0 ? $venda->empresaId : null,
                $venda->total,
                $venda->desconto,
                $venda->formaPagamento,
                $venda->valorPago,
                $venda->troco,
            ]);
            $vendaId = (int) $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare(
                'INSERT INTO venda_itens (venda_id, produto_id, quantidade, preco_unitario, subtotal)
                 VALUES (?,?,?,?,?)'
            );

            foreach ($venda->itens as $item) {
                $stmtItem->execute([
                    $vendaId,
                    $item->produtoId,
                    $item->quantidade,
                    $item->precoUnitario,
                    $item->subtotal,
                ]);
            }

            $this->pdo->commit();
            return $vendaId;

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
