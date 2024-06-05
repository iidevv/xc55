<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\Reviews\Mapper;

use Doctrine\Common\Collections\Collection;
use XC\Reviews\Model\Review;

/**
 * Class Cart
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\Reviews")
 *
 */

class Product extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product
{
    /**
     * @param \XC\Reviews\Model\Product $product
     *
     * @return \XcartGraphqlApi\DTO\ProductDTO
     */
    public function mapToDto(\XLite\Model\Product $product, array $fields = [])
    {
        $dto = parent::mapToDto($product, $fields);

        $mapper = new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\Reviews\Mapper\Review();

        $dto->review_rate = $product->getAverageRating();
        $dto->votes_count = $product->getVotesCount();
        $dto->reviews     = array_map(static function ($review) use ($mapper) {
            return $mapper->mapToArray($review);
        }, \XLite\Core\Database::getRepo(Review::class)
            ->search(new \XLite\Core\CommonCell([
                'product'   => $product,
                'status'    => Review::STATUS_APPROVED
            ])));

        return $dto;
    }
}
