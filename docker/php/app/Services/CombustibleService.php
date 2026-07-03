<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\FileUploader;
use App\Repositories\AlertaRepository;
use App\Repositories\CatalogoRepository;
use App\Repositories\CombustibleRepository;
use App\Repositories\VehiculoRepository;

final class CombustibleService
{
    public function __construct(
        private readonly CombustibleRepository $repo = new CombustibleRepository(),
        private readonly VehiculoRepository $vehiculos = new VehiculoRepository(),
        private readonly AlertaRepository $alertas = new AlertaRepository(),
        private readonly CatalogoRepository $catalogos = new CatalogoRepository(),
    ) {
    }

    public function paginate(int $page = 1, ?int $vehiculoId = null): array
    {
        $filters = array_filter(['vehiculo_id' => $vehiculoId]);
        return $this->repo->paginate($page, 15, $filters);
    }

    public function getFormData(?int $vehiculoId = null): array
    {
        return [
            'vehiculos' => $this->catalogos->getVehiculosCatalogo(),
            'proveedores' => $this->catalogos->getProveedores('combustible'),
            'tipos_gasolina' => $this->catalogos->getTiposGasolina(),
        ];
    }

    public function create(array $data, int $userId): int
    {
        $data['registrado_por'] = $userId;
        return $this->registrarCarga($data);
    }

    public function registrarCarga(array $data): int
    {
        $vehiculoId = (int) $data['vehiculo_id'];
        $vehiculo = $this->vehiculos->findById($vehiculoId);
        if ($vehiculo === null) {
            throw new \RuntimeException('Vehículo no encontrado');
        }
        $kilometraje = (int) $data['kilometraje'];
        $kmActual = (int) $vehiculo['kilometraje_actual'];
        if ($kilometraje < $kmActual) {
            throw new \RuntimeException(
                'El kilometraje al cargar (' . number_format($kilometraje) . ' km) no puede ser menor al actual del vehículo (' . number_format($kmActual) . ' km).'
            );
        }
        $tipoGasolinaId = (int) ($data['tipo_gasolina_id'] ?? 0);
        if ($tipoGasolinaId <= 0) {
            throw new \RuntimeException('Seleccione el tipo de gasolina cargada.');
        }
        $tipoGasolina = $this->catalogos->getTiposGasolina();
        $tipoValido = false;
        foreach ($tipoGasolina as $tg) {
            if ((int) $tg['id'] === $tipoGasolinaId) {
                $tipoValido = true;
                break;
            }
        }
        if (!$tipoValido) {
            throw new \RuntimeException('El tipo de gasolina seleccionado no es válido.');
        }
        $data['tipo_gasolina_id'] = $tipoGasolinaId;

        $litros = (float) $data['litros'];
        $metricas = $this->repo->calcularRendimiento($vehiculoId, $kilometraje, $litros);
        if ($metricas !== null) {
            $importe = (float) $data['importe'];
            $data['rendimiento'] = $metricas['rendimiento'];
            $data['costo_por_km'] = $metricas['km_recorridos'] > 0 ? round($importe / $metricas['km_recorridos'], 4) : null;
        }
        $ticketFile = $this->extractTicketFile($data);
        $id = $this->repo->create($data);

        if ($ticketFile !== null) {
            $ruta = FileUploader::uploadDocument($ticketFile, 'combustible/' . $id);
            if ($ruta !== null) {
                $this->ensureTicketPreview($ruta);
                $carga = $this->repo->findById($id);
                if ($carga !== null) {
                    $this->repo->update($id, array_merge($carga, ['factura_ruta' => $ruta]));
                    $data['factura_ruta'] = $ruta;
                }
            }
        }

        $this->vehiculos->updateKilometraje($vehiculoId, $kilometraje, auth_id());
        AuditService::log('CREATE', 'combustible_cargas', $id, null, $data);
        return $id;
    }

    public function find(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function getFormDataForEdit(int $id): ?array
    {
        $carga = $this->repo->findById($id);
        if ($carga === null) {
            return null;
        }

        return array_merge(
            $this->getFormData((int) $carga['vehiculo_id']),
            ['carga' => $carga]
        );
    }

    public function update(int $id, array $data): ?string
    {
        $carga = $this->repo->findById($id);
        if ($carga === null) {
            return 'Carga de combustible no encontrada.';
        }

        try {
            $vehiculoId = (int) $carga['vehiculo_id'];
            $kilometraje = (int) ($data['kilometraje'] ?? 0);
            $anterior = $this->repo->getAnteriorCarga($vehiculoId, $id);
            if ($anterior !== null && $kilometraje < (int) $anterior['kilometraje']) {
                throw new \RuntimeException(
                    'El kilometraje al cargar (' . number_format($kilometraje) . ' km) no puede ser menor al de la carga anterior (' . number_format((int) $anterior['kilometraje']) . ' km).'
                );
            }
            $siguiente = $this->repo->getSiguienteCarga($vehiculoId, $id);
            if ($siguiente !== null && $kilometraje > (int) $siguiente['kilometraje']) {
                throw new \RuntimeException(
                    'El kilometraje al cargar (' . number_format($kilometraje) . ' km) no puede ser mayor al de la carga siguiente (' . number_format((int) $siguiente['kilometraje']) . ' km).'
                );
            }

            $tipoGasolinaId = (int) ($data['tipo_gasolina_id'] ?? 0);
            if ($tipoGasolinaId <= 0) {
                throw new \RuntimeException('Seleccione el tipo de gasolina cargada.');
            }

            $litros = (float) ($data['litros'] ?? 0);
            $importe = (float) ($data['importe'] ?? 0);
            $fecha = (string) ($data['fecha'] ?? '');

            $updateData = [
                'proveedor_id' => !empty($data['proveedor_id']) ? (int) $data['proveedor_id'] : null,
                'tipo_gasolina_id' => $tipoGasolinaId,
                'fecha' => $fecha,
                'litros' => $litros,
                'importe' => $importe,
                'kilometraje' => $kilometraje,
                'folio_ticket' => trim((string) ($data['folio_ticket'] ?? '')) ?: null,
                'factura_ruta' => $carga['factura_ruta'] ?? null,
                'observaciones' => trim((string) ($data['observaciones'] ?? '')) ?: null,
            ];

            $metricas = $this->repo->calcularRendimiento($vehiculoId, $kilometraje, $litros, $id);
            if ($metricas !== null) {
                $updateData['rendimiento'] = $metricas['rendimiento'];
                $updateData['costo_por_km'] = $metricas['km_recorridos'] > 0
                    ? round($importe / $metricas['km_recorridos'], 4)
                    : null;
            } else {
                $updateData['rendimiento'] = null;
                $updateData['costo_por_km'] = null;
            }

            $ticketFile = $this->extractTicketFile($data);
            if ($ticketFile !== null) {
                $this->eliminarArchivoTicket((string) ($carga['factura_ruta'] ?? ''));
                $ruta = FileUploader::uploadDocument($ticketFile, 'combustible/' . $id);
                if ($ruta !== null) {
                    $this->ensureTicketPreview($ruta);
                    $updateData['factura_ruta'] = $ruta;
                }
            }

            if (!$this->repo->update($id, $updateData)) {
                throw new \RuntimeException('No se pudo actualizar la carga.');
            }

            $ultima = $this->repo->getUltimaCarga($vehiculoId);
            if ($ultima !== null && (int) $ultima['id'] === $id) {
                $this->vehiculos->updateKilometraje($vehiculoId, $kilometraje, auth_id());
            }

            AuditService::log('UPDATE', 'combustible_cargas', $id, $carga, $updateData);
            return null;
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        } catch (\Throwable $e) {
            return user_facing_error($e, 'No se pudo actualizar la carga de combustible.');
        }
    }

    public function eliminar(int $id): ?string
    {
        $carga = $this->repo->findById($id);
        if ($carga === null) {
            return 'Carga de combustible no encontrada.';
        }

        try {
            $vehiculoId = (int) $carga['vehiculo_id'];
            $ultima = $this->repo->getUltimaCarga($vehiculoId);
            $esUltima = $ultima !== null && (int) $ultima['id'] === $id;

            $this->eliminarArchivoTicket((string) ($carga['factura_ruta'] ?? ''));

            if (!$this->repo->delete($id)) {
                throw new \RuntimeException('No se pudo eliminar la carga.');
            }

            if ($esUltima) {
                $anterior = $this->repo->getAnteriorCarga($vehiculoId, $id);
                if ($anterior !== null) {
                    $this->vehiculos->setKilometraje($vehiculoId, (int) $anterior['kilometraje'], auth_id());
                }
            }

            AuditService::log('DELETE', 'combustible_cargas', $id, $carga, null);
            return null;
        } catch (\Throwable $e) {
            return user_facing_error($e, 'No se pudo eliminar la carga de combustible.');
        }
    }

    private function eliminarArchivoTicket(string $ruta): void
    {
        $ruta = trim($ruta);
        if ($ruta === '') {
            return;
        }
        $full = storage_path('uploads/' . ltrim($ruta, '/'));
        if (is_file($full)) {
            unlink($full);
        }
        $preview = preg_replace('/\.[^.]+$/i', '_preview.jpg', $full);
        if ($preview !== null && $preview !== $full && is_file($preview)) {
            unlink($preview);
        }
    }

    private function extractTicketFile(array &$data): ?array
    {
        $file = $data['archivo_ticket'] ?? null;
        unset($data['archivo_ticket']);
        if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            return $file;
        }
        return null;
    }

    private function ensureTicketPreview(string $relativePath): void
    {
        $full = storage_path('uploads/' . ltrim($relativePath, '/'));
        if (!is_file($full)) {
            return;
        }

        $ext = strtolower((string) pathinfo($full, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return;
        }

        $preview = preg_replace('/\.[^.]+$/i', '_preview.jpg', $full);
        if ($preview === null || $preview === $full) {
            return;
        }

        if (!is_file($preview)) {
            image_save_as_jpeg($full, $preview);
        }
    }
}
