<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Relation to product views statistics
     *
     * @var   \CDev\ProductAdvisor\Model\ProductStats
     *
     * @ORM\OneToMany (targetEntity="CDev\ProductAdvisor\Model\ProductStats", mappedBy="viewed_product",
     *            fetch="LAZY")
     */
    protected $views_stats;

    /**
     * Relation to product purchase statistics
     *
     * @var   \CDev\ProductAdvisor\Model\ProductStats
     *
     * @ORM\OneToMany (targetEntity="CDev\ProductAdvisor\Model\ProductStats", mappedBy="bought_product",
     *            fetch="LAZY")
     */
    protected $purchase_stats;


    /**
     * Returns true if product is classified as a new product
     *
     * @return boolean
     */
    public function isNewProduct()
    {
        $currentDate = \XLite\Core\Converter::getDayEnd(static::getUserTime());

        $daysOffset = \CDev\ProductAdvisor\Main::getNewArrivalsOffset();

        return \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_enabled
               && $this->getArrivalDate()
               && $this->getArrivalDate() < $currentDate
               && $this->getArrivalDate() > $currentDate - 86400 * $daysOffset;
    }

    /**
     * Returns true if product is classified as an upcoming product
     *
     * @return boolean
     */
    public function isUpcomingProduct()
    {
        return \XLite\Core\Config::getInstance()->CDev
               && \XLite\Core\Config::getInstance()->CDev->ProductAdvisor
               && \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled
               && $this->getArrivalDate()
               && $this->getArrivalDate() > \XLite\Core\Converter::getDayEnd(static::getUserTime());
    }

    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    public function isShowStockWarning()
    {
        return $this->isUpcomingProduct()
            ? false
            : parent::isShowStockWarning();
    }

    /**
     * Add views_stats
     *
     * @param \CDev\ProductAdvisor\Model\ProductStats $viewsStats
     *
     * @return Product
     */
    public function addViewsStats(\CDev\ProductAdvisor\Model\ProductStats $viewsStats)
    {
        $this->views_stats[] = $viewsStats;
        return $this;
    }

    /**
     * Get views_stats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getViewsStats()
    {
        return $this->views_stats;
    }

    /**
     * Add purchase_stats
     *
     * @param \CDev\ProductAdvisor\Model\ProductStats $purchaseStats
     *
     * @return Product
     */
    public function addPurchaseStats(\CDev\ProductAdvisor\Model\ProductStats $purchaseStats)
    {
        $this->purchase_stats[] = $purchaseStats;
        return $this;
    }

    /**
     * Get purchase_stats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchaseStats()
    {
        return $this->purchase_stats;
    }
}
