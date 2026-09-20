<?php

namespace App\Http\Middleware;

use App\Helpers\SysUtils;
use App\Support\TenantResourceResolver;
use Closure;
use Illuminate\Http\Request;

class ResolveTenantResource
{
    public function __construct(private TenantResourceResolver $resolver)
    {
    }

    public function handle(
        Request $request,
        Closure $next,
        string $resource,
        string $source,
        string $ability = 'view',
        ?string $attribute = null
    )
    {
        $codedId = $request->route($source) ?? $request->input($source);
        abort_unless(is_string($codedId), 404);

        $model = $resource === 'photo'
            ? $this->resolver->resolvePhoto($codedId, SysUtils::getLoggedInUser())
            : $this->resolver->resolve($resource, $codedId, SysUtils::getLoggedInUser(), $ability);

        $request->attributes->set($attribute ?? 'tenant.' . $resource, $model);

        return $next($request);
    }
}
