<?php

namespace App\Services;

use App\Models\PerfilUsuario;
use App\Repositories\PerfilUsuarioRepository;

class PerfilUsuarioService
{
    public function __construct(private PerfilUsuarioRepository $repo) {}

    /** @return PerfilUsuario[] */
    public function listar(): array
    {
        return $this->repo->findAll();
    }

    /** @return PerfilUsuario[] */
    public function listarAtivos(): array
    {
        return $this->repo->findAtivos();
    }

    public function buscarPorId(int $id): ?PerfilUsuario
    {
        return $this->repo->findById($id);
    }

    /**
     * @return array{ok: bool, errors: string[], perfil: PerfilUsuario}
     */
    public function salvar(array $dados, int $id = 0): array
    {
        $errors = [];
        $nome = trim($dados['nome'] ?? '');
        $codigo = trim($dados['codigo'] ?? '');
        $ativo = isset($dados['ativo']);

        if ($nome === '') {
            $errors[] = 'Nome e obrigatorio.';
        }

        if ($codigo === '') {
            $codigo = $this->gerarCodigo($nome);
        } else {
            $codigo = $this->gerarCodigo($codigo);
        }

        if ($codigo === '') {
            $errors[] = 'Codigo e obrigatorio.';
        } elseif ($this->repo->codigoExists($codigo, $id)) {
            $errors[] = 'Este codigo de perfil ja esta em uso.';
        }

        $perfil = $id > 0 ? ($this->repo->findById($id) ?? new PerfilUsuario()) : new PerfilUsuario();
        $perfil->id = $id;
        $perfil->codigo = $codigo;
        $perfil->nome = $nome;
        $perfil->ativo = $ativo;

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors, 'perfil' => $perfil];
        }

        $this->repo->save($perfil);
        return ['ok' => true, 'errors' => [], 'perfil' => $perfil];
    }

    public function toggle(int $id): void
    {
        $this->repo->toggle($id);
    }

    private function gerarCodigo(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '_', $value) ?? '';
        return trim($value, '_');
    }
}
