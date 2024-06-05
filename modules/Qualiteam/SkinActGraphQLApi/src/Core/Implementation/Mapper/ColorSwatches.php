<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;


class ColorSwatches
{
    /**
     * @return array
     */
    public function mapColorSwatches(\XLite\Model\Product $product)
    {
        $swatches = $product->getColorSwatches();

        if ($swatches) {

            $result = [];

            foreach ($swatches as $swatch) {
                if ($swatch) {
                    $result[] = [
                        'color' => $swatch->getColor(),
                        'name' => $swatch->getName(),
                    ];
                }
            }

            return $result;
        }

        return [];
    }
}
