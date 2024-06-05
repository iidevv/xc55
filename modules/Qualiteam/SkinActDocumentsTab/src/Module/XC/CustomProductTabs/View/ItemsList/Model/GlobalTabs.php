<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActDocumentsTab\Module\XC\CustomProductTabs\View\ItemsList\Model;

use Includes\Utils\Module\Manager;
use Qualiteam\SkinActDocumentsTab\Trait\DocumentsTabTrait;
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
    use DocumentsTabTrait;

    /**
     * @param GlobalTab $model
     *
     * @return string
     */
    protected function getTabHelpText(GlobalTab $model)
    {
        if ($model->getServiceName() === $this->getDocumentsTabLabel()) {
            return static::t(
                'SkinActDocumentsTab tab displaying documents list. Added by the addon Documents Tab',
                [
                    'url' => Manager::getRegistry()->getModuleServiceURL('Qualiteam\SkinActDocumentsTab'),
                ]
            );
        }

        return parent::getTabHelpText($model);
    }
}