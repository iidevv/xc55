<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FroalaEditor\Controller\Admin;

class FroalaSettings extends \XLite\Controller\Admin\Settings
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Froala Editor settings');
    }

    /**
     * Return current module options
     *
     * @return array
     */
    public function getOptions($getAllOptions = false)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config')
            ->findByCategoryAndVisible('XC\\FroalaEditor');
    }
}
