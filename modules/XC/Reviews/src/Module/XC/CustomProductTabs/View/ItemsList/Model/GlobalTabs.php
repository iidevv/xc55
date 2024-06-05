<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Module\XC\CustomProductTabs\View\ItemsList\Model;

use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Product\GlobalTab;

/**
 * GlobalTabs
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\CustomProductTabs")
 */
class GlobalTabs extends \XC\CustomProductTabs\View\ItemsList\Model\GlobalTabs
{
    protected function getTabHelpText(GlobalTab $model)
    {
        if ($model->getServiceName() === 'Reviews') {
            return static::t(
                'Tab displaying product reviews. Added by the addon Product Reviews',
                [
                    'url' => Manager::getRegistry()->getModuleServiceURL('XC\Reviews'),
                ]
            );
        }

        return parent::getTabHelpText($model);
    }
}
