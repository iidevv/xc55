<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("!XC\MultiVendor")
 */
abstract class Settings extends \XLite\View\Model\Settings
{
    /**
     * Check if current page is page with sale module settings
     *
     * @return boolean
     */
    protected function isSaleModuleSettings()
    {
        return $this->getTarget() === 'module'
            && $this->getModule()
            && $this->getModule() === 'CDev-Sale';
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFieldsForSection($section)
    {
        $list = parent::getSchemaFieldsForSection($section);

        if ($this->isSaleModuleSettings()) {
            unset($list['allow_vendors_edit_discounts']);
        }

        return $list;
    }
}
