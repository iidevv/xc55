<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Page;

use XCart\Extender\Mapping\ListChild;

/**
 * Record page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Record extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['back_in_stock_record']);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/BackInStock/record/body.twig';
    }
}
