<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Back in stock record (stock control)
 *
 * @ORM\Entity (repositoryClass="QSL\BackInStock\Model\Repo\Record")
 * @ORM\Table (name="back2stock_records")
 * @ORM\HasLifecycleCallbacks
 */
class Record extends \QSL\BackInStock\Model\ARecord
{
    public const DEFAULT_QUANTITY = 1;

    /**
     * Desired quantity
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $quantity;

    /**
     * Current quantity
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $currentQuantity;

    /**
     * @inheritdoc
     */
    public function checkWaiting()
    {
        $result = false;
        $product = $this->getProduct();
        $quantity = $this->getQuantity();
        if (
            $product
            && (
                ($product->getPublicAmount() > 0 && ($quantity === null || $quantity === 1))
                || ($quantity > 1 && $product->getPublicAmount() >= $quantity)
            )
            && ($product->getArrivalDate() <= 0 || $product->getArrivalDate() < \XLite\Core\Converter::time())
        ) {
            $this->markAsBack();
            $result = true;
        }

        return $result;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return static
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get current quantity
     *
     * @return integer
     */
    public function getCurrentQuantity()
    {
        return $this->currentQuantity;
    }

    /**
     * Set current quantity
     *
     * @param integer $currentQuantity
     *
     * @return static
     */
    public function setCurrentQuantity($currentQuantity)
    {
        $this->currentQuantity = $currentQuantity;

        return $this;
    }
}
