<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 */
abstract class Attribute extends \XLite\Model\Attribute
{

    /**
     * @param \XLite\Model\Repo\ARepo $repo
     * @param \XLite\Model\Product    $product
     * @param array                   $data
     * @param int                     $id
     * @param mixed                   $value
     *
     * @return array
     */
    protected function setAttributeValueSelectItem(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data,
        $id,
        $value
    ) {
        $result = parent::setAttributeValueSelectItem($repo, $product, $data, $id, $value);

        if (isset($data['linked_product_id'])
            && ($product_id = (int)$data['linked_product_id'][$id])
            && $product_id > 0
            && $attributeValue = $result[1])
        {
            $repo = Database::getRepo('XLite\Model\Product');
            $product = $repo->find($product_id);
            $attributeValue->setLinkedProduct($product);
        } elseif (isset($data['linked_product_id'][$id])
            && empty($data['linked_product_id'][$id])
            && $attributeValue = $result[1])
        {
            $attributeValue->setLinkedProduct(null);
        }

        return $result;
    }

    protected function setAttributeValueCheckboxItem(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data,
        $value
    ) {
        $result = parent::setAttributeValueCheckboxItem($repo, $product, $data, $value);

        $attributeValue = $repo->findOneBy(
            [
                'product'   => $product,
                'attribute' => $this,
                'value'     => $value,
            ]
        );

        if ($attributeValue) {
            $value = (int) $value;
            if (isset($data['linked_product_id'])
                && ($product_id = (int)$data['linked_product_id'][$value])
                && $product_id > 0)
            {
                $repo = Database::getRepo('XLite\Model\Product');
                $product = $repo->find($product_id);
                $attributeValue->setLinkedProduct($product);
            } else {
                $attributeValue->setLinkedProduct(null);
            }

        }

        return $result;
    }
}