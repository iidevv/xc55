<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator;

use XLite\Model\AEntity;

/**
 * Abstract class DTO
 */
abstract class BaseConverter
{
    /**
     * @fixme leaking abstraction = AEntity
     * Convert entity to DTO
     *
     * @param AEntity $entity
     *
     * @return array
     */
    abstract public function convert(AEntity $entity): array;
}
