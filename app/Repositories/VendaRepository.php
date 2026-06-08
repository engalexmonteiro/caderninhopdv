<?php

namespace App\Repositories;

use App\Models\Venda;
use App\Models\VendaItem;
use PDO;

class VendaRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return Venda[] */
    public function findByPeriodo(string $dataInicio, string $dataFim): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT v.*, u.nome AS usuario_nome,
                    (SELECT COUNT(*) FROM venda_itens vi WHERE vi.venda_id = v.id) AS qtd_itens
             FROM vendas v
             JOIN usuarios u ON u.id = v.usuario_id
             WHERE DATE(v.criado_em) BETWEEN ? AND ?
             ORDER BY v.criado_em DESC"
        );
        $stmt->execute([$dataInicio, $dataFim]);
        return array_map([Venda::class, 'fromArray'], $stmt->fetchAll());
    }

    /** @return Venda[] */
    public function findRecentes(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT v.*, u.nome AS usuario_nome
             FROM vendas v
             JOIN usuarios u ON u.id = v.usuario_id
             WHERE v.status = 'concluida'
             ORDER BY v.criado_em DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return array_map([Venda::class, 'fromArray'], $stmt->fetchAll());
    }

    public function totalHoje(): float
    {
        return (float) $this->pdo->query(
            "SELECT COALESCE(SUM(total),0) FROM vendas
             WHERE DATE(criado_em) = CURDATE() AND status = 'concluida'"
        )->fetchColumn();
    }

    public function countHoje(): int
    {
        return (int) $this->pdo->query(
            "SELECT COUNT(*) FROM vendas WHERE DATE(criado_em) = CURDATE() AND status = 'concluida'"
        )->fetchColumn();
    }

    public function save(Venda $venda): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO vendas (usuario_id,total,desconto,forma_pagamento,valor_pago,troco)
                 VALUES (?,?,?,?,?,?)'
            );
            $stmt->execute([
                $venda->usuarioId,
                $venda->total,
                $venda->desconto,
                $venda->formaPagamento,
                $venda->valorPago,
                $venda->troco,
            ]);
            $vendaId = (int) $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare(
                'INSERT INTO venda_itens (venda_id,produto_id,quantidade,preco_unitario,subtotal)
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
