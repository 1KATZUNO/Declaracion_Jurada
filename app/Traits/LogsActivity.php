<?php

namespace App\Traits;

use App\Models\ActividadLog;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        // Registrar cuando se crea un registro
        static::created(function ($model) {
            $model->logActivity('crear', $model->getLogDescription('crear'), $model->getKey(), null, $model->getAttributes());
        });

        // Registrar cuando se actualiza un registro
        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();
            
            // Excluir timestamps si no quieres registrarlos
            unset($changes['updated_at']);
            
            if (!empty($changes)) {
                $model->logActivity('editar', $model->getLogDescription('editar'), $model->getKey(), $original, $model->getAttributes());
            }
        });

        // Registrar cuando se elimina un registro
        static::deleted(function ($model) {
            $model->logActivity('eliminar', $model->getLogDescription('eliminar'), $model->getKey(), $model->getAttributes(), null);
        });
    }

    protected function logActivity($accion, $descripcion, $idRegistro, $datosAnteriores, $datosNuevos)
    {
        ActividadLog::registrar(
            $accion,
            $this->getModuleName(),
            $descripcion,
            $idRegistro,
            $datosAnteriores,
            $datosNuevos
        );
    }

    protected function getModuleName()
    {
        // Obtener el nombre del modelo (ej: Usuario, Declaracion)
        return class_basename($this);
    }

    protected function getLogDescription($accion)
    {
        $modelName = $this->getModuleName();
        $identifier = $this->getLogIdentifier();
        
        switch ($accion) {
            case 'crear':
                return "Se creó {$modelName}: {$identifier}";
            case 'editar':
                return "Se editó {$modelName}: {$identifier}";
            case 'eliminar':
                return "Se eliminó {$modelName}: {$identifier}";
            default:
                return "Acción {$accion} en {$modelName}: {$identifier}";
        }
    }

    protected function getLogIdentifier()
    {
        // Intenta obtener un identificador legible
        if (isset($this->nombre)) {
            return $this->nombre;
        }
        if (isset($this->titulo)) {
            return $this->titulo;
        }
        if (isset($this->email)) {
            return $this->email;
        }
        return "ID: {$this->getKey()}";
    }
}
