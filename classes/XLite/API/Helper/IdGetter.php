<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Helper;

class IdGetter implements IdGetterInterface
{
    protected string $methodName;

    public function __construct(
        string $methodName
    ) {
        $this->methodName = $methodName;
    }

    public function getId(object $entity)
    {
        return $entity->{$this->methodName}();
    }
}
