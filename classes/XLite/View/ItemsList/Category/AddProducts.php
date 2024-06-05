<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Category;

use XLite\Core\Request;
use XLite\View\ItemsList\Model\ProductSelection;

/**
 * Product selections items list
 */
class AddProducts extends ProductSelection
{
    /**
     * Necessary values added to the list of the form options.
     *
     * @return array
     */
    protected function getFormOptions()
    {
        $id = (int)Request::getInstance()->id;

        return array_merge(
            parent::getFormOptions(),
            [
                'target' => 'category_product_selections',
                'params' => [
                    'id'           => $id,
                    'redirect_url' => $this->buildURL(
                        'category_products',
                        '',
                        [ 'id' => $id ]
                    ),
                ],
            ]
        );
    }
}
