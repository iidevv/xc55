<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Search controller
 * @Extender\Mixin
 */
class Search extends \XLite\Controller\Customer\Search
{
    /**
     * @inheritdoc
     */
    protected function doActionSearch()
    {
        parent::doActionSearch();

        $sessionCell = \XLite\View\ItemsList\Product\Customer\Search::getSessionCellName();
        $data = \XLite\Core\Session::getInstance()->{$sessionCell};
        if ($data) {
            \QSL\Segment\Core\Mediator::getInstance()->doProductsSearch($data);
        }
    }
}
