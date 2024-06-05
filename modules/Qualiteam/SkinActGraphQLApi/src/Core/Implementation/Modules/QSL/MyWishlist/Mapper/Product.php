<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\MyWishlist\Mapper;

use Doctrine\Common\Collections\Collection;

/**
 * Class Cart
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\MyWishlist")
 *
 */

class Product extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product
{
    /**
     * @param \QSL\MyWishlist\Model\Product $product
     *
     * @return \XcartGraphqlApi\DTO\ProductDTO
     */
    public function mapToDto(\XLite\Model\Product $product, array $fields = [])
    {
        $dto = parent::mapToDto($product, $fields);

        $dto->is_wishlisted = $this->isProductInWishlist($product);

        return $dto;
    }

    /**
     * @param $product
     *
     * @return bool
     */
    protected function isProductInWishlist($product)
    {
        $context = \XLite::getGraphQLContext();

        $profile = $context->getLoggedProfile() ?: null;
        $wishlist = \QSL\MyWishlist\Core\Wishlist::getInstance()->getWishlist(null, $profile);

        return (bool) $wishlist
            && (bool) $wishlist->getWishlistLink($product);
    }
}
