<?php

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\PerfilUsuarioRepository;
use App\Repositories\UsuarioRepository;

class UsuarioService
{
    public function __construct(
        private UsuarioRepository $repo,
        private AuthService $auth,
        private ?PerfilUsuarioRepository $perfilRepo = null,
    ) {}

    /** @return Usuario[] */
    public function listar(): array
    {
        return $this->repo->findAll();
    }

    public function buscarPorId(int $id): ?Usuario
    {
        return $this->repo->findById($id);
    }

    /**
     * Valida e salva um usuario.
     * @return array{ok: bool, errors: string[]}
     */
    public function salvar(array $dados, int $id = 0): array
    {
        $errors = [];

        $nome = trim($dados['nome'] ?? '');
        $email = trim($dados['email'] ?? '');
        $perfil = trim($dados['perfil'] ?? 'usuario');
        $ativo = isset($dados['ativo']);
        $senha = $dados['senha'] ?? '';
        $confirmacao = $dados['confirmacao'] ?? '';

        if ($nome === '') {
            $errors[] = 'Nome e obrigatorio.';
        }

        if ($email === '') {
            $errors[] = 'E-mail e obrigatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-mail invalido.';
        }

        if ($perfil === '') {
            $errors[] = 'Perfil e obrigatorio.';
        } elseif ($this->perfilRepo && !$this->perfilRepo->findByCodigo($perfil)) {
            $errors[] = 'Perfil de usuario invalido.';
        }

        if ($id === 0 && $senha === '') {
            $errors[] = 'Senha obrigatoria para novo usuario.';
        } elseif ($senha !== '' && strlen($senha) < 6) {
            $errors[] = 'A senha deve ter ao menos 6 caracteres.';
        } elseif ($senha !== '' && $senha !== $confirmacao) {
            $errors[] = 'As senhas nao conferem.';
        }

        if (empty($errors) && $this->repo->emailExists($email, $id)) {
            $errors[] = 'Este e-mail ja esta em uso.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors];
        }

        $usuario = $id > 0 ? ($this->repo->findById($id) ?? new Usuario()) : new Usuario();
        $usuario->id = $id;
        $usuario->nome = $nome;
        $usuario->email = $email;
        $usuario->perfil = $perfil;
        $usuario->ativo = $ativo;
        $usuario->senha = $senha !== '' ? $this->auth->hashSenha($senha) : '';

        $this->repo->save($usuario);

        return ['ok' => true, 'errors' => []];
    }

    public function toggle(int $id): void
    {
        $this->repo->toggle($id);
    }
}
