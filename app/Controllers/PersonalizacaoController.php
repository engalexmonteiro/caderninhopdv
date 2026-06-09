<?php

namespace App\Controllers;

use App\Repositories\EmpresaRepository;
use App\Repositories\PersonalizacaoRepository;
use App\Services\EmpresaService;
use App\Services\PersonalizacaoService;

class PersonalizacaoController
{
    private PersonalizacaoService $service;
    private EmpresaService $empresaService;

    public function __construct()
    {
        $pdo = getDB();
        $empresaRepo = new EmpresaRepository($pdo);
        $this->service = new PersonalizacaoService(new PersonalizacaoRepository($pdo), $empresaRepo);
        $this->empresaService = new EmpresaService($empresaRepo);
    }

    public function edit(): void
    {
        requireAdmin();
        render('personalizacao/form', [
            'pageTitle' => 'Personalizacao',
            'personalizacao' => $this->service->get(),
            'empresas' => $this->empresaService->listarAtivas(),
            'paletas' => PersonalizacaoService::PALETAS,
            'errors' => [],
            'flash' => flash('personalizacao'),
        ]);
    }

    public function update(): void
    {
        requireAdmin();
        $result = $this->service->salvar($_POST, $_FILES);
        if (!$result['ok']) {
            render('personalizacao/form', [
                'pageTitle' => 'Personalizacao',
                'personalizacao' => $result['personalizacao'],
                'empresas' => $this->empresaService->listarAtivas(),
                'paletas' => PersonalizacaoService::PALETAS,
                'errors' => $result['errors'],
                'flash' => '',
            ]);
            return;
        }

        redirect('/personalizacao', 'personalizacao', 'Personalizacao salva com sucesso.');
    }
}
