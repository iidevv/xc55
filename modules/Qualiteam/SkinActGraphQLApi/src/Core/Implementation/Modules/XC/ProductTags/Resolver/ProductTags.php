<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\ProductTags\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XLite\Core\Database;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Mapper\Offer;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\ProductTags\Mapper\Tag;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class Offers
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\ProductTags")
 *
 */

class ProductTags extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\ProductTags
{
    /**
     * @param                                    $val
     * @param                                    $args
     * @param XCartContext                       $context
     * @param ResolveInfo                        $info
     *
     * @return array|mixed
     * @throws \Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $mapper = new Tag();
        $tags = $this->getTags();

        return array_map(function ($item) use ($mapper) {
            return $mapper->mapToArray($item);
        }, $tags);
    }

    protected function getTags()
    {
        $repo = Database::getRepo('\XC\ProductTags\Model\Tag');

        return $repo->findAllTags();
    }
}
