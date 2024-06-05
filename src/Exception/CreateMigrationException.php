<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Exception;

use Exception;
use Throwable;

final class CreateMigrationException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = "Error in getCreateMigration method.\n{$message}";

        parent::__construct($message, $code, $previous);
    }
}
