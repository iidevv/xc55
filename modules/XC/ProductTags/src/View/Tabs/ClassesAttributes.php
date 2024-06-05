<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ClassesAttributes extends \XLite\View\Tabs\ClassesAttributes
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'tags';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list['tags'] = [
            'weight'   => 200,
            'title'    => static::t('Product tags'),
            'widget' => 'XC\ProductTags\View\Tags',
        ];

        return $list;
    }
}
