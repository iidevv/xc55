<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Migration\Action;

/**
 * Abstract action
 */
abstract class AAction extends \XLite\View\Dialog
{
    /**
     * Get directory where template is located
     *
     * @return string
     */
    protected function getDir()
    {
        return static::MIGRATION_WIZARD_MODULE_PATH . '/actions';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.action';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->isAvailableAction($this);
    }

    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'migration_wizard';

        return $list;
    }
}
