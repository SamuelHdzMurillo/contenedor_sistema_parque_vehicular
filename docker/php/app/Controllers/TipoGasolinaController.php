<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\TipoGasolinaService;

final class TipoGasolinaController extends BaseController
{
    public function __construct(
        private readonly TipoGasolinaService $tipos = new TipoGasolinaService()
    ) {
    }

    public function index(Request $request): never
    {
        $page = max(1, (int) $request->input('page', 1));
        $filters = array_filter([
            'q' => $request->input('q'),
            'activo' => $request->input('activo'),
        ], static fn ($v) => $v !== null && $v !== '');

        $this->render('catalogos.tipos_gasolina.index', $this->tipos->paginate($page, $filters));
    }

    public function create(Request $request): never
    {
        $this->render('catalogos.tipos_gasolina.create');
    }

    public function store(Request $request): never
    {
        $this->validateCsrf($request);
        $data = $request->all();
        $result = $this->tipos->create($data);
        if (is_string($result)) {
            flash_old($data);
            flash('error', $result);
            $this->redirect('catalogos/tipos-gasolina/create');
        }
        flash('success', 'Tipo de gasolina registrado correctamente.');
        $this->redirect('catalogos/tipos-gasolina');
    }

    public function quickStore(Request $request): never
    {
        if (!can('catalogos.create') && !can('combustible.create')) {
            Response::json(['ok' => false, 'error' => 'No tiene permiso para agregar tipos de gasolina.'], 403);
        }

        $token = $request->input('_token');
        if (!Csrf::validate(is_string($token) ? $token : null)) {
            Response::json(['ok' => false, 'error' => 'Token de seguridad inválido. Recargue la página.'], 419);
        }

        $result = $this->tipos->create($request->all());
        if (is_string($result)) {
            Response::json(['ok' => false, 'error' => $result], 422);
        }

        $tipo = $this->tipos->find((int) $result);
        if ($tipo === null) {
            Response::json(['ok' => false, 'error' => 'No se pudo recuperar el tipo creado.'], 500);
        }

        Response::json([
            'ok' => true,
            'tipo_gasolina' => [
                'id' => (int) $tipo['id'],
                'nombre' => (string) $tipo['nombre'],
                'label' => (string) $tipo['nombre'],
            ],
        ]);
    }

    public function edit(Request $request, string $id): never
    {
        $tipo = $this->tipos->find((int) $id);
        if ($tipo === null) {
            flash('error', 'Tipo de gasolina no encontrado.');
            $this->redirect('catalogos/tipos-gasolina');
        }
        $this->render('catalogos.tipos_gasolina.edit', ['tipo' => $tipo]);
    }

    public function update(Request $request, string $id): never
    {
        $this->validateCsrf($request);
        $data = $request->all();
        $result = $this->tipos->update((int) $id, $data);
        if (is_string($result)) {
            flash('error', $result);
            $this->redirect('catalogos/tipos-gasolina/' . $id . '/edit');
        }
        if ($result === false) {
            flash('error', 'No se pudo actualizar el tipo de gasolina.');
            $this->redirect('catalogos/tipos-gasolina/' . $id . '/edit');
        }
        flash('success', 'Tipo de gasolina actualizado correctamente.');
        $this->redirect('catalogos/tipos-gasolina');
    }

    public function toggle(Request $request, string $id): never
    {
        $this->validateCsrf($request);
        $activo = (string) $request->input('activo', '1') === '1';
        $result = $this->tipos->setActivo((int) $id, $activo);
        if (is_string($result)) {
            flash('error', $result);
            $this->redirect('catalogos/tipos-gasolina');
        }
        if ($result === false) {
            flash('error', 'No se pudo actualizar el estado.');
            $this->redirect('catalogos/tipos-gasolina');
        }
        flash('success', $activo ? 'Tipo activado.' : 'Tipo desactivado.');
        $this->redirect('catalogos/tipos-gasolina');
    }
}
