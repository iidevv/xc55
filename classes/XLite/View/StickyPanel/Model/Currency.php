<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Model;

/**
 * Panel for Currency management form.
 */
class Currency extends \XLite\View\StickyPanel\Model\AModel
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'sticky_panel/currency.js';

        return $list;
    }
}
