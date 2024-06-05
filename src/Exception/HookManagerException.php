<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Exception;

use Exception;

final class HookManagerException extends Exception
{
    public static function fromRunHook(Exception $e, string $moduleId, string $hookType): self
    {
        $message = "Error in runHook method (hookType: {$hookType}, moduleId: $moduleId)\n"
            . $e->getMessage();

        return new self($message, $e->getCode(), $e);
    }

    public static function fromEmptyModuleId(string $class): self
    {
        return new self("moduleId was not parsed from {$class}");
    }

    public static function fromEmptyHookType(string $class, string $moduleId): self
    {
        return new self("hookType is empty (moduleId: {$moduleId}, class: {$class})");
    }
}
