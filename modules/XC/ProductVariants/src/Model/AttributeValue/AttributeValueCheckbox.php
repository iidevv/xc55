<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Model\AttributeValue;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute value (checkbox)
 *
 * @Extender\Mixin
 */
class AttributeValueCheckbox extends \XLite\Model\AttributeValue\AttributeValueCheckbox
{
    /**
     * Variants
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany (targetEntity="XC\ProductVariants\Model\ProductVariant", mappedBy="attributeValueC", cascade={"all"})
     */
    protected $variants;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
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
     * @return AttributeValueCheckbox
     */
    public function addVariants(\XC\ProductVariants\Model\ProductVariant $variants)
    {
        $this->variants[] = $variants;
        return $this;
    }

    /**
     * Get variants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVariants()
    {
        return $this->variants;
    }
}
