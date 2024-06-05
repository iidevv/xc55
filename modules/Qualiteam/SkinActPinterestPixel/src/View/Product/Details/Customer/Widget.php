<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\View\Product\Details\Customer;

use XC\ProductVariants\Model\ProductVariant;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
abstract class Widget extends \XLite\View\Product\Details\Customer\Widget
{
    protected function getDefaultAttributeValues()
    {
        $variantId = Request::getInstance()->variant_id;

        if (isset($variantId) && !empty($variantId)) {

            /** @var ProductVariant $variant */
            $variant = \XLite\Core\Database::getRepo(ProductVariant::class)->findOneBy(['id' => $variantId]);

            if ($variant) {
                $paramValues = [];

                foreach ($variant->getValues() as $value) {
                    $paramValues[] = $value->getAttribute()->getId() . "_" . $value->getId();
                }

                return implode(',', $paramValues);
            }
        }

        return parent::getDefaultAttributeValues();
    }
}