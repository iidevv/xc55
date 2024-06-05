<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Monolog\Processor;

use Monolog\Processor\ProcessorInterface;
use XLite;

class ExecutionContextProcessor implements ProcessorInterface
{
    private string $context = 'unknown';

    public function __construct()
    {
        // To initialize classes and reserve some memory
        $this->detectContext();
    }

    public function __invoke(array $record): array
    {
        $this->detectContext();
        $record['extra']['execution_context'] = $this->context;

        return $record;
    }

    protected function detectContext(): void
    {
        if (PHP_SAPI === 'cli') {
            $this->context = 'cli';
        } elseif (XLite::isAdminZone()) {
            $this->context = 'web_admin';
        } else {
            $this->context = 'web_customer';
        }
    }
}
