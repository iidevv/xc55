<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Model\AttributeValue;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute value (select)
 *
 * @Extender\Mixin
 */
class AttributeValueSelect extends \XLite\Model\AttributeValue\AttributeValueSelect
{
    /**
     * Variants
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany (targetEntity="XC\ProductVariants\Model\ProductVariant", mappedBy="attributeValueS", cascade={"all"})
     */
    protected $variants;

    /**
     * @var boolean
     */
    protected $variantAvailable = true;

    /**
     * @var boolean
     */
    protected $variantOutOfStock = false;

    /**
     * @var boolean
     */
    protected $variantStockWarning = false;

    /**
     * @var int
     */
    protected $availableAmount = 0;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->variants = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add variants
     *
     * @param \XC\ProductVariants\Model\ProductVariant $variants
     */
    public function addVariants(\XC\ProductVariants\Model\ProductVariant $variants)
    {
        $this->variants[] = $variants;
    }

    /**
     * Get variants
     *
     * @return \Doctrine\Common\Collections\Collection|\XC\ProductVariants\Model\ProductVariant[]
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @return boolean
     */
    public function isVariantAvailable()
    {
        return $this->variantAvailable;
    }

    /**
     * @param boolean $variantAvailable
     */
    public function setVariantAvailable($variantAvailable)
    {
        $this->variantAvailable = $variantAvailable;
    }

    public function isVariantOutOfStock(): bool
    {
        return $this->variantOutOfStock;
    }

    public function setVariantOutOfStock(bool $variantOutOfStock): void
    {
        $this->variantOutOfStock = $variantOutOfStock;
    }

    public function isVariantStockWarning(): bool
    {
        return $this->variantStockWarning;
    }

    public function setVariantStockWarning(bool $variantStockWarning): void
    {
        $this->variantStockWarning = $variantStockWarning;
    }

    public function getAvailableAmount(): int
    {
        return $this->availableAmount;
    }

    public function setAvailableAmount(int $amount): void
    {
        $this->availableAmount = $amount;
    }
}
