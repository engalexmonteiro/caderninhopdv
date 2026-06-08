<?php

namespace App\Services;

use App\Models\Empresa;
use App\Repositories\EmpresaRepository;

class EmpresaService
{
    public function __construct(private EmpresaRepository $repo) {}

    /** @return Empresa[] */
    public function listar(): array
    {
        return $this->repo->findAll();
    }

    /** @return Empresa[] */
    public function listarAtivas(): array
    {
        return $this->repo->findAtivas();
    }

    public function buscarPorId(int $id): ?Empresa
    {
        return $this->repo->findById($id);
    }

    public function salvar(array $data, array $files, int $id = 0): array
    {
        $errors = [];

        $razaoSocial  = trim($data['razao_social']  ?? '');
        $nomeFantasia = trim($data['nome_fantasia'] ?? '');
        $cnpj         = preg_replace('/\D/', '', $data['cnpj'] ?? '');
        $email        = trim($data['email']    ?? '');
        $telefone     = trim($data['telefone'] ?? '');
        $endereco     = trim($data['endereco'] ?? '');
        $cidade       = trim($data['cidade']   ?? '');
        $estado       = trim($data['estado']   ?? '');
        $cep          = preg_replace('/\D/', '', $data['cep'] ?? '');
        $ativo        = isset($data['ativo']);

        if ($razaoSocial === '') {
            $errors[] = 'Razão Social é obrigatória.';
        }
        if (strlen($cnpj) !== 14) {
            $errors[] = 'CNPJ inválido — informe os 14 dígitos.';
        } elseif ($this->repo->cnpjExiste($cnpj, $id)) {
            $errors[] = 'CNPJ já cadastrado para outra empresa.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors];
        }

        $empresa               = ($id > 0 ? $this->repo->findById($id) : null) ?? new Empresa();
        $empresa->razaoSocial  = $razaoSocial;
        $empresa->nomeFantasia = $nomeFantasia;
        $empresa->cnpj         = $cnpj;
        $empresa->email        = $email;
        $empresa->telefone     = $telefone;
        $empresa->endereco     = $endereco;
        $empresa->cidade       = $cidade;
        $empresa->estado       = $estado;
        $empresa->cep          = $cep;
        $empresa->ativo        = $ativo;

        if (!empty($files['logomarca']['tmp_name'])) {
            $ext = strtolower(pathinfo($files['logomarca']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            if (!in_array($ext, $allowed, true)) {
                return ['ok' => false, 'errors' => ['Formato de imagem inválido. Use JPG, PNG, GIF, WebP ou SVG.']];
            }

            $logoDir = BASE_PATH . '/public/assets/logos';
            if (!is_dir($logoDir)) {
                mkdir($logoDir, 0755, true);
            }

            if ($empresa->logomarca !== '' && file_exists($logoDir . '/' . $empresa->logomarca)) {
                unlink($logoDir . '/' . $empresa->logomarca);
            }

            $filename = 'empresa_' . $id . '_' . time() . '.' . $ext;
            move_uploaded_file($files['logomarca']['tmp_name'], $logoDir . '/' . $filename);
            $empresa->logomarca = $filename;
        }

        $newId = $this->repo->save($empresa);
        return ['ok' => true, 'id' => $newId];
    }

    public function toggle(int $id): void
    {
        $this->repo->toggle($id);
    }
}
