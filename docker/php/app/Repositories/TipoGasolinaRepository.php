<?php

declare(strict_types=1);

namespace App\Repositories;

final class TipoGasolinaRepository extends BaseRepository
{
    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM tipos_gasolina WHERE id = ?', [$id]);
    }

    public function findByNombre(string $nombre, ?int $excludeId = null): ?array
    {
        $sql = 'SELECT * FROM tipos_gasolina WHERE nombre = ?';
        $params = [$nombre];
        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }

        return $this->fetchOne($sql, $params);
    }

    public function listForSelect(bool $soloActivos = true): array
    {
        $sql = 'SELECT id, nombre FROM tipos_gasolina';
        if ($soloActivos) {
            $sql .= ' WHERE activo = 1';
        }
        $sql .= ' ORDER BY nombre ASC';

        return $this->fetchAll($sql);
    }

    public function paginate(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = 'WHERE 1=1';

        if (!empty($filters['q'])) {
            $where .= ' AND nombre LIKE ?';
            $params[] = '%' . $filters['q'] . '%';
        }
        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $where .= ' AND activo = ?';
            $params[] = (int) $filters['activo'];
        }

        $total = (int) ($this->fetchOne("SELECT COUNT(*) AS c FROM tipos_gasolina {$where}", $params)['c'] ?? 0);
        $queryParams = array_merge($params, [$perPage, $offset]);
        $rows = $this->fetchAll(
            "SELECT id, nombre, activo, created_at
             FROM tipos_gasolina {$where}
             ORDER BY nombre ASC
             LIMIT ? OFFSET ?",
            $queryParams
        );

        return ['data' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO tipos_gasolina (nombre, activo) VALUES (?, ?)',
            [$data['nombre'], (int) ($data['activo'] ?? 1)]
        );

        return (int) $this->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return $this->execute(
            'UPDATE tipos_gasolina SET nombre = ?, activo = ? WHERE id = ?',
            [$data['nombre'], (int) ($data['activo'] ?? 1), $id]
        );
    }

    public function setActivo(int $id, bool $activo): bool
    {
        return $this->execute('UPDATE tipos_gasolina SET activo = ? WHERE id = ?', [$activo ? 1 : 0, $id]);
    }

    public function countCargas(int $id): int
    {
        return (int) ($this->fetchOne(
            'SELECT COUNT(*) AS c FROM combustible_cargas WHERE tipo_gasolina_id = ?',
            [$id]
        )['c'] ?? 0);
    }
}
