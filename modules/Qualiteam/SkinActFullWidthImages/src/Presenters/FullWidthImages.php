<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFullWidthImages\Presenters;

use Qualiteam\SkinActFullWidthImages\Model\Image\Product\FullWidthImage;
use XLite\Model\Product;

class FullWidthImages
{
    /**
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    public function getImages(Product $product): array
    {
        return $product
            ? $product->getPublicFullWidthImages()
            : [];
    }

    /**
     * @param \Qualiteam\SkinActFullWidthImages\Model\Image\Product\FullWidthImage $image
     *
     * @return string
     */
    public function getImageAlt(FullWidthImage $image): string
    {
        return $image->getAlt();
    }

    /**
     * @param \XLite\Model\Product $product
     *
     * @return bool
     */
    public function isVisible(Product $product): bool
    {
        return $product->countFullWidthImages() > 0;
    }
}