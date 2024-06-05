<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping;

/**
 * Shipping Interface
 */
interface IShipping
{
    /**
     * Define shipping processor
     *
     * @return string class
     */
    public static function defineProcessor();
}
