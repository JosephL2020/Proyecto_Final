<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecommendationService
{
    public function suggest(string $title, string $description): array
    {
        $text = trim($title.' '.$description);
        $key = config('services.openai.key');
        if ($key) {
            $res = Http::withToken($key)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un asistente técnico conciso. Devuelve exactamente 3 recomendaciones numeradas, una por línea.'],
                    ['role' => 'user', 'content' => 'Problema: '.$text],
                ],
                'temperature' => 0.2,
                'max_tokens' => 120,
            ]);
            if ($res->successful()) {
                $content = data_get($res->json(), 'choices.0.message.content', '');
                $lines = preg_split('/[\r\n]+/', trim($content));
                $out = [];
                foreach ($lines as $l) {
                    $l = preg_replace('/^\s*\d+[\).\-\:]\s*/', '', $l);
                    if ($l !== '') $out[] = $l;
                    if (count($out) === 3) break;
                }
                if (count($out) === 3) return $out;
            }
        }
        return $this->fallback($text);
    }

    protected function fallback(string $text): array
    {
        $t = mb_strtolower($text);
        if (str_contains($t, 'monitor') || str_contains($t, 'pantalla')) {
            return [
                'Verificar cables y energía y probar otro puerto o cable',
                'Probar con otro monitor y revisar salida de video del equipo',
                'Reiniciar equipo y revisar logs de eventos o BIOS por errores de GPU'
            ];
        }
        if (str_contains($t, 'red') || str_contains($t, 'internet') || str_contains($t, 'wifi')) {
            return [
                'Reiniciar adaptador y renovar IP',
                'Probar conectividad con ping o tracert y revisar DNS',
                'Revisar router o switch, cables y reglas de firewall'
            ];
        }
        if (str_contains($t, 'impresora')) {
            return [
                'Revisar cola de impresión y servicio del spooler',
                'Comprobar conexión y consumibles',
                'Reinstalar o actualizar el driver y fijarla como predeterminada'
            ];
        }
        return [
            'Reproducir el problema y capturar mensajes o logs',
            'Aplicar actualizaciones pendientes del sistema o aplicación',
            'Escalar con evidencia si persiste y definir siguiente acción'
        ];
    }
}
