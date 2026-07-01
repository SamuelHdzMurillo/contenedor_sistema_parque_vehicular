<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\TipoGasolinaRepository;

final class TipoGasolinaService
{
    public function __construct(
        private readonly TipoGasolinaRepository $repo = new TipoGasolinaRepository()
    ) {
    }

    public function paginate(int $page = 1, array $filters = []): array
    {
        return $this->repo->paginate($page, 15, $filters);
    }

    public function find(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function listForSelect(): array
    {
        return $this->repo->listForSelect();
    }

    public function create(array $data): int|string
    {
        $clean = $this->sanitize($data);
        if (is_string($clean)) {
            return $clean;
        }
        if ($this->repo->findByNombre($clean['nombre']) !== null) {
            return 'Ya existe un tipo de gasolina con ese nombre.';
        }
        $id = $this->repo->create($clean);
        AuditService::log('CREATE', 'tipos_gasolina', $id, null, $clean);

        return $id;
    }

    public function update(int $id, array $data): bool|string
    {
        $before = $this->repo->findById($id);
        if ($before === null) {
            return false;
        }
        $clean = $this->sanitize($data);
        if (is_string($clean)) {
            return $clean;
        }
        if ($this->repo->findByNombre($clean['nombre'], $id) !== null) {
            return 'Ya existe otro tipo de gasolina con ese nombre.';
        }
        $result = $this->repo->update($id, $clean);
        if ($result) {
            AuditService::log('UPDATE', 'tipos_gasolina', $id, $before, $clean);
        }

        return $result;
    }

    public function setActivo(int $id, bool $activo): bool|string
    {
        $before = $this->repo->findById($id);
        if ($before === null) {
            return false;
        }
        $result = $this->repo->setActivo($id, $activo);
        if ($result) {
            AuditService::log('UPDATE', 'tipos_gasolina', $id, $before, ['activo' => $activo ? 1 : 0]);
        }

        return $result;
    }

    private function sanitize(array $data): array|string
    {
        $nombre = trim((string) ($data['nombre'] ?? ''));
        if ($nombre === '') {
            return 'El nombre del tipo de gasolina es obligatorio.';
        }

        return [
            'nombre' => $nombre,
            'activo' => isset($data['activo']) ? (int) (bool) $data['activo'] : 1,
        ];
    }
}
