<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AEntity;

/**
 * Quickbooks products model
 *
 * @ORM\Entity
 * @ORM\Table  (name="quickbooks_products",
 *      indexes={
 *          @ORM\Index (name="product_id", columns={"product_id"}),
 *          @ORM\Index (name="variant_id", columns={"variant_id"}),
 *          @ORM\Index (name="price", columns={"price"}),
 *          @ORM\Index (name="quickbooks_fullname", columns={"quickbooks_fullname"}),
 *          @ORM\Index (name="quickbooks_editsequence", columns={"quickbooks_editsequence"}),
 *          @ORM\Index (name="quickbooks_listid", columns={"quickbooks_listid"})
 *      }
 * )
 */
class QuickbooksProducts extends AEntity
{
    /**
     * Product ID
     *
     * @var XLite\Model\Product
     *
     * @ORM\Id
     * @ORM\OneToOne   (targetEntity="XLite\Model\Product", cascade={"merge","detach","persist"})
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product_id;
    
    /**
     * Variant ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column (type="integer", options={"default": 0, "unsigned": true})
     */
    protected $variant_id;
    
    /**
     * Price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $price = 0.0000;
    
    /**
     * Quickbooks fullname
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $quickbooks_fullname;
    
    /**
     * Quickbooks editsequence
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $quickbooks_editsequence;

    /**
     * Quickbooks listid
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $quickbooks_listid;
    
    /**
     * Get product_id
     * 
     * @return integer
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set product_id
     * 
     * @param integer $id
     * 
     * @return QuickbooksProducts
     */
    public function setProductId($id)
    {
        $this->product_id = $id;
        
        return $this;
    }
    
    /**
     * Get variant_id
     * 
     * @return integer
     */
    public function getVariantId()
    {
        return $this->variant_id;
    }

    /**
     * Set variant_id
     * 
     * @param integer $id
     * 
     * @return QuickbooksProducts
     */
    public function setVariantId($id)
    {
        $this->variant_id = $id;
        
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
     * Set price
     *
     * @param float $price
     *
     * @return QuickbooksProducts
     */
    public function setPrice($price)
    {
        $this->price = \XLite\Core\Converter::toUnsigned32BitFloat($price);

        return $this;
    }
    
    /**
     * Set quickbooks fullname
     *
     * @param string $value
     *
     * @return QuickbooksProducts
     */
    public function setQuickbooksFullname($value)
    {
        $this->quickbooks_fullname = $value;
    }
    
    /**
     * Set quickbooks editsequence
     *
     * @param string $value
     *
     * @return QuickbooksProducts
     */
    public function setQuickbooksEditsequence($value)
    {
        $this->quickbooks_editsequence = $value;
    }
    
    /**
     * Set quickbooks listid
     *
     * @param string $value
     *
     * @return QuickbooksProducts
     */
    public function setQuickbooksListid($value)
    {
        $this->quickbooks_listid = $value;
    }
    
    /**
     * Get Quickbooks Fullname
     *
     * @return string
     */
    public function getQuickbooksFullname()
    {
        return $this->quickbooks_fullname;
    }

    /**
     * Get Quickbooks Editsequence
     *
     * @return string
     */
    public function getQuickbooksEditsequence()
    {
        return $this->quickbooks_editsequence;
    }

    /**
     * Get Quickbooks Listid
     *
     * @return string
     */
    public function getQuickbooksListid()
    {
        return $this->quickbooks_listid;
    }
}