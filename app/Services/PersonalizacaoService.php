<?php

namespace App\Services;

use App\Models\Personalizacao;
use App\Repositories\EmpresaRepository;
use App\Repositories\PersonalizacaoRepository;

class PersonalizacaoService
{
    public const PALETAS = [
        'azul' => [
            'nome' => 'Azul',
            'primary' => '#0d6efd',
            'primary_rgb' => '13,110,253',
            'success' => '#198754',
            'login_bg' => 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)',
        ],
        'verde' => [
            'nome' => 'Verde',
            'primary' => '#198754',
            'primary_rgb' => '25,135,84',
            'success' => '#146c43',
            'login_bg' => 'linear-gradient(135deg, #198754 0%, #0f5132 100%)',
        ],
        'indigo' => [
            'nome' => 'Indigo',
            'primary' => '#4f46e5',
            'primary_rgb' => '79,70,229',
            'success' => '#0f766e',
            'login_bg' => 'linear-gradient(135deg, #4f46e5 0%, #0f766e 100%)',
        ],
        'grafite' => [
            'nome' => 'Grafite',
            'primary' => '#334155',
            'primary_rgb' => '51,65,85',
            'success' => '#15803d',
            'login_bg' => 'linear-gradient(135deg, #334155 0%, #111827 100%)',
        ],
        'vinho' => [
            'nome' => 'Vinho',
            'primary' => '#9f1239',
            'primary_rgb' => '159,18,57',
            'success' => '#15803d',
            'login_bg' => 'linear-gradient(135deg, #9f1239 0%, #4c0519 100%)',
        ],
    ];

    public function __construct(
        private PersonalizacaoRepository $repo,
        private EmpresaRepository $empresaRepo,
    ) {}

    public function get(): Personalizacao
    {
        return $this->repo->get();
    }

    public function salvar(array $dados, array $files): array
    {
        $errors = [];
        $personalizacao = $this->repo->get();
        $paleta = (string) ($dados['paleta'] ?? 'personalizada');
        $corPrimaria = strtolower(trim($dados['cor_primaria'] ?? $personalizacao->corPrimaria));
        $corSucesso = strtolower(trim($dados['cor_sucesso'] ?? $personalizacao->corSucesso));
        $modoNoturno = isset($dados['modo_noturno']);
        $empresaId = (int) ($dados['empresa_id'] ?? 0);

        if ($paleta !== 'personalizada' && !isset(self::PALETAS[$paleta])) {
            $errors[] = 'Paleta invalida.';
        }

        if (!$this->isHexColor($corPrimaria)) {
            $errors[] = 'Cor primaria invalida.';
        }

        if (!$this->isHexColor($corSucesso)) {
            $errors[] = 'Cor de sucesso invalida.';
        }

        if ($empresaId > 0 && !$this->empresaRepo->findById($empresaId)) {
            $errors[] = 'Empresa invalida.';
        }

        $logoLogin = $this->upload($files['logo_login'] ?? null, 'logo', $personalizacao->logoLogin, $errors);
        $favicon = $this->upload($files['favicon'] ?? null, 'favicon', $personalizacao->favicon, $errors);

        if (!empty($errors)) {
            return ['ok' => false, 'errors' => $errors, 'personalizacao' => $personalizacao];
        }

        $personalizacao->paleta = $paleta;
        $personalizacao->corPrimaria = $corPrimaria;
        $personalizacao->corSucesso = $corSucesso;
        $personalizacao->modoNoturno = $modoNoturno;
        $personalizacao->empresaId = $empresaId;
        $personalizacao->logoLogin = $logoLogin;
        $personalizacao->favicon = $favicon;
        $this->repo->save($personalizacao);

        return ['ok' => true, 'errors' => [], 'personalizacao' => $personalizacao];
    }

    public function contexto(): array
    {
        $personalizacao = $this->repo->get();
        $empresa = $personalizacao->empresaId > 0 ? $this->empresaRepo->findById($personalizacao->empresaId) : null;
        $nomeAplicacao = $empresa ? $empresa->nomeExibicao() : 'PDV Sistema';
        $paleta = $this->montarPaleta($personalizacao);

        return [
            'personalizacao' => $personalizacao,
            'nomeAplicacao' => $nomeAplicacao,
            'paleta' => $paleta,
        ];
    }

    private function montarPaleta(Personalizacao $personalizacao): array
    {
        $primary = $this->isHexColor($personalizacao->corPrimaria)
            ? $personalizacao->corPrimaria
            : (self::PALETAS[$personalizacao->paleta]['primary'] ?? self::PALETAS['azul']['primary']);
        $success = $this->isHexColor($personalizacao->corSucesso)
            ? $personalizacao->corSucesso
            : (self::PALETAS[$personalizacao->paleta]['success'] ?? self::PALETAS['azul']['success']);

        return [
            'nome' => self::PALETAS[$personalizacao->paleta]['nome'] ?? 'Personalizada',
            'primary' => $primary,
            'primary_rgb' => $this->hexToRgb($primary),
            'success' => $success,
            'login_bg' => 'linear-gradient(135deg, ' . $primary . ' 0%, ' . $this->darken($primary, 28) . ' 100%)',
        ];
    }

    private function isHexColor(string $value): bool
    {
        return (bool) preg_match('/^#[0-9a-fA-F]{6}$/', $value);
    }

    private function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        return hexdec(substr($hex, 0, 2)) . ',' .
            hexdec(substr($hex, 2, 2)) . ',' .
            hexdec(substr($hex, 4, 2));
    }

    private function darken(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        $factor = max(0, min(100, 100 - $percent)) / 100;
        $r = (int) round(hexdec(substr($hex, 0, 2)) * $factor);
        $g = (int) round(hexdec(substr($hex, 2, 2)) * $factor);
        $b = (int) round(hexdec(substr($hex, 4, 2)) * $factor);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    private function upload(?array $file, string $prefix, string $current, array &$errors): string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $current;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors[] = 'Falha ao enviar arquivo.';
            return $current;
        }

        $allowed = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            'image/x-icon' => 'ico',
            'image/vnd.microsoft.icon' => 'ico',
            'image/svg+xml' => 'svg',
        ];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            $errors[] = 'Arquivo deve ser uma imagem PNG, JPG, WEBP, ICO ou SVG.';
            return $current;
        }

        $dir = BASE_PATH . '/public/uploads/personalizacao';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $name = $prefix . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $errors[] = 'Nao foi possivel salvar o arquivo enviado.';
            return $current;
        }

        return '/uploads/personalizacao/' . $name;
    }
}
