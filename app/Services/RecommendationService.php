<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    public function suggest(string $title, string $description): array
    {
        $text = trim($title . ' ' . $description);

        
        $fromIa = $this->fromOpenRouter($title, $description);
        if ($fromIa !== null) {
            return $fromIa;
        }

     
        Log::info('Usando fallback local para recomendaciones');
        return $this->fallback($text);
    }

    protected function fromOpenRouter(string $title, string $description): ?array
    {
        $key = config('services.openrouter.key');

        if (!$key) {
            Log::warning('OpenRouter: key no configurada');
            return null;
        }

        $prompt = <<<PROMPT
Eres un técnico de soporte IT.

Ticket:
- Título: {$title}
- Descripción: {$description}

Instrucciones IMPORTANTES:
- Responde EXACTAMENTE con 3 líneas.
- Cada línea debe comenzar con "1. ", "2. ", "3. "
- Prohibido cualquier texto antes o después de esas 3 líneas.
- Prohibido explicaciones, introducciones o despedidas.
- Sé técnico, concreto y en español.

PROMPT;

        try {
            $response = Http::withToken($key)->post(
                rtrim(config('services.openrouter.base_url'), '/') . '/chat/completions',
                [
                    'model' => config('services.openrouter.model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres un asistente técnico conciso, pero creativo dentro de lo razonable.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.7,
                    'max_tokens'  => 160,
                ]
            );

            Log::info('OpenRouter respuesta', [
                'status' => $response->status(),
            ]);

            if (!$response->successful()) {
                Log::error('OpenRouter no exitoso', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $content = data_get($response->json(), 'choices.0.message.content', '');

            if (trim($content) === '') {
                Log::warning('OpenRouter devolvió contenido vacío');
                return null;
            }

            $lines = preg_split('/[\r\n]+/', trim($content));
            $out = [];

            foreach ($lines as $l) {
                $l = preg_replace('/^\s*\d+[\).\-\:]\s*/', '', $l);
                $l = trim($l);
                if ($l !== '') {
                    $out[] = $l;
                }
                if (count($out) === 3) {
                    break;
                }
            }

            if (count($out) === 3) {
                return $out;
            }

            Log::warning('OpenRouter no devolvió 3 líneas útiles', [
                'content' => $content,
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Excepción al llamar a OpenRouter', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function fallback(string $text): array
    {
        $t = mb_strtolower($text);

        if (str_contains($t, 'monitor') || str_contains($t, 'pantalla')) {
            return [
                'Verificar cables y energía y probar otro puerto o cable',
                'Probar con otro monitor y revisar salida de video del equipo',
                'Reiniciar equipo y revisar logs de eventos o BIOS por errores de GPU',
            ];
        }

        if (str_contains($t, 'red') || str_contains($t, 'internet') || str_contains($t, 'wifi')) {
            return [
                'Reiniciar adaptador y renovar IP',
                'Probar conectividad con ping o tracert y revisar DNS',
                'Revisar router o switch, cables y reglas de firewall',
            ];
        }

        if (str_contains($t, 'impresora')) {
            return [
                'Revisar cola de impresión y servicio del spooler',
                'Comprobar conexión y consumibles',
                'Reinstalar o actualizar el driver y fijarla como predeterminada',
            ];
        }

        return [
            'Reproducir el problema y capturar mensajes o logs',
            'Aplicar actualizaciones pendientes del sistema o aplicación',
            'Escalar con evidencia si persiste y definir siguiente acción',
        ];
    }
}
