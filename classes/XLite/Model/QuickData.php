<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quick data
 *
 * @ORM\Entity
 * @ORM\Table (name="quick_data",
 *      indexes={
 *          @ORM\Index (name="customerArea", columns={"membership_id","product_id"})
 *      }
 * )
 */
class QuickData extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Product
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="quickData")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $price = 0.0000;

    /**
     * Membership (relation)
     *
     * @var \XLite\Model\Membership
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Membership", inversedBy="quickData")
     * @ORM\JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")
     */
    protected $membership;

    /**
     * Zone (relation)
     *
     * @var \XLite\Model\Zone
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Zone")
     * @ORM\JoinColumn (name="zone_id", referencedColumnName="zone_id", onDelete="CASCADE")
     */
    protected $zone;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return QuickData
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
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
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return QuickData
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set membership
     *
     * @param \XLite\Model\Membership $membership
     * @return QuickData
     */
    public function setMembership(\XLite\Model\Membership $membership = null)
    {
        $this->membership = $membership;
        return $this;
    }

    /**
     * Get membership
     *
     * @return \XLite\Model\Membership
     */
    public function getMembership()
    {
        return $this->membership;
    }

    /**
     * Set zone
     *
     * @param \XLite\Model\Zone $zone
     * @return QuickData
     */
    public function setZone(\XLite\Model\Zone $zone = null)
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * Get zone
     *
     * @return \XLite\Model\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }
}
