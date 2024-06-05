<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product list item widget
 * @Extender\Mixin
 */
abstract class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * Check - if .buttons-container is present
     *
     * @return boolean
     */
    protected function hasButtons()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isLogged()
    {
        return \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Add 'snapshot product' class attribute for the product cell
     *
     * @return string
     */
    public function getProductCellClass()
    {
        $result = parent::getProductCellClass();

        if ($this->getProduct() && $this->getProduct()->isSnapshotProduct()) {
            $result = 'product not-available snapshot-product';
        }

        return $result;
    }

    /**
     * Get item hover parameters
     *
     * @return array
     */
    protected function defineItemHoverParams()
    {
        $result = parent::defineItemHoverParams();
        $result[] = $this->defineItemHoverParamSnapshot();

        return $result;
    }

    /**
     * Get item hover data for item which is not available now (snapshot)
     *
     * @return array
     */
    protected function defineItemHoverParamSnapshot()
    {
        return [
            'text'  => static::t('The product is unavailable in the catalog'),
            'style' => 'snapshot-product',
        ];
    }

    /**
     * Return true if 'Add to cart' buttons shoud be displayed on the list items
     *
     * @return boolean
     */
    protected function isDisplayAdd2CartButton()
    {
        return parent::isDisplayAdd2CartButton() && !$this->getProduct()->isSnapshotProduct();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params[] = $this->isProductIdInWishlist($this->getProductId());

        return $params;
    }

    /**
     * Disable quick-look feature if product is a snapshot
     *
     * @return boolean
     */
    protected function isQuickLookEnabled()
    {
        return parent::isQuickLookEnabled() && !$this->getProduct()->isSnapshotProduct();
    }
}
