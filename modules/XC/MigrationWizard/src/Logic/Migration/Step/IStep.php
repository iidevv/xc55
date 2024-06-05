<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration\Step;

/**
 * Step Interface
 */
interface IStep
{
    /**
     * Return step display title
     *
     * @return string
     */
    public static function getLineTitle();
}
