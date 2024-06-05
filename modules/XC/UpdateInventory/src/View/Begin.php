<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UpdateInventory\View;

use XCart\Extender\Mapping\Extender;

/**
 * Begin import section widget
 * @Extender\Mixin
 */
class Begin extends \XLite\View\Import\Begin
{
    /**
     * Return samples URL
     *
     * @return string
     */
    protected function getSamplesURL()
    {
        return $this->isUpdateQty()
            ? static::t('https://support.x-cart.com/en/articles/5356146-update-inventory-add-on')
            : parent::getSamplesURL();
    }

    /**
     * Return samples URL text
     *
     * @return string
     */
    protected function getSamplesURLText()
    {
        return $this->isUpdateQty()
            ? static::t('Update quantity import guide')
            : parent::getSamplesURLText();
    }

    /**
     * Return true if current widget used in 'update_inventory' page
     *
     * @return boolean
     */
    protected function isUpdateQty()
    {
        return $this->getImportTarget() == \XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY;
    }
}
