<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Product;

use QSL\LoyaltyProgram\Logic\LoyaltyProgram;

/**
 * Widget displaying the amount of reward points customers will earn for purchasing the product.
 * When converting the price to reward points, this widget uses the total price including selected options/attributes.
 *
 * We use the price widget to not modify all the modules which extend the price widget with their surcharges.
 */
abstract class AProductPoints extends \XLite\View\Price
{
    /**
     * Get reward points for a product with selected options.
     *
     * @return integer
     */
    public function getRewardPoints()
    {
        $points = 0;

        $product = $this->getProduct();

        if ($product) {
            $points = $product->hasDefinedRewardPoints()
                ? $product->getRewardPoints()
                : LoyaltyProgram::getInstance()->calculateEarnedRewardPoints($this->getListPrice());
        }

        return $points;
    }

    /**
     * Check whether the item has reward points.
     *
     * @return boolean
     */
    public function hasRewardPoints()
    {
        return 0 < $this->getRewardPoints();
    }

    /**
     * Check whether the product gives a fixed number of reward points.
     *
     * @return boolean
     */
    public function hasFixedPoints()
    {
        $product = $this->getProduct();

        return $product && $product->hasDefinedRewardPoints();
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-reward-points';
    }

    /**
     * Get filename of the promo template.
     *
     * @return string
     */
    abstract protected function getPromoTemplateName();

    /**
     * Get relative path to the default widget template.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/LoyaltyProgram/promo/' . $this->getPromoTemplateName();
    }

    // We hide the message (for products with zero points) with an IF in the template, not with the isVisisble() method.
    // The reason is that we should always display the wrapping div to let AJAX replace it with the new contents.
}
