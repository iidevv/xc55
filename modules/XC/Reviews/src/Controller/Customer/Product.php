<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Product page controller
 * @Extender\Mixin
 */
abstract class Product extends \XLite\Controller\Customer\Product
{
    /**
     * @inheritdoc
     */
    public function handleRequest()
    {
        if ($rkey = \XLite\Core\Request::getInstance()->rkey) {
            // rkey is passed in parameters
            if ($reviewKey = $this->detectReviewKey($rkey)) {
                $rkeys = \XLite\Core\Session::getInstance()->savedReviewKeys;
                if (!$rkeys) {
                    $rkeys = [];
                }
                $rkeys[] = $reviewKey->getId();
                \XLite\Core\Session::getInstance()->savedReviewKeys = array_unique($rkeys);
            }
            $this->redirect($this->buildURL('product', '', [
                'product_id' => $this->getProductId(),
            ]) . '#product-details-tab-reviews');
        }

        parent::handleRequest();
    }

    /**
     * Return review key object
     *
     * @param string $rkey rkey parameter value
     *
     * @return \XC\Reviews\Model\OrderReviewKey
     */
    protected function detectReviewKey($rkey)
    {
        $reviewKey = $this->getReviewKey($rkey);

        if ($reviewKey && !$reviewKey->getFirstClickDate()) {
            // Save date of first click on link with rkey
            $reviewKey->setFirstClickDate(\XLite\Core\Converter::time());
            \XLite\Core\Database::getEM()->flush($reviewKey);
        }

        return $reviewKey;
    }
}
