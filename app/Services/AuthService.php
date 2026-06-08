<?php

namespace App\Services;

use App\Repositories\UsuarioRepository;

class AuthService
{
    public function __construct(private UsuarioRepository $usuarios) {}

    /**
     * Autentica o usuário e popula a sessão.
     * Retorna null se credenciais inválidas ou usuário inativo.
     */
    public function login(string $email, string $senha): ?array
    {
        $usuario = $this->usuarios->findByEmail($email);

        if (!$usuario || !$usuario->ativo) {
            return null;
        }

        if (!password_verify($senha, $usuario->senha)) {
            return null;
        }

        return [
            'id'     => $usuario->id,
            'nome'   => $usuario->nome,
            'perfil' => $usuario->perfil,
        ];
    }

    public function hashSenha(string $senha): string
    {
        return password_hash($senha, PASSWORD_DEFAULT);
    }
}
