<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Module\XC\CustomProductTabs\View\ItemsList\Model;

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
        if ($model->getServiceName() === 'Questions') {
            $url = \Includes\Utils\Module\Manager::getRegistry()
                ->getModuleServiceURL('QSL', 'ProductQuestions');

            return static::t(
                'Tab displaying product questions. Added by the addon Product Questions',
                [
                    'url' => $url,
                ]
            );
        }

        return parent::getTabHelpText($model);
    }
}
