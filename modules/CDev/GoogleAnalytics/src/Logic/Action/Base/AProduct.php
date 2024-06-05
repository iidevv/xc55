<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action\Base;

use XLite\Model\OrderItem;
use XLite\Model\Product;
use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Logic\Action;

abstract class AProduct extends Action\Base\AAction
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var string
     */
    protected $listName;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var string
     */
    protected $coupon;

    /**
     * @var int
     */
    protected $qty;

    /**
     * Action constructor.
     *
     * @param Product        $product
     * @param string         $listName
     * @param integer        $position
     * @param string         $coupon
     * @param null           $qty
     * @param OrderItem|null $orderItem
     */
    public function __construct(Product $product, string $listName = '', int $position = 0, string $coupon = '', $qty = null, ?OrderItem $orderItem = null)
    {
        $this->product  = $product;
        $this->listName = $listName;
        $this->position = $position;
        $this->coupon   = $coupon;
        $this->qty      = $qty;
    }

    public function isApplicable(): bool
    {
        return parent::isApplicable()
            && GA::getResource()->isECommerceEnabled()
            && $this->product instanceof Product;
    }

    protected function buildRequestData(): array
    {
        return GA::getProductDataMapper()->getAddProductData(
            $this->product,
            $this->listName,
            $this->position,
            $this->coupon,
            $this->qty
        );
    }
}
