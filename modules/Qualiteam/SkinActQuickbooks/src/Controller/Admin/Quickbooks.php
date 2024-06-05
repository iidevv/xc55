<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Admin;

use XLite\Controller\Admin\AAdmin;
use XLite\Core\Database;
use XLite\Core\Request;

class Quickbooks extends AAdmin
{
    const OPTIONS = [];

    public function getTitle()
    {
        return '"QuickBooks Connector" Addon Settings';
    }

    public function getOptions()
    {
        $options = Database::getRepo('XLite\Model\Config')->findByCategoryAndVisible('Qualiteam\SkinActQuickbooks');

        return array_filter($options, function ($option) {
            return in_array($option->getName(), static::OPTIONS);
        });
    }

    /**
     * Update model
     */
    public function doActionUpdate()
    {
        $data = array_filter(Request::getInstance()->getData(), function ($key) {
            return in_array($key, static::OPTIONS);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($data as $k => $v) {
            Database::getRepo('XLite\Model\Config')->createOption(
                [
                    'category' => 'Qualiteam\SkinActQuickbooks',
                    'name'     => $k,
                    'value'    => $v,
                ]
            );
        }
    }
}