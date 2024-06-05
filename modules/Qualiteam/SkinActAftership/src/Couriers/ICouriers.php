<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Couriers;

/**
 * Interface couriers
 */
interface ICouriers
{
    /**
     * Create courier item
     *
     * @return void
     */
    public function create(): void;

    /**
     * Update help block info after create/update action
     *
     * @return void
     */
    public function update(): void;
}
