<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class AttributeOption extends \XLite\Model\AttributeOption
{
    /**
     * Attribute value (select)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueSelect", mappedBy="attribute_option", cascade={"all"})
     */
    protected $attributeValueS;

    /**
     * Check if it is an option of the "brand" attribute.
     *
     * @return bool
     */
    public function isBrandAttribute()
    {
        return $this->getAttribute()->isBrandAttribute();
    }

    /**
     * Creates a new brand model and associates it with the attribute option.
     */
    public function createAssociatedBrand()
    {
        $brand = new \QSL\ShopByBrand\Model\Brand();
        $brand->setOption($this);
        $brand->getRepository()->insert($brand);
    }

    /**
     * Delete the brand model associated with the attribute option.
     */
    public function deleteAssociatedBrand()
    {
        $brand = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->findOneByOption($this);
        if ($brand) {
            $brand->getRepository()->delete($brand);
        }
    }

    /**
     * Add attributeValueS
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $attributeValueS
     *
     * @return AttributeOption
     */
    public function addAttributeValueS(\XLite\Model\AttributeValue\AttributeValueSelect $attributeValueS)
    {
        $this->attributeValueS[] = $attributeValueS;

        return $this;
    }

    /**
     * Get attributeValueS
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributeValueS()
    {
        return $this->attributeValueS;
    }
}
