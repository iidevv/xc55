<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Pull\Updater;

/**
 * Interface IObjectUpdater
 */
interface IObjectUpdater
{
    /**
     * Get DTO
     *
     * @param array $data
     * @return void
     *
     * @throws UpdaterException
     */
    public function updateFromData(array $data): void;
}
