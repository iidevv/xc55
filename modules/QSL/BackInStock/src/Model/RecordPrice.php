<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Back in stock record (price control)
 *
 * @ORM\Entity (repositoryClass="QSL\BackInStock\Model\Repo\RecordPrice")
 * @ORM\Table (name="back2stock_price_records")
 * @ORM\HasLifecycleCallbacks
 */
class RecordPrice extends \QSL\BackInStock\Model\ARecord
{
    /**
     * Desired price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4, nullable=true)
     */
    protected $price;

    /**
     * Current price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $currentPrice;

    /**
     * @inheritdoc
     */
    public function checkWaiting()
    {
        $result = false;
        $product = $this->getProduct();
        $price = $this->getPrice();
        $currentPrice = $this->getCurrentPrice();
        if (
            $product
            && (
                ($price && $product->getPrice() <= $price)
                || (!$price && $product->getPrice() <= $currentPrice)
            )
        ) {
            $this->markAsBack();
            $result = true;
        }

        return $result;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return static
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get current price
     *
     * @return float
     */
    public function getCurrentPrice()
    {
        return $this->currentPrice;
    }

    /**
     * Set current price
     *
     * @param float $currentPrice
     *
     * @return static
     */
    public function setCurrentPrice($currentPrice)
    {
        $this->currentPrice = $currentPrice;

        return $this;
    }
}
