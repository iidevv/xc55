<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Module\XC\CustomProductTabs\View\ItemsList\Model;

use Includes\Utils\Module\Manager;
use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Model\Product\GlobalTab;

/**
 * Class global tabs
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\CustomProductTabs")
 */
class GlobalTabs extends \XC\CustomProductTabs\View\ItemsList\Model\GlobalTabs
{
    use VideoTourTrait;

    /**
     * @param GlobalTab $model
     *
     * @return string
     */
    protected function getTabHelpText(GlobalTab $model)
    {
        if ($model->getServiceName() === $this->getVideoToursLabel()) {
            return static::t(
                'SkinActVideoTour tab displaying video tours list. Added by the addon Video tour',
                [
                    'url' => Manager::getRegistry()->getModuleServiceURL('Qualiteam\SkinActVideoTour'),
                ]
            );
        }

        return parent::getTabHelpText($model);
    }
}