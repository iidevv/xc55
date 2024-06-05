<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * AttributeProperty repository
 */
class AttributeProperty extends \XLite\Model\Repo\ARepo
{
    public function generateClassAttributeProperties(\XLite\Model\Product $product, \XLite\Model\ProductClass $class)
    {
        /** @var \XLite\Model\Attribute $attribute  */
        foreach ($class->getAttributes() as $attribute) {
            /** @var \XLite\Model\AttributeProperty $prop  */
            $prop = $this->insert(null);
            $prop->setProduct($product);
            $prop->setAttribute($attribute);
            $prop->setPosition($attribute->getPosition());
            $prop->setDisplayMode($attribute->getDisplayMode());
        }
    }

    public function updateFromAttribute(\XLite\Model\Attribute $attribute)
    {
        $this->createQueryBuilder('ap')
            ->update(\XLite\Model\AttributeProperty::class, 'ap')
            ->set('ap.displayAbove', ':displayAbove')
            ->where('ap.attribute = :attribute')
            ->setParameter('displayAbove', $attribute->getDisplayAbove() ?: 0)
            ->setParameter('attribute', $attribute)
            ->getQuery()
            ->execute();
    }
}
