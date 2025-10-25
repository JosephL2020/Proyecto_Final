<?php
namespace App\Services;

class SugerenciasSoporte
{

    public function get(string $titulo, string $descripcion): array
    {
        $base = strtolower($titulo.' '.$descripcion);
        if (str_contains($base, 'impresora')) {
            return [
                'Verificar energía y conexión de red de la impresora.',
                'Reinstalar/actualizar el driver del fabricante.',
                'Probar imprimir desde otro equipo para aislar el problema.',
            ];
        }
        if (str_contains($base, 'correo') || str_contains($base, 'outlook')) {
            return [
                'Revisar parámetros SMTP/IMAP y credenciales.',
                'Limpiar caché de credenciales y reautenticar.',
                'Verificar cuota y reglas de bandeja/antispam.',
            ];
        }
        return [
            'Reiniciar servicio/equipo y revisar eventos o logs.',
            'Actualizar a la última versión disponible.',
            'Revisar procedimientos internos similares.',
        ];
    }
}
