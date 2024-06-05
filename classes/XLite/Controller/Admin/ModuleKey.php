<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * ModuleKey
 */
class ModuleKey extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Enter license key');
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['view']);
    }

    /**
     * Action of view license view
     *
     * @return void
     */
    protected function doActionView()
    {
    }
}
