<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

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
        $list[] = 'color_swatches';

        return $list;
    }


    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        if (Auth::getInstance()->hasRootAccess()) {
            $list['color_swatches'] = [
                'weight'   => 900,
                'title'    => static::t('Color Swatches'),
                'template' => 'modules/QSL/ColorSwatches/swatches/list.twig',
            ];
        }

        return $list;
    }
}
