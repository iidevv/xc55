<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Helper;

interface IdGetterInterface
{
    /**
     * @param object $entity
     *
     * @return int|string
     */
    public function getId(object $entity);
}
