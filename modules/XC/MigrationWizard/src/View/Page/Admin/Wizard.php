<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Page\Admin;

use XCart\Extender\Mapping\ListChild;

/**
 * Wizard dialog
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Wizard extends \XLite\View\Dialog
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/wizard.less';

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/wizard.js';

        return $list;
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return 'migration_wizard';
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/MigrationWizard';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/wizard.twig';
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

    /**
     * Used in modules/XC/MigrationWizard/templates/web/admin/modules/XC/MigrationWizard/wizard.twig to meet rules from wizard.less
     */
    public function getStepName(): string
    {
        return str_replace('_', '-', \Includes\Utils\Converter::convertFromCamelCase(call_user_func_array([\XLite::getController(), 'getStepName'], [])));
    }
}
