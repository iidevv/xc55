<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Model\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wholesale price model (abstract)
 *
 * @ORM\MappedSuperclass
 */
abstract class AWholesalePrice extends \XLite\Model\AEntity
{
    public const WHOLESALE_TYPE_PRICE = 'price';
    public const WHOLESALE_TYPE_PERCENT = 'percent';

    /**
     * Wholesale price unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=false)
     */
    protected $type = self::WHOLESALE_TYPE_PRICE;

    /**
     * Value
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $price = 0.0000;

    /**
     * Quantity range (begin)
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $quantityRangeBegin = 1;

    /**
     * Quantity range (end)
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $quantityRangeEnd = 0;

    /**
     * Relation to a membership entity
     *
     * @var \XLite\Model\Membership
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Membership")
     * @ORM\JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")
     */
    protected $membership;

    /**
     * Return Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return owner
     *
     * @return mixed
     */
    abstract public function getOwner();

    /**
     * Return Price
     *
     * @return float
     */
    abstract public function getPrice();

    /**
     * Return Owner Price
     *
     * @return float
     */
    abstract public function getOwnerPrice();

    /**
     * Set Price
     *
     * @param float $price
     *
     * @return static
     */
    abstract public function setPrice($price);

    /**
     * Return DiscountType
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set DiscountType
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Return QuantityRangeBegin
     *
     * @return int
     */
    public function getQuantityRangeBegin()
    {
        return $this->quantityRangeBegin;
    }

    /**
     * Set QuantityRangeBegin
     *
     * @param int $quantityRangeBegin
     *
     * @return $this
     */
    public function setQuantityRangeBegin($quantityRangeBegin)
    {
        $this->quantityRangeBegin = $quantityRangeBegin;
        return $this;
    }

    /**
     * Return QuantityRangeEnd
     *
     * @return int
     */
    public function getQuantityRangeEnd()
    {
        return $this->quantityRangeEnd;
    }

    /**
     * Set QuantityRangeEnd
     *
     * @param int $quantityRangeEnd
     *
     * @return $this
     */
    public function setQuantityRangeEnd($quantityRangeEnd)
    {
        $this->quantityRangeEnd = $quantityRangeEnd;
        return $this;
    }

    /**
     * Return Membership
     *
     * @return \XLite\Model\Membership
     */
    public function getMembership()
    {
        return $this->membership;
    }

    /**
     * Set Membership
     *
     * @param \XLite\Model\Membership $membership
     *
     * @return $this
     */
    public function setMembership($membership)
    {
        $this->membership = $membership;
        return $this;
    }

    /**
     * Get clear price (required for net and display prices calculation)
     *
     * @return float
     */
    public function getClearPrice()
    {
        if ($this->getType() === static::WHOLESALE_TYPE_PERCENT) {
            return $this->getOwnerPrice() * $this->getPrice() / 100;
        }

        return $this->getPrice();
    }

    /**
     * Get net Price
     *
     * @return float
     */
    public function getNetPrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getClearPrice', ['taxable'], 'net');
    }

    /**
     * Get display Price
     *
     * @return float
     */
    public function getDisplayPrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getNetPrice', ['taxable'], 'display');
    }

    /**
     * Get clear display Price
     *
     * @return float
     */
    public function getClearDisplayPrice()
    {
        return $this->getDisplayPrice();
    }

    /**
     * Get "SAVE" value (percent difference)
     *
     * @return integer
     */
    public function getSavePriceValue()
    {
        if (!$this->getOwner()) {
            return 0;
        }

        if (\XLite::getController() instanceof \XLite\Controller\Customer\ACustomer) {
            $profile = \XLite::getController()->getCart()->getProfile()
                ?: \XLite\Core\Auth::getInstance()->getProfile();
            $membership = $profile
                ? $profile->getMembership()
                : null;
        } else {
            $membership = \XLite\Core\Auth::getInstance()->getProfile()
                ? \XLite\Core\Auth::getInstance()->getProfile()->getMembership()
                : null;
        }

        $price = $this->getRepository()->getPrice(
            $this->getOwner(),
            $this->getOwner()->getMinQuantity($membership),
            $membership
        );

        if (is_null($price)) {
            $price = $this->getOwner()->getBasePrice();
        }

        if ($price == 0) {
            return 0;
        }

        return max(0, (float)round(($price - $this->getClearPrice()) / $price * 100, 2));
    }

    /**
     * Return true if this price is for 1 item of product and for all customers
     *
     * @return boolean
     */
    public function isDefaultPrice()
    {
        return $this->getQuantityRangeBegin() == 1
            && is_null($this->getMembership())
            && $this->isPersistent();
    }

    /**
     * Returns "true" if owner is taxable
     *
     * @return boolean
     */
    public function getTaxable()
    {
        return $this->getOwner()->getTaxable();
    }
}
