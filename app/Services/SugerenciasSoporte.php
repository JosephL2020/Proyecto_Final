<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SugerenciasSoporte
{
    public function get(string $title, string $description, array $ctx = []): array
    {
        [$recs] = $this->generate($title, $description, $ctx);
        return $recs;
    }

    public function generate(string $title, string $description, array $ctx = []): array
    {
        $texto = trim($title.' '.$description);
        $cat = trim((string)($ctx['category'] ?? ''));
        $messages = [
            ['role'=>'system','content'=>'Devuelve exactamente 3 pasos concretos, cortos y accionables, específicos al problema. Prioriza acciones básicas primero. Solo lista numerada, una línea por paso.'],
            ['role'=>'user','content'=>"Categoría: Hardware\nProblema: Teclado no escribe algunas teclas"],
            ['role'=>'assistant','content'=>"1. Desconecta y conecta el teclado; prueba otro puerto y verifica Bloq Mayús/Num\n2. Prueba el teclado en otro equipo y limpia las teclas afectadas\n3. En Administrador de dispositivos, desinstala el teclado y reinicia"],
            ['role'=>'user','content'=>"Categoría: Software\nProblema: No encuentro Google Chrome en mi computadora"],
            ['role'=>'assistant','content'=>"1. Pulsa Win y escribe «Chrome»; si no aparece, Win+R y ejecuta chrome\n2. Revisa Programas instalados; si no está, descárgalo e instálalo desde el sitio oficial\n3. Si existe, crea acceso directo desde su ejecutable y ancla a la barra de tareas"],
            ['role'=>'user','content'=>"Categoría: Red\nProblema: Wi-Fi se desconecta intermitentemente"],
            ['role'=>'assistant','content'=>"1. Olvida la red y vuelve a conectarte; reinicia el router\n2. Desactiva ahorro de energía del adaptador y actualiza el driver Wi-Fi\n3. Cambia a banda 5 GHz u otro canal; prueba por cable para descartar interferencias"],
            ['role'=>'user','content'=>"Categoría: ".$cat."\nProblema: ".$texto],
        ];

        $orKey = config('services.openrouter.key') ?: env('OPENROUTER_API_KEY');
        if ($orKey) {
            $model = env('AI_MODEL', 'meta-llama/llama-3.1-70b-instruct');
            $res = $this->callOpenRouter($orKey, $model, $messages);
            if ($res['ok']) return [$res['data'], 'ai'];
        }

        $oaKey = config('services.openai.key') ?: env('OPENAI_API_KEY');
        if ($oaKey) {
            $res = $this->callOpenAI($oaKey, env('AI_OPENAI_MODEL', 'gpt-4o-mini'), $messages);
            if ($res['ok']) return [$res['data'], 'ai'];
        }

        return [[
            'Repite la acción y anota el mensaje exacto o ruta afectada',
            'Reconecta o reinicia según el caso y verifica puertos, rutas o credenciales',
            'Actualiza o reinstala el componente implicado y prueba en otro equipo o sesión'
        ], 'fallback'];
    }

    protected function callOpenRouter(string $key, string $model, array $messages): array
    {
        try {
            $r = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$key,
                    'HTTP-Referer' => url('/'),
                    'X-Title' => config('app.name', 'Laravel'),
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.2,
                    'top_p' => 0.3,
                    'max_tokens' => 160,
                ]);
            if (!$r->successful()) return ['ok'=>false,'status'=>$r->status(),'body'=>$r->json()];
            return ['ok'=>true,'data'=>$this->toList($r->json())];
        } catch (\Throwable $e) {
            return ['ok'=>false,'status'=>0,'body'=>$e->getMessage()];
        }
    }

    protected function callOpenAI(string $key, string $model, array $messages): array
    {
        try {
            $r = Http::timeout(20)
                ->withToken($key)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.2,
                    'top_p' => 0.3,
                    'max_tokens' => 160,
                ]);
            if (!$r->successful()) return ['ok'=>false,'status'=>$r->status(),'body'=>$r->json()];
            return ['ok'=>true,'data'=>$this->toList($r->json())];
        } catch (\Throwable $e) {
            return ['ok'=>false,'status'=>0,'body'=>$e->getMessage()];
        }
    }

    protected function toList(array $json): array
    {
        $content = data_get($json, 'choices.0.message.content', '');
        $lines = preg_split('/[\r\n]+/', trim($content));
        $out = [];
        foreach ($lines as $l) {
            $l = preg_replace('/^\s*\d+[\).\-\:]\s*/', '', $l);
            if ($l !== '') $out[] = $l;
            if (count($out) === 3) break;
        }
        return $out ?: [$content];
    }
}
