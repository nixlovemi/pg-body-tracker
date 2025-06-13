<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Manipula a requisição e aplica headers de cache.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Apenas para rotas GET públicas e status 200
        if (
            $request->isMethod('GET') &&
            $response->getStatusCode() === 200 &&
            $this->isPublicPage($request)
        ) {
            $response->headers->set('Cache-Control', 'public, max-age=86400, s-maxage=86400');
        }

        return $response;
    }

    /**
     * Define quais rotas devem ter cache público.
     */
    protected function isPublicPage(Request $request): bool
    {
        $pathsToCache = [
            '/',
            'privacy',
            'terms',
            'faq',
            'sitemap.xml',
        ];

        foreach ($pathsToCache as $path) {
            if ($request->is($path)) {
                return true;
            }
        }

        // Também cacheia âncoras internas (ex: /#features)
        return $request->path() === '/';
    }
}
