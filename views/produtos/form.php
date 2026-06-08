<?php
$moneyValue = fn (float $value): string => number_format($value, 2, ',', '');
$numberValue = fn (float $value): string => rtrim(rtrim(number_format($value, 3, ',', ''), '0'), ',');
?>
<div class="container py-4" style="max-width:1100px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-<?= $produto->id > 0 ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
            <?= e($pageTitle) ?>
        </h4>
        <a href="<?= BASE_URL ?>/produtos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-2"></i>
        <ul class="mb-0 mt-1">
            <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST"
          action="<?= BASE_URL . ($produto->id > 0 ? '/produtos/editar/' . $produto->id : '/produtos/novo') ?>"
          novalidate>
        <div class="card mb-3">
            <div class="card-header fw-semibold">Dados principais</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Código de Barras <span class="text-danger">*</span></label>
                        <input type="text" name="codigo_barras" class="form-control"
                               value="<?= e($produto->codigoBarras) ?>" required autofocus>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tipo de Produto</label>
                        <input type="text" name="tipo_produto" class="form-control"
                               value="<?= e($produto->tipoProduto) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Unidade</label>
                        <input type="text" name="unidade" class="form-control"
                               value="<?= e($produto->unidade) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Descrição <span class="text-danger">*</span></label>
                        <input type="text" name="descricao" class="form-control"
                               value="<?= e($produto->descricao) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Categoria do Produto</label>
                        <select name="categoria_produto" class="form-select" id="categoriaProduto">
                            <option value="">Selecione...</option>
                            <?php
                            $categoriaSelecionada = false;
                            foreach ($categorias ?? [] as $categoria):
                                $selected = $produto->categoriaProduto === $categoria->nome;
                                $categoriaSelecionada = $categoriaSelecionada || $selected;
                            ?>
                            <option value="<?= e($categoria->nome) ?>" <?= $selected ? 'selected' : '' ?>>
                                <?= e($categoria->nome) ?>
                            </option>
                            <?php endforeach; ?>
                            <?php if ($produto->categoriaProduto !== '' && !$categoriaSelecionada): ?>
                            <option value="<?= e($produto->categoriaProduto) ?>" selected><?= e($produto->categoriaProduto) ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subcategoria do Produto</label>
                        <select name="subcategoria_produto" class="form-select" id="subcategoriaProduto">
                            <option value="">Selecione...</option>
                            <?php
                            $subcategoriaSelecionada = false;
                            foreach ($subcategorias ?? [] as $subcategoria):
                                $selected = $produto->subcategoriaProduto === $subcategoria->nome;
                                $subcategoriaSelecionada = $subcategoriaSelecionada || $selected;
                            ?>
                            <option value="<?= e($subcategoria->nome) ?>"
                                    data-categoria="<?= e($subcategoria->categoriaNome) ?>"
                                    <?= $selected ? 'selected' : '' ?>>
                                <?= e($subcategoria->categoriaNome) ?> / <?= e($subcategoria->nome) ?>
                            </option>
                            <?php endforeach; ?>
                            <?php if ($produto->subcategoriaProduto !== '' && !$subcategoriaSelecionada): ?>
                            <option value="<?= e($produto->subcategoriaProduto) ?>" data-categoria="<?= e($produto->categoriaProduto) ?>" selected>
                                <?= e($produto->subcategoriaProduto) ?>
                            </option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Marca</label>
                        <input type="text" name="marca" class="form-control" value="<?= e($produto->marca) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Modelo</label>
                        <input type="text" name="modelo" class="form-control" value="<?= e($produto->modelo) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tags</label>
                        <input type="text" name="tags" class="form-control" value="<?= e($produto->tags) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-semibold">Preços e estoque</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ([
                        'preco_custo' => ['Preço de Custo', $produto->precoCusto],
                        'preco_venda_varejo' => ['Preço Venda Varejo', $produto->precoVendaVarejo],
                        'preco_venda_atacado' => ['Preço Venda Atacado', $produto->precoVendaAtacado],
                        'preco_de' => ['Preço De', $produto->precoDe],
                        'preco_por' => ['Preço Por', $produto->precoPor],
                    ] as $name => [$label, $value]): ?>
                    <div class="col-md">
                        <label class="form-label fw-semibold"><?= e($label) ?><?= $name === 'preco_venda_varejo' ? ' <span class="text-danger">*</span>' : '' ?></label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" name="<?= $name ?>" class="form-control"
                                   value="<?= $moneyValue($value) ?>" <?= $name === 'preco_venda_varejo' ? 'required' : '' ?>>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Quantidade Mínima Atacado</label>
                        <input type="number" name="quantidade_minima_atacado" class="form-control"
                               min="0" value="<?= $produto->quantidadeMinimaAtacado ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Estoque mínimo</label>
                        <input type="number" name="estoque_minimo" class="form-control"
                               min="0" value="<?= $produto->estoqueMinimo ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Quantidade em Estoque</label>
                        <input type="number" name="quantidade_estoque" class="form-control"
                               min="0" value="<?= $produto->quantidadeEstoque ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-4">
                        <div class="form-check form-switch">
                            <input type="hidden" name="movimenta_estoque" value="0">
                            <input class="form-check-input" type="checkbox" name="movimenta_estoque" id="movimenta_estoque"
                                   value="1" <?= $produto->movimentaEstoque ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="movimenta_estoque">Movimenta Estoque</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="ativo" value="0">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo"
                                   value="1" <?= $produto->ativo ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="ativo">Ativo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-semibold">Fiscal, PDV e loja virtual</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ([
                        'codigo_balanca' => ['Código Balança', $produto->codigoBalanca],
                        'codigo_interno' => ['Código Interno', $produto->codigoInterno],
                        'tipo' => ['Tipo', $produto->tipo],
                        'ncm' => ['NCM', $produto->ncm],
                        'cfop' => ['CFOP', $produto->cfop],
                        'origem' => ['Origem', $produto->origem],
                        'cest' => ['CEST', $produto->cest],
                        'categoria_pdv' => ['Categoria PDV', $produto->categoriaPdv],
                        'categoria_loja_virtual' => ['Categoria na Loja Virtual', $produto->categoriaLojaVirtual],
                        'subcategoria_loja_virtual' => ['Subcategoria na Loja Virtual', $produto->subcategoriaLojaVirtual],
                        'nome_loja_virtual' => ['Nome na Loja Virtual', $produto->nomeLojaVirtual],
                    ] as $name => [$label, $value]): ?>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><?= e($label) ?></label>
                        <input type="text" name="<?= $name ?>" class="form-control" value="<?= e($value) ?>">
                    </div>
                    <?php endforeach; ?>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input type="hidden" name="botao_pdv" value="0">
                            <input class="form-check-input" type="checkbox" name="botao_pdv" id="botao_pdv"
                                   value="1" <?= $produto->botaoPdv ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="botao_pdv">Botão PDV</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-semibold">Dimensões e detalhes</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ([
                        'altura_cm' => ['Altura (cm)', $produto->alturaCm],
                        'largura_cm' => ['Largura (cm)', $produto->larguraCm],
                        'profundidade_cm' => ['Profundidade (cm)', $produto->profundidadeCm],
                        'peso_kg' => ['Peso (Kg)', $produto->pesoKg],
                    ] as $name => [$label, $value]): ?>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><?= e($label) ?></label>
                        <input type="text" name="<?= $name ?>" class="form-control" value="<?= $numberValue($value) ?>">
                    </div>
                    <?php endforeach; ?>
                    <?php foreach ([
                        'descricao_produto' => ['Descrição do Produto', $produto->descricaoProduto],
                        'garantia' => ['Garantia', $produto->garantia],
                        'itens_inclusos' => ['Itens Inclusos', $produto->itensInclusos],
                        'especificacoes' => ['Especificações', $produto->especificacoes],
                    ] as $name => [$label, $value]): ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><?= e($label) ?></label>
                        <textarea name="<?= $name ?>" class="form-control" rows="3"><?= e($value) ?></textarea>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mb-4">
            <a href="<?= BASE_URL ?>/produtos" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Salvar Produto
            </button>
        </div>
    </form>
</div>

<script>
const categoriaProduto = document.getElementById('categoriaProduto');
const subcategoriaProduto = document.getElementById('subcategoriaProduto');

function filtrarSubcategorias() {
    const categoria = categoriaProduto.value;
    [...subcategoriaProduto.options].forEach(option => {
        if (option.value === '') {
            option.hidden = false;
            return;
        }
        const matches = !categoria || option.dataset.categoria === categoria;
        option.hidden = !matches;
        if (!matches && option.selected) {
            subcategoriaProduto.value = '';
        }
    });
}

categoriaProduto.addEventListener('change', filtrarSubcategorias);
filtrarSubcategorias();
</script>
