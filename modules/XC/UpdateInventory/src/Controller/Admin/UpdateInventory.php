<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UpdateInventory\Controller\Admin;

/**
 * Update inventory page controller
 */
class UpdateInventory extends \XLite\Controller\Admin\Import
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Import & export');
    }

    /**
     * Get import target
     *
     * @return string
     */
    public function getImportTarget()
    {
        return \XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY;
    }

    /**
     * Get array of import options
     *
     * @param array $options Array of additional options OPTIONAL
     *
     * @return array
     */
    protected function getImportOptions($options = [])
    {
        $options = parent::getImportOptions($options);

        $options['target'] = \XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY;
        $options['warningsAccepted'] = true;
        $options['importMode'] = \XLite\View\Import\Begin::MODE_UPDATE_ONLY;

        if (!empty(\XLite\Core\Request::getInstance()->options['delimiter'])) {
            $options['delimiter'] = \XLite\Core\Request::getInstance()->options['delimiter'];
        }

        return $options;
    }
}
