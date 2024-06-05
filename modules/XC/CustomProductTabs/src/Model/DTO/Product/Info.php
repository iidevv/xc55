<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @inheritdoc
     */
    public function afterCreate($object, $rawData = null)
    {
        parent::afterCreate($object, $rawData);

        \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->createGlobalTabsAliases($object);
    }
}
