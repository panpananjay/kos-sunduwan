<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PwaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Pastikan kita hanya menyentuh response yang berupa HTML (bukan file/JSON)
        if (method_exists($response, 'getContent') && str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $content = $response->getContent();

            // Tag Manifest & Theme Color
            $pwaHead = '
    <link rel="manifest" href="/build/manifest.webmanifest">
    <meta name="theme-color" content="#f43f5e">
    <link rel="apple-touch-icon" href="/images/logo-sunduwan-pwa.png">';

            // Script Service Worker
            $pwaScript = "
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>";

            // Suntikkan kodenya secara otomatis
            $content = str_replace('</head>', $pwaHead . '</head>', $content);
            $content = str_replace('</body>', $pwaScript . '</body>', $content);

            $response->setContent($content);
        }

        return $response;
    }
}