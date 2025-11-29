<?php

namespace App\Services;

class SugerenciasSoporte
{
    /**
     * Atajo: devuelve solo la lista de pasos.
     */
    public function get(string $title, string $description, array $ctx = []): array
    {
        [$recs] = $this->generate($title, $description, $ctx);
        return $recs;
    }

    /**
     * Genera sugerencias de soporte en base al título, descripción y contexto.
     * Retorna [array $pasos, string $origen]
     *  - $origen = 'local' (por si luego quieres mostrar de dónde viene).
     */
    public function generate(string $title, string $description, array $ctx = []): array
    {
        $text = mb_strtolower(trim($title . ' ' . $description), 'UTF-8');
        $category = mb_strtolower(trim((string)($ctx['category'] ?? '')), 'UTF-8');

        $steps = $this->buildSuggestions($text, $category);

        return [$steps, 'local'];
    }

    /**
     * Reglas locales para construir sugerencias.
     * No usa ninguna API externa.
     */
    protected function buildSuggestions(string $text, string $category): array
    {
        $pool = [];

        // =========================
        // HARDWARE: no enciende / PC / laptop
        // =========================
        if (
            str_contains($text, 'no enciende') ||
            str_contains($text, 'no prende') ||
            str_contains($text, 'no enciende mi computadora') ||
            str_contains($category, 'hardware')
        ) {
            $pool = [
                'Verifica que el equipo esté conectado correctamente a la corriente y prueba otro tomacorriente o extensión.',
                'Revisa el cable de alimentación, cargador o UPS y valida si otro dispositivo en el mismo contacto funciona bien.',
                'Realiza un reinicio eléctrico: desconecta el equipo 1 minuto, mantén presionado el botón de encendido 10–15 segundos y vuelve a conectar.',
                'Prueba con otro cargador o cable de poder para descartar daño del adaptador o fuente.',
                'Si el equipo enciende pero no da imagen, prueba con otro monitor o salida de video.',
            ];
        }

        // =========================
        // TECLADO / MOUSE
        // =========================
        elseif (
            str_contains($text, 'teclado') ||
            str_contains($text, 'teclas') ||
            str_contains($text, 'mouse') ||
            str_contains($text, 'ratón')
        ) {
            $pool = [
                'Conecta el teclado o mouse en otro puerto USB y valida si Windows lo detecta correctamente.',
                'Prueba el periférico en otro equipo para descartar que el problema sea del dispositivo.',
                'En Administrador de dispositivos, desinstala el teclado/mouse y reinicia el equipo para que se reinstale el driver.',
                'Limpia físicamente las teclas o el sensor del mouse y revisa si hay suciedad que obstruya el mecanismo.',
            ];
        }

        // =========================
        // MONITOR / PANTALLA
        // =========================
        elseif (
            str_contains($text, 'monitor') ||
            str_contains($text, 'pantalla') ||
            str_contains($text, 'no da imagen')
        ) {
            $pool = [
                'Verifica que el monitor esté encendido y con el botón de encendido iluminado.',
                'Revisa que el cable de video (HDMI, VGA, DisplayPort, DVI) esté bien conectado en PC y monitor.',
                'Prueba con otro cable de video o en otro puerto de la tarjeta gráfica si está disponible.',
                'Conecta el monitor a otro equipo para confirmar si el problema es del monitor o de la PC.',
            ];
        }

        // =========================
        // IMPRESORAS
        // =========================
        elseif (
            str_contains($text, 'impresora') ||
            str_contains($text, 'imprimir') ||
            str_contains($category, 'impresora')
        ) {
            $pool = [
                'Revisa que la impresora esté encendida, sin errores en el panel y con papel en la bandeja.',
                'Verifica que esté seleccionada como impresora predeterminada en el sistema operativo.',
                'Comprueba que el cable USB o la conexión de red (Wi-Fi / Ethernet) esté activa y sin fallos.',
                'Reinstala o actualiza el driver desde la página oficial del fabricante.',
                'Limpia la cola de impresión, reinicia el servicio de cola y vuelve a enviar una prueba.',
            ];
        }

        // =========================
        // INTERNET / RED / WIFI
        // =========================
        elseif (
            str_contains($text, 'internet') ||
            str_contains($text, 'red') ||
            str_contains($text, 'wifi') ||
            str_contains($text, 'wi-fi') ||
            str_contains($text, 'conexión') ||
            str_contains($category, 'red')
        ) {
            $pool = [
                'Valida si otros dispositivos en la misma red también presentan el problema (para descartar equipo vs. red).',
                'Reinicia el router o punto de acceso y espera al menos 2–3 minutos a que restablezca la conexión.',
                'Ejecuta pruebas básicas: ping al gateway, renovar IP (ipconfig /release /renew) y limpiar DNS (ipconfig /flushdns).',
                'Revisa el cable de red, conectores RJ45 y que el adaptador de red aparezca habilitado en el sistema.',
                'Si es Wi-Fi, prueba acercarte al router o cambiar de banda (2.4 / 5 GHz) para descartar interferencias.',
            ];
        }

        // =========================
        // CORREO / EMAIL
        // =========================
        elseif (
            str_contains($text, 'correo') ||
            str_contains($text, 'email') ||
            str_contains($text, 'outlook') ||
            str_contains($category, 'correo')
        ) {
            $pool = [
                'Confirma que el usuario y la contraseña sean correctos e intenta acceder desde el webmail.',
                'Revisa la conexión a internet y verifica si otros sitios se cargan normalmente.',
                'Valida la configuración del servidor de correo (IMAP/POP3/SMTP, puertos y cifrado).',
                'Limpia la bandeja de salida, archivos PST/OST en mal estado y ejecuta una reparación de Outlook si aplica.',
                'Consulta si existe alguna política de tamaño límite o bloqueo de adjuntos que esté afectando el envío.',
            ];
        }

        // =========================
        // SOFTWARE / SISTEMA / PROGRAMAS
        // =========================
        elseif (
            str_contains($text, 'error') ||
            str_contains($text, 'aplicación') ||
            str_contains($text, 'programa') ||
            str_contains($text, 'sistema') ||
            str_contains($category, 'software')
        ) {
            $pool = [
                'Anota el mensaje de error exacto y en qué momento aparece (inicio, carga de archivo, guardar, etc.).',
                'Cierra y vuelve a abrir la aplicación; si es posible, reinicia el equipo y prueba de nuevo.',
                'Limpia archivos temporales, caché del sistema y revisa si hay actualizaciones pendientes del programa.',
                'Reinstala o repara la aplicación desde su instalador oficial manteniendo las configuraciones necesarias.',
                'Verifica permisos del usuario (lectura/escritura) sobre las carpetas o archivos que utiliza el sistema.',
            ];
        }

        // =========================
        // CASO GENÉRICO
        // =========================
        else {
            $pool = [
                'Reproduce el problema y documenta paso a paso lo que haces y el resultado obtenido.',
                'Verifica conexiones físicas, accesos, credenciales y permisos relacionados con el servicio afectado.',
                'Realiza pruebas básicas de reinicio (equipo, servicio o aplicación) y valida si el comportamiento cambia.',
                'Consulta registros del sistema (logs, visor de eventos o consola) para identificar mensajes de error.',
                'Registra la evidencia (capturas, hora, usuario, equipo) para documentar la incidencia o escalarla.',
            ];
        }

        // ---- Tomar máximo 3 pasos, mezclados para dar pequeña variación ----
        shuffle($pool);
        return array_slice($pool, 0, 3);
    }
}
