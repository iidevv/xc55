<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\Model\Repo\Image\Product;


use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class Image extends \XLite\Model\Repo\Image\Product\Image
{
    protected function prepareCndProductId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('i.product = :productId')
            ->setParameter('productId', (int) $value);
    }

}