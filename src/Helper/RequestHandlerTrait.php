<?php

declare(strict_types=1);

namespace Marshal\Util\Helper;

use Marshal\Server\Platform\PlatformInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

trait RequestHandlerTrait
{
    private function getPlatform(ServerRequestInterface $request): PlatformInterface
    {
        $platform = $request->getAttribute(PlatformInterface::class);
        return $platform;
    }

    private function getRouteResult(ServerRequestInterface $request): RouteResult
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        return $routeResult;
    }
}
