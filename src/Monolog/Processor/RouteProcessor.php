<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Monolog\Processor;

use Monolog\Processor\ProcessorInterface;

/**
 * Should be replaced with \Symfony\Bridge\Monolog\Processor\RouteProcessor once ready
 */
class RouteProcessor implements ProcessorInterface
{
    private array $routeData = [];

    public function __construct()
    {
        // To initialize classes and reserve some memory
        $this->detectRouteData();
    }

    public function __invoke(array $record): array
    {
        $record['extra']['route'] = $this->routeData;

        return $record;
    }

    public function detectRouteData(): void
    {
        if (
            PHP_SAPI !== 'cli'
            && isset($_SERVER['REQUEST_METHOD'])
            && strtolower($_SERVER['REQUEST_METHOD']) === 'post'
        ) {
            $this->routeData = [
                'target' => $this->getParamFromRawRequest('target'),
                'action' => $this->getParamFromRawRequest('action'),
            ];
        }
    }

    protected function getParamFromRawRequest(string $name): string
    {
        return (string)($_REQUEST[$name] ?? '');
    }
}
