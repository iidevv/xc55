<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\SearchPanel\Product\Admin;

/**
 * Main admin product search panel
 */
class Main extends \XLite\View\SearchPanel\Product\Admin\Main
{
    /**
     * @inheritdoc
     */
    protected function getFormClass()
    {
        return 'QSL\BackInStock\View\Form\ItemsList\Product\Search';
    }
}
