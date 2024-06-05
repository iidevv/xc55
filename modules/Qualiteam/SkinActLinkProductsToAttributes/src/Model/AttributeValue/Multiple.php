<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\Model\AttributeValue;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute value (select)
 * @Extender\Mixin
 */
abstract class Multiple extends \XLite\Model\AttributeValue\Multiple
{
    /**
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Product")
     * @ORM\JoinColumn (name="linked_product_id", referencedColumnName="product_id", onDelete="SET NULL")
     */
    protected $linked_product;

    public function getLinkedProduct()
    {
        return $this->linked_product;
    }

    public function setLinkedProduct($linked_product)
    {
        if ($this->getAttribute()->getDisplayMode() !== \QSL\ColorSwatches\Model\Attribute::COLOR_SWATCHES_MODE) {
            $this->linked_product = $linked_product;
        } else {
            $this->linked_product = null;
        }
    }

    public function getLinkedProductId()
    {
        return $this->getLinkedProduct() ? $this->getLinkedProduct()->getProductId() : null;
    }

    public function cloneEntity()
    {
        $newEntity = parent::cloneEntity();

        if ($this->getLinkedProduct()) {
            $newEntity->setLinkedProduct($this->getLinkedProduct());
        }

        return $newEntity;
    }
}