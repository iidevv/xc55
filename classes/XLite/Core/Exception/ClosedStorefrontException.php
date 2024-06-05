<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use XLite\Core\Exception;

class ClosedStorefrontException extends Exception implements HttpExceptionInterface
{
    public function getStatusCode(): int
    {
        return 200;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
