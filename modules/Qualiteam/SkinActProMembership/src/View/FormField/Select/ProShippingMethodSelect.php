<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View\FormField\Select;

use XLite\Core\Database;

class ProShippingMethodSelect extends \XLite\View\FormField\Select\Regular
{

    protected function getDefaultOptions()
    {
        /** @var \XLite\Model\Shipping\Method[] $methods */
        $methods = Database::getRepo('XLite\Model\Shipping\Method')->findBy([
            'added' => 1
        ]);

        $list = [
            0 => static::t('SkinActProMembership shipping method for paid membership not selected')
        ];

        if ($methods) {
            foreach ($methods as $method) {
                $list[$method->getMethodId()] = $method->getName();
            }
        }

        return $list;
    }

}