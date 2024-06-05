<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product list item widget
 *
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * Cancel shade using 'cancel-ui-state-disabled' class attribute
     *
     * @return object
     */
    public function getProductCellClass()
    {
        $result = parent::getProductCellClass();

        if (
            $this->getProduct()->isNotAllowedSubscription()
            && !str_contains($result, 'cancel-ui-state-disabled')
        ) {
            $result .= ' cancel-ui-state-disabled';
        }

        return $this->getSafeValue($result);
    }

    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $labels = parent::getLabels();

        if ($this->getProduct()->isNotAllowedSubscription()) {
            // Add label into the beginning of labels list
            $labels = ['subscription' => static::t('Only for registered users')] + $labels;
        }

        return $labels;
    }

    /**
     * Get item hover data for draggable item
     *
     * @return array
     */
    protected function defineItemHoverParamDraggable()
    {
        return $this->getProduct()->isNotAllowedSubscription()
            ? []
            : parent::defineItemHoverParamDraggable();
    }

    /**
     * Is draggable
     *
     * @return boolean
     */
    public function isDraggable()
    {
        return $this->getProduct()->isNotAllowedSubscription()
            ? false
            : parent::isDraggable();
    }

    /**
     * Return true if 'Add to cart' buttons shoud be displayed on the list items
     *
     * @return boolean
     */
    protected function isDisplayAdd2CartButton()
    {
        $product = $this->getProduct();

        $result = parent::isDisplayAdd2CartButton();

        if ($result && $product && $product->isNotAllowedSubscription()) {
            // Disable 'Add to cart' button for upcoming products
            $result = false;
        }

        return $result;
    }
}
