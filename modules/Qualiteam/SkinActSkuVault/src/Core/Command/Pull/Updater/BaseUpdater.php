<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Pull\Updater;

use XLite\Model\AEntity;

/**
 * Abstract updater
 */
abstract class BaseUpdater
{
    /**
     * @fixme leaking abstraction = AEntity
     * @param AEntity $entity
     * @param array $data
     * @return void
     */
    abstract public function update(AEntity $entity, array $data): void;
}
