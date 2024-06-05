<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Presenter;

use XLite\Model\Product;

class YotpoReviews
{

    /** @var \XLite\Model\Product|null */
    protected ?Product $product = null;

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return void
     */
    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getProductSku(): string
    {
        return $this->product ? $this->product->getSku() : '';
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->product ? $this->product->getName() : '';
    }

    /**
     * @return string
     */
    public function getProductUrl(): string
    {
        return $this->product ? $this->product->getURL() : '';
    }

    /**
     * @return string
     */
    public function getProductImageUrl(): string
    {
        $image = $this->product?->getImageURL();
        return $image ?? '';
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return \XLite::getInstance()->getCurrency()->getCode();
    }

    /**
     * @return float
     */
    public function getProductPrice(): float
    {
        return $this->product ? $this->product->getDisplayPrice() : 0;
    }
}