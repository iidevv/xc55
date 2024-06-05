<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\CDev\SimpleCMS\View\Menu\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Primary menu
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Top extends \XLite\View\Menu\Customer\Top implements ProviderInterface
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/CDev/SimpleCMS/top_menu.js';

        return $list;
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'More' => static::t('More'),
        ];
    }
}
