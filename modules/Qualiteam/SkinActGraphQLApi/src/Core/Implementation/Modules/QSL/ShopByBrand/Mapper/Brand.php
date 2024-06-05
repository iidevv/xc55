<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ShopByBrand\Mapper;

class Brand
{
    /**
     * @param \QSL\ShopByBrand\Model\Brand $brand
     *
     * @return array
     */
    public function mapToArray(\QSL\ShopByBrand\Model\Brand $brand = null)
    {
        return $brand ? [
            'id' => $brand->getBrandId(),
            'image'=> $brand->getImage() ? $brand->getImage()->getFrontURL() : '',
            'name' => $brand->getName(),
        ] : [
            'id' => '',
            'image'=> '',
            'name' => '',
        ];
    }
}