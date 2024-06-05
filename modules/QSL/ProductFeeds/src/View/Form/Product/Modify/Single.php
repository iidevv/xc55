<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\Form\Product\Modify;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Product Modify form.
 * @Extender\Mixin
 */
class Single extends \XLite\View\Form\Product\Modify\Single
{
    /**
     * Set validators pairs for products data.
     *
     * @param mixed &$data Data
     *
     * @return void
     */
    protected function setDataValidators(&$data)
    {
        $data->addPair('nextag_id', new \XLite\Core\Validator\TypeInteger(), null, 'NexTag category');
        $data->addPair('shopzilla_id', new \XLite\Core\Validator\TypeInteger(), null, 'Shopzilla category');
        $data->addPair('pricegrabber_id', new \XLite\Core\Validator\TypeInteger(), null, 'Pricegrabber category');
        $data->addPair('ebay_id', new \XLite\Core\Validator\TypeInteger(), null, 'eBay Commerce Network category');
        $data->addPair('google_id', new \XLite\Core\Validator\TypeInteger(), null, 'Google Shopping category');
    }
}
