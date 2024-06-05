<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ShopByBrand\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XLite\Core\Database;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ShopByBrand\Mapper\Brand;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class Brands
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\ShopByBrand")
 *
 */

class Brands extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\Brands
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
        $mapper = new Brand();

        return array_map(static function ($item) use ($mapper) {
            return $mapper->mapToArray($item);
        }, $this->getBrands());
    }

    protected function getBrands()
    {
        return Database::getRepo(\QSL\ShopByBrand\Model\Brand::class)
            ->findAll();
    }
}
