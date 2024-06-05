<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Logic\Import\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Import step
 * @Extender\Mixin
 */
class Import extends \XLite\Logic\Import\Step\Import
{
    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        parent::finalize();

        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->createNonExistentAliases();
    }
}
