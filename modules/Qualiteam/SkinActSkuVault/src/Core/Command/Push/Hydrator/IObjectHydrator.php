<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator;

/**
 * Interface IObjectHydrator
 */
interface IObjectHydrator
{
    /**
     * Get DTO
     *
     * @return array
     * @throws HydratorException
     */
    public function getDTO(): array;
}
