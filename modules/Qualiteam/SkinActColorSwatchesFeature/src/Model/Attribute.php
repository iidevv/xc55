<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\Model;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * Abstract class attribute
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
        \XLite\Model\Product    $product,
        array                   $data,
                                $id,
                                $value
    ) {
        $result = parent::setAttributeValueSelectItem($repo, $product, $data, $id, $value);

        if (isset($data['shipdate']) && $attributeValue = $result[1]) {
            $attributeValue->setShipdate($data['shipdate'][$id]);
        }

        return $result;
    }
}
