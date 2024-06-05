<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class RecordPrice extends \QSL\BackInStock\Model\RecordPrice
{
    /**
     * Variant
     *
     * @var \XC\ProductVariants\Model\ProductVariant
     *
     * @ORM\ManyToOne  (targetEntity="XC\ProductVariants\Model\ProductVariant", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="variant_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $variant;

    /**
     * Set variant
     *
     * @param \XC\ProductVariants\Model\ProductVariant $variant
     *
     * @return static
     */
    public function setVariant(\XC\ProductVariants\Model\ProductVariant $variant = null)
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Get variant
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * Return extended product name for record
     *
     * @return string
     */
    public function getExtendedRecordProductName()
    {
        $name = parent::getExtendedRecordProductName();

        if ($variant = $this->getVariant()) {
            $attrs = [];
            foreach ($variant->getValues() as $attributeValue) {
                if ($attributeValue->getAttribute()->isVariable($this->getProduct())) {
                    $attrs[] = $attributeValue->getAttribute()->getName() . ': ' . $attributeValue->asString();
                }
            }
            $name .= ' ' . implode(', ', $attrs);
        }

        return $name;
    }

    /**
     * @inheritdoc
     */
    public function checkWaiting()
    {
        $variant = $this->getVariant();
        if (!$variant) {
            return parent::checkWaiting();
        }

        $result = false;
        $product = $this->getProduct();
        $price = $this->getPrice();
        $currentPrice = $this->getCurrentPrice();
        if (
            $product
            && (
                ($price && $variant->getPrice() <= $price)
                || (!$price && $variant->getPrice() <= $currentPrice)
            )
        ) {
            $this->markAsBack();
            $result = true;
        }

        return $result;
    }
}
