<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product;


use GraphQL\Type\Definition\ResolveInfo;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CommonError;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Database;

class ProductVariantImage implements ResolverInterface
{

    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        /** @var \XLite\Model\Product $product */
        $product = Database::getRepo('\XLite\Model\Product')->find($args['productId'] ?? 0);

        if (!$product) {
            throw new CommonError('Product not found');
        }

        $variants = $product->getVariantsCollection()->toArray();

        if (empty($variants)) {
            throw new CommonError('Product has no variants');
        }

        $varData = [];
        /** @var \XC\ProductVariants\Model\ProductVariant $variant */
        foreach ($variants as $variant) {
            // $variant->getAttributeValue()
            $attrsValues = $variant->getValues();

            foreach ($attrsValues as $aValue) {
                $id = $aValue->getId();
                $val = $aValue->asString();
                $varData[$variant->getId()][] = [
                    'id' => $id,
                    'val' => $val,
                    'isMatch' => false
                ];

            }

        }


        foreach ($args['selectedOptions'] as $selectedOption) {

            foreach ($varData as $vid => &$options) {
                foreach ($options as &$option) {
                    if ($option['id'] == $selectedOption['optionId']
                        //    && $option['val'] == $selectedOption['value'] // no need to check since one option has one value
                    ) {
                        $option['isMatch'] = true;
                    }
                }
                unset($option);
            }
            unset($options);
        }

        foreach ($varData as $vid => $options) {
            $allOptionsMatched = true;
            foreach ($options as $option) {
                if ($option['isMatch'] === false) {
                    $allOptionsMatched = false;
                }
            }
            if ($allOptionsMatched) {
                foreach ($variants as $variant) {
                    if ($variant->getId() == $vid
                        && $variant->getImage()
                        && !empty($variant->getImage()->getURL())
                    ) {
                        return $variant->getImage()->getURL();
                    }
                }
            }
        }

        // no matches - default image
        if ($product->getImage() && !empty($product->getImage()->getURL())) {
            return $product->getImage()->getURL();
        }

        throw new CommonError('Product has no image');
    }
}