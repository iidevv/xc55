<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * ATabs is a component allowing you to display multiple widgets as tabs depending
 * on their targets
 * 
 * @Extender\Mixin
 */
abstract class ATabs extends \XLite\View\Tabs\ATabs
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActQuickbooks/common/tabs.css';

        return $list;
    }

    /**
     * Returns the default widget template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActQuickbooks/common/tabs.twig';
    }
}